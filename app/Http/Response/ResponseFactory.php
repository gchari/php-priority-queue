<?php
namespace App\Http\Response;

class ResponseFactory {
	public static function getResponse($type, $message) {

		switch ($type) {
			case 'success':
				$response = response($message, 200)
                  ->header('Content-Type', 'application/json');
				break;
			case 'error':
				$response = response($message, 500)
                  ->header('Content-Type', 'application/json');
				break;
			case 'App\Exception\InvalidInputException':
				$response = response($message, 400)
                  ->header('Content-Type', 'application/json');
				break;
			case 'App\Exception\DBReadException':
				$response = response($message, 202)
                  ->header('Content-Type', 'application/json');
				break;
			case 'notfound':
				$response = response($message, 404)
                  ->header('Content-Type', 'application/json');
				break;
			default:
				$response = response('Internal Error', 500)
                  ->header('Content-Type', 'application/json');
				break;
		}

		return $response;
	}
}