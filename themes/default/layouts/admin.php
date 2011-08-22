<?php

$app->loadPartial('header');
$app->loadView('admin/menu');

include "themes/{$this->config->theme}/$view.php";

$app->loadPartial('footer');

?>