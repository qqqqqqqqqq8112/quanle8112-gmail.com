<?php



function resize_image($method,$image_loc,$new_loc,$width,$height) {

    if (!is_array(@$GLOBALS['errors'])) { $GLOBALS['errors'] = array(); }



    if (!in_array($method,array('force','max','crop'))) { $GLOBALS['errors'][] = 'Invalid method selected.'; }



    if (!$image_loc) { $GLOBALS['errors'][] = 'No source image location specified.'; }

    else {

        if ((substr(strtolower($image_loc),0,7) == 'http://') || (substr(strtolower($image_loc),0,7) == 'https://')) { /*don't check to see if file exists since it's not local*/ }

        elseif (!file_exists($image_loc)) { $GLOBALS['errors'][] = 'Image source file does not exist.'; }

        $extension = strtolower(substr($image_loc,strrpos($image_loc,'.')));

        if (!in_array($extension,array('.jpg','.jpeg','.png','.gif','.bmp'))) { $GLOBALS['errors'][] = 'Invalid source file extension!'; }

    }



    if (!$new_loc) { $GLOBALS['errors'][] = 'No destination image location specified.'; }

    else {

        $new_extension = strtolower(substr($new_loc,strrpos($new_loc,'.')));

        if (!in_array($new_extension,array('.jpg','.jpeg','.png','.gif','.bmp'))) { $GLOBALS['errors'][] = 'Invalid destination file extension!'; }

    }



    $width = abs(intval($width));

    if (!$width) { $GLOBALS['errors'][] = 'No width specified!'; }



    $height = abs(intval($height));

    if (!$height) { $GLOBALS['errors'][] = 'No height specified!'; }



    if (count($GLOBALS['errors']) > 0) { echo_errors(); return false; }



    if (in_array($extension,array('.jpg','.jpeg'))) { $image = @imagecreatefromjpeg($image_loc); }

    elseif ($extension == '.png') { $image = @imagecreatefrompng($image_loc); }

    elseif ($extension == '.gif') { $image = @imagecreatefromgif($image_loc); }

    elseif ($extension == '.bmp') { $image = @imagecreatefromwbmp($image_loc); }



    if (!$image) { $GLOBALS['errors'][] = 'Image could not be generated!'; }

    else {

        $current_width = imagesx($image);

        $current_height = imagesy($image);

        if ((!$current_width) || (!$current_height)) { $GLOBALS['errors'][] = 'Generated image has invalid dimensions!'; }

    }

    if (count($GLOBALS['errors']) > 0) { @imagedestroy($image); echo_errors(); return false; }



    if ($method == 'force') { $new_image = resize_image_force($image,$width,$height); }

    elseif ($method == 'max') { $new_image = resize_image_max($image,$width,$height); }

    elseif ($method == 'crop') { $new_image = resize_image_crop($image,$width,$height); }



    if ((!$new_image) && (count($GLOBALS['errors'] == 0))) { $GLOBALS['errors'][] = 'New image could not be generated!'; }

    if (count($GLOBALS['errors']) > 0) { @imagedestroy($image); echo_errors(); return false; }



    $save_error = false;

    if (in_array($extension,array('.jpg','.jpeg'))) { imagejpeg($new_image,$new_loc) or ($save_error = true); }

    elseif ($extension == '.png') { imagepng($new_image,$new_loc) or ($save_error = true); }

    elseif ($extension == '.gif') { imagegif($new_image,$new_loc) or ($save_error = true); }

    elseif ($extension == '.bmp') { imagewbmp($new_image,$new_loc) or ($save_error = true); }

    if ($save_error) { $GLOBALS['errors'][] = 'New image could not be saved!'; }

    if (count($GLOBALS['errors']) > 0) { @imagedestroy($image); @imagedestroy($new_image); echo_errors(); return false; }



    imagedestroy($image);

    imagedestroy($new_image);



    return true;

}



function echo_errors() {

    if (!is_array(@$GLOBALS['errors'])) { $GLOBALS['errors'] = array('Unknown error!'); }

    foreach ($GLOBALS['errors'] as $error) { echo '<p style="color:red;font-weight:bold;">Error: '.$error.'</p>'; }

}



?>