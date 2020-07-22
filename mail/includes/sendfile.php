<?php

defined('_IN_JOHNCMS') or die('Error: restricted access');
$textl = 'Gửi file';
require_once('../incfiles/head.php');
if (!$id) {
    echo functions::display_error($lng['error_wrong_data']);
    require('../incfiles/end.php');
    exit;
}
if ($id) {
    $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1");
    if (mysql_num_rows($req) == 0) {
        echo functions::display_error($lng['error_user_not_exist']);
        require_once("../incfiles/end.php");
        exit;
    }
}
    echo '<div class="phdr"><a href="index.php?act=write' . ($id ? '&amp;id=' . $id : '') . '">Mail</a> | Gửi file</div>';
    echo '<div class="gmenu"><div class="story-publisher-box">' .
        '<form name="form" action="index.php?act=write' . ($id ? '&amp;id=' . $id : '') . '" method="post"  enctype="multipart/form-data">';
    echo bbcode::auto_bb('form', 'text');
    echo '<textarea rows="' . $set_user['field_h'] . '" name="text"></textarea></p>';
    echo '<p><input type="file" name="fail" style="width: 100%; max-width: 160px"/></p>';
    echo '<p><input type="submit" name="submit" value="' . $lng['sent'] . '"/></p>' .
        '</form></div></div>';