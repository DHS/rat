<?php

$subject = '[' . $this->config->name . '] An invitation from ' . $user->username;

$body = '<p>Hi there,</p>
<p>I think you should check out ' . $this->config->name . '! Click the following link to get started:</p>
<p>' . $link . '</p>
<p>Regards,</p>
<p>' . $user->username . '</p>';
