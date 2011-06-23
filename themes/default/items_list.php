<?php echo $avatar_string; ?>

<!-- Title and author -->
<h2><a href="article.php?id=<?php echo $item['id']; ?>"><?php echo $item['title']; ?></a></h2>
<p>By <a href="user.php?id=<?php echo $item['user_id']; ?>"><?php if ($item['user']['full_name'] != NULL) { echo $item['user']['full_name']; } else { echo $item['user']['username']; } ?></a></p>

<!-- Read count -->
<p><?php echo $read_string; ?></p>

<!-- Byline -->
<?php echo $item['byline']; ?>


<!-- Read now -->
<p><a href="article.php?id=<?php echo $item['id']; ?>">Read now</a></p>
