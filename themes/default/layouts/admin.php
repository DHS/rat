<?php

$this->loadPartial('header');
$this->loadView('admin/menu');

include "themes/{$this->config->theme}/$view.php";

$this->loadPartial('footer');

?>