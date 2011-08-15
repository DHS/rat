<?php

require_once 'config/init.php';

$segment1 = $_GET['segment1'];
$segment2 = $_GET['segment2'];
$segment3 = $_GET['segment3'];
$segment4 = $_GET['segment4'];
$segment5 = $_GET['segment5'];
$segment6 = $_GET['segment6'];

// If user is logged out, app is private and page is not in public_pages then show splash page
if ($_SESSION['user'] == NULL && $app->config->private == TRUE && in_array($segment1, $app->config->public_pages) == FALSE) {

	if (count($app->admin->list_users()) == 0 && $segment1 == 'admin' && $segment2 == 'setup') {

		// Make an exception for setup
		
		// So at the moment, setup requires $app->config->private to be TRUE
		// and admin.php must NOT be in public_pages
		
	} else {

		// Show splash page
		$app->loadView('header');
		$app->loadView('splash');
		$app->loadView('footer');

		// Stop processing the rest of the page
		exit();		
		
	}

}

if ($segment1 == '' && $segment2 == '' && $segment3 == '' && $segment4 == '' && $segment5 == '' && $segment6 == '') {
	// All segments empty ie. root url
	
	$app->loadController('index');
	
} elseif (substr($segment1, -5) == '.json') {
	// Segment1 ends in .json so either search or user
	
	if (substr($segment1, 0, 6) == 'search') {
		//echo 'Search results json for: "'.urlencode($segment2).'"';
		$app->loadController('search')->json($_GET['q']);
	} else {
		//echo 'User json for: "'.substr($segment1, 0, -5).'"';
		$app->loadController('users')->json(substr($segment1, 0, -5));
	}
	
} elseif (file_exists("controllers/$segment1.php") == TRUE) {
	// Controller file exists, load with function and params if available
	
	$app->controller = $app->loadController($segment1);

	// Check to see if controller has been updated with a class, still works if not :)
	if (get_class($app->controller)) {
		
		if ($segment1 == 'search') {
			$app->controller->index($_GET['q']);
		} elseif ($segment2 != '') {
			$app->controller->$segment2($segment3);
		} else {
			$app->controller->index();
		}
		
	}

} elseif (is_numeric($segment2) == TRUE) {
	// Paginated user view
	
	//echo "Username: $segment1, page: $segment2";
	$app->loadController('users')->show($segment1, $segment2);
	
} elseif ($segment2 == $app->config->items['name']) {
	
	if ($segment3 == '' || $segment3 == 'add') {
		// Show new item form
		
		$app->loadController('item');
		
	} elseif (substr($segment3, -5) == '.json') {
		// Item json
		
		echo 'JSON for #'.substr($segment3, 0, -5);
		
	} elseif ($segment4 == 'update') {
		// Update item
		
		echo "Update #$segment3";
		
	} elseif ($segment4 == 'remove') {
		// Remove item
		
		echo "Remove #$segment3";
		
	} elseif ($segment4 == 'likes') {
		// List likes
	
		//echo "Likes for #$segment3";
		$app->loadController('likes')->show($segment3);
	
	} elseif ($segment4 == 'likes.json') {
		// Likes json
		
		//echo "Likes JSON for #$segment3";
		$app->loadController('likes')->json($segment3);

	} elseif ($segment4 == 'like') {
		
		if ($segment5 == 'add') {
			// Add like
			
			//echo "Add like to #$segment3";
			$app->loadController('likes')->add($_SESSION['user']['id'], $segment3);
			
		} elseif($segment5 == 'remove') {
			// Remove like
			
			//echo "Remove like from #$segment3";
			$app->loadController('likes')->remove($_SESSION['user']['id'], $segment3);
			
		}

	} elseif ($segment4 == 'comments') {
		// List comments

		//echo "Comments for #$segment3";
		$app->loadController('comments')->show($segment3);

	} elseif ($segment4 == 'comments.json') {
		// Comments json
   
		//echo "Comments JSON for #$segment3";
		$app->loadController('comments')->json($segment3);
		
	} elseif ($segment4 == 'comment') {
		
		if ($segment5 == 'add') {
   			// Add comment
			
			//echo "Add comment to item #$segment3";
			$app->loadController('comments')->add($_SESSION['user']['id'], $segment3, $_POST['content']);
			
		} elseif($segment6 == 'remove') {
   			// Remove comment
			
			//echo "Remove comment #$segment5";
			$app->loadController('comments')->remove($_SESSION['user']['id'], $segment3, $segment5);
			
		}

	} else {
		// No extra gubbins so just show item
		
		$_GET['id'] = $segment3;
		$app->loadController('item');
		
	}
		
} elseif ($segment1 != '') {
	// User profile

	$app->loadController('users')->show($segment1);

} else {
	// Router is puzzled
	
	echo '404 - Page not found';
	
}

?>