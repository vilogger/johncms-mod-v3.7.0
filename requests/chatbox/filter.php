<?php
$html = '';
$st = 0;
$before = "";
if (! empty($_GET['start_row']) && $_GET['start_row'] > 0)
{
    $st = $_GET['start_row'];
}

if (!empty($_GET['before_id']) && $_GET['before_id'] > 0)
{
    $before_id = $_GET['before_id'];
    $before = "`guest`.`id` > '$before_id' AND";
}

$req = mysql_query("SELECT `guest`.*, `guest`.`id` AS `gid`, `users`.`lastdate`, `users`.`id`, `users`.`rights`, `users`.`name` FROM `guest` LEFT JOIN `users` ON `guest`.`user_id` = `users`.`id` WHERE ".$before." `guest`.`adm`='0' ORDER BY `time` DESC LIMIT  $st,$kmess");

        while ($gres = mysql_fetch_assoc($req)) {
            $post = functions::checkout($gres['text'], 1, 1);
            if ($set_user['smileys'])
            $post = functions::smileys($post, $gres['rights'] ? 1 : 0);

            $html .= '<div id="story_'.$gres['gid'].'" class="menu story_'.$gres['gid'].'" data-story-id="'.$gres['gid'].'">';
            $html .= (time() > $gres['lastdate'] + 30 ? ''.functions::image('user/off.png', array('class' => 'icon-r3')).'' : ''.functions::image('user/on.png', array('class' => 'icon-r3')).'');
            if ($user_id && $user_id != $gres['id']) {
                $html .= '<a href="/users/profile.php?user=' . $gres['id'] . '"><b>' . functions::nickcolor($gres['id']) . '</b></a>';
            } else {
                $html .= '<b>' . functions::nickcolor($gres['id']) . '</b> ';
            }
            $html .= ' <span class="gray font-xs">('.(round((time()-$gres['time'])/3600) < 2 ? '<span class="ajax-time" title="' . $gres['timestamp'] . '">':'').functions::display_date($gres['time']).''.(round((time()-$gres['time'])/3600) < 2 ? '</span>':'').')</span><br />';

            $html .= $post . '</div>';
             ++$i;
        }

$data = array(
    'status' => 200,
    'html' => $html
);

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
exit();