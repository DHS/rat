
<span class="item_meta">

<a href="item.php?id=<?php echo $item['id']; ?>"><?php echo $item['date']; ?></a> 

<?php

if ($GLOBALS['app']['items']['comments'] == TRUE)
	echo ' &middot <a href="#" onclick="document.getElementById(\'comment_form_'.$item['id'].'\').style.visibility = \'visible\'; document.getElementById(\'comment_form_'.$item['id'].'\').style.height = \'auto\'; document.getElementById(\'comment_form_'.$item['id'].'\').content.focus(); return false;">'.$GLOBALS['app']['items']['comments']['name'].'</a>';

if ($GLOBALS['app']['items']['likes'] == TRUE) {
	
	// find if current user likes the item
	if (is_array($item['likes'])) {
		foreach ($item['likes'] as $value) {
			if ($value['user']['id'] == $_SESSION['user']['id'])
				$i_like = TRUE;
		}
	}
	if ($i_like == TRUE) {
		echo ' &middot <span id="like_link_'.$item['id'].'"><a href="#" onclick="like_remove('.$_SESSION['user']['id'].', '.$item['id'].'); return false;">'.$GLOBALS['app']['items']['likes']['opposite_name'].'</a></a>';
	} else {
		echo ' &middot <span id="like_link_'.$item['id'].'"><a href="#" onclick="like_add('.$_SESSION['user']['id'].', '.$item['id'].'); return false;">'.$GLOBALS['app']['items']['likes']['name'].'</a></a>';
	}
	unset ($i_like);

}

if ($item['user']['id'] == $_SESSION['user']['id'])
	echo ' &middot; <a onclick="return confirm(\'Are you sure you want to delete this?\')" href="item.php?delete='.$item['id'].'">Delete</a>';

?>

</span>
