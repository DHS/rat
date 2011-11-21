<?php

function generate_thumbnail($filename, $type, $max_width = 100, $max_height = 100, $dir = 'thumbnails', $upload_dir = 'uploads') {
	
	// Create temporary source image resource
	if ($type == 'image/jpeg' || $type == 'image/pjpeg') {
		$src = imagecreatefromjpeg("$upload_dir/originals/$filename");
	} elseif ($type == 'image/png') {
		$src = imagecreatefrompng("$upload_dir/originals/$filename");
	} elseif ($type == 'image/gif') {
		$src = imagecreatefromgif("$upload_dir/originals/$filename");
	}
	
	// Find existing dimensions
	$old_width = imagesx($src);
	$old_height = imagesy($src);
	
	// Generate new dimensions, check width first
	if ($old_width > $max_width) {
		$new_width = $old_width * ($max_width / $old_width);
		$new_height = $old_height * ($max_width / $old_width);
	} else {
		$new_width = $old_width;
		$new_height = $old_height;
	}
	
	// Then check height
	if ($new_height > $max_height) {
		$new_width = $old_width * ($max_height / $old_height);
		$new_height = $old_height * ($max_height / $old_height);
	}
	
	// Create temporary destination image resource
	$new = imagecreatetruecolor($new_width, $new_height);
	
	// Preserve transparency on PNGs
	imagealphablending($new, FALSE);
	imagesavealpha($new, TRUE);
	
	// Generate new image
	imagecopyresampled($new, $src, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
	
	// Save new image
	if ($type == 'image/jpeg' || $type == 'image/pjpeg') {
		imagejpeg($new, "$upload_dir/$dir/$filename");
	} elseif ($type == 'image/png') {
		imagepng($new, "$upload_dir/$dir/$filename");
	} elseif ($type == 'image/gif') {
		imagegif($new, "$upload_dir/$dir/$filename");
	}
	
	// Delete temporary image resources
	imagedestroy($new);
	imagedestroy($src);
	
}

?>