<?php

class ItemsController extends Application {
	
	function __construct() {
		
		global $app;
		
		// To add an item you must be logged in
		if (($app->uri['action'] == 'add' || $app->uri['action'] == 'remove') && $_SESSION['user'] == NULL) {
			$app->page->name = 'Page not found';
			$app->loadView('partials/header');
			$app->loadView('partials/footer');
			exit;
		}
		
	}
	
	// Show stream of everyone's items
	function index() {
	
		$this->page->name = $this->config->tagline;
		$this->page->items = $this->item->list_all();
		
		$this->loadLayout('items/index');
		
	}
	
	// Add an item
	function add() {
		
		global $app;
		
		if (isset($_POST['content']) || (isset($_POST['title']) && $app->config->items['titles']['enabled'] == TRUE)) {
			
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
					
					include 'lib/upload.php';
					
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
				
				$page = $app->link_to(NULL, 'users', 'show', $_SESSION['user']['id']);
				
				// Go forth!
				if (SITE_IDENTIFIER == 'live') {
					header('Location: '.$app->config->url.$page.'?message='.urlencode($app->page->message));
				} else {
					header('Location: '.$app->config->dev_url.$page.'user.php?message='.urlencode($app->page->message));
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
				$app->loadLayout('items/add');
				exit();
				
			}
			
		} else {
			
			$app->loadLayout('items/add');
			
		}
		
	}
	
	function remove($item_id) {
		
		global $app;
		
		$item = $app->item->get($item_id);
		
		if ($_SESSION['user']['id'] == $item['user']['id'] && $item != NULL) {
			
			// Delete item
			$app->item->remove($item_id);
			if (isset($app->plugins->log))
				$app->plugins->log->add($_SESSION['user']['id'], 'item', $item_id, 'remove');
			
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
		
	}
	
	// Show a single item
	function show($id) {
		
		global $app;
		
		$app->page->item = $app->item->get($id);
		
		$app->loadLayout('items/show');
		
	}
	
	// Show feed of friends' new items
	function feed() {
		
		global $app;
		
		if ($app->config->friends['enabled'] == TRUE) {
			
			// If friends enabled then show feed of friends' activity
			
			$app->page->name = $app->config->tagline;
			$app->page->items = $app->item->list_feed($_SESSION['user']['id']);
			$app->loadLayout('items/index');
			
		} else {
			
			// Friends not enabled so fall back to showing everyone's activity
			
			$this->index();
			
		}
		
	}
	
}

?>
