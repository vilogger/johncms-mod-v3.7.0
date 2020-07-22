<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

require('incfiles/core.php');
$textl = $lng['registration'];
$headmod = 'registration';
require('incfiles/head.php');
$lng_reg = core::load_lng('registration');


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


// Если регистрация закрыта, выводим предупреждение
if (core::$deny_registration || !$set['mod_reg'] || core::$user_id) {
    echo '<p>' . $lng_reg['registration_closed'] . '</p>';
    require('incfiles/end.php');
    exit;
}

$captcha = isset($_POST['captcha']) ? trim($_POST['captcha']) : NULL;
$reg_nick = isset($_POST['nick']) ? trim($_POST['nick']) : '';
$lat_nick = functions::rus_lat(mb_strtolower($reg_nick));
$reg_pass = isset($_POST['password']) ? trim($_POST['password']) : '';
$reg_sex = isset($_POST['sex']) ? functions::check(mb_substr(trim($_POST['sex']), 0, 2)) : '';

echo '<div class="phdr"><a href="login.php">' . $lng['login'] . '</a> | ' . $lng['registration'] . '</div>';
if (isset($_POST['submit'])) {
    // Принимаем переменные
    $error = array();

    // Проверка Логина
    if (empty($reg_nick)) {
        $error['login'][] = $lng_reg['error_nick_empty'];
    } elseif (mb_strlen($reg_nick) < 2 || mb_strlen($reg_nick) > 15) {
        $error['login'][] = $lng_reg['error_nick_lenght'];
    }

    if (preg_match('/[^\da-z\-\@\*\(\)\?\!\~\_\=\[\]]+/', $lat_nick)) {
        $error['login'][] = $lng['error_wrong_symbols'];
    }

    // Проверка пароля
    if (empty($reg_pass)) {
        $error['password'][] = $lng['error_empty_password'];
    } elseif (mb_strlen($reg_pass) < 3 || mb_strlen($reg_pass) > 10) {
        $error['password'][] = $lng['error_wrong_lenght'];
    }

    if (preg_match('/[^\dA-Za-z]+/', $reg_pass)) {
        $error['password'][] = $lng['error_wrong_symbols'];
    }

    // Проверка пола
    if ($reg_sex != 'm' && $reg_sex != 'zh') {
        $error['sex'] = $lng_reg['error_sex'];
    }

    // Проверка кода CAPTCHA
    if (!$captcha
        || !isset($_SESSION['code'])
        || mb_strlen($captcha) < 4
        || $captcha != $_SESSION['code']
    ) {
        $error['captcha'] = $lng['error_wrong_captcha'];
    }
    unset($_SESSION['code']);

    // Проверка переменных
    if (empty($error)) {
        $pass = md5(md5($reg_pass));
        $reg_name = functions::check(mb_substr($reg_name, 0, 20));
        // Проверка, занят ли ник
        $req = mysql_query("SELECT * FROM `users` WHERE `name_lat`='" . mysql_real_escape_string($lat_nick) . "'");
        if (mysql_num_rows($req) != 0) {
            $error['login'][] = $lng_reg['error_nick_occupied'];
        }
    }
    if (empty($error)) {
        $preg = $set['mod_reg'] > 1 ? 1 : 0;
        mysql_query("INSERT INTO `users` SET
            `name` = '" . mysql_real_escape_string($reg_nick) . "',
            `name_lat` = '" . mysql_real_escape_string($lat_nick) . "',
            `password` = '" . mysql_real_escape_string($pass) . "',
            `sex` = '$reg_sex',
            `rights` = '0',
            `ip` = '" . core::$ip . "',
            `ip_via_proxy` = '" . core::$ip_via_proxy . "',
            `browser` = '" . mysql_real_escape_string($agn) . "',
            `datereg` = '" . time() . "',
            `lastdate` = '" . time() . "',
            `sestime` = '" . time() . "',
            `preg` = '$preg',
            `set_user` = '',
            `set_forum` = '',
            `set_mail` = '',
            `smileys` = '',
            `status` = 'Thành viên mới.!',
            `balans` = '10000'
        ") or exit(__LINE__ . ': ' . mysql_error());
        $usid = mysql_insert_id();
        require ('new-avt.php');
        // Отправка системного сообщения
        $set_mail = unserialize($set['setting_mail']);

        if (!isset($set_mail['message_include'])) {
            $set_mail['message_include'] = 0;
        }

        if ($set_mail['message_include']) {
            $array = array('{LOGIN}', '{TIME}');
            $array_replace = array($reg_nick, '{TIME=' . time() . '}');

            if (empty($set['them_message'])) {
                $set['them_message'] = $lng_mail['them_message'];
            }

            if (empty($set['reg_message'])) {
                $set['reg_message'] = $lng['hi'] . ", {LOGIN}\r\n" . $lng_mail['pleased_see_you'] . "\r\n" . $lng_mail['come_my_site'] . "\r\n" . $lng_mail['respectfully_yours'];
            }

            $theme = str_replace($array, $array_replace, $set['them_message']);
            $system = str_replace($array, $array_replace, $set['reg_message']);
            mysql_query("INSERT INTO `cms_mail` SET
    `user_id` = '0',
    `from_id` = '" . $usid . "',
    `text` = '" . mysql_real_escape_string($system) . "',
    `time` = '" . time() . "',
    `sys` = '1',
    `them` = '11'
");
        }

        echo '<div class="menu"><p><h3>' . $lng_reg['you_registered'] . '</h3><br />' . $lng_reg['your_id'] . ': <b>' . $usid . '</b><br/>' . $lng_reg['your_login'] . ': <b>' . $reg_nick . '</b><br/>' . $lng_reg['your_password'] . ': <b>' . $reg_pass . '</b></p>';

        if ($set['mod_reg'] == 1) {
            echo '<p><span class="red"><b>' . $lng_reg['moderation_note'] . '</b></span></p>';
        } else {
            $_SESSION['uid'] = $usid;
            $_SESSION['ups'] = md5(md5($reg_pass));
            echo '<p><a href="' . $home . '">' . $lng_reg['enter'] . '</a></p>';
        }

        echo '</div>';
        require('incfiles/end.php');
        exit;
    }
}

/*
-----------------------------------------------------------------
Форма регистрации
-----------------------------------------------------------------
*/
if ($set['mod_reg'] == 1) echo '<div class="rmenu"><p>' . $lng_reg['moderation_warning'] . '</p></div>';
        echo '<div class="menu">Đăng ký với: <a href="/facebook_login/"><img src="/images/fb.png" /></a> <a href="/google_login/"><img src="/images/googlep.png" /></a> <img src="/images/twitter.png" /></div>';
echo '<form action="registration.php" method="post"><div class="gmenu">' .
    '<p><h3>' . $lng_reg['login'] . '</h3><br />' .
    (isset($error['login']) ? '<span class="red"><small>' . implode('<br />', $error['login']) . '</small></span><br />' : '') .
    '<input type="text" name="nick" maxlength="15" value="' . htmlspecialchars($reg_nick) . '"' . (isset($error['login']) ? ' style="background-color: #FFCCCC"' : '') . '/><br />' .
    '<small>' . $lng_reg['login_help'] . '</small></p>' .
    '<p><h3>' . $lng_reg['password'] . '</h3><br />' .
    (isset($error['password']) ? '<span class="red"><small>' . implode('<br />', $error['password']) . '</small></span><br />' : '') .
    '<input type="text" name="password" maxlength="20" value="' . htmlspecialchars($reg_pass) . '"' . (isset($error['password']) ? ' style="background-color: #FFCCCC"' : '') . '/><br/>' .
    '<small>' . $lng_reg['password_help'] . '</small></p>' .
    '<p><h3>' . $lng_reg['sex'] . '</h3><br />' .
    (isset($error['sex']) ? '<span class="red"><small>' . $error['sex'] . '</small></span><br />' : '') .
    '<select name="sex"' . (isset($error['sex']) ? ' style="background-color: #FFCCCC"' : '') . '>' .
    '<option value="?">-?-</option>' .
    '<option value="m"' . ($reg_sex == 'm' ? ' selected="selected"' : '') . '>' . $lng_reg['sex_m'] . '</option>' .
    '<option value="zh"' . ($reg_sex == 'zh' ? ' selected="selected"' : '') . '>' . $lng_reg['sex_w'] . '</option>' .
    '</select></p></div>' .
    '<div class="gmenu"><p>' .
    '<h3>' . $lng_reg['captcha'] . '</h3><br />' .
    '<img src="captcha.php?r=' . rand(1000, 9999) . '" alt="' . $lng_reg['captcha'] . '" border="1"/><br />' .
    (isset($error['captcha']) ? '<span class="red"><small>' . $error['captcha'] . '</small></span><br />' : '') .
    '<input type="text" size="5" maxlength="5"  name="captcha" ' . (isset($error['captcha']) ? ' style="background-color: #FFCCCC"' : '') . '/><br />' .
    '<small>' . $lng_reg['captcha_help'] . '</small></p>' .
    '<p><input type="submit" name="submit" value="' . $lng_reg['registration'] . '"/></p></div></form>' .
    '<div class="phdr"><small>' . $lng_reg['registration_terms'] . '</small></div>';

require('incfiles/end.php');