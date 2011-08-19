<?php

//	Critical: One of the following must be set: item id (to show) or content (to add) or delete

if (!isset($_GET['id']) && !isset($_POST['title']) && !isset($_POST['content']) && !isset($_GET['delete'])) {
	
	if ($_SESSION['user'] != NULL) {
		$app->loadView('header');
		$app->loadView('items_add');
		$app->loadView('footer');
		exit;
	} else {
		$app->page->name = ucfirst($app->config->items['name']).' not found';
		$app->loadView('header');
		$app->loadView('footer');
		exit;
	}
	
}

function generate_thumbnail($filename, $type, $max_width = 100, $max_height = 100, $dir = 'thumbnails') {
	
	global $app;
	
	// Create temporary source image resource
	if ($type == 'image/jpeg' || $type == 'image/pjpeg') {
		$src = imagecreatefromjpeg("{$app->config->items['uploads']['directory']}/originals/$filename");
	} elseif ($type == 'image/png') {
		$src = imagecreatefrompng("{$app->config->items['uploads']['directory']}/originals/$filename");
	} elseif ($type == 'image/gif') {
		$src = imagecreatefromgif("{$app->config->items['uploads']['directory']}/originals/$filename");
	}
	
	// Find existing dimensions
	$old_width = imagesx($src);
	$old_height = imagesy($src);
	
	// Generate new dimensions, check width first
	if ($old_width > $max_width) {
		$new_width = $old_width * ($max_width / $old_width);
		$new_height = $old_height * ($max_width / $old_width);
	} else {
		$new_width = $old_width;
		$new_height = $old_height;
	}

	// Then check height
	if ($new_height > $max_height) {
		$new_width = $old_width * ($max_height / $old_height);
		$new_height = $old_height * ($max_height / $old_height);
	}
	
	// Create temporary destination image resource
	$new = imagecreatetruecolor($new_width, $new_height);
	
	// Preserve transparency on PNGs
	imagealphablending($new, FALSE);
	imagesavealpha($new, TRUE);
	
	// Generate new image
	imagecopyresampled($new, $src, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
	
	// Save new image
	if ($type == 'image/jpeg' || $type == 'image/pjpeg') {
		imagejpeg($new, "{$app->config->items['uploads']['directory']}/$dir/$filename");
	} elseif ($type == 'image/png') {
		imagepng($new, "{$app->config->items['uploads']['directory']}/$dir/$filename");
	} elseif ($type == 'image/gif') {
		imagegif($new, "{$app->config->items['uploads']['directory']}/$dir/$filename");
	}
	
	// Delete temporary image resources
	imagedestroy($new);
	imagedestroy($src);
	
}

if (isset($_POST['title']) || isset($_POST['content'])) {
	// Process new item

	// Form validation
	
	if ($app->config->items['titles']['enabled'] == FALSE && $_POST['content'] == '')
		$error .= ucfirst($app->config->items['name']).' must include '.strtolower($app->config->items['content']['name']).'.<br />';

	if ($app->config->items['uploads']['enabled'] == TRUE) {
		
		if ($_FILES['file']['error'] > 0)
			$error .= 'Error code: '.$_FILES['file']['error'].'<br />';
		
		if (!in_array($_FILES['file']['type'], $app->config->items['uploads']['mime-types']))
			$error .= 'Invalid file type: '.$_FILES['file']['type'].'<br />';
		
		if ($_FILES['file']['size'] > $app->config->items['uploads']['max-size'])
			$error .= 'File too large.<br />';
		
	}
	
	// Error processing

	if ($error == '') {
		// No error so proceed...
		
		if ($app->config->items['uploads']['enabled'] == TRUE) {
			
			// Check for file with same name and rename if neccessary
			if (file_exists("{$app->config->items['uploads']['directory']}/originals/{$_FILES['file']['name']}")) {
				
				// Find filename and extension, works for filenames that include dots and any length extension!
				$filename = substr($_FILES['file']['name'], 0, strrpos($_FILES['file']['name'], '.'));
				$extension = substr($_FILES['file']['name'], 0-(strlen($_FILES['file']['name']) - strrpos($_FILES['file']['name'], '.') - 1));
				
				// Extends clashing filenames as such: for clash.jpg try clash-1.jpg, clash-2.jpg etc
				$i = 1;
				do {
					$_FILES['file']['name'] = "$filename-$i.$extension";
					$i++;
				} while (file_exists("{$app->config->items['uploads']['directory']}/originals/{$_FILES['file']['name']}"));
				
			}
			
			// Grab the file
			move_uploaded_file($_FILES['file']['tmp_name'], "{$app->config->items['uploads']['directory']}/originals/{$_FILES['file']['name']}");
			
			// Generate thumbnail
			generate_thumbnail($_FILES['file']['name'], $_FILES['file']['type'], 100, 100, 'thumbnails');
			
			// Generate stream image
			generate_thumbnail($_FILES['file']['name'], $_FILES['file']['type'], 350, 500, 'stream');

			$item_id = $app->item->add($_SESSION['user']['id'], $_POST['content'], $_POST['title'], $_FILES['file']['name']);
			
		} else {
			$item_id = $app->item->add($_SESSION['user']['id'], $_POST['content'], $_POST['title']);
		}
		
		// Give points
		if (isset($app->plugins->points))
			$app->plugins->points->update($_SESSION['user']['id'], $app->plugins->points['per_item']);

		// Log item add
		if (isset($app->plugins->log))
			$app->plugins->log->add($_SESSION['user']['id'], 'item', $item_id, 'add', "title = {$_POST['title']}\ncontent = {$_POST['content']}");

		$app->page->message = ucfirst($app->config->items['name']).' added!';

		// Go forth!
		if (SITE_IDENTIFIER == 'live') {
			header('Location: '.$app->config->url.'user.php?message='.urlencode($app->page->message));
		} else {
			header('Location: '.$app->config->dev_url.'user.php?message='.urlencode($app->page->message));
		}
		
		exit();
		
	
	} else {
		// There was an error
		
		// Propagate get vars to be picked up by the form
		$_GET['title']		= $_POST['title'];
		$_GET['content']	= $_POST['content'];
		
		// Commented out the line below while objectifying $app
		//$app = $GLOBALS['app'];
		
		// Show error message
		$app->page->message = $error;
		$app->loadView('header');
		$app->loadView('items_add');
		$app->loadView('footer');
		exit();
	
	}
	
} elseif (isset($_GET['delete'])) {
	// Delete an item

	$item = $app->item->get($_GET['delete']);
	
	if ($_SESSION['user']['id'] == $item['user']['id'] && $item != NULL) {

		// Delete item
		$app->item->remove($_GET['delete']);
		if (isset($app->plugins->log))
			$app->plugins->log->add($_SESSION['user']['id'], 'item', $_GET['delete'], 'remove');

		// Delete comments
		if (is_array($item['comments'])) {
			foreach ($item['comments'] as $key => $value) {
				$id = $app->comment->remove($value['user_id'], $item['id'], $value['id']);
				if (isset($app->plugins->log))
					$app->plugins->log->add($_SESSION['user']['id'], 'comment', $id, 'remove');
			}
		}
		
		// Delete likes
		if (is_array($item['comments'])) {
			foreach ($item['likes'] as $key => $value) {
				$id = $app->likeremove($value['user_id'], $item['id']);
				if (isset($app->plugins->log))
					$app->plugins->log->add($_SESSION['user']['id'], 'like', $id, 'remove');
			}
		}
		
		// Set message
		$app->page->message = ucfirst($app->config->items['name']).' removed!';

		// Return from whence you came
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit();

	} else {
		// Naughtiness = expulsion!
		
		// Go forth
		if (SITE_IDENTIFIER == 'live') {
			header('Location: '.$app->config->url);
		} else {
			header('Location: '.$app->config->dev_url);
		}
		
		exit();
		
	}
	
} elseif (isset($_GET['id'])) {
	// No new item so get item info based on get var
	
	$app->page->item = $app->item->get($_GET['id']);

	// Fail gracefully if item doesn't exist
	if ($app->page->item == NULL) {

		$app->page->name = ucfirst($app->config->items['name']).' not found';
		$app->loadView('header');
		$app->loadView('footer');
		exit;
		
	}

}

// Header

if (isset($app->plugins->gravatar))
	$app->page->title_gravatar = $app->page->item['user']['email'];

$app->page->head_title = $app->page->item['title'].' by '.$app->page->item['user']['name'];
$app->page->title = '<a href="/'.$app->page->item['user']['username'].'">'.$app->page->item['user']['name'].'</a> on <a href="/">'.$app->config->name.'</a>';

$app->loadView('header');

// Show item

$app->loadView('items/single');

// Footer

$app->loadView('footer');

?>