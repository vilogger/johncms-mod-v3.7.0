<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

require('../incfiles/head.php');
if (!$id || !$user_id) {
    echo functions::display_error($lng['error_wrong_data']);
    require('../incfiles/end.php');
    exit;
}
// Проверяем, тот ли юзер заливает файл
$req = mysql_query("SELECT * FROM `forum` WHERE `id` = '$id'");
$res = mysql_fetch_assoc($req);
if (!$res || $res['user_id'] != $user_id) {
    echo functions::display_error($lng['error_wrong_data']);
    require('../incfiles/end.php');
    exit;
}

function format($name) {
    $f1 = strrpos($name, ".");
    $f2 = substr($name, $f1 + 1, 999);
    $fname = strtolower($f2);
    return $fname;
}
switch ($res['type']) {
    case "m":
        if (isset($_POST['submit'])) {
$url = trim($_POST['url']);
        $newn = @functions::check(functions::seourl(trim($_POST['newn'])));
        $tipf = format($url);
        $tentaptin=basename($url);
        $path_parts = pathinfo($url);
        $duoi=$path_parts['extension'];
        $ten=$path_parts['filename'];
if (empty($newn)) {$fname = @functions::check(functions::seourl($ten)).'.'.$duoi;}else{$fname=''.$newn.'.'.$duoi.'';}
                $al_ext = array_merge($ext_win, $ext_java, $ext_sis, $ext_doc, $ext_pic, $ext_arch, $ext_video, $ext_audio, $ext_other);
                $ext = explode(".", $fname);
                $error = array ();

                if (!in_array($duoi, $al_ext))
                    $error[] = $lng_forum['error_file_ext'] . ':<br />' . implode(', ', $al_ext);

                if (strlen($fname) > 30)
                    $error[] = $lng_forum['error_file_name_size'];

                if(preg_match("/$%^&#/", $fname))
                    $error[] = $lng_forum['error_file_symbols'];

                if (file_exists("../files/forum/attach/$fname")) {
                    $fname = time() . $fname;
                }
                $import = "../files/forum/attach/$fname";
                if (!$error) {
                    if(!copy($url, $import)) {

                        $error[] = $lng_forum['error_upload_error'];
                    }
                }
                if (!$error) {
                    echo $lng_forum['file_uploaded'] . '<br/>';
                    // Определяем тип файла
                    $ext = $duoi;
                    if (in_array($ext, $ext_win))
                        $type = 1;
                    elseif (in_array($ext, $ext_java))
                        $type = 2;
                    elseif (in_array($ext, $ext_sis))
                        $type = 3;
                    elseif (in_array($ext, $ext_doc))
                        $type = 4;
                    elseif (in_array($ext, $ext_pic))
                        $type = 5;
                    elseif (in_array($ext, $ext_arch))
                        $type = 6;
                    elseif (in_array($ext, $ext_video))
                        $type = 7;
                    elseif (in_array($ext, $ext_audio))
                        $type = 8;
                    else
                        $type = 9;
                    // Определяем ID субкатегории и категории
                    $req2 = mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $res['refid'] . "'");
                    $res2 = mysql_fetch_array($req2);
                    $req3 = mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $res2['refid'] . "'");
                    $res3 = mysql_fetch_array($req3);
                    // Заносим данные в базу
                    mysql_query("INSERT INTO `cms_forum_files` SET
                        `cat` = '" . $res3['refid'] . "',
                        `subcat` = '" . $res2['refid'] . "',
                        `topic` = '" . $res['refid'] . "',
                        `post` = '$id',
                        `time` = '" . time() . "',
                        `filename` = '" . mysql_real_escape_string($fname) . "',
                        `filetype` = '$type'
                    ");
                } else {
                    echo functions::display_error($error, '<a href="index.php?act=import&amp;id=' . $id . '">' . $lng['repeat'] . '</a>');
                }
            $pa = mysql_query("SELECT `id` FROM `forum` WHERE `type` = 'm' AND `refid` = '" . $res['refid'] . "'");
            $patt = mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $res['refid'] . "'");
            $respatt = mysql_fetch_array($patt);
            $pa2 = mysql_num_rows($pa);
            $page = ceil($pa2 / $kmess);
            echo '<br/><a href="/forum/' . $respatt['id'] . '/' . $respatt['seo'] . '_p' . $page . '.html#post' . $id . '">' . $lng['continue'] . '</a><br/>';
        } else {
            echo '<div class="phdr"><a href="index.php?act=addfile&amp;id=' . $id . '">' . $lng_forum['add_file'] . '</a> | ';
            echo 'Nhập khẩu file</div>' .
                '<div class="list1"><form action="index.php?act=import&amp;id=' . $id . '" method="post" enctype="multipart/form-data"><p>';
                echo "URL(địa chỉ file):<br/><input type='text' name='url' value='http://'/> <br/>Lưu với tên mới. <small>(Có thể bỏ trống)</small>:<br/><input type='text' name='newn'/>";
            echo '</p><p><input type="submit" name="submit" value="Nhập khẩu"/></p></form></div>' .
                '<div class="phdr">' . $lng_forum['max_size'] . ': ' . $set['flsz'] . 'Kb.</div>';
        }
        break;

    default:
        echo functions::display_error($lng['error_wrong_data']);
}
?>