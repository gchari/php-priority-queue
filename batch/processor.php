<?php

$processorId = uniqid();
$wait = 0;
while (true) {
	sleep(1);

	$ch = curl_init("app:80/index.php/api/task");
	curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $job = curl_exec($ch);
	$job = json_decode($job, true);
	/*
	 * Wait for 10 second if queue is empty.
	 * Consecutive 3 waits will stop the processor.
	 */
	if (!$job) {
		if ($wait >= 3) {
			# Stop the processor.
			#exit(0);
			file_put_contents("/app/src/run.log", "Sleeping for 100s....." . "\n", FILE_APPEND);
			sleep(100);
		} else {
			file_put_contents("/app/src/run.log", "Sleeping for 10s....." . "\n", FILE_APPEND);
			sleep(10);
			$wait++;
			continue;
		}
	}

	$wait = 0;
	$job['status'] = 'PROCESSING';
	$job['processorId'] = $processorId;
	try {
		$message = "Processor $processorId is executing command: " . $job['command'] . "Priority:" . $job['command'];
		file_put_contents("/app/src/run.log", $message . "\n", FILE_APPEND);
		$job['status'] = 'PROCESSED';
	} catch(Exception $e) {
		$job['status'] = 'FAILED';
	}

	$ch = curl_init("app:80/index.php/api/task");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($job));
    $res = curl_exec($ch);
}