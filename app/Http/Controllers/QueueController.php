<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Http\Response\ResponseFactory;
use App\Exception\InvalidInputException;

class QueueController extends Controller
{
    /**
     * Store a new podcast.
     *
     * @param  Request  $request
     * @return Response
     */
    public function addOrUpdateTask(Request $request)
    {
        try {
            $jobIds = array();
            if ($request->isMethod('post')) { # Add
                $jobsdata = $request->post();

                if (empty($jobsdata)) {
                    throw new InvalidInputException(json_encode(["error" => "InvalidInput."]));
                }

                foreach ($jobsdata as $jobdata) {
                    $jobIds[] = $this->addOrUpdate($jobdata);
                }
            } else if ($request->isMethod('put')) { # Update
                $jobdata = $request->all();
                $jobIds[] = $this->addOrUpdate($jobdata);
            }
            $response = ResponseFactory::getResponse('success', $jobIds);
        } catch (InvalidInputException $e) {
            $response = ResponseFactory::getResponse(get_class($e), $e->getMessage());
        } catch (Exception $e) {
            $response = ResponseFactory::getResponse('error', $e->getMessage());
        }

        return $response;
    }

    public function getTask(Request $request)
    {
        try {
            $job = array();
            DB::beginTransaction();

            try { 
                $job = DB::table('queuejobs')->where('status', 'PENDING')->orderByRaw("FIELD(status, \"HIGH\", \"MEDIUM\", \"LOW\")")->limit(1)->lockForUpdate()->first();

            } catch (Exception $e) {
                DB::rollback();
                throw new DBReadException(json_encode(["error" => "Could not read record."]));
            }

            if ($job) {
                DB::table('queuejobs')
                    ->where('id', $job->id)
                    ->update(['status' => 'DISPATCHED']);
            }
            DB::commit();
            $response = ResponseFactory::getResponse('success', json_encode($job));
        } catch (DBReadException $e) {
            $response = ResponseFactory::getResponse(get_class($e), $e->getMessage());
        } catch (Exception $e) {
            $response = ResponseFactory::getResponse('error', $e->getMessage());
        }

        return $response;
    }

    public function getTaskById(Request $request)
    {
        try {
            if ($request->id) {

                $job = Redis::get('job:id:'.$request->id);

                if (!$job) {
                    $job = DB::select('select * from queuejobs where id = ?', [$request->id]);
                    Redis::set('job:id:'.$$job['id'], json_encode($job));
                }
                $response = ResponseFactory::getResponse('success', $job);
            } else {
                $err = ['error' => 'id not found'];
                $response = ResponseFactory::getResponse('notfound', $err);
            }
        } catch (Exception $e) {
            $response = ResponseFactory::getResponse('error', $e->getMessage());
        }

        return $response;
    }

    private function addOrUpdate($jobdata = array()) {


        if (!isset($jobdata['producerId']) 
              || !isset($jobdata['command']) 
              || !isset($jobdata['priority'])
            ) {

            return NULL;
        }

        if (isset($jobdata['id'])) {
            $update = true;
        } else {
            $update = false;
        }

        DB::beginTransaction();
        if ($update) {
            DB::table('queuejobs')
            ->updateOrInsert(
                ['id' => $jobdata['id']],
                ['processorId' => $jobdata['processorId'], 'status' => $jobdata['status']]
            );
        } else {
            $jobdata['id'] = DB::table('queuejobs')
            ->insertGetId(
                ['producerId' => $jobdata['producerId'], 'command' => $jobdata['command']
                    , 'priority' => $jobdata['priority']]
            );
        }

        DB::commit();

        $storedJob = DB::table('queuejobs')->find($jobdata['id']);

        Redis::set('job:id:'.$jobdata['id'], json_encode($storedJob));

        return $jobdata['id'];
    }
}