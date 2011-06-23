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
	//Process new item

	$item_id = items_add($_SESSION['user']['id'], $_POST['content'], $_POST['title']);

	// give points
	if (is_object($GLOBALS['points']))
		$GLOBALS['points']->update($_SESSION['user']['id'], $app['points']['per_item']);
	
	// Log item add
	if (is_object($GLOBALS['log']))
		$GLOBALS['log']->add($_SESSION['user']['id'], 'item', $item_id, 'add', "title = {$_POST['title']}\ncontent = {$_POST['content']}");

	$message = ucfirst($GLOBALS['app']['items']['name']).' added!';

	// return from whence you came
	header('Location: '.$_SERVER['HTTP_REFERER'].'?message='.urlencode($message));
	exit();
	
} elseif ($_GET['delete'] != '') {
	// Delete an item

	$item = items_get_by_id($_GET['delete']);
	
	if ($_SESSION['user']['id'] == $item['user']['id'] && $item != NULL) {

		// delete item
		items_remove($_GET['delete']);
		if (is_object($GLOBALS['log']))
			$GLOBALS['log']->add($_SESSION['user']['id'], 'item', $_GET['delete'], 'remove');

		// delete comments
		if (is_array($item['comments'])) {
			foreach ($item['comments'] as $key => $value) {
				$id = comments_remove($value['user_id'], $item['id'], $value['id']);
				if (is_object($GLOBALS['log']))
					$GLOBALS['log']->add($_SESSION['user']['id'], 'comment', $id, 'remove');
			}
		}
		
		// delete likes
		if (is_array($item['comments'])) {
			foreach ($item['likes'] as $key => $value) {
				$id = likes_remove($value['user_id'], $item['id']);
				if (is_object($GLOBALS['log']))
					$GLOBALS['log']->add($_SESSION['user']['id'], 'like', $id, 'remove');
			}
		}
		
		// set message
		$message = ucfirst($GLOBALS['app']['items']['name']).' removed!';

		// return from whence you came
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit();

	} else {
		// naughtiness, expulsion
		
		// go forth
		if (SITE_IDENTIFIER == 'live') {
			header('Location: '.$GLOBALS['app']['url']);
		} else {
			header('Location: '.$GLOBALS['app']['dev_url']);
		}
		
		exit();
		
	}
	
} elseif ($_GET['id'] != '') {
	// no new item so get item info based on get var
	
	$item = items_get_by_id($_GET['id']);

	// fail gracefully if item doesn't exist
	if ($item == NULL) {

		$page['name'] = ucfirst($GLOBALS['app']['items']['name']).' not found';
		include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
		include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
		exit;
		
	}

}

/* Header */

if (is_object($GLOBALS['gravatar']))
	$app['page_title_gravatar'] = $item['user']['email'];

if ($item['user']['full_name'] != NULL) {
	// Full name set so use that for page title
	$page['head_title'] = $item['title'].' by '.$item['user']['full_name'];
	$page['title'] = '<a href="user.php?id='.$item['user']['id'].'">'.$item['user']['full_name'].'</a> on <a href="index.php">'.$app['name'].'</a>';
} else {
	// Full name not set so use username for page title
	$page['head_title'] = $item['title'].' by '.$item['user']['username'];
	$page['title'] = '<a href="user.php?id='.$item['user']['id'].'">'.$item['user']['username'].'</a> on <a href="index.php">'.$app['name'].'</a>';
}

include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

/* Show item  */

include 'themes/'.$GLOBALS['app']['theme'].'/items_single.php';

/* Footer */

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>