<?php
define('_IN_JOHNCMS', 1);
require('incfiles/core.php');

if (!$user_id) {
    echo functions::display_error($lng['access_forbidden']);
    exit;
}
$time = time();
function stringEscape($content, $nullvalue=false){
        if (empty($content) && $nullvalue != false) {
            $content = $nullvalue;
        }

        $content = trim($content);
        $content = mysql_real_escape_string($content);
        $content = htmlspecialchars($content, ENT_QUOTES);
        $content = stripslashes($content);
    
        return $content;
    }

/* Fix Orientation */
function fixOrientation($path)
{
    if (file_exists ($path))
    {
        if (strrpos($path, '.'))
        {
            $ext = substr($path, strrpos($path,'.') + 1, strlen($path) - strrpos($path, '.'));

            if (in_array($ext, array('jpeg', 'jpg')))
            {
                $fxt = true;
            }
        }
    }

    if (! isset($fxt))
    {
        return false;
    }

    $image = imagecreatefromjpeg($path);
    $exif = exif_read_data($path);
 
    if (!empty($exif['Orientation']))
    {
        switch ($exif['Orientation'])
        {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;

            case 6:
                $image = imagerotate($image, -90, 0);
                break;
                
            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }
    }

    imagejpeg($image, $path);
    return true;
}

    /* Process Images */
function processMedia($run, $photo_src, $save_src, $width=0, $height=0, $quality=80){
    
    if (! is_numeric($quality) or $quality < 0 or $quality > 100)
    {
        $quality = 80;
    }

    if (file_exists ($photo_src))
    {
        if (strrpos($photo_src, '.'))
        {
            $ext = substr($photo_src, strrpos($photo_src,'.') + 1, strlen($photo_src) - strrpos($photo_src, '.'));
            $fxt = (in_array($ext, array('jpeg', 'png', 'gif'))) ? $ext : "jpeg";
        }
        else
        {
            $ext = $fxt = 0;
        }
        
        if (preg_match('/(jpg|jpeg|png|gif)/', $ext))
        {
            if ($fxt == "gif")
            {
                copy($photo_src, $save_src);
                return true;
            }

            list($photo_width, $photo_height) = getimagesize($photo_src);
            $create_from = "imagecreatefrom" . $fxt;
            $photo_source = $create_from($photo_src);
            
            if ($run == "crop")
            {
                if ($width > 0 && $height > 0)
                {
                    $crop_width = $photo_width;
                    $crop_height = $photo_height;
                    $k_w = 1;
                    $k_h = 1;
                    $dst_x = 0;
                    $dst_y = 0;
                    $src_x = 0;
                    $src_y = 0;
                    
                    if ($width == 0 or $width > $photo_width)
                    {
                        $width = $photo_width;
                    }
                    
                    if ($height == 0 or $height > $photo_height)
                    {
                        $height = $photo_height;
                    }
                    
                    $crop_width = $width;
                    $crop_height = $height;
                    
                    if ($crop_width > $photo_width)
                    {
                        $dst_x = ($crop_width - $photo_width) / 2;
                    }
                    
                    if ($crop_height > $photo_height)
                    {
                        $dst_y = ($crop_height - $photo_height) / 2;
                    }
                    
                    if ($crop_width < $photo_width || $crop_height < $photo_height)
                    {
                        $k_w = $crop_width / $photo_width;
                        $k_h = $crop_height / $photo_height;
                        
                        if ($crop_height > $photo_height)
                        {
                            $src_x  = ($photo_width - $crop_width) / 2;
                        }
                        elseif ($crop_width > $photo_width)
                        {
                            $src_y  = ($photo_height - $crop_height) / 2;
                        }
                        else
                        {
                            if ($k_h > $k_w)
                            {
                                $src_x = round(($photo_width - ($crop_width / $k_h)) / 2);
                            }
                            else
                            {
                                $src_y = round(($photo_height - ($crop_height / $k_w)) / 2);
                            }
                        }
                    }
                    
                    $crop_image = @imagecreatetruecolor($crop_width, $crop_height);
                    
                    if ($ext == "png")
                    {
                        @imagesavealpha($crop_image, true);
                        @imagefill($crop_image, 0, 0, @imagecolorallocatealpha($crop_image, 0, 0, 0, 127));
                    }
                    
                    @imagecopyresampled($crop_image, $photo_source ,$dst_x, $dst_y, $src_x, $src_y, $crop_width - 2 * $dst_x, $crop_height - 2 * $dst_y, $photo_width - 2 * $src_x, $photo_height - 2 * $src_y);
                    
                    @imageinterlace($crop_image, true);

                    if ($fxt == "jpeg")
                    {
                        @imagejpeg($crop_image, $save_src, $quality);
                    }
                    elseif ($fxt == "png")
                    {
                        @imagepng($crop_image, $save_src);
                    }
                    elseif ($fxt == "gif")
                    {
                        @imagegif($crop_image, $save_src);
                    }

                    @imagedestroy($crop_image);
                }
            }
            elseif ($run == "resize")
            {
                if ($width == 0 && $height == 0)
                {
                    return false;
                }
                
                if ($width > 0 && $height == 0)
                {
                    $resize_width = $width;
                    $resize_ratio = $resize_width / $photo_width;
                    $resize_height = floor($photo_height * $resize_ratio);
                }
                elseif ($width == 0 && $height > 0)
                {
                    $resize_height = $height;
                    $resize_ratio = $resize_height / $photo_height;
                    $resize_width = floor($photo_width * $resize_ratio);
                }
                elseif ($width > 0 && $height > 0)
                {
                    $resize_width = $width;
                    $resize_height = $height;
                }
                
                if ($resize_width > 0 && $resize_height > 0)
                {
                    $resize_image = @imagecreatetruecolor($resize_width, $resize_height);
                    
                    if ($ext == "png")
                    {
                        @imagesavealpha($resize_image, true);
                        @imagefill($resize_image, 0, 0, @imagecolorallocatealpha($resize_image, 0, 0, 0, 127));
                    }
                    
                    @imagecopyresampled($resize_image, $photo_source, 0, 0, 0, 0, $resize_width, $resize_height, $photo_width, $photo_height);
                    @imageinterlace($resize_image, true);

                    if ($fxt == "jpeg")
                    {
                        @imagejpeg($resize_image, $save_src, $quality);
                    }
                    elseif ($fxt == "png")
                    {
                        @imagepng($resize_image, $save_src);
                    }
                    elseif ($fxt == "gif")
                    {
                        @imagegif($resize_image, $save_src);
                    }

                    @imagedestroy($resize_image);
                }
            }
            elseif ($run == "scale")
            {
                if ($width == 0)
                {
                    $width = 100;
                }
                
                if ($height == 0)
                {
                    $height = 100;
                }
                
                $scale_width = $photo_width * ($width / 100);
                $scale_height = $photo_height * ($height / 100);
                $scale_image = @imagecreatetruecolor($scale_width, $scale_height);
                
                if ($ext == "png")
                {
                    @imagesavealpha($scale_image, true);
                    @imagefill($scale_image, 0, 0, imagecolorallocatealpha($scale_image, 0, 0, 0, 127));
                }
                
                @imagecopyresampled($scale_image, $photo_source, 0, 0, 0, 0, $scale_width, $scale_height, $photo_width, $photo_height);
                @imageinterlace($scale_image, true);

                if ($fxt == "jpeg")
                {
                    @imagejpeg($scale_image, $save_src, $quality);
                }
                elseif ($fxt == "png")
                {
                    @imagepng($scale_image, $save_src);
                }
                elseif ($fxt == "gif")
                {
                    @imagegif($scale_image, $save_src);
                }

                @imagedestroy($scale_image);
            }
        }
    }
    }

