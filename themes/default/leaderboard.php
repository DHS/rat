<table>

<?php $i = 1; ?>

<?php foreach ($leaderboard as $row) { ?>
	
	<tr>
		<td><?php echo $i; ?>.</td>
		<td><a href="user.php?id=<?php echo $row['id']; ?>"><?php echo $row['username']; ?></a></td>
		<td><?php echo $row['points']; ?></td>
	</tr>

	<?php $i++; ?>
	
<?php } ?>

</table>