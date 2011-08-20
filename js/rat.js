
function like_add(item_id, url) {
	
	var new_url = url.substr(0, url.length - 3) + 'remove';
	
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
					like_link.innerHTML = '<a href="#" onclick="like_remove('+item_id+', \''+new_url+'\'); return false;">Unlike</a>';
				
				if (likes = document.getElementById('likes_'+item_id))
					likes.innerHTML = xmlhttp.responseText;
				
			}
		}
	}

	xmlhttp.open("GET", url, true);
	xmlhttp.send();
	
}

function like_remove(item_id, url) {
	
	var new_url = url.substr(0, url.length - 6) + 'add';

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
					like_link.innerHTML = '<a href="#" onclick="like_add('+item_id+', \''+new_url+'\'); return false;">Like</a>';
				
				if (likes = document.getElementById('likes_'+item_id))
					likes.innerHTML = xmlhttp.responseText;

			}
		}
	}

	xmlhttp.open("GET", url, true);
	xmlhttp.send();
	
}

function comment_add(item_id, url) {
	
	var content = 'item_id=' + item_id + '&content=' + document.forms('comment_form_'+item_id).content.value;
	
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
	
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	xmlhttp.send(content);
	
}

function comment_remove(item_id, url) {
	
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

	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
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