<?php

class ItemsController extends Application {
	
	function __construct() {
		
		// To add an item you must be logged in
		if (($this->uri['action'] == 'add' || $this->uri['action'] == 'remove') && $_SESSION['user'] == NULL) {
			$this->title = 'Page not found';
			$this->loadView('partials/header');
			$this->loadView('partials/footer');
			exit;
		}
		
	}
	
	// Show stream of everyone's items
	function index() {
	
		$this->title = $this->config->tagline;
		$this->items = Item::list_all();
		
		$this->loadLayout('items/index');
		
	}
	
	// Add an item
	function add() {
		
		if (isset($_POST['content']) || (isset($_POST['title']) && $this->config->items['titles']['enabled'] == TRUE)) {
			
			// Form validation
			
			if ($this->config->items['titles']['enabled'] == FALSE && $_POST['content'] == '') {
				$error .= ucfirst($this->config->items['name']).' must include '.strtolower($this->config->items['content']['name']).'.<br />';
			}
			
			if ($this->config->items['uploads']['enabled'] == TRUE) {
				
				if ($_FILES['file']['error'] > 0) {
					$error .= 'Error code: '.$_FILES['file']['error'].'<br />';
				}
				
				if (!in_array($_FILES['file']['type'], $this->config->items['uploads']['mime-types'])) {
					$error .= 'Invalid file type: '.$_FILES['file']['type'].'<br />';
				}
				
				if ($_FILES['file']['size'] > $this->config->items['uploads']['max-size']) {
					$error .= 'File too large.<br />';
				}
				
			}
			
			// Error processing
			
			if ($error == '') {
				// No error so proceed...
				
				if ($this->config->items['uploads']['enabled'] == TRUE) {
					
					// Check for file with same name and rename if neccessary
					if (file_exists("{$this->config->items['uploads']['directory']}/originals/{$_FILES['file']['name']}")) {
						
						// Find filename and extension, works for filenames that include dots and any length extension!
						$filename = substr($_FILES['file']['name'], 0, strrpos($_FILES['file']['name'], '.'));
						$extension = substr($_FILES['file']['name'], 0-(strlen($_FILES['file']['name']) - strrpos($_FILES['file']['name'], '.') - 1));
						
						// Extends clashing filenames as such: for clash.jpg try clash-1.jpg, clash-2.jpg etc
						$i = 1;
						do {
							$_FILES['file']['name'] = "$filename-$i.$extension";
							$i++;
						} while (file_exists("{$this->config->items['uploads']['directory']}/originals/{$_FILES['file']['name']}"));
						
					}
					
					// Grab the file
					move_uploaded_file($_FILES['file']['tmp_name'], "{$this->config->items['uploads']['directory']}/originals/{$_FILES['file']['name']}");
					
					include 'lib/upload.php';
					
					// Generate thumbnail
					generate_thumbnail($_FILES['file']['name'], $_FILES['file']['type'], 100, 100, 'thumbnails');
					
					// Generate stream image
					generate_thumbnail($_FILES['file']['name'], $_FILES['file']['type'], 350, 500, 'stream');
					
					$item_id = Item::add($_SESSION['user']['id'], $_POST['content'], $_POST['title'], $_FILES['file']['name']);
					
				} else {
					
					$item_id = Item::add($_SESSION['user']['id'], $_POST['content'], $_POST['title']);
					
				}
				
				// Give points
				if (isset($this->plugins->points)) {
					$this->plugins->points->update($_SESSION['user']['id'], $this->plugins->points['per_item']);
				}
				
				// Log item add
				if (isset($this->plugins->log)) {
					$this->plugins->log->add($_SESSION['user']['id'], 'item', $item_id, 'add', "title = {$_POST['title']}\ncontent = {$_POST['content']}");
				}
				
				$this->message = ucfirst($this->config->items['name']).' added!';
				
				$page = $this->link_to(NULL, 'users', 'show', $_SESSION['user']['id']);
				
				// Go forth!
				if (SITE_IDENTIFIER == 'live') {
					header('Location: '.$this->config->url.$page.'?message='.urlencode($this->message));
				} else {
					header('Location: '.$this->config->dev_url.$page.'user.php?message='.urlencode($this->message));
				}
				
				exit();
				
				
			} else {
				// There was an error
				
				// Propagate get vars to be picked up by the form
				$_GET['title']		= $_POST['title'];
				$_GET['content']	= $_POST['content'];
				
				// Show error message
				$this->message = $error;
				$this->loadLayout('items/add');
				exit();
				
			}
			
		} else {
			
			$this->loadLayout('items/add');
			
		}
		
	}
	
	function remove($item_id) {
		
		$item = Item::get_by_id($item_id);
		
		if ($_SESSION['user']['id'] == $item->user->id && $item != NULL) {
			
			// Delete item
			Item::remove($item_id);
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($_SESSION['user']['id'], 'item', $item_id, 'remove');
			}
			
			// Delete comments
			if (is_array($item->comments)) {
				foreach ($item->comments as $key => $value) {
					$id = Comment::remove($value['user_id'], $item->id, $value->id);
					if (isset($this->plugins->log))
						$this->plugins->log->add($_SESSION['user']['id'], 'comment', $id, 'remove');
				}
			}
			
			// Delete likes
			if (is_array($item->comments)) {
				foreach ($item->likes as $key => $value) {
					$id = Like::remove($value->user_id, $item->id);
					if (isset($this->plugins->log))
						$this->plugins->log->add($_SESSION['user']['id'], 'like', $id, 'remove');
				}
			}
			
			// Set message
			$this->message = ucfirst($this->config->items['name']).' removed!';
			
			// Return from whence you came
			header('Location: '.$_SERVER['HTTP_REFERER']);
			exit();
			
		} else {
			// Naughtiness = expulsion!
			
			// Go forth
			if (SITE_IDENTIFIER == 'live') {
				header('Location: '.$this->config->url);
			} else {
				header('Location: '.$this->config->dev_url);
			}
			
			exit();
			
		}
		
	}
	
	// Show a single item
	function show($id) {
		
		$this->item = Item::get_by_id($id);
		
		$this->loadLayout('items/show');
		
	}
	
	// Show feed of friends' new items
	function feed() {
		
		if ($this->config->friends['enabled'] == TRUE) {
			
			// If friends enabled then show feed of friends' activity
			
			$this->title = $this->config->tagline;
			$this->items = Item::list_feed($_SESSION['user']['id']);
			$this->loadLayout('items/index');
			
		} else {
			
			// Friends not enabled so fall back to showing everyone's activity
			
			$this->index();
			
		}
		
	}
	
}

?>
