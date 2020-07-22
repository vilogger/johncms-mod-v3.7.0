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
if (empty($_GET['id'])) {
    echo functions::display_error($lng['error_wrong_data']);
    require('../incfiles/end.php');
    exit;
}



// Запрос сообщения
$req = mysql_query("SELECT `forum`.*, `users`.`sex`, `users`.`facebook_ID`, `users`.`google_ID`, `users`.`rights`, `users`.`lastdate`, `users`.`status`, `users`.`datereg`
FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
WHERE `forum`.`type` = 'm' AND `forum`.`id` = '$id'" . ($rights >= 7 ? "" : " AND `forum`.`close` != '1'") . " LIMIT 1");
$res = mysql_fetch_array($req);

// Запрос темы
$them = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `type` = 't' AND `id` = '" . $res['refid'] . "'"));
echo '<div class="phdr"><b>' . $lng_forum['topic'] . ':</b> ' . $them['text'] . '</div><div class="menu" '.($res['facebook_ID'] ? 'style="background: #fff url(/images/facebook.png) no-repeat right top;" ' : ($res['google_ID'] ? 'style="background: #fff url(/images/googlep.png) no-repeat right top;" ' : '')).'>';

// Данные пользователя
if ($set_user['avatar']) {
    echo '<table style="padding: 0;border-spacing: 0;"><tr><td>';
        echo '<img src="' . $home . '/avatar/' . $res['user_id'] . '-24-48.png" width="48" height="48" alt="' . $res['from'] . '" />&#160;';
    echo '</td><td>';
}
if ($res['sex']){
    if(time() > $res['lastdate'] + 30){
        echo functions::image(($res['sex'] == 'm' ? 'user/man_of' : 'user/j_of') . '.png', array('class' => 'icon-inline'));
    }else{
        echo functions::image(($res['sex'] == 'm' ? 'm' : 'w') . ($res['datereg'] > time() - 86400 ? '_new' : '') . '.png', array('class' => 'icon-inline'));
    }
}else{
    echo functions::image('del.png', array('class' => 'icon-inline'));
}
// Ник юзера и ссылка на его анкету
if ($user_id && $user_id != $res['user_id']) {
    echo '<a href="/users/profile.php?user=' . $res['user_id'] . '"><b>' . functions::nickcolor($res['user_id']) . '</b></a> ';
} else {
    echo'<b>' . functions::nickcolor($res['user_id']) . '</b> ';
}
// Метка должности
$user_rights = array(
    3 => '(FMod)',
    6 => '(Smd)',
    7 => '(Adm)',
    9 => '(SV!)'
);

// Ссылки на ответ и цитирование

// Время поста

// Статус юзера
if (!empty($res['status']))
    echo '<div class="status">' . functions::image('label.png', array('class' => 'icon-inline')) . $res['status'] . '</div>';
if ($set_user['avatar'])
    echo '</td></tr></table>';
echo '</div><div class="list1">';
                    echo '<div class="info-112"> <table class="f-table"><tr><td style="text-align: left;"><span class="info-c"> <i class="fa fa-clock-o" style="font-size: 14px;"></i> '.functions::display_date($res['time']).'</span></td><td class="right"><a href="/forum/post-' . $res['id'] . '.html" title="Link to post"><span class="info-c">[#]</span></a></td></tr></table></div>';
// Вывод текста поста
$text = htmlentities($res['text'], ENT_QUOTES, 'UTF-8');
$text = nl2br($text);
$text = bbcode::tags($text);
if ($set_user['smileys'])
    $text = functions::smileys($text, ($res['rights'] >= 1) ? 1 : 0);
echo $text . '';

// Если есть прикрепленный файл, выводим его описание
$freq = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = '" . $res['id'] . "'");
if (mysql_num_rows($freq) > 0) {
    echo '<div class="info-file1">' . $lng_forum['attached_file'] . ':</div><div class="info-file2">';
    while ($fres = mysql_fetch_assoc($freq)) {
    $fls = round(@filesize('../files/forum/attach/' . $fres['filename']) / 1024, 2);
    echo '<div class="gray info-file3">';
    // Предпросмотр изображений
    $att_ext = strtolower(functions::format('./files/forum/attach/' . $fres['filename']));
    $pic_ext = array(
        'gif',
        'jpg',
        'jpeg',
        'png'
    );
    if (in_array($att_ext, $pic_ext)) {
        echo '<table><tr><td><a href="index.php?act=file&amp;id=' . $fres['id'] . '">';
        echo '<img src="thumbinal.php?file=' . (urlencode($fres['filename'])) . '" alt="' . $lng_forum['click_to_view'] . '" /></a></td><td class="font-xs"> (' . $fls . ' kb.)<br/>'.$lng_forum['downloads'] . ': ' . $fres['dlcount'] . ' lượt.</td></tr></table>';
    } else {
        echo '<a href="index.php?act=file&amp;id=' . $fres['id'] . '">' . $fres['filename'] . '</a> (' . $fls . ' kb.)<br/>'.$lng_forum['downloads'] . ': ' . $fres['dlcount'] . ' lượt.';
    }
    echo '</div>';
    $file_id = $fres['id'];
    $i;
    }
echo '</div>';
}

                    // mrt
                    $omsg_id = $res['id'];
                    if($user_id){
                        // Reaction status check for "Like"
                        $like=functions::Like_Check($res['id'],$user_id, "Like");
                        // If post is not reactioned then show the $like_statusicon = 'icon-like-blf';
                        // $like_statusicon working with all reaction status
                        $like_statusicon = 'icon-like-blf';
                        // $lostyle will working reaction status 
                        // For example if not reactioned post then the style will be display:none;
                        $lostyle='display:none;';
                        if($like) {
                            //If post liked then show UnLike from the div rel and title
                            $like_status='UnLike'; 
                            // If post liked then show new UnLike icon from the reactions box
                            $like_statusicon='icon-like-new';
                        } else {
                            // If post not liked then show UnLike from the div rel and title
                            $like_status='Like'; 
                        }
                        // Reaction status check for "Love"
                        $love=functions::Like_Check($res['id'],$user_id, "Love");
                        if($love){
                            // If post reaction status is UnLove then show UnLove from the div rel and title
                            $love_status='UnLove';
                            // If post reaction status is UnLove then show Love icon from the reactions box
                            $like_statusicon='icon-love-new'; 
                            // If post reaction status is UnLove then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnLove then show Love from the div rel and title
                            $love_status='Love';
                         }
                        // Reaction status check for "Haha"
                        $haha=functions::Like_Check($res['id'],$user_id, "Haha");
                        if($haha){
                            // If post reaction status is UnHaha then show UnHaha from the div rel and title
                            $haha_status='UnHaha';
                            // If post reaction status is UnHaha then show Haha icon from the reactions box
                            $like_statusicon='icon-haha-new'; 
                            // If post reaction status is UnHaha then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnHaha then show Haha from the div rel and title
                            $haha_status='Haha';
                        }
                        // Reaction status check for "Hihi"
                        $hihi=functions::Like_Check($res['id'],$user_id, "Hihi");
                        if($hihi){
                            // If post reaction status is UnHihi then show UnHihi from the div rel and title
                            $hihi_status='UnHihi';
                            // If post reaction status is UnHihi then show Hihi icon from the reactions box
                            $like_statusicon='icon-mmmm-new'; 
                            // If post reaction status is UnHihi then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            //If post reaction status is not UnHihi then show Hihi from the div rel and title
                            $hihi_status='Hihi';
                        }
                        // Reaction status check for "Woww"
                        $woww=functions::Like_Check($res['id'],$user_id, "Woww");
                        if($woww){
                            // If post reaction status is UnWoww then show UnWoww from the div rel and title
                            $woww_status='UnWoww';
                            // If post reaction status is UnWoww then show Woww icon from the reactions box
                            $like_statusicon='icon-wowww-new'; 
                            // If post reaction status is UnWoww then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnWoww then show Woww from the div rel and title
                            $woww_status='Woww';
                        }
                        // Reaction status check for "Cry"
                        $Cry=functions::Like_Check($res['id'],$user_id, "Cry");
                        if($Cry){
                            // If post reaction status is UnCry then show UnCry from the div rel and title
                            $cry_status='UnCry';
                            // If post reaction status is UnCry then show Cry icon from the reactions box
                            $like_statusicon='icon-crying-new'; 
                            // If post reaction status is UnCry then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnCry then show Cry from the div rel and title
                            $cry_status='Cry'; 
                        }
                        // Reaction status check for "Angry"
                        $angry=functions::Like_Check($res['id'],$user_id, "Angry");
                        if($angry){
                            // If post reaction status is UnAngry then show UnAngry from the div rel and title
                            $angry_status='UnAngry';
                            // If post reaction status is UNAngry then show Angry icon from the reactions box
                            $like_statusicon='icon-angry-new'; 
                            // If post reaction status is UnAngry then reaction div's style to be display:block;
                            $lostyle='display:block;';
                        } else {
                            // If post reaction status is not UnAngry then show Angry from the div rel and title
                            $angry_status='Angry';
                        }

                        echo '<div class="post-like-unlike-comment">
                            <div class="like-post openCommentArea" id="'.$omsg_id.'" title="Comment" rel="'.$res['id'].'" data-id="'.$res['id'].'">
                                <div class="icon-like-comment icon-talk-chat-bubble"></div>
                            </div>
                            <div class="like-it">
                                <div class="new_like" tabindex="0" id="'.$res['id'].'">
                                    <div class="like-pit first_click">
                                        <div class="icon-lpn '.$like_statusicon.'" id="ulk'.$res['id'].'"></div>
                                        <div class="new_like_items first_click_wrap_content">
                                            <div class="op-lw like_button" data-id="0" id="like'.$omsg_id.'" rel="'.$like_status.'" title="'.$like_status.'"><div class="icon-newL icon-like-new"></div></div>
                                            <div class="op-lw like_button" data-id="1" id="love'.$omsg_id.'" rel="'.$love_status.'" title="'.$love_status.'"><div class="icon-newL icon-love-new"></div></div>
                                            <div class="op-lw like_button" data-id="2" id="haha'.$omsg_id.'" rel="'.$haha_status.'" title="'.$haha_status.'"><div class="icon-newL icon-haha-new"></div></div>
                                            <div class="op-lw like_button" data-id="3" id="hihi'.$omsg_id.'" rel="'.$hihi_status.'" title="'.$hihi_status.'"><div class="icon-newL icon-mmmm-new"></div></div>
                                            <div class="op-lw like_button" data-id="4" id="woww'.$omsg_id.'" rel="'.$woww_status.'" title="'.$woww_status.'"><div class="icon-newL icon-wowww-new"></div></div>
                                            <div class="op-lw like_button" data-id="5" id="cry'.$omsg_id.'" rel="'.$cry_status.'" title="'.$cry_status.'"><div class="icon-newL icon-crying-new"></div></div>
                                            <div class="op-lw like_button" data-id="6" id="angry'.$omsg_id.'" rel="'.$angry_status.'" title="'.$angry_status.'"><div class="icon-newL icon-angry-new"></div></div>
                                        </div>
                                    </div>
                                </div>
                            </div>';

                        if ($user_id && $user_id != $res['user_id']){
                            if (($user_id && !$type1['edit'] && !$set_forum['upfp'] && $set['mod_forum'] != 3 && $allow != 4) || ($rights >= 7 && !$set_forum['upfp'])){
                                echo '<div class="like-it">';
                                echo '<a href="/forum/index.php?act=say&amp;id=' . $res['id'] . '&amp;start=' . $start . '&amp;cyt"><div class="icon-newL icon-quotes"></div></a>';
                                echo '</div>';
                            }
                        }
                        echo '</div>';

                    }

                    $sep = '';
                    $lstyle = '';
                    if(functions::Like_CountTotal($res['id'], $user_id, in_array($sep, array('Like','Love','Haha','Hihi','Woww','Cry','Angry')))>0){
                        $lstyle="display:block;";
                    } else {
                        //$lstyle="display:none;";
                    }
                    echo '<div class="who-likes-this-post likes reaction_wrap-style" id="likess'.$omsg_id.'" style="'.$lstyle.'">';
                    //Like Started
                    if(functions::Like_CountT($res['id'], $user_id, 'Like')>0) {
                        echo '<div class="likes reaction_wrap-style bbc" id="elikes'.$omsg_id.'" style="'.$lstyle.'"><span id="like_count'.$omsg_id.'" class="numcount bbc"><div class="icon-newL icon-like-new lpos" id="clk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'uPages\', \'Like\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\')"></div><div class="lvspan no_display" id="public_Like_user_block'.$res['id'].'" onMouseOver="wall_like_users_five(\''.$res['id'].'\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\')"><div id="Like_users'.$omsg_id.'"></div></div><div class="lcl" id="lcl'.$omsg_id.'">'.functions::Like_CountT($res['id'], $user_id, 'Like').'</div></span></div>'; 
                    } else {
                        echo '<div class="likes reaction_wrap-style bbc" id="elikes'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Love Started
                    if(functions::Like_CountT($res['id'], $user_id, 'Love')){
                        echo '<div class="loves reaction_wrap-style bbc" id="eloves'.$omsg_id.'" style="'.$lstyle.'"><span id="love_count'.$omsg_id.'" class="numcount bbc"><div class="icon-newL icon-love-new lpos" id="llk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'uPages\', \'Love\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\')"></div><div class="lvspan no_display" id="public_Love_user_block'.$res['id'].'" onMouseOver="wall_like_users_five(\''.$res['id'].'\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\')"><div id="Love_users'.$omsg_id.'"></div></div><div class="lco" id="lco'.$omsg_id.'">'.functions::Like_CountT($res['id'], $user_id, 'Love').'</div></span></div>'; 
                    } else {
                        echo '<div class="loves reaction_wrap-style bbc" id="eloves'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Haha Started
                    if(functions::Like_CountT($res['id'], $user_id, 'Haha')){
                        echo '<div class="hahas reaction_wrap-style bbc" id="ehaha'.$omsg_id.'" style="'.$lstyle.'"><span id="haha_count'.$omsg_id.'" class="numcount bbc " id="haha'.$omsg_id.'"><div class="icon-newL icon-haha-new lpos" id="hlk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'uPages\', \'Haha\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\')"></div><div class="lvspan no_display" id="public_Haha_user_block'.$res['id'].'" onMouseOver="wall_like_users_five(\''.$res['id'].'\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\')"><div id="Haha_users'.$omsg_id.'"></div></div><div class="hco" id="hco'.$omsg_id.'">'.functions::Like_CountT($res['id'], $user_id, 'Haha').'</div></span></div>'; 
                    } else {
                        echo '<div class="hahas reaction_wrap-style bbc" id="ehaha'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Hihi Started
                    if(functions::Like_CountT($res['id'], $user_id, 'Hihi')){
                        echo '<div class="hihis reaction_wrap-style bbc" id="ehihi'.$omsg_id.'" style="'.$lstyle.'"><span id="hihi_count'.$omsg_id.'" class="numcount bbc " id="hihi'.$omsg_id.'"><div class="icon-newL icon-mmmm-new lpos" id="hilk'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'uPages\', \'Hihi\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\')"></div><div class="lvspan no_display" id="public_Hihi_user_block'.$res['id'].'" onMouseOver="wall_like_users_five(\''.$res['id'].'\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\')"><div id="Hihi_users'.$omsg_id.'"></div></div><div class="hico" id="hico'.$omsg_id.'">'.functions::Like_CountT($res['id'], $user_id, 'Hihi').'</div></span></div>'; 
                    } else {
                        echo '<div class="hihis reaction_wrap-style bbc" id="ehihi'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Woww Started
                    if(functions::Like_CountT($res['id'], $user_id, 'Woww')){
                        echo '<div class="wowws reaction_wrap-style bbc" id="ewoww'.$omsg_id.'" style="'.$lstyle.'"><span id="woww_count'.$omsg_id.'" class="numcount bbc " id="woww'.$omsg_id.'"><div class="icon-newL icon-wowww-new lpos" id="woow'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'uPages\', \'Woww\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\')"></div><div class="lvspan no_display" id="public_Woww_user_block'.$res['id'].'" onMouseOver="wall_like_users_five(\''.$res['id'].'\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\')"><div id="Woww_users'.$omsg_id.'"></div></div><div class="wco" id="wco'.$omsg_id.'">'.functions::Like_CountT($res['id'], $user_id, 'Woww').'</div></span></div>'; 
                    } else {
                        echo '<div class="wowws reaction_wrap-style bbc" id="ewoww'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Cry Started
                    if(functions::Like_CountT($res['id'], $user_id, 'Cry')){
                        echo '<div class="crys reaction_wrap-style bbc" id="ecry'.$omsg_id.'" style="'.$lstyle.'"><span id="cry_count'.$omsg_id.'" class="numcount bbc " id="cry'.$omsg_id.'"><div class="icon-newL icon-crying-new lpos" id="cry'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'uPages\', \'Cry\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\')"></div><div class="lvspan no_display" id="public_Cry_user_block'.$res['id'].'" onMouseOver="wall_like_users_five(\''.$res['id'].'\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\')"><div id="Cry_users'.$omsg_id.'"></div></div><div class="cco" id="cco'.$omsg_id.'">'.functions::Like_CountT($res['id'], $user_id, 'Cry').'</div></span></div>';
                    } else {
                        echo '<div class="crys reaction_wrap-style bbc" id="ecry'.$omsg_id.'" style="display:none"></div>';
                    }
                    //Angry Started
                    if(functions::Like_CountT($res['id'], $user_id, 'Angry')){
                        echo '<div class="angrys reaction_wrap-style bbc" id="eangrys'.$omsg_id.'" style="'.$lstyle.'"><span id="angry_count'.$omsg_id.'" class="numcount bbc " id="angrys'.$omsg_id.'"><div class="icon-newL icon-angry-new lpos" id="angry'.$omsg_id.'" onMouseOver="wall_like_users_five(\''.$res['id'].'\', \'uPages\', \'Angry\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\')"></div><div class="lvspan no_display" id="public_Angry_user_block'.$res['id'].'" onMouseOver="wall_like_users_five(\''.$res['id'].'\')" onMouseOut="wall_like_users_five_hide(\''.$res['id'].'\')"><div id="Angry_users'.$omsg_id.'"></div></div><div class="eco" id="eco'.$omsg_id.'">'.functions::Like_CountT($res['id'], $user_id, 'Angry').'</div></span></div>'; 
                    } else {
                        echo '<div class="angrys reaction_wrap-style bbc" id="eangrys'.$omsg_id.'" style="display:none"></div>';
                    }
                    echo '</div>';

                    // mrt

echo '<div class="gray font-xs" style="text-align: right; margin: 0px -3px -4px 0px">'.$res['soft'].'</div>';
echo '</div>';

// Вычисляем, на какой странице сообщение?
$page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">=" : "<=") . " '$id'"), 0) / $kmess);
echo '<div class="phdr"><a href="/forum/' . $res['refid'] . '/' . $them['seo'] . '_p' . $page . '.html#post' . $id . '">' . $lng_forum['back_to_topic'] . '</a></div>';
echo '<p><a href="/forum/index.html">' . $lng['to_forum'] . '</a></p>';