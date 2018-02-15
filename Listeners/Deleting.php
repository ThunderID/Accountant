<?php

namespace Thunderlabid\Finance\Listeners;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

class Deleting
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle event
	 * @param  UserCreated $event [description]
	 * @return [type]             [description]
	 */
	public function handle($event)
	{
		$model = $event->data;
		if ($model->is_deletable === false) 
		{
			throw new AppException($model->errors, AppException::DATA_VALIDATION);
		}
	}
}