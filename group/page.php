<?php
/*///////////////////////
//@Tac gia: Nguyen Ary
//@Site: gochep.net
//@Facebook: facebook.com/tia.chophht
///////////////////////*/
define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');
$nhom = mysql_fetch_array(mysql_query("SELECT * FROM `nhom` WHERE `id`='".$id."'"));
$textl= $nhom['name'];
require('../incfiles/head.php');
require('func.php');

$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom` WHERE `id`='$id'"),0);
if(!isset($id) || $dem == 0) {
echo '<br/><div class="rmenu">Nhóm không tồn tại hoặc đã bị xoá!</div>';
require('../incfiles/end.php');
exit;
}
    echo head_nhom($id, $user_id);
    $kt = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='".$id."'AND `user_id`='".$user_id."' AND `duyet`='1'") ,0);
    $thanhvien = mysql_result( mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `user_id`='$user_id' AND `id`='$id'"),0);
    if(isset($_GET['thamgia'])) {
        if($thanhvien == 0) {
            mysql_query("INSERT INTO `nhom_user` SET `user_id`='$user_id', `id`='$id', `time`='$time', `rights`='0', `duyet`='0'");
            header('Location: page.php?id='.$id.'');
        }
    }
    if(isset($_GET['rutkhoi'])) {
        if($thanhvien >= 1 && $nhom['user_id'] != $user_id) {
            mysql_query("DELETE FROM `nhom_user` WHERE `user_id`='$user_id' AND `id`='$id'");
            header('Location: page.php?id='.$id.'');
        }
    }
    //Ô đăng bài
    $ktviet = mysql_result( mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `user_id`='$user_id' AND `id`='$id' AND `duyet`='1'"),0);
    if($nhom['set'] == 0 || $ktviet == 0 && $nhom['set'] == 1) {
        echo '<div class="phdr"><b>Hoạt động của nhóm</b></div>';
    }else if($nhom['set'] == 0 || $ktviet != 0 && $nhom['set'] == 1 || $ktviet != 0 && $nhom['set'] == 2) {
        echo '<div class="phdr"><b>Hoạt động của nhóm</b></div>';
    }
    if($ktviet == 0 && $nhom['set'] == 1) {
        echo '<div class="rmenu">Chỉ thành viên của nhóm mới thấy hoạt động của nhóm</div>';
    }else if($ktviet == 0 && $nhom['set'] == 2){

    }

    if($ktviet != 0) {

        $text = functions::checkin(trim($_POST['text']));
        $kttt = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `text`='{$text}' AND `sid`='{$id}' AND `type`='0'"), 0);
        if(isset($_POST['submit'])) {
            if(strlen($noidung) > 45) {
                $vbcat = mb_substr($text, 0, 45).'....';
            } else {
                $vbcat = $text;
            }
            if(!empty($ban)) {
                echo '<div class="rmenu">Tài khoản của bạn đang bị khoá nên không thể sử dụng chức năng này!</div>';
            }else if(empty($text)) {
                echo '<div class="rmenu">Chưa nhập nội dung!</div>';
            } else if(($datauser['lastpost'] + 5) > time()) {
                echo '<div class="rmenu">Đợi <b>'.(($datauser['lastpost']+5) - time()).'s</b> nữa để đăng tiếp!</div>';
            } else if(strlen($text) > 5000) {
                echo '<div class="rmenu">Nội dung quá dài. Chỉ tối đa 5000 kí tự!</div>';
            } else {
                mysql_query("INSERT INTO `nhom_bd` SET `sid`='".$id."', `user_id`='".$user_id."', `time`='".$time."', `stime`='".$time."',  `text`='".mysql_real_escape_string($text)."', `type`='0'");
                $rid = mysql_insert_id();
                mysql_query("UPDATE `users` SET `postgroup`=`postgroup`+'1' WHERE `id` = '$user_id' ");
                $exists = array();
                if(preg_match('#@([a-zA-Z0-9\-\@\*\(\)\?\!\~\_\=\[\]]+)#si', $text)) {
                    preg_match_all('#@([a-zA-Z0-9\-\@\*\(\)\?\!\~\_\=\[\]]+)#si', $text, $tex);
                    $dem = count($tex[1]);
                    for($i=0; $i<=$dem; $i++) {
                        $ktu = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `name`='{$tex[1][$i]}'"), 0);

                        $resq = mysql_fetch_array(mysql_query("SELECT `id` FROM `users` WHERE `name`='{$tex[1][$i]}'"));
                        if($ktu == 1 && $resq['id'] != $user_id && isset($exists[intval($resq['id'])]) == false) {
                            $exists[intval($resq['id'])] = true;
                            mysql_query("UPDATE `users` SET `bl`= `bl` + '1' WHERE `id`='".$resq['id']."'");

                            mysql_query("INSERT INTO `cms_mail` SET `user_id` = '$user_id', `from_id`='".$resq['id']."', `them`='1', `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã nhắc đến bạn trong bài viết của nhóm [url=".$home."/group/cmt.php?id=".$rid."]".$textl."[/url]: ".addslashes($vbcat)."', `sys`='1', `time`='".time()."'");
                        }
                    }
                }
                $rpr = mysql_query("SELECT DISTINCT `user_id` FROM `nhom_user` WHERE `id`='$id' AND `user_id`!='$user_id' AND `duyet`!='0'");
                while($rspr = mysql_fetch_array($rpr)){
                    mysql_query("UPDATE `nhom_user` SET `view`=`view`+'1' WHERE `id`='$id' AND `user_id`='$rsqr[user_id]' ");
                }
            $reqp = mysql_query("SELECT DISTINCT `user_id` FROM `nhom_user` WHERE `id`='$id' AND `user_id` != '$user_id'");
            while ($resp = mysql_fetch_array($reqp)) {
                    mysql_query("INSERT INTO `cms_mail` SET `user_id` = '$user_id', `from_id`='".$resp['user_id']."', `them`='1', `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã đăng một bài viết vào nhóm [url=".$home."/group/cmt.php?id=".$rid."]".$textl."[/url]: ".addslashes($vbcat)."', `sys`='1', `time`='".time()."'");
             }
            mysql_query("UPDATE `users` SET `lastpost`='".time()."' WHERE `id`='".$user_id."'");
            $trave = isset($_POST['trave']) ? base64_decode($_POST['trave']) : 'page.php?id='.$id.'';
            header("Location: $trave");
            exit;
            }
        }
        $trave = base64_encode($_SERVER['REQUEST_URI']);
        echo '<div class="list1"><div class="gmenu">Hãy viết gì đó.!</div><form method="post"><textarea name="text" cows="3"></textarea><input type="hidden" name="trave" value="'.$trave.'" /><br/ ><input type="submit" name="submit" value="Đăng" /><div style="float:right; padding-top:4px;"><a href="img.php?id='.$id.'"><b>Chia sẻ ảnh</b></a></div></form></div>';
    }
    //Bài đăng khác
    if($nhom['set'] == 0 || $ktviet != 0 && $nhom['set'] == 1 || $ktviet != 0 &&$nhom['set'] == 2) {
        $dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `sid`='$id' AND `type`!='1'"),0);
        if($dem) {

            $req = mysql_query("SELECT * FROM `nhom_bd` WHERE `sid`='$id' AND `type`!='1' ORDER BY `stime` DESC LIMIT $start,$kmess");
            while($res=mysql_fetch_array($req)) {
                $var = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id`='".$res['user_id']."'"));
                $vad = mysql_fetch_array(mysql_query("SELECT * FROM `nhom_user` WHERE `id`='".$res['id']."'AND `user_id`='".$res['user_id']."'"));





                echo '<div class="list2" style=" border: 1px solid #d2d2d2; margin: -1px 1px 4px 1px; box-shadow: 0px 1px 1px #ccc; -moz-box-shadow: 0px 1px 1px #ccc; -webkit-box-shadow: 0px 1px 1px #ccc; background-color: #f9f9f9;"><table cellpadding="0" cellspacing="0"><tr><td>';
                if (file_exists(('../files/users/avatar/' . $res['user_id'] . '.png'))) {
                    echo '<div style="WIDTH: 40px; BACKGROUND: url(/files/users/avatar/'.$res['user_id'].'.png) no-repeat; HEIGHT: 40px; background-size: 40px 40px; -webkit-border-radius: 50%; border-radius: 50%; -moz-border-radius: 50%;"></div>';
                } else {
                    echo '<div style="WIDTH: 40px; BACKGROUND: url(/images/empty.png) no-repeat; HEIGHT: 40px; background-size: 40px 40px; -webkit-border-radius: 50%; border-radius: 50%; -moz-border-radius: 50%;"></div>';
                }
                echo '</td><td style="padding: 0px 0px 0px 4px;">';
                echo (time() > $var['lastdate']+300 ? '<span style="color:red;">&#8226;</span>' : '<span style="color:green;">&#8226;</span>').' <a href="/users/profile.php?user='.$res['user_id'].'"><b>'.$var['name'].'</b></a><br />'.ngaygio($res['time']).'';

                echo '</td></tr></table>';
                if($res['type'] == 2) {

                    $GetImageSize = GetImageSize('files/anh_'.$res['time'].'.jpg');
                    $imgx = $GetImageSize[0];
                    $imgy = $GetImageSize[1];
                    if($imgx <= $imgy && $imgx >= 150){
                        echo '<div align="center"><a href="cmt.php?id='.$res['id'].'"><img src="files/anh_'.$res['time'].'.jpg" width="150" height="auto" alt="image" /></a></div>';
                    }else if($imgx >= $imgy && $imgy >= 210) {
                        echo '<div align="center"><a href="cmt.php?id='.$res['id'].'"><img src="files/anh_'.$res['time'].'.jpg" width="210" height="auto" alt="image" /></a></div>';
                    }else{
                        echo '<div align="center"><a href="cmt.php?id='.$res['id'].'"><img src="files/anh_'.$res['time'].'.jpg" alt="image" style="max-width: 160px; height: auto;" /></a></div>';
                    }

                }
                echo thugon($res['text'], $res['id']);
                echo '<br />';
                //Phan menu bai dang
                $like = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_like` WHERE `id`='".$res['id']."' AND `type`!='1'"),0);
                $klike = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_like` WHERE `id`='".$res['id']."' AND `user_id`='".$user_id."' AND `type`!='1'"),0);
                $bl = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `cid`='".$res['id']."' AND `type`='1'"),0);
                $xoa = mysql_fetch_array(mysql_query("SELECT `rights` FROM `nhom_user` WHERE `id`='".$id."' AND `user_id`='".$res['user_id']."'"));
                $xoa2 = mysql_fetch_array(mysql_query("SELECT * FROM `nhom_user` WHERE `id`='".$id."' AND `user_id`='".$user_id."'"));

                echo '<div class="sub">'.($like > 0 ? '<a href="more.php?act=like&id='.$res['id'].'"><img src="img/l.png" alt="l" /> '.$like.'</a> · ':'').''.($kt >= 1 ? ''.($klike == 0 ? '<a href="action.php?act=like&id='.$res['id'].'">Thích</a>':'<a href="action.php?act=dislike&id='.$res['id'].'">Bỏ thích</a>').' · ' : '').'<a href="cmt.php?id='.$res['id'].'">Bình luận ('.$bl.')</a>'.($xoa2['rights'] > $xoa['rights'] || $res['user_id'] == $user_id || $rights == 9 ? ' · <a href="action.php?act=del&id='.$res['id'].'">Xóa</a>':'').'</div>';

                echo '</div>';
            }

        } else {
            echo '<div class="rmenu">Chưa có bài đăng!</div>';
        }

        if ($dem > $kmess){
            echo '<div class="topmenu">' . functions::display_pagination('page.php?id='.$id.'&', $start, $dem, $kmess) . '</div>';
        }
    }
    if($ktviet == 0 && $nhom['set'] == 2){}else{
        //Trinh don nhom
        $tv =mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='".$id."' AND `duyet`='1'") ,0);
        $anh =mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `sid`='$id' AND `type`='2'"),0);

        echo '<div class="phdr"><b>Menu nhóm</b></div><div class="list1"><a href="more.php?act=mem&id='.$id.'">Thành viên ('.$tv.')</a></div>'.($nhom['set'] == 0 || $ktviet != 0 ? '<div class="list1"><a href="album.php?id='.$id.'">Album ảnh ('.$anh.')</a></div>' : '').'<div class="list1"><a href="thongtin.php?id='.$id.'">Thông tin</a></div>'.($nhom['user_id'] == $user_id ? '<div class="list1"><a href="edit.php?id='.$id.'">Chỉnh sửa nhóm</a></div>':'').'';
    }
    $kt = mysql_fetch_array(mysql_query("SELECT * FROM `nhom_user` WHERE `user_id`='".$user_id."' AND `id`='".$id."'"));
    echo ''.($nhom['user_id'] == $user_id || $rights==9 ? '<div class="list1"><a href="action.php?act=xoanhom&id='.$id.'" style="color:red;">Xóa bỏ nhóm</a></div>':'').''.($kt['duyet'] == 1 && $kt['rights'] != 2 ? '<form method="post" action="action.php?act=rutkhoi&id='.$id.'"><input type="submit" name="sub" value="Rút khỏi nhóm" /></form>':'').'';

require('../incfiles/end.php');
?>