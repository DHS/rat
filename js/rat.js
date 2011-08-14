
function like_add(user_id, item_id) {

	if (window.XMLHttpRequest) {
		// IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {
		// IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			if (xmlhttp.responseText != '') {
				
				if (like_link = document.getElementById('like_link_'+item_id))
					like_link.innerHTML = '<a href="#" onclick="like_remove('+user_id+', '+item_id+'); return false;">Unlike</a>';
				
				if (likes = document.getElementById('likes_'+item_id))
					likes.innerHTML = xmlhttp.responseText;
				
			}
		}
	}

	xmlhttp.open("GET", 'controllers/ajax.php?page=like_add&user_id='+user_id+'&item_id='+item_id, true);
	xmlhttp.send();
	
}

function like_remove(user_id, item_id) {

	if (window.XMLHttpRequest) {
		// IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {
		// IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			if (xmlhttp.responseText != '') {
				
				if (like_link = document.getElementById('like_link_'+item_id))
					like_link.innerHTML = '<a href="#" onclick="like_add('+user_id+', '+item_id+'); return false;">Like</a>';
				
				if (likes = document.getElementById('likes_'+item_id))
					likes.innerHTML = xmlhttp.responseText;

			}
		}
	}

	xmlhttp.open("GET", 'ajax.php?page=like_remove&user_id='+user_id+'&item_id='+item_id, true);
	xmlhttp.send();
	
}

function comment_add(user_id, item_id) {
	
	var content = document.forms('comment_form_'+item_id).content.value;

	if (window.XMLHttpRequest) {
		// IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {
		// IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			if (xmlhttp.responseText != '') {
				
				if (comments = document.getElementById('comments_'+item_id))
					comments.innerHTML = xmlhttp.responseText;
				
				if (comment_form = document.forms('comment_form_'+item_id))
					comment_form.content.value = '';
				
			}
		}
	}

	xmlhttp.open("GET", 'ajax.php?page=comment_add&user_id='+user_id+'&item_id='+item_id+'&content='+content, true);
	xmlhttp.send();
	
}

function comment_remove(user_id, item_id, comment_id) {
	
	if (window.XMLHttpRequest) {
		// IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {
		// IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			if (xmlhttp.responseText != '') {
				
				if (comments = document.getElementById('comments_'+item_id))
					comments.innerHTML = xmlhttp.responseText;
				
			}
		}
	}

	xmlhttp.open("GET", 'ajax.php?page=comment_remove&user_id='+user_id+'&item_id='+item_id+'&comment_id='+comment_id, true);
	xmlhttp.send();
	
}

function friend_add(user_id, friend_user_id) {

	if (window.XMLHttpRequest) {
		// IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {
		// IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			if (xmlhttp.responseText != '') {
				
				if (friends_button = document.getElementById('friends_'+friend_user_id))
					friends_button.innerHTML = xmlhttp.responseText;
				
			}
		}
	}

	xmlhttp.open("GET", 'ajax.php?page=friend_add&user_id='+user_id+'&friend_user_id='+friend_user_id, true);
	xmlhttp.send();
	
}

function friend_remove(user_id, friend_user_id) {
		
	if (window.XMLHttpRequest) {
		// IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {
		// IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			if (xmlhttp.responseText != '') {
				
				if (friends_button = document.getElementById('friends_'+friend_user_id))
					friends_button.innerHTML = xmlhttp.responseText;
				
			}
		}
	}

	xmlhttp.open("GET", 'ajax.php?page=friend_remove&user_id='+user_id+'&friend_user_id='+friend_user_id, true);
	xmlhttp.send();
	
}