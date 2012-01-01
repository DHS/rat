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
*			if (isset($this->plugins->gravatar)) {
*				echo $this->plugins->gravatar->show($email, array('user_id' => $_SESSION['user_id'], 'size' => 20, 'style' => "margin: 10px;"));
*			}
*
*/

class gravatar extends Application {

	function format_email($email) {
		
		return md5(strtolower(trim($email)));
				
	}
	
	function show($email, $params = array()) {
		
		$email = $this->format_email($email);
		
		// start the gravatar string
		$return = '<img src="http://www.gravatar.com/avatar/'.$email;

		// if size is set, add it in
		if (isset($params['size'])) {
			$return .= '?s='.$params['size'];
		}
		
		$return .= '"';
		
		// if style is set, add it in
		if (isset($params['style'])) {
			$return .= ' style="'.$params['style'].'"';
		}

		$return .= ' />';
		
		// if user_id is set, make it a link
		if (isset($params['link'])) {
			$return = '<a href="'.$params['link'].'">'.$return.'</a>';
		}
		
		echo $return;
		
	}
	
	function show_settings($email) {

echo '<h2>Picture</h2>
'.$this->show($email).'
<p>Visit <a href="http://gravatar.com/">Gravatar.com</a> to change your picture.</p>';
	
	}

}

?>