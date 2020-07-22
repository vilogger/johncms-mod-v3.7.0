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

function importMedia($url=''){
    global $usid;
    
    if (empty($url))
    {
        return false;
    }
    
    if (($source = @file_get_contents($url)) == false)
    {
        return false;
    }
    
    $photo_dir = '../files/users/avatar';
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

    if($user_id){
        header('Location: '.$home.'');
        exit();
    } else {
        require_once ('libraries/Google/autoload.php');
        $client_id = '1087366805966-j65cu429il2je7g871lbj1usncm27kmc.apps.googleusercontent.com'; 
        $client_secret = 'PLMzXdNwNrnt8n3QGAG-4XrC';
        $redirect_uri = $home.'/google_login/';
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->addScope("email");
        $client->addScope("profile");
        $service = new Google_Service_Oauth2($client);
        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $client->getAccessToken();
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
            exit;
        }
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
        } else {
            $authUrl = $client->createAuthUrl();
        }
        if (isset($authUrl)){
            header('Location: '.$authUrl.'');
        } else {
            $g_user = $service->userinfo->get();
            $g_name = @functions::check($g_user->name);
            $g_picture = $g_user->picture;
            $g_mail = @functions::check($g_user->mail);
            $gsex = $g_user->gender;
            if($gsex == 'male'){$sex = 'm';} else {$sex = 'zh';}
            $pass_rand = rand(100000,999999);
            $password = md5(md5($pass_rand));
            $check = mysql_query("select * from `users` where `google_ID` = '".$g_user->id."'");
            $check_number = mysql_num_rows($check);
            if($check_number != 0){
                $user_login = mysql_fetch_array($check);
                $_SESSION['uid'] = $user_login['id'];
                $_SESSION['ups'] = $user_login['password'];
                $usid = $user_login['id'];

                            if (!empty($g_picture))
                            {
                                $avatar = importMedia($g_picture);
                                
                                if (is_array($avatar))
                                {
                                    mysql_query("UPDATE `users` SET avatar_extension='" . $avatar['extension'] . "' WHERE id=" . $usid);
                                }
                            }

                header('Location: '.$home.'');
            } else {
                mysql_query("INSERT INTO `users` SET
                    `name` = '".mysql_real_escape_string($g_name)."',
                    `name_lat` = '".mysql_real_escape_string($g_name)."',
                    `imname` = '".mysql_real_escape_string($g_name)."',
                    `google_ID` = '".$g_user->id."',
                    `google_Link` = '".$g_user->link."',
                    `google_Update` = '" . time() . "',
                    `password` = '" . mysql_real_escape_string($password) . "',
                    `sex` = '".$sex."',
                    `rights` = '0',
                    `ip` = '" . core::$ip . "',
                    `ip_via_proxy` = '" . core::$ip_via_proxy . "',
                    `browser` = '" . mysql_real_escape_string($agn) . "',
                    `datereg` = '" . time() . "',
                    `lastdate` = '" . time() . "',
                    `sestime` = '" . time() . "',
                    `preg` = '1',
                    `status` = 'Login with Google.!',
                    `balans` = '10000',
                    `mail` = '".$g_mail."'
                ") or exit(__LINE__ . ': ' . mysql_error());

                $usid = mysql_insert_id();

                $_SESSION['uid'] = $usid;
                $_SESSION['ups'] = $password;

                            if (!empty($g_picture))
                            {
                                $avatar = importMedia($g_picture);
                                
                                if (is_array($avatar))
                                {
                                    mysql_query("UPDATE `users` SET avatar_extension='" . $avatar['extension'] . "' WHERE id=" . $usid);
                                }
                            }

                header('Location: '.$home.'');
            }
        }
    }
?>