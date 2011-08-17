<?php

$app->loadPartial('header');

include "themes/{$this->config->theme}/$view.php";

$app->loadPartial('footer');

?>