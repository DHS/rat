<ul class="tabs">
  <li<?php if ($this->uri['action'] == 'index' || $this->uri['action'] == 'dashboard') { echo ' class="active"'; } ?>><?php $this->link_to('Dashboard', 'admin'); ?></li>
  <li<?php if ($this->uri['action'] == 'spec') { echo ' class="active"'; } ?>><?php $this->link_to('Spec', 'admin', 'spec'); ?></li>
  <li<?php if ($this->uri['action'] == 'signups') { echo ' class="active"'; } ?>><?php $this->link_to('Beta signups', 'admin', 'signups'); ?></li>
  <li<?php if ($this->uri['action'] == 'users') { echo ' class="active"'; } ?>><?php $this->link_to('Users', 'admin', 'users'); ?></li>
<?php if (isset($this->plugins->log)) { ?>
  <li<?php if ($this->uri['action'] == 'history') { echo ' class="active"'; } ?>><?php $this->link_to('Log', 'admin', 'history'); ?></li>
<?php } ?>
</ul>