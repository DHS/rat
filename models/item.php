<?php

class Item {
	
	// Create an item, returns item id
	public static function add($user_id, $content, $title = NULL, $image = NULL) {
		
		$config = new AppConfig;
		
		$user_id = sanitize_input($user_id);
		$content = sanitize_input($content);
		
		$sql = "INSERT INTO `{$config->database[SITE_IDENTIFIER]['prefix']}items` SET `user_id` = $user_id, `content` = $content";
		
		if ($title != NULL) {
			$title = sanitize_input($title);
			$sql .= ", title = $title";
		}
		
		if ($image != NULL) {
			$image = sanitize_input($image);
			$sql .= ", image = $image";
		}
		
		$query = mysql_query($sql);
		
		$id = mysql_insert_id();
		
		return $id;
		
	}

	// Get an item by id, returns an Item object
	public static function get_by_id($id) {
		
		$config = new AppConfig;
		
		$id = sanitize_input($id);
		
		$sql = "SELECT `id`, `user_id`, `title`, `content`, `image`, `date` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}items` WHERE `id` = $id ORDER BY `id` DESC";
		$query = mysql_query($sql);
		$result = mysql_fetch_array($query, MYSQL_ASSOC);
		
		if (!is_array($result)) {
			
			$item = NULL;
			
		} else {
			
			$item = new Item;
			
			foreach ($result as $k => $v) {
				$item->$k = $v;
			}
			
			$item->user = $item->user();
			$item->comments = $item->comments();
			$item->likes = $item->likes();
			
		}
		
		return $item;

	}
	
	// Get recent items, returns array of Item objects
	public static function list_all($limit = 10, $offset = 0) {
		
		$config = new AppConfig;
		
		$sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}items` ORDER BY `id` DESC";
		
		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";
		
		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";
		
		$query = mysql_query($sql);
		
		// Loop through item ids, fetching objects
		$items = array();
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$items[] = Item::get_by_id($result['id']);
		}
		
		return $items;
		
	}
	
	// Update an item
	public function update($title, $content) {
		
		$config = new AppConfig;
		
		$sql = "UPDATE `{$config->database[SITE_IDENTIFIER]['prefix']}items` SET ";
		
		if ($title != '') {
			$title = sanitize_input($title);
		} else {
			$title = 'NULL';
		}
		$sql .= "`title` = $title, ";	
		
		if ($content != '') {
			$content = sanitize_input($content);
		} else {
			$content = 'NULL';
		}
		$sql .= "`content` = $content";
		
		$sql .= " WHERE `id` = $this->id";
		
		$query = mysql_query($sql);
		
	}
	
	// Get the user for an item, returns a User object
	public function user() {
		
		return User::get_by_id($this->user_id);
		
	}
	
	// Get comments for an item, returns an array of Comment objects
	public function comments($limit = 10, $offset = 0) {
		
		$config = new AppConfig;
		
		$sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}comments` WHERE `item_id` = $this->id ORDER BY `id` ASC";
		
		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";
		
		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";
		
		$query = mysql_query($sql);
		
		$comments = array();
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$comments[] = Comment::get_by_id($result['id']);
		}
		
		return $comments;
		
	}
	
	// Get likes for an item, returns an array of Like objects
	public function likes($limit = 10, $offset = 0) {
		
		$config = new AppConfig;
		
		$sql = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}likes` WHERE `item_id` = $this->id";
		
		// Limit string
		$limit = sanitize_input($limit);
		$sql .= " LIMIT $limit";
		
		// Offset string
		$offset = sanitize_input($offset);
		$sql .= " OFFSET $offset";
		
		$query = mysql_query($sql);
		
		$likes = array();
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$likes[$result['id']] = Like::get_by_id($result['id']);
		}
		
		return $likes;
		
	}
	
	// Remove an item, returns item id
	public function remove() {
		
		$config = new AppConfig;
		
		// Check item exists
		$sql_check = "SELECT `id` FROM `{$config->database[SITE_IDENTIFIER]['prefix']}items` WHERE `id` = $this->id";
		$count_query = mysql_query($sql_check);
		
		if (mysql_num_rows($count_query) > 0) {
			// If item exists, go ahead and delete
			$sql_delete = "DELETE FROM `{$config->database[SITE_IDENTIFIER]['prefix']}items` WHERE `id` = $this->id";
			$query = mysql_query($sql_delete);
		}
		
	}

}

?>