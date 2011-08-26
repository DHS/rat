

    </div> <!--! end of #main -->

    <footer>

        <p class="float_right"><?php echo $this->config->name; ?> was created using <a href="http://github.com/DHS/rat">Rat</a></p>
        <p>
			<?php echo $this->config->name; ?> &copy; <?php echo date('Y'); ?> &middot;
			<?php echo $this->link_to('About', 'pages', 'show', 'about'); ?> &middot;
			<?php echo $this->link_to('Help', 'pages', 'show', 'help'); ?> &middot;
			<?php echo $this->link_to('Search', 'search'); ?>
		</p>

    </footer>
  </div> <!--! end of #container -->


  <!-- JavaScript at the bottom for fast page loading -->

  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="/js/libs/jquery-1.6.2.min.js"><\/script>')</script>


  <!-- scripts concatenated and minified via ant build script-->
  <script src="<?php echo BASE_DIR; ?>/js/plugins.js"></script>
  <script src="<?php echo BASE_DIR; ?>/js/rat.js"></script>
  <!-- end scripts-->


  <?php
  if (isset($this->plugins->analytics)) {
    $this->plugins->analytics->view();
  }
  ?>


  <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you want to support IE 6.
       chromium.org/developers/how-tos/chrome-frame-getting-started -->
  <!--[if lt IE 7 ]>
    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
    <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
  <![endif]-->
  
</body>
</html>