<?php

class Routes {

  // Simple alias
  public $signup = array('users', 'new');

  // Complex routes
  public $complexRoutes = array(

    '/<username>' => array('controller' => 'users', 'action' => 'show', 'params' => '<username>'),
    '/<username>/item/<id>' => array('controller' => 'items', 'action' => 'show', 'params' => '<id>')

  );

}

?>
