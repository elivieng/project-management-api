<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

/**
* This trait will make it easier to build our API responses, 
* whether they are success or error responses.
*/
trait ApiResponser
{
	private function successResponse($data, $code)
	{
		return response()->json($data, $code);
	}

	protected function errorResponse($message, $code)
	{
		return response()->json(['error' => $message, 'code' => $code], $code);
	}

	protected function showAll(Collection $collection, $code = 200)
	{
		return $this->successResponse($collection, $code);
	}

	protected function showOne(Model $instance, $code = 200)
	{
		return $this->successResponse($instance, $code);
	}

	protected function showMessage($message, $code = 200, $index_name = 'data')
	{
		return $this->successResponse([$index_name => $message], $code);
	}
	
}