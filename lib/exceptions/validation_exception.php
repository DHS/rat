<?php

class ValidationException extends Exception {

  public $app;

  public function __construct($app, $message, $code = 0, Exception $previous = null) {

    parent::__construct($message, $code);

    $this->app = $app;

  }

}
