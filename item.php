<?php

require_once 'config/init.php';

//	Critical: One of the following must be set: item id (to show) or content (to add) or delete

if ($_GET['id'] == '' && $_POST['title'] == '' && $_POST['content'] == '' && $_GET['delete'] == '') {
	$page['name'] = ucfirst($GLOBALS['app']['items']['name']).' not found';
	include 'themes/'.$GLOBALS['app']['theme'].'/header.php';
	include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';
	exit;
}

function generate_thumbnail($filename, $type, $max_width = 100, $max_height = 100, $dir = 'thumbnails') {
	
	// Create temporary source image resource
	if ($type == 'image/jpeg' || $type == 'image/pjpeg') {
		$src = imagecreatefromjpeg("{$GLOBALS['app']['items']['uploads']['directory']}/originals/$filename");
	} elseif ($type == 'image/png') {
		$src = imagecreatefrompng("{$GLOBALS['app']['items']['uploads']['directory']}/originals/$filename");
	} elseif ($type == 'image/gif') {
		$src = imagecreatefromgif("{$GLOBALS['app']['items']['uploads']['directory']}/originals/$filename");
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
		imagejpeg($new, "{$GLOBALS['app']['items']['uploads']['directory']}/$dir/$filename");
	} elseif ($type == 'image/png') {
		imagepng($new, "{$GLOBALS['app']['items']['uploads']['directory']}/$dir/$filename");
	} elseif ($type == 'image/gif') {
		imagegif($new, "{$GLOBALS['app']['items']['uploads']['directory']}/$dir/$filename");
	}
	
	// Delete temporary image resources
	imagedestroy($new);
	imagedestroy($src);
	
}

if ($_POST['title'] != '' || $_POST['content'] != '') {
	// Process new item

	// Form validation
	
	if ($GLOBALS['app']['items']['titles'] == FALSE && $_POST['content'] == '')
		$error .= ucfirst($GLOBALS['app']['items']['name']).' must include '.strtolower($GLOBALS['app']['items']['content']['name']).'.<br />';

	if ($GLOBALS['app']['items']['uploads']['enabled'] == TRUE) {
		
		if ($_FILES['file']['error'] > 0)
			$error .= 'Error code: '.$_FILES['file']['error'].'<br />';
		
		if (!in_array($_FILES['file']['type'], $GLOBALS['app']['items']['uploads']['mime-types']))
			$error .= 'Invalid file type: '.$_FILES['file']['type'].'<br />';
		
		if ($_FILES['file']['size'] > $GLOBALS['app']['items']['uploads']['max-size'])
			$error .= 'File too large.<br />';
		
	}
	
	// Error processing

	if ($error == '') {
		// No error so proceed...
		
		if ($GLOBALS['app']['items']['uploads']['enabled'] == TRUE) {
			
			// Check for file with same name and rename if neccessary
			if (file_exists("{$GLOBALS['app']['items']['uploads']['directory']}/originals/{$_FILES['file']['name']}")) {
				
				// Find filename and extension, works for filenames that include dots and any length extension!
				$filename = substr($_FILES['file']['name'], 0, strrpos($_FILES['file']['name'], '.'));
				$extension = substr($_FILES['file']['name'], 0-(strlen($_FILES['file']['name']) - strrpos($_FILES['file']['name'], '.') - 1));
				
				// Extends clashing filenames as such: for clash.jpg try clash-1.jpg, clash-2.jpg etc
				$i = 1;
				do {
					$_FILES['file']['name'] = "$filename-$i.$extension";
					$i++;
				} while (file_exists("{$GLOBALS['app']['items']['uploads']['directory']}/originals/{$_FILES['file']['name']}"));
				
			}
			
			// Grab the file
			move_uploaded_file($_FILES['file']['tmp_name'], "{$GLOBALS['app']['items']['uploads']['directory']}/originals/{$_FILES['file']['name']}");
			
			// Generate thumbnail
			generate_thumbnail($_FILES['file']['name'], $_FILES['file']['type'], 100, 100, 'thumbnails');
			
			// Generate stream image
			generate_thumbnail($_FILES['file']['name'], $_FILES['file']['type'], 350, 500, 'stream');

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

$page['head_title'] = $item['title'].' by '.$item['user']['name'];
$page['title'] = '<a href="user.php?id='.$item['user']['id'].'">'.$item['user']['name'].'</a> on <a href="index.php">'.$app['name'].'</a>';

include 'themes/'.$GLOBALS['app']['theme'].'/header.php';

// Show item

include 'themes/'.$GLOBALS['app']['theme'].'/items_single.php';

// Footer

include 'themes/'.$GLOBALS['app']['theme'].'/footer.php';

?>