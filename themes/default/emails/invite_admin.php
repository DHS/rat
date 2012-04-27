<?php

$subject = '[' . $this->config->name . '] Your ' . $this->config->name . ' invite is here!';

$body = '<p>Hi there,</p>
<p>Your ' . $this->config->name . ' invite is here! Click the following link to get started:</p>
<p>' . $link . '</p>
<p>We value your feedback very highly. Once you\'ve had a play with ' . $this->config->name . ', please reply to this email with your thoughts!</p>
<p>Many thanks,</p>
<p>' . $user->username . ', ' . $this->config->name . ' admin</p>';
