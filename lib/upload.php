<?php

/**
 * Master function for uploading, checks filenames and generates thumbnails
 */
function upload($file, $dir = 'uploads') {

  $filename = check_filename($file['name'], $dir);

  // Pop the original file in /uploads/originals
  move_uploaded_file($file['tmp_name'], $dir . '/originals/' . $filename);

  // Generate thumbnail
  upload_file($filename, $file['type'], 100, 100, 'thumbnails', $dir);

  // Generate stream image
  upload_file($filename, $file['type'], 350, 500, 'stream', $dir);

  return $filename;

}

/**
* Check for original filename and increment if necessary
*/
function check_filename($filename, $dir = 'uploads') {

  // Check for file with same name and rename if neccessary
  if (file_exists($dir . '/originals/' . $filename)) {

    // Find file and extension, works for filenames that include dots and any length extension
    $file = substr($filename, 0, strrpos($filename, '.'));

    $length = strlen($filename);
    $dot_position = strrpos($filename, '.');
    $extension_start = 0 - ($length - $dot_position - 1);

    $extension = substr($filename, $extension_start);

    // Extends clashing filenames as such: for clash.jpg try clash-1.jpg, clash-2.jpg etc
    $i = 1;
    do {
      $filename = $file . '-' . $i . '.' . $extension;
      $i++;
    } while (file_exists($dir . '/originals/' . $filename));

  }

  return $filename;

}

/**
* Upload the file and resize to the specified dimensions
*/
function upload_file($filename, $type, $max_width = 100, $max_height = 100, $dir = 'thumbnails', $upload_dir = 'uploads') {

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