/* Register Media */
function registerMedia($upload, $album_id=0)
{

    $photo_dir = 'files/users/avatar';

    if (is_uploaded_file($upload['tmp_name']))
    {
        global $user_id;
        $upload['name'] = stringEscape($upload['name']);
        $name = preg_replace('/([^A-Za-z0-9_\-\.]+)/i', '', $upload['name']);
        $ext = strtolower(substr($upload['name'], strrpos($upload['name'], '.') + 1, strlen($upload['name']) - strrpos($upload['name'], '.')));
        
        if ($upload['size'] > 1024)
        {
            if (preg_match('/(jpg|jpeg|png|gif)/', $ext))
            {
                list($width, $height) = getimagesize($upload['tmp_name']);

                    $original_file_name = $photo_dir . '/' . $user_id;
                    $original_file = $original_file_name . '.' . $ext;
                    
                    if (move_uploaded_file($upload['tmp_name'], $original_file))
                    {

        $image_mime = image_type_to_mime_type(exif_imagetype($original_file));
        switch ($image_mime) { 
            case "image/gif": 
                $base_mime = "gif"; 
                break; 
            case "image/jpeg": 
                $base_mime = "jpg"; 
                break; 
            case "image/png": 
                $base_mime = "png"; 
                break; 
        }

        if ($base_mime != $ext){
            rename($original_file, $original_file_name . '.' . $base_mime);
            $original_file = $original_file_name . '.' . $base_mime;
            $ext = $base_mime;
        }

                        @fixOrientation($original_file);

                        $min_size = $width;
                        
                        if ($width > $height)
                        {
                            $min_size = $height;
                        }
                        
                        $min_size = floor($min_size);
                        
                        if ($min_size > 920)
                        {
                            $min_size = 920;
                        }
                        
                        $imageSizes = array(
                            'thumb' => array(
                                'type' => 'crop',
                                'width' => 64,
                                'height' => 64,
                                'name' => $original_file_name . '_thumb'
                            ),
                            '100x100' => array(
                                'type' => 'crop',
                                'width' => $min_size,
                                'height' => $min_size,
                                'name' => $original_file_name . '_100x100'
                            ),
                            '100x75' => array(
                                'type' => 'crop',
                                'width' => $min_size,
                                'height' => floor($min_size * 0.75),
                                'name' => $original_file_name . '_100x75'
                            )
                        );
                        
                        foreach ($imageSizes as $ratio => $data)
                        {
                            $save_file = $data['name'] . '.' . $ext;
                            processMedia($data['type'], $original_file, $save_file, $data['width'], $data['height']);
                        }
                        
                        processMedia('resize', $original_file, $original_file, $min_size, 0);

                        $get = array(
                            'id' => '1',
                            'active' => 1,
                            'extension' => $ext,
                            'name' => $name,
                            'url' => $original_file_name
                        );
                        
                        return $get;
                    }
                
            }
        }
    }
}

    /* Register cover */
