<?php

/*
*	A gravatar plugin for Rat by @DHS
*
*	Installation
*	
*		Comes installed by default
*
*	Usage
*	
*		To display a users's gravatar:
*		
*			if (isset($GLOBALS['gravatar']))
*				echo $GLOBALS['gravatar']->show($_SESSION['user']['email'], array('user_id' => $_SESSION['user']['id'], 'size' => 20, 'style' => "margin: 10px;"));
*
*/

class gravatar {

	function format_email($email) {
		
		return md5(strtolower(trim($email)));
				
	}
	
	function show($email, $params = array()) {
		
		$email = $this->format_email($email);
		
		// start the gravatar string
		$return = '<img src="http://www.gravatar.com/avatar/'.$email;

		// if size is set, add it in
		if ($params['size'])
			$return .= '?s='.$params['size'];
		
		$return .= '"';
		
		// if style is set, add it in
		if ($params['style'])
			$return .= ' style="'.$params['style'].'"';

		$return .= ' />';
		
		// if user_id is set, make it a link
		if ($params['user_id'])
			$return = '<a href="user.php?id='.$params['user_id'].'">'.$return.'</a>';
		
		return $return;
		
	}
	
	function show_settings($email) {

echo '<h2>Picture</h2>
'.$this->show($email).'
<p>Visit <a href="http://gravatar.com/">Gravatar.com</a> to change your picture.</p>';
	
	}

}

?>