<?php

/*
*	A Google Analytics plugin for Rat by @DHS
*
*	Installation
*	
*		Comes installed by default
*
*	Usage
*	
*		To include Google Analytics tracking code:
*		
*			if (is_object($GLOBALS['analytics']))
*				echo $GLOBALS['analytics']->show();
*
*/

class analytics {

	function __construct($analytics_id) {
		
		$this->analytics_id = $analytics_id;
		
	}

	function show() {
		
		if ($this->analytics_id != NULL && SITE_IDENTIFIER == 'live') {
		
			return <<<HTML
<!-- Google Analytics -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '{$this->analytics_id}']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

HTML;
		
		} else {
		
			return NULL;
	
		}
		
	}

}

?>