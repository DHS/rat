<?php

class Routes {

  // Simple alias
  public $signup = array('users', 'new');

  // Complex routes
  public $complexRoutes = array(

    array('/<username>', 'users', 'show', '<username>'),
    array('/<username>/item/<id>', 'items', 'show', '<id>')

  );

}

?>
