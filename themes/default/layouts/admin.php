<?php

$this->loadPartial('header');

echo '<div class="row">
  <div class="span8 columns offset4">';

$this->loadView('admin/menu');

include "themes/{$this->config->theme}/$view.php";

echo '</div></div>';

$this->loadPartial('footer');

?>