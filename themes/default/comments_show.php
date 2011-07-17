<span id="comments_<?php echo $item['id']; ?>">

<?php

if (count($item['comments']) > 0) {

foreach ($item['comments'] as $comment) {

?>

    <p class="meta">
      "<?php echo $comment['content']; ?>" - <a href="user.php?id=<?php echo $comment['user']['id']; ?>"><?php echo $comment['user']['username']; ?></a>
<?php
if (!empty($_SESSION['user']['id']) && $GLOBALS['app']['items']['comments']['upvotes'] == TRUE)
	echo ' &middot; <span class="small"><a href="#" onclick="alert('.$comment['user']['id'].', '.$item['id'].', '.$comment['id'].'); return false;">Like</a></a></span>';
if ($comment['user']['id'] == $_SESSION['user']['id'])
	echo ' &middot; <span class="small"><a href="#" onclick="comment_remove('.$comment['user']['id'].', '.$item['id'].', '.$comment['id'].'); return false;">Delete</a></span>';
?>
    </p>

<?php

}

}

?>

</span>