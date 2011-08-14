<table>

<?php $i = 1; ?>

<?php foreach ($app->page->leaderboard as $row) { ?>
	
	<tr>
		<td><?php echo $i; ?>.</td>
		<td><a href="/<?php echo $row['username']; ?>"><?php echo $row['username']; ?></a></td>
		<td><?php echo $row['points']; ?></td>
	</tr>

	<?php $i++; ?>
	
<?php } ?>

</table>