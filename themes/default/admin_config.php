<?

/*
If array, call self w/ new values
Echo header
Echo line

*/

function form_handler($data, $parent_key = NULL) {
	
	foreach ($data as $key => $value) {
	
		if (is_array($value)) {
			
			$integer_keys = TRUE;
			
			echo '<tr><th colspan="2">'.ucfirst($key).'</th></tr>';
			
			form_handler($value, $key);
			
			//echo '<tr><td colspan="2"><a href="#">Add new</a></td></tr>';
	    
			echo '<tr><td colspan="2"><hr /></td></tr>';
    
		} else {
    
			echo '<tr>';
			
			if ($value === TRUE || $value === FALSE) {
				
				echo '<td colspan="2"><input type="checkbox" name="';
				
				if ($parent_key) {
					if (is_numeric($keyvalue)) {
						echo $parent_key.'['.$key.']';
					} else {
						echo $parent_key."[".$key."]";
					}
				} else {
					echo $key;
				}
				
				echo '"';
				
				if ($value2 === TRUE) {
					echo ' checked';
				}
				
				echo ' /> '.ucfirst($key);
				
			} elseif (is_numeric($key)) {
			
				echo '<td colspan="2"><input type="text" name="';
				if ($parent_key) {
					if (is_numeric($keyvalue)) {
						echo $parent_key.'['.$key.']';
					} else {
						echo $parent_key."[".$key."]";
					}
				} else {
					echo $key;
				}
				echo '" value="'.$value.'" />';
			
			} else {
				
				echo '<td class="right">'.ucfirst($key).':</td><td><input type="text" name="';
				if ($parent_key) {
					if (is_numeric($keyvalue)) {
						echo $parent_key.'['.$key.']';
					} else {
						echo $parent_key."[".$key."]";
					}
				} else {
					echo $key;
				}
				echo '" value="'.$value.'" />';
				
			}
		
			echo '</td></tr>'."\n";
		
		}
	
	}
	
}

?>

<div class="center_container">

<form action="admin.php?page=config" method="POST">

	<table class="center">
		
		<?php form_handler($GLOBALS['app']);
		
		/*
		?>
		
		<?php foreach ($GLOBALS['app'] as $key => $value) { ?>
		
			<?php if (is_array($value)) { ?>
				
				<?php $integer_keys = TRUE; ?>
				
				<th colspan="2"><?php echo ucfirst($key); ?></th>
				
				<?php foreach ($value as $key2 => $value2) { ?>
        	
					<?php if (!is_numeric($key2)) { $integer_keys = FALSE; } ?>

					<?php if ($value2 === TRUE || $value2 === FALSE) { ?>
						
						<tr><td colspan="2"><input type="checkbox" name="<?php if (is_numeric($key2)) { echo $key.'['.$key2.']'; } else { echo $key."[".$key2."]"; }  ?>" <?php if ($value2 === TRUE) { echo 'checked'; } ?> /> <?php echo ucfirst($key2); ?></td></tr>
						
					<?php } elseif (is_numeric($key2)) { ?>
					
						<tr><td colspan="2"><input type="text" name="<?php if (is_numeric($key2)) { echo $key.'['.$key2.']'; } else { echo $key."[".$key2."]"; }  ?>" value="<?php echo $value2; ?>" /></td></tr>

					<?php } else { ?>
						
						<tr><td class="right"><?php echo ucfirst($key2); ?>:</td><td><input type="text" name="<?php if (is_numeric($key2)) { echo $key.'['.$key2.']'; } else { echo $key."[".$key2."]"; }  ?>" value="<?php echo $value2; ?>" /></td></tr>
						
					<?php } ?>

				<?php } ?>
				
				
				<?php //if ($integer_keys == TRUE) { ?>
					
					<tr><td colspan="2"><a href="#">Add new</a></td></tr>
					
				<?php //} ?>
				
				
				<?php $integer_keys == FALSE; ?>

				<tr><td colspan="2"><hr /></td></tr>

			<?php } else { ?>

				<?php if ($value === TRUE || $value === FALSE) { ?>

					<tr><td colspan="2"><input type="checkbox" name="<?php echo $key; ?>" <?php if ($value === TRUE) { echo 'checked'; } ?> /> <?php echo ucfirst($key); ?></td></tr>

				<?php } else { ?>
				
					<tr><td class="right"><?php echo ucfirst($key); ?>:</td><td><input type="text" name="<?php echo $key; ?>" value="<?php echo $value; ?>" /></td></tr>

				<?php } ?>
			
				
			
			<?php } ?>
		
		<?php }
		
		*/ ?>
		
		<tr><td></td><td class="align_left"><input type="submit" value="Save" /></td></tr>
		
	</table>

</form>

</div>
