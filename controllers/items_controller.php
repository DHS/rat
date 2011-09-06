<?php

class ItemsController extends Application {

	protected $requireLoggedIn = array('add', 'remove');
	
	// Show stream of everyone's items
	function index() {
		
		// Page zero so overwrite to 1
		if ($this->uri['params']['page'] == 0) {
			$this->uri['params']['page'] = 1;
		}
		
		// Items per page, change this to test pagination
		$limit = 10;
		
		if (isset($this->uri['params']['page'])) {
			$offset = ($this->uri['params']['page'] - 1) * $limit;
		} else {
			$offset = 0;
		}
		
		$this->items = Item::list_all($limit, $offset);
		
		if ($this->json) {
			$this->render_json($this->items);
		} else {
			$this->loadView('items/index');
		}
		
	}
	
	// Add an item
	function add() {
		
		if ($_POST['content'] != '' || ($_POST['title'] != '' && $this->config->items['titles']['enabled'] == TRUE)) {
			
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
					generate_thumbnail($_FILES['file']['name'], $_FILES['file']['type'], 100, 100, 'thumbnails', $this->config->items['uploads']['directory']);
					
					// Generate stream image
					generate_thumbnail($_FILES['file']['name'], $_FILES['file']['type'], 350, 500, 'stream', $this->config->items['uploads']['directory']);
					
					$item_id = Item::add($_SESSION['user_id'], $_POST['content'], $_POST['title'], $_FILES['file']['name']);
					
				} else {
					
					$item_id = Item::add($_SESSION['user_id'], $_POST['content'], $_POST['title']);
					
				}
				
				// Give points
				if (isset($this->plugins->points)) {
					$this->plugins->points->update($_SESSION['user_id'], $this->plugins->points['per_item']);
				}
				
				// Log item add
				if (isset($this->plugins->log)) {
					$this->plugins->log->add($_SESSION['user_id'], 'item', $item_id, 'add', "title = {$_POST['title']}\ncontent = {$_POST['content']}");
				}
				
				Application::flash('success', ucfirst($this->config->items['name']).' added!');
				
				// Go forth!
				if (SITE_IDENTIFIER == 'live') {
					header('Location: '.$this->url_for('users', 'show', $_SESSION['user_id']));
				} else {
					header('Location: '.$this->url_for('users', 'show', $_SESSION['user_id']));
				}
				
				exit();
				
				
			} else {
				// There was an error
				
				// Propagate get vars to be picked up by the form
				$this->uri['params']['title']		= $_POST['title'];
				$this->uri['params']['content']	= $_POST['content'];
				
				// Show error message
				Application::flash('error', $error);
				$this->loadView('items/add');
				exit();
				
			}
			
		} else {
			
			$this->loadView('items/add');
			
		}
		
	}
	
	function remove($item_id) {
		
		$item = Item::get_by_id($item_id);
		
		if ($_SESSION['user_id'] == $item->user->id && $item != NULL) {
			
			// Delete item
			$item->remove();
			
			// Log item deletion
			if (isset($this->plugins->log)) {
				$this->plugins->log->add($_SESSION['user_id'], 'item', $item->id, 'remove');
			}
			
			// Delete comments
			if (is_array($item->comments)) {
				
				foreach ($item->comments as $comment) {
					
					// Remove comment
					$id = $comment->remove();
					
					// Log comment removal
					if (isset($this->plugins->log)) {
						$this->plugins->log->add($_SESSION['user_id'], 'comment', $id, 'remove');
					}
					
				}
				
			}
			
			// Delete likes
			if (is_array($item->comments)) {
				
				foreach ($item->likes as $like) {
					
					// Remove like
					$id = $like->remove();
					
					// Log like removal
					if (isset($this->plugins->log)) {
						$this->plugins->log->add($_SESSION['user_id'], 'like', $like->id, 'remove');
					}

				}
				
			}
			
			// Set message
			Application::flash('success', ucfirst($this->config->items['name']).' removed!');
			
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
		
		if ($this->json) {
			$this->render_json($this->item);
		} else {
			$this->loadView('items/show');
		}

	}
	
	// Show feed of friends' new items
	function feed() {
		
		if ($this->config->friends['enabled'] == TRUE || isset($_SESSION['user_id'])) {
			
			// If friends enabled then show feed of friends' activity
			
			$user = User::get_by_id($_SESSION['user_id']);
			
			// Page zero so overwrite to 1
			if ($this->uri['params']['page'] == 0) {
				$this->uri['params']['page'] = 1;
			}

			// Items per page, change this to test pagination
			$limit = 10;

			if (isset($this->uri['params']['page'])) {
				$offset = ($this->uri['params']['page'] - 1) * $limit;
			} else {
				$offset = 0;
			}
			
			$this->items = $user->list_feed($limit, $offset);
			$this->loadView('items/index');
			
		} else {
			
			// Friends not enabled so fall back to showing everyone's activity
			
			$this->index();
			
		}
		
	}
	
}

?>
