<?php

class 404Exception extends Exception {

	public function __construct($uri, $message, $code = 0, Exception $previous = null) {

		parent::__construct($message, $code, $previous);

		$this->uri = $uri;
	
	}

}

?>