function registerCoverImage($upload, $pos=0)
{
        global $user_id;
        $photo_dir = 'files/users/photo';

        if (is_uploaded_file($upload['tmp_name']))
        {
            $upload['name'] = stringEscape($upload['name']);
            $name = preg_replace('/([^A-Za-z0-9_\-\.]+)/i', '', $upload['name']);
            $ext = strtolower(substr($upload['name'], strrpos($upload['name'], '.') + 1, strlen($upload['name']) - strrpos($upload['name'], '.')));
        
            if ($upload['size'] > 1024)
            {
                if (preg_match('/(jpg|jpeg|png)/', $ext))
                {
                    list($width, $height) = getimagesize($upload['tmp_name']);

                        $original_file_name = $photo_dir . '/' . $user_id;
                        $original_file = $original_file_name . '.' . $ext;
                    
                        if (move_uploaded_file($upload['tmp_name'], $original_file))
                        {
        $image_mime = image_type_to_mime_type(exif_imagetype($original_file));
        switch ($image_mime) { 
            case "image/gif": 
                $base_mime = "gif"; 
                break; 
            case "image/jpeg": 
                $base_mime = "jpg"; 
                break; 
            case "image/png": 
                $base_mime = "png"; 
                break; 
        }

        if ($base_mime != $ext){
            rename($original_file, $original_file_name . '.' . $base_mime);
            $original_file = $original_file_name . '.' . $base_mime;
            $ext = $base_mime;
        }



                            processMedia('resize', $original_file, $original_file, $width, 0, 100);

                            $img = $original_file;
                            $cover_img_url = $original_file_name . '_cover.' . $ext;
                            $dst_x = 0;
                            $dst_y = 0;
                            $src_x = 0;
                            $src_y = 0;
                            $dst_w = $width;
                            $dst_h = $dst_w * (0.39);
                            $src_w = $width;
                            $src_h = $dst_h;
                        
                            if (! empty($pos) && is_numeric($pos) && $pos < $width)
                            {
                                $pos = stringEscape($pos);
                                $src_y = $width * $pos;
                            }
                        
                            $cover_img = imagecreatetruecolor($dst_w, $dst_h);

                            if ($ext == "png")
                            {
                                $image = imagecreatefrompng($img);
                            }
                            else
                            {
                                $image = imagecreatefromjpeg($img);
                            }
                        
                            imagecopyresampled($cover_img, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
                            imagejpeg($cover_img, $cover_img_url, 100);

                            processMedia('crop', $original_file, $original_file_name.'_thumb.'.$ext, '64', '64');


                            $get = array(
                                'id' => '1',
                                'active' => 1,
                                'extension' => $ext,
                                'name' => $name,
                                'url' => $original_file_name,
                                'cover_url' => $original_file_name . '_cover.' . $ext
                            );
                        
                            return $get;
                        }
                    
                }
            }
        }
    }

function createCover($cover_id=0, $pos=0)
{
        $photo_dir = 'files/users/photo';
        $cover = functions::get_user($cover_id);
            $img = $photo_dir . '/' . $cover_id . '.' . $cover['cover_extension'];
            $cover_img_url = $photo_dir . '/' . $cover_id . '_cover.' . $cover['cover_extension'];

            list($width, $height) = getimagesize($img);
            $dst_x = 0;
            $dst_y = 0;
            $src_x = 0;
            $src_y = 0;
            $dst_w = $width;
            $dst_h = $dst_w * (0.39);
            $src_w = $width;
            $src_h = $dst_h;
        
            if (!empty($pos) && is_numeric($pos) && $pos < $width)
            {
                $pos = stringEscape($pos);
                $src_y = $width * $pos;
            }
        
            $cover_img = imagecreatetruecolor($dst_w, $dst_h);
        
            if ($cover['extension'] == "png")
            {
                $image = imagecreatefrompng($img);
            }
            else
            {
                $image = imagecreatefromjpeg($img);
            }

            imagecopyresampled($cover_img, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
            imagejpeg($cover_img, $cover_img_url, 100);
            return $cover_img_url;
        
    }

$t = (!isset($_GET['t']) ? "" : stringEscape($_GET['t']));
$a = (!isset($_GET['a']) ? "" : stringEscape($_GET['a']));


$data = array(
    'status' => 417
);

if (empty($t))
{
exit('error');
}

include('requests/' . $t . '.php');