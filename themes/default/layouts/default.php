<?php

$this->loadPartial('header');

include "themes/{$this->config->theme}/$view.php";

$this->loadPartial('footer');
