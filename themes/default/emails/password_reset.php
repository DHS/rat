<?php

$subject = "[{$this->config->name}] Password reset";

$body = '<p>Hi '.$user->username.',</p>
<p>Here is the link to reset your '.$this->config->name.' password:</p>
<p>'.$link.'</p>
<p>You should publish another '.$this->config->items['name'].' to celebrate!</p>
<p>Best regards,</p>
<p>David Haywood Smith, creator of '.$this->config->name.'</p>';

?>