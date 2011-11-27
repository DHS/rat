<?php

$subject = '['.$this->config->name.'] '.$user->username.' is now following you on '.$this->config->name.'!';

$body = '<p>Hi '.$friend['username'].',</p>
<p>Just to let you know that you have a new follower on '.$this->config->name.':</p>
<p>'.$link.'</p>
<p>You should publish another '.$this->config->items['name'].' to celebrate!</p>
<p>Best regards,</p>
<p>David Haywood Smith, creator of '.$this->config->name.'</p>';

?>