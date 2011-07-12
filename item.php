<?php

require_once 'config/init.php';

//	Critical: One of the following must be set: item id (to show) or content (to add) or delete

if ($_GET['id'] == '' && $_POST['content'] == '' && $_GET['delete'] == '') {
	$page['name'] = ucfirst($GLOBALS['app']['items']['name']).' not found';
	include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
	exit;
}


if ($_POST['content'] != '') {
	// Process new item

	// Form validation
	
	if ($_POST['content'] == '')
		$error .= ucfirst($GLOBALS['app']['items']['name']).' must include '.strtolower($GLOBALS['app']['items']['content']['name']);

	if ($GLOBALS['app']['items']['uploads']['enabled'] == TRUE) {
		
		//print_r($_FILES['file']);
		
		if ($_FILES['file']['error'] > 0)
			$error .= 'Error code: '.$_FILES['file']['error'].'<br />';
		
		if (!in_array($_FILES['file']['type'], $GLOBALS['app']['items']['uploads']['mime-types']))
			$error .= 'Invalid file type: '.$_FILES['file']['type'].'<br />';
		
		if ($_FILES['file']['size'] > $GLOBALS['app']['items']['uploads']['max-size'])
			$error .= 'File too large.<br />';
		
		if (file_exists("upload/" . $_FILES['file']["name"]))
			$error .= 'File already exists.<br />';
		
	}
	
	// Error processing

	if ($error == '') {
		// No error so proceed...
		
		if ($GLOBALS['app']['items']['uploads']['enabled'] == TRUE) {
			move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/'.$_FILES['file']['name']);
			$item_id = items_add($_SESSION['user']['id'], $_POST['content'], $_POST['title'], $_FILES['file']['name']);
		} else {
			$item_id = items_add($_SESSION['user']['id'], $_POST['content'], $_POST['title']);
		}
		
		// Give points
		if (is_object($GLOBALS['points']))
			$GLOBALS['points']->update($_SESSION['user']['id'], $app['points']['per_item']);

		// Log item add
		if (is_object($GLOBALS['log']))
			$GLOBALS['log']->add($_SESSION['user']['id'], 'item', $item_id, 'add', "title = {$_POST['title']}\ncontent = {$_POST['content']}");

		$message = ucfirst($GLOBALS['app']['items']['name']).' added!';

		// Go forth!
		if (SITE_IDENTIFIER == 'live') {
			header('Location: '.$GLOBALS['app']['url'].'user.php?message='.urlencode($message));
		} else {
			header('Location: '.$GLOBALS['app']['dev_url'].'user.php?message='.urlencode($message));
		}
		
		exit();
		
	
	} else {
		// There was an error
		
		// Propagate get vars to be picked up by the form
		$_GET['title']		= $_POST['title'];
		$_GET['content']	= $_POST['content'];
		
		$app = $GLOBALS['app'];
		
		// Show error message
		$message = $error;
		include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
		include 'themes/'.$GLOBALS['app']['theme'].'/items_new.php';
		include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
		exit();
	
	}
	
} elseif ($_GET['delete'] != '') {
	// Delete an item

	$item = items_get_by_id($_GET['delete']);
	
	if ($_SESSION['user']['id'] == $item['user']['id'] && $item != NULL) {

		// Delete item
		items_remove($_GET['delete']);
		if (is_object($GLOBALS['log']))
			$GLOBALS['log']->add($_SESSION['user']['id'], 'item', $_GET['delete'], 'remove');

		// Delete comments
		if (is_array($item['comments'])) {
			foreach ($item['comments'] as $key => $value) {
				$id = comments_remove($value['user_id'], $item['id'], $value['id']);
				if (is_object($GLOBALS['log']))
					$GLOBALS['log']->add($_SESSION['user']['id'], 'comment', $id, 'remove');
			}
		}
		
		// Delete likes
		if (is_array($item['comments'])) {
			foreach ($item['likes'] as $key => $value) {
				$id = likes_remove($value['user_id'], $item['id']);
				if (is_object($GLOBALS['log']))
					$GLOBALS['log']->add($_SESSION['user']['id'], 'like', $id, 'remove');
			}
		}
		
		// Set message
		$message = ucfirst($GLOBALS['app']['items']['name']).' removed!';

		// Return from whence you came
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit();

	} else {
		// Naughtiness = expulsion!
		
		// Go forth
		if (SITE_IDENTIFIER == 'live') {
			header('Location: '.$GLOBALS['app']['url']);
		} else {
			header('Location: '.$GLOBALS['app']['dev_url']);
		}
		
		exit();
		
	}
	
} elseif ($_GET['id'] != '') {
	// No new item so get item info based on get var
	
	$item = items_get_by_id($_GET['id']);

	// Fail gracefully if item doesn't exist
	if ($item == NULL) {

		$page['name'] = ucfirst($GLOBALS['app']['items']['name']).' not found';
		include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
		include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
		exit;
		
	}

}

// Header

if (is_object($GLOBALS['gravatar']))
	$app['page_title_gravatar'] = $item['user']['email'];

if ($item['user']['full_name'] != NULL) {
	// Full name set so use that for page title
	$page['title'] = $item['title'].' by '.$item['user']['full_name'];
	$app['page_title'] = '<a href="user.php?id='.$item['user']['id'].'">'.$item['user']['full_name'].'</a> on <a href="index.php">'.$app['name'].'</a>';
} else {
	// Full name not set so use username for page title
	$page['title'] = $item['title'].' by '.$item['user']['username'];
	$app['page_title'] = '<a href="user.php?id='.$item['user']['id'].'">'.$item['user']['username'].'</a> on <a href="index.php">'.$app['name'].'</a>';
}

include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

// Show item

include 'themes/'.$GLOBALS['app']['theme'].'/items_single.php';

// Footer

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>