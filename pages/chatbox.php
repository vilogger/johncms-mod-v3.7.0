<?php
if($user_id){
    echo '<script type="text/javascript" src="/js/jquery.form.min.js"></script><script type="text/javascript" src="/js/chatbox.js"></script>';

}
    echo '<div class="phdr"><a href="/chat">Phòng chat</a></div>';

if($user_id){
    echo '<div class="gmenu" style="overflow: auto;"><div class="story-publisher-box">' .
        '<form name="form" method="post">';
    echo bbcode::auto_bb('form', 'text');
    echo '<textarea rows="' . $set_user['field_h'] . '" name="text"></textarea></p>';
    echo '<button class="submit-btn active" name="story_submit_btn"><i class="fa fa-edit progress-icon"></i><span> Chat</span></button>' .
        '</form></div></div>';
}else{
    echo '<div class="gmenu">Đăng nhập để chat cùng thành viên <span style="color: red"><strong>Phiêu Bạc</strong></span>.!!</div>';
}
echo '<div class="chatbox-container"><div class="chatbox-wrapper">';

$totalchat = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='0'"), 0);

$req = mysql_query("SELECT `guest`.*, `guest`.`id` AS `gid`, `users`.`lastdate`, `users`.`id`, `users`.`rights`, `users`.`name` FROM `guest` LEFT JOIN `users` ON `guest`.`user_id` = `users`.`id` WHERE `guest`.`adm`='0' ORDER BY `time` DESC LIMIT 10");

        while ($gres = mysql_fetch_assoc($req)) {
            $post = functions::checkout($gres['text'], 1, 1);
            if ($set_user['smileys'])
            $post = functions::smileys($post, $gres['rights'] ? 1 : 0);

            echo '<div id="story_'.$gres['gid'].'" class="menu story_'.$gres['gid'].'" data-story-id="'.$gres['gid'].'">';
            echo (time() > $gres['lastdate'] + 30 ? ''.functions::image('user/off.png', array('class' => 'icon-r3')).'' : ''.functions::image('user/on.png', array('class' => 'icon-r3')).'');
            if ($user_id && $user_id != $gres['id']) {
                echo '<a href="/users/profile.php?user=' . $gres['id'] . '"><b>' . functions::nickcolor($gres['id']) . '</b></a>';
            } else {
                echo '<b>' . functions::nickcolor($gres['id']) . '</b> ';
            }
            echo ' <span class="gray font-xs">('.(round((time()-$gres['time'])/3600) < 2 ? '<span class="ajax-time" title="' . $gres['timestamp'] . '">':'').functions::display_date($gres['time']).''.(round((time()-$gres['time'])/3600) < 2 ? '</span>':'').')</span><br />';

            echo $post . '</div>';

          ++$i;
        }

    echo '</div>';
    if($totalchat > 10){
        echo '<div align="center">
        <div class="load-btn" onclick="SK_loadOldStories();">
            <i class="fa fa-reorder progress-icon"></i> Xem thêm...</div>
<a href="/chat" class="load-btn">Phòng chat >></a>
        </div>';

      }
    echo '</div>';
