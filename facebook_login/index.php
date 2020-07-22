<?php

    define('_IN_JOHNCMS',1);
    require('../incfiles/core.php');

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

function createCover($cover_id=0, $pos=0)
{
        $photo_dir = '../files/users/photo';
        $cover = functions::get_user($cover_id);
            $img = $photo_dir . '/' . $cover_id . '.' . $cover['cover_extension'];
            $cover_img_url = $photo_dir . '/' . $cover_id . '_cover.' . $cover['cover_extension'];

            list($width, $height) = getimagesize($img);
            $dst_x = 0;
            $dst_y = 0;
            $src_x = 0;
            $src_y = 0;
            $dst_w = $width;
            $dst_h = $dst_w * (0.3);
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

function importMedia($url='', $app=''){
    global $usid;
    
    if (empty($url))
    {
        return false;
    }
    
    if (($source = @file_get_contents($url)) == false)
    {
        return false;
    }
    
    $photo_dir = '../files/users/'.$app;
    $name = preg_replace('/([^A-Za-z0-9_\-\.]+)/i', '', $url);
    $url_ext = $url;
    
    if (($qs_ext_pos = strrpos($url, '?')) !== false) {
        $url_ext = substr($url, 0, $qs_ext_pos);
    }
    
    $dot_ext_pos = strrpos($url_ext, '.');
    $url_ext = strtolower(substr($url_ext, $dot_ext_pos + 1, strlen($url_ext) - $dot_ext_pos));
    
    if (!preg_match('/^(jpg|jpeg|png)$/', $url_ext)) {
        return false;
    }
    
    $original_file_name = $photo_dir . '/' . $usid;
    $original_file = $original_file_name . '.' . $url_ext;
    $register_cover = @file_put_contents($original_file, $source);
    
    if ($register_cover)
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

        if ($base_mime != $url_ext){
            rename($original_file, $original_file_name . '.' . $base_mime);
            $original_file = $original_file_name . '.' . $base_mime;
            $url_ext = $base_mime;
        }
        if($app == "avatar"){
        list($width, $height) = getimagesize($original_file);
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
            $save_file = $data['name'] . '.' . $url_ext;
            processMedia($data['type'], $original_file, $save_file, $data['width'], $data['height']);
        }
        }
        if($app == "photo"){
            mysql_query("UPDATE `users` SET cover_extension='" . $url_ext . "' WHERE id=" . $usid);
            processMedia('crop', $original_file, $original_file_name.'_thumb.'.$url_ext, '64', '64');
            createCover($usid);
        }
        $get = array(
            'id' => 1,
            'active' => 1,
            'extension' => $url_ext,
            'name' => $name,
            'url' => $original_file_name
        );
        
        return $get;
    }
}
    include('src/facebook.php');
    if($user_id){
        header('Location: '.$home.'');
        exit();
    } else {
        $config['appId'] = '429585823904973';
        $config['secret'] = '1513d56a3bb0f2367b6030da60076c0e';
        $params = array(
            'scope' => 'email,public_profile,user_birthday,user_hometown,user_location,user_photos,user_about_me,publish_actions,user_work_history',
            'redirect_uri' => ''.$home.'/facebook_login/'
        );
        $facebook = new Facebook($config);
        $user_id_fb = $facebook->getUser();
        if($user_id_fb){
            try{
                $user = $facebook->api('/me?fields=email,birthday,gender,name,cover,location,hometown,bio,link,picture.width(720).height(720)');
                $name_fb = @functions::check($user['name']); // full name
                $country = $user['hometown']['name']; //hometown
                $bio = @functions::check($user['bio']); //about
                $link = $user['link']; //link facebook
                $icover = $user['cover']; 
                $ipicture = $user['picture'];
                $cover = $user['cover']['source']; 
                $picture = $user['picture']['data']['url'];
                $birthday = $user['birthday'];
                $fbbirthday = explode('/', $birthday);
                    $fbbirthday[0]= (empty($fbbirthday[0])) ? 0 : $fbbirthday[0];
                    $fbbirthday[1] = (empty($fbbirthday[1])) ? 0 : $fbbirthday[1];
                    $fbbirthday[2] = (empty($fbbirthday[2])) ? 0 : $fbbirthday[2];
                $sex_fb = $user['gender']; //sex
                $email_fb = $user['email']; //email
                $pass_rand = rand(100000,999999);
                $password = md5(md5($pass_rand));
                $fb_ID = trim($user_id_fb);
                if($sex_fb == 'male'){$sex = 'm';} else {$sex = 'zh';}
                $dango = @functions::check($country);
                $mail = @functions::check($email_fb);
                $check = mysql_query("select * from `users` where `facebook_ID` = '$fb_ID'");
                $check_number = mysql_num_rows($check);
                if($check_number > 0){
                    $user_login = mysql_fetch_array($check);
                    $_SESSION['uid'] = $user_login['id'];
                    $_SESSION['ups'] = $user_login['password'];
                    mysql_query("UPDATE `users` SET
                        `name` = '".mysql_real_escape_string($name_fb)."',
                        `name_lat` = '".mysql_real_escape_string($name_fb)."',
                        `imname` = '".mysql_real_escape_string($name_fb)."',
                        `facebook_ID` = '$fb_ID',
                        `facebook_Link` = '$link',
                        `facebook_Update` = '" . time() . "',
                        `sex` = '$sex',
                        `dayb` = '" . $fbbirthday[1] . "',
                        `monthb` = '" . $fbbirthday[0] . "',
                        `yearofbirth` = '" . $fbbirthday[2] . "',
                        `about` = '".$bio."',
                        `ip` = '" . core::$ip . "',
                        `ip_via_proxy` = '" . core::$ip_via_proxy . "',
                        `browser` = '" . mysql_real_escape_string($agn) . "',
                        `preg` = '1',
                        `mail` = '$mail',
                        `live` = '$dango'
                        WHERE `id`='" . $user_login['id'] . "';
                    ");
                    mysql_query("UPDATE `users` SET `sestime` = '" . time() . "' WHERE `id` = '" . $user_login['id'] . "'");
                    $usid = $user_login['id'];

                            if (!empty($cover) && is_array($icover))
                            {
                                $cover = importMedia($cover, 'photo');
                            }
                            
                            if (is_array($ipicture) && ! empty($picture))
                            {
                                $avatar = importMedia($picture, 'avatar');
                                
                                if (is_array($avatar))
                                {
                                    mysql_query("UPDATE `users` SET avatar_extension='" . $avatar['extension'] . "' WHERE id=" . $usid);
                                }
                            }

                    header('Location: '.$home.'');
                } else {
                    mysql_query("INSERT INTO `users` SET
                        `name` = '".mysql_real_escape_string($name_fb)."',
                        `name_lat` = '".mysql_real_escape_string($name_fb)."',
                        `imname` = '".mysql_real_escape_string($name_fb)."',
                        `facebook_ID` = '$fb_ID',
                        `facebook_Link` = '$link',
                        `facebook_Update` = '" . time() . "',
                        `password` = '" . mysql_real_escape_string($password) . "',
                        `sex` = '$sex',
                        `dayb` = '" . $fbbirthday[1] . "',
                        `monthb` = '" . $fbbirthday[0] . "',
                        `yearofbirth` = '" . $fbbirthday[2] . "',
                        `about` = '".$bio."',
                        `rights` = '0',
                        `ip` = '" . core::$ip . "',
                        `ip_via_proxy` = '" . core::$ip_via_proxy . "',
                        `browser` = '" . mysql_real_escape_string($agn) . "',
                        `datereg` = '" . time() . "',
                        `lastdate` = '" . time() . "',
                        `sestime` = '" . time() . "',
                        `preg` = '1',
                        `mail` = '$mail',
                        `status` = 'Login with Facebook.!',
                        `balans` = '10000',
                        `live` = '$dango'
                    ") or exit(__LINE__ . ': ' . mysql_error());
                    $usid = mysql_insert_id();
                    $_SESSION['uid'] = $usid;
                    $_SESSION['ups'] = $password;

                            if (! empty($cover) && is_array($icover))
                            {
                                $cover = importMedia($cover, 'photo');
                            }
                            
                            if (is_array($ipicture) && ! empty($picture))
                            {
                                $avatar = importMedia($picture, 'avatar');
                                
                                if (is_array($avatar))
                                {
                                    mysql_query("UPDATE `users` SET avatar_extension='" . $avatar['extension'] . "' WHERE id=" . $usid);
                                }
                            }

header('Location: '.$home.'');

}

// Kết thúc phần lưu dữ liệu
 
}
catch(FacebookApiException $e) //Nếu chưa đăng nhập vào Facebook ứng dụng tự động đăng nhập lại
{
echo 'Lỗi:'.$e->getMessage();
$loginUrl = $facebook->getLoginUrl($params);
exit("Vui lòng click <a href='$loginUrl' target='_top'>vào đây</a> để đăng nhập lại");
}
}
else //Nếu chưa lấy được userid ứng dụng tự động đăng nhập vào Facebook
{

$loginUrl = $facebook->getLoginUrl($params);
header('Location: '.$loginUrl.'');

}
}