<?

/*
If array, call self w/ new values
Echo header
Echo line

*/

function formify($data, $parent_key = NULL) {
	
	foreach ($data as $key => $value) {
		
		if (is_array($value)) {
			
			$GLOBALS['numeric_keys'] = TRUE;
			
			if ($parent_key) {
				// Subheading
				echo '<tr><td colspan="2">'.ucfirst($key).'</td></tr>';
			} else {
				// Section heading
				echo '<tr><th colspan="2">'.ucfirst($key).'</th></tr>';
			}
			
			// Preserve square brackets, this will only work with recursion up to 3x
			if ($parent_key) {
				formify($value, $parent_key.'['.$key.']');
			} else {
				formify($value, $key);
			}
			
			
			//if ($GLOBALS['numeric_keys'] == TRUE)
			//	echo '<tr><td colspan="2"><a href="#">Add new</a></td></tr>';
	    
			echo '<tr><td colspan="2"><hr /></td></tr>';
    	
		} else {
			
			if (!is_numeric($key) && $GLOBALS['numeric_keys'] == TRUE)
				$GLOBALS['numeric_keys'] = FALSE;
			
			echo '<tr>';
			
			if ($value === TRUE || $value === FALSE) {
				
				// Checkbox

				echo '<td colspan="2"><input type="checkbox" name="';
				if ($parent_key) {
					echo $parent_key."[$key]";
				} else {
					echo $key;
				}
				echo '"';
				
				if ($value === TRUE)
					echo ' checked';
				
				echo ' /> '.ucfirst($key);
				
			} else {
				
				if (is_numeric($key)) {
					
					// Numeric key = list
					
					echo '<td colspan="2"><input type="text" name="';
					if ($parent_key) {
						if ($parent_key) {
							echo $parent_key."[$key]";
						} else {
							echo $key;
						}
					} else {
					    echo $key;
					}
					echo '" value="'.$value.'" />';
					
				} else {
					
					// Non-numeric key so show value in a text box with key
					
					echo '<td class="align_right">';
					echo ucfirst($key).':</td>';
					echo '<td><input type="text" name="';
					if ($parent_key) {
						echo $parent_key."[$key]";
					} else {
						echo $key;
					}
					echo '" value="'.$value.'" />';
				}
				
			}
		
			echo '</td></tr>'."\n";
		
		}
	
	}
	
}

?>

<div class="center_container">

<form action="admin.php?page=config" method="POST">

	<table class="center">
		
		<?php
		
		formify($GLOBALS['app']);
		unset($GLOBALS['numeric_keys']);
		
		?>
		
		<tr><td></td><td class="align_left"><input type="submit" value="Save" /></td></tr>
		
	</table>

</form>

</div>
