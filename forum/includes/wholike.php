<?php

defined('_IN_JOHNCMS') or die('Error: restricted access');
$textl = 'Lượt like bài viết';
require('../incfiles/head.php');
if (!$id) {
    echo functions::display_error($lng['error_wrong_data']);
    require('incfiles/end.php');
    exit;
} else {

$count_like = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_thank` WHERE `topic` = '$id'"), 0);
if($count_like) {
    echo '<div class="phdr">' . $count_like . ' người thích</div>';
    $req_like = mysql_query("SELECT * FROM `forum_thank` WHERE `topic` = '$id' ORDER BY `id` LIMIT $start, $kmess");
    for($i = 0; $res_like = mysql_fetch_array($req_like); $i++) {
        $usr = functions::get_user($res_like['userthank']);
        echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
        echo '<table style="padding: 0; border-spacing: 0;"><tr><td>';

                    echo '<img src="/avatar/'.$usr['id'].'-24-48.png" alt="'.$usr['name'].'">';
                echo '</td><td style="padding: 0px 0px 0px 4px;">';
                $name = mysql_fetch_array(mysql_query("SELECT `name`,`lastdate` FROM `users` WHERE `id`='".$usr['id']."'"));

                echo (time()> $name['lastdate']+600 ? ''.functions::image('user/off.png', array('class' => 'icon-inline')).'' : ''.functions::image('user/on.png', array('class' => 'icon-inline')).'');
                echo ''.($user_id && $user_id != $usr['id'] ? '<a href="/users/profile.php?user='.$usr['id'].'"><strong>'.functions::nickcolor($usr['id']).'</strong></a>' : '<strong>'.functions::nickcolor($usr['id']).'</strong>').' <span style="color: gray; font-size: x-small">('.functions::display_date($res_like['time']).')</span>'.(!empty($usr['status']) ? '<div class="status"> '.functions::image('label.png', array('class' => 'icon-inline')) . $usr['status'].'</div>' : '').'';




        echo '</td></tr></table></div>';
    }
    if($count_like > $kmess) {
        echo '<div class="topmenu">' . functions::display_pagination($home . '/forum/?act=wholike&amp;id=' . $id . '&amp;', $start, $count_like, $kmess) . '</div>';
    }
} else {
    echo functions::display_error($lng['list_empty']);
}


    $req = mysql_query("SELECT `forum`.*, `users`.`sex`, `users`.`rights`, `users`.`lastdate`, `users`.`status`, `users`.`datereg`
    FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
    WHERE `forum`.`type` = 'm' AND `forum`.`id` = '$id'" . ($rights >= 7 ? "" : " AND `forum`.`close` != '1'") . " LIMIT 1");
    $res = mysql_fetch_array($req);
    $them = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `type` = 't' AND `id` = '" . $res['refid'] . "'"));
    $page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">=" : "<=") . " '$id'" . ($rights >= 7 ? '' : " AND `close` != '1'")), 0) / $kmess);
    echo '<div class="phdr"><a href="/forum/' . $res['refid'] . '/'.$them['seo'].'_p' . $page . '.html#post'.$id.'">' . $lng_forum['back_to_topic'] . '</a></div>';
echo '<p><a href="/forum/index.html">Tới diễn đàn</a></p>';
}

?>