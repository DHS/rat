<?php

if (!isset($this->head_title)) {
	if (isset($this->title)) {
		$this->head_title = $this->config->name.' - '.$this->title;
	} else {
		$this->head_title = $this->config->name;
	}
}

if (isset($this->title)) {
	$this->title = '<!-- Page title -->
  <h1>'.$this->title.'</h1>';
}

?>
<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!-- Consider adding an manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <!-- Use the .htaccess and remove these lines to avoid edge case issues.
       More info: h5bp.com/b/378 -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title><?php if (isset($this->head_title)) { echo $this->head_title; } ?></title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

  <!-- CSS: implied media=all -->
  <link rel="stylesheet" href="<?php echo BASE_DIR; ?>/themes/<?php echo $this->config->theme; ?>/css/style.css">
  <!-- Include Twitter Bootstrap http://twitter.github.com/bootstrap/ -->
  <link rel="stylesheet" href="<?php echo BASE_DIR; ?>/themes/<?php echo $this->config->theme; ?>/css/bootstrap-1.0.0.min.css">
  <!-- end CSS-->

  <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

  <!-- All JavaScript at the bottom, except for Modernizr / Respond.
       Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries
       For optimal performance, use a custom Modernizr build: www.modernizr.com/download/ -->
  <script src="<?php echo BASE_DIR; ?>/js/libs/modernizr-2.0.6.min.js"></script>
  <script src="<?php echo BASE_DIR; ?>/js/libs/respond.min.js"></script>
</head>



<body>

  <div id="container" class="container">

<?php if (isset($_SESSION['user_id'])) { ?>

    <div class="topbar">
      <div class="fill">
        <div class="container">
          <h3><?php echo $this->link_to($this->config->name, $this->config->default_controller); ?></h3>
          <ul>
            <li<?php if ($this->uri['controller'] == $this->config->default_controller) { echo ' class="active" '; } ?>><?php echo $this->link_to('Home', $this->config->default_controller); ?></li>
            <li<?php if ($this->uri['controller'] == 'users') { echo ' class="active" '; } ?>><?php echo $this->link_to('My profile', 'users', 'show', $_SESSION['user_id']); ?></li>
            <?php if ($this->config->invites['enabled'] == TRUE) { ?>
            <li<?php if ($this->uri['controller'] == 'invites') { echo ' class="active" '; } ?>><?php echo $this->link_to('Invites', 'invites'); ?></li>
            <?php } ?>
            <li<?php if ($this->uri['params']['id'] == 'help') { echo ' class="active" '; } ?>><?php echo $this->link_to('Help', 'pages', 'show', 'help'); ?></li>
          </ul>
          <ul class="nav secondary-nav">
            <li>
              <form class="nav secondary-nav" action="/search/" method="get">
                <input type="text" name="q" placeholder="Search" value="<?php if ($this->uri['params']['q']) { echo $this->uri['params']['q']; } ?>" />
              </form>
            </li>
            <li class="menu">
              <a href="#" class="menu"><?php $viewer = User::get_by_id($_SESSION['user_id']); echo $viewer->username; ?></a>
              <ul class="menu-dropdown">
                <li><?php echo $this->link_to('Profile', 'users', 'show', $_SESSION['user_id']); ?></li>
                <li><?php echo $this->link_to('Settings', 'users', 'update'); ?></li>
                <li class="divider"></li>
                <li><?php echo $this->link_to('Logout', 'sessions', 'remove'); ?></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>

<?php } else { ?>
	
    <div class="topbar">
      <div class="fill">
        <div class="container">
          <h3><?php echo $this->link_to($this->config->name, $this->config->default_controller); ?></h3>
          <ul class="nav secondary-nav">
            <li<?php if ($this->uri['controller'] == 'users' && $this->uri['action'] == 'add') { echo ' class="active" '; } ?>><?php echo $this->link_to('Signup', 'users', 'add') ?></li>
            <li<?php if ($this->uri['controller'] == 'sessions' && $this->uri['action'] == 'add') { echo ' class="active" '; } ?>><?php echo $this->link_to('Login', 'sessions', 'add') ?></li>
            <li<?php if ($this->uri['params']['id'] == 'help') { echo ' class="active" '; } ?>><?php echo $this->link_to('Help', 'pages', 'show', 'help') ?></li>
          </ul>
        </div>
      </div>
    </div>

<?php } ?>

	<p class="clear">&nbsp;</p>
	<p class="clear">&nbsp;</p>

    <?php if (isset($this->title)) { echo $this->title; } ?>

<?php

if (isset($_SESSION['flash'])) {
	echo '
  <!-- Message -->
  <div class="row">
    <div class="span8 columns offset4">
  	  <div class="alert-message info '.$_SESSION['flash']['category'].'">
        <p>'.$_SESSION['flash']['message'].'</p>
      </div>
    </div>
  </div>';
}

?>

    <div id="main" role="main">

