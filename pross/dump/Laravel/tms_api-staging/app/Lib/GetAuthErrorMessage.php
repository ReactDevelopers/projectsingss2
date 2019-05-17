<?php

namespace App\Lib;

class GetAuthErrorMessage {

	private $applicationName = '';
	private $vendorEmail = '';
	private $serviceDeskEmail = '';
	private $messages = [];

	function __construct () {

		$this->applicationName = env('APPLICATION_NAME');
		$this->vendorEmail = env('VENDOR_EMAIL');
		$this->serviceDeskEmail = env('SERVICE_DESK_EMAIL');

		$messages = file_get_contents(env('AUTH_ERROR_MESSAGE_PATH'));
		$messages = str_replace('{SERVICE_DESK_EMAIL}',$this->serviceDeskEmail, $messages);
		$messages = str_replace('{VENDOR_EMAIL}',$this->vendorEmail, $messages);
		$messages = str_replace('{APPLICATION_NAME}',$this->applicationName, $messages);
		preg_match_all('/(?<=CASE[0-9])'.PHP_EOL.'(.*)(?=\nENDCASE[0-9])/', $messages, $cases);
		$this->messages = $cases;
	}

	function getCase($no=1) {

		return isset($this->messages[0][$no]) ? $this->messages[0][$no] : '';
	}

	function getServiceDeskEmail() {

		return $this->serviceDeskEmail;
	}
}