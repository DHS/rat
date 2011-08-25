<?php

class Item {

	// Create an item, returns item id
	public static function add($user_id, $content, $title = NULL, $image = NULL) {

		$user_id = sanitize_input($user_id);
		$content = sanitize_input($content);

		$sql = "INSERT INTO items SET user_id = $user_id, content = $content";

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

		$id = sanitize_input($id);

		$sql = "SELECT * FROM items WHERE id = $id ORDER BY id DESC";
		$query = mysql_query($sql);
		$result = mysql_fetch_array($query, MYSQL_ASSOC);

		if (!is_array($result)) {

			$item = NULL;

		} else {

			$item = new Item;

			foreach ($result as $k => $v) {
				$item->$k = $v;
			}

			$item->user = User::get_by_id($result['user_id']);
			$item->comments = $item->comments($id);
			$item->likes = $item->comments($id);

		}

		return $item;

	}

	// Get recent items, returns array of Item objects
	public static function list_all($limit = 20) {

		$sql = "SELECT * FROM items ORDER BY id DESC";

		// Limit not null so create limit string
		if ($limit != NULL) {
			$limit = sanitize_input($limit);
			$sql .= " LIMIT $limit";
		}

		$query = mysql_query($sql);

		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			
			$item = new Item;

			foreach($result as $k => $v) {
				$item->$k = $v;
			}
			
			$item->comments = $item->comments($result['id']);
			$item->likes = $item->comments($result['id']);
			$item->user = User::get_by_id($result['user_id']);

			$items[] = $item;

		}

		return $items;

	}

	// Get a feed of a friend's activity, returns array of Item objects
	public static function list_feed($user_id) {
		
		// Start by adding the viewer to the query string
		$friends_string = "user_id = $user_id";
		
		$user = User::get_by_id($user_id);
		$friends = $user->friends($user_id);
		
		// Loop through friends adding them to the query string
		foreach ($friends as $friend) {
			$friends_string .= " OR user_id = {$friend['friend_user_id']}";
		}
		
		$sql = "SELECT * FROM items WHERE $friends_string ORDER BY id DESC LIMIT 100";
		$query = mysql_query($sql);
		
		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			
			$item = new Item;
			
			foreach($result as $k => $v) {
				$item->$k = $v;
			}
			
			$item->user = User::get_by_id($result['user_id']);
			$item->comments = $item->comments($result['id']);
			$item->likes = $item->comments($result['id']);
			
			$items[] = $item;
			
		}
		
		return $items;
		
	}

	// Get comments for an item, returns an array of Comment objects
	public function comments($item_id) {
		
		$item_id = sanitize_input($item_id);

		$sql = "SELECT id, content, user_id, date FROM comments WHERE item_id = $item_id ORDER BY id ASC";
		$query = mysql_query($sql);

		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			
			$comment = new Comment;
			
			foreach($result as $k => $v) {
				$comment->$k = $v;
			}
			
			$comment->user = User::get_by_id($result['user_id']);

			$comments[] = $comment;
			
		}

		return $comments;

	}

	// Get likes for an item, returns an array of Like objects
	public function likes($item_id) {

		$item_id = sanitize_input($item_id);

		$sql = "SELECT id, user_id, date FROM likes WHERE item_id = $item_id";
		$query = mysql_query($sql);

		while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
			
			$like = new Like;

			foreach($result as $k => $v) {
				$like->$k = $v;
			}
			
			$like->user = User::get_by_id($result['user_id']);
			
			$likes[$result['id']] = $like;
			
		}

		return $likes;

	}

	// Remove an item, returns item id
	public static function remove($item_id) {

		$item_id = sanitize_input($item_id);

		// Check item exists
		$sql_check = "SELECT id FROM items WHERE id = $item_id";
		$count_query = mysql_query($sql_check);
		$id = mysql_result($count_query, 0);


		if (mysql_num_rows($count_query) > 0) {
			// If item exists, go ahead and delete
			$sql_delete = "DELETE FROM items WHERE id = $item_id";
			$query = mysql_query($sql_delete);
		}

		return $id;

	}

}

?>