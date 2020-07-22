<?php
/*///////////////////////
//@Tac gia: Nguyen Ary
//@Site: gochep.net
//@Facebook: facebook.com/tia.chophht
///////////////////////*/
define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');
$textl= 'Hành động';
require('../incfiles/head.php');
require('func.php');
$id= intval(abs($_GET['id']));
switch($act) {
default:
header('Location: index.php');
break;
case 'rutkhoi':
echo '<div class="phdr">Rút khỏi nhóm.</div>';
echo '<div class="list1">Bạn có thật sự muốn rút khỏi nhóm này?<br /><a href="page.php?rutkhoi&id='.$id.'">Rút khỏi nhóm</a> | <a href="page.php?id='.$id.'">Hủy</a></div>';
break;
case 'share':
echo '<div class="phdr"><b>Chia sẻ bài đăng.</b></div>';
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `id`='".$id."'"),0);
if($dem == 0) {
echo '<div class="rmenu">Bài đăng không tồn tại hoặc đã bị xoá!</div>';
require('../incfiles/end.php');
exit;
}
$kt = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='".$req['sid']."'AND `user_id`='".$user_id."' AND `duyet`='1'") ,0);
if($kt == 0) {
echo '<div class="rmenu">Phải là thành viên của nhóm!</div>';
require('../incfiles/end.php');
exit;
}
if(isset($_POST['submit'])) {
$noidung = functions::checkin(trim($_POST['text']));
mysql_query("INSERT INTO `status` SET `gshare`='".$id."', `user_id`='".$user_id."', `text`='" . mysql_real_escape_string($noidung) . "', `time`='".time()."'");
mysql_query("UPDATE `users` SET `poststatus`=`poststatus`+'1' WHERE `id` = '$user_id' ");
header("Location: /status/index.php");
} else {
echo '<div class="list1">';
echo 'Nội dung muốn chia sẻ (có thể bỏ trống) : <br /><form action="hanhdong.php?act=share&id='.$id.'" method="post"><textarea rows="3" name="text"></textarea><br />';
echo ''.($kt['img'] == 1 ? '<a href="/status/index.php?id='.$id.'">'.functions::cattu($kt['text'], 0, 30).'....<br /><center><img src="../files/status/'.$id.'.png" alt="'.$id.'"  /></center></a><br />' : '<a href="index.php?id='.$id.'">'.functions::cattu($kt['text'], 0, 30).'....</a><br />').'';
echo '<input type="submit" name="submit" value="Chia sẻ" /></form>';
echo '</div>';
}
break;
case 'like':
$req = mysql_fetch_array(mysql_query("SELECT `sid`, `type` FROM `nhom_bd` WHERE `id`='$id'"));
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `id`='".$id."'"),0);
$tnhom = mysql_fetch_array(mysql_query("SELECT * FROM `nhom` WHERE `id`='".$req['sid']."'"));
if($dem == 0) {
echo '<br/><div class="rmenu">Bài đăng không tồn tại hoặc đã bị xoá!</div>';
require('../incfiles/end.php');
exit;
}
$kt = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='".$req['sid']."'AND `user_id`='".$user_id."' AND `duyet`='1'") ,0);
if($kt == 0) {
echo '<div class="rmenu">Phải là thành viên của nhóm!</div>';
require('../incfiles/end.php');
exit;
}
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_like` WHERE `id`='$id' AND `user_id`='$user_id'"),0);
if($dem == 1) {
echo '<br/><div class="rmenu">Bạn đã thích bài viết rồi!</div>';
require('../incfiles/end.php');
exit;
}
$tkl = mysql_fetch_array(mysql_query("SELECT * FROM `nhom_bd` WHERE `id`='{$id}'"));
if($tkl['user_id'] != $user_id) {
                        mysql_query("INSERT INTO `cms_mail` SET
                            `user_id` = '$user_id', 
                            `from_id` = '".$tkl['user_id']."', 

                            `them`='7',
                            `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã thích bài viết của bạn trong nhóm: [url=".$home."/group/cmt.php?id=".$id."]".$tnhom['name']."[/url]',
                            `sys`='1',
                            `time` = '" . time() . "'
                        ");
mysql_query("UPDATE `users` SET `thank_duoc` = `thank_duoc` + '1' WHERE `id` = '{$tkl['user_id']}'");
mysql_query("UPDATE `users` SET `thank_di` = `thank_di` + '1' WHERE `id` = '{$user_id}'");
}


mysql_query("INSERT INTO `nhom_like` SET `id`='".$id."', `user_id`='".$user_id."', `type`='".$req['type']."', `time`='".$time."'");
mysql_query("UPDATE `nhom_bd` SET `stime`='$time' WHERE `id`='$id'");
$ref = $_SERVER['HTTP_REFERER'];
if(isset($ref)) {
header("Location: $ref"); } else {
$ur = 'page.php?id='.$req['sid'].'';
header("Location: $ur");
}
break;
case 'dislike':
$req = mysql_fetch_array(mysql_query("SELECT `sid`, `type` FROM `nhom_bd` WHERE `id`='$id'"));
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `id`='".$id."'"),0);
if($dem == 0) {
echo '<br/><div class="rmenu">Bài đăng không tồn tại hoặc đã bị xoá!</div>';
require('../incfiles/end.php');
exit;
}
$kt = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='".$req['sid']."'AND `user_id`='".$user_id."' AND `duyet`='1'") ,0);
if($kt == 0) {
echo '<div class="rmenu">Phải là thành viên của nhóm!</div>';
require('../incfiles/end.php');
exit;
}
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_like` WHERE `id`='$id' AND `user_id`='$user_id'"),0);
if($dem == 0) {
echo '<br/><div class="rmenu">Bạn chưa thích bài viết nên không thể bỏ thích!</div>';
require('../incfiles/end.php');
exit;
}
$tkl = mysql_fetch_array(mysql_query("SELECT `user_id` FROM `nhom_bd` WHERE `id`='{$id}'"));
if($tkl['user_id'] != $user_id) {
mysql_query("UPDATE `users` SET `thank_duoc` = `thank_duoc` - '1' WHERE `id` = '{$tkl['user_id']}'");
mysql_query("UPDATE `users` SET `thank_di` = `thank_di` - '1' WHERE `id` = '{$user_id}'");
}


mysql_query("DELETE FROM `nhom_like` WHERE `id`='".$id."' AND `user_id`='".$user_id."'");
$ref = $_SERVER['HTTP_REFERER'];
if(isset($ref)) {
header("Location: $ref"); } else {
$ur = 'page.php?id='.$req['sid'].'';
header("Location: $ur");
}
break;
case 'del':
$req = mysql_fetch_array(mysql_query("SELECT `sid`,`user_id` FROM `nhom_bd` WHERE `id`='$id'"));
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `id`='".$id."'"),0);
if($dem == 0) {
echo '<div class="rmenu">Bài đăng không tồn tại hoặc đã bị xoá!</div>';
require('../incfiles/end.php');
exit;
}
$xoa = mysql_fetch_array(mysql_query("SELECT `rights` FROM `nhom_user` WHERE `id`='".$req['sid']."' AND `user_id`='".$req['user_id']."'"));
$xoa2 = mysql_fetch_array(mysql_query("SELECT * FROM `nhom_user` WHERE `id`='".$req['sid']."' AND `user_id`='".$user_id."'"));
$kt = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='".$req['sid']."'AND `user_id`='".$user_id."' AND `duyet`='1'") ,0);
if($xoa2['rights']< $xoa['rights'] && $kt==1  || $req['user_id'] != $user_id && $rights < 9) {
echo '<div class="rmenu">Bạn không đủ quyền để thực hiện điều này!</div>';
require('../incfiles/end.php');
exit;
}
$bv = mysql_fetch_array(mysql_query("SELECT `time` FROM `nhom_bd` WHERE `id`='$id'"));
if(isset($_POST['sub'])) {
$cl = mysql_query("SELECT `id` FROM `nhom_bd` WHERE `cid`='$id'");
while($clike = mysql_fetch_array($cl)){
mysql_query("DELETE FROM `nhom_like` WHERE `id`='{$clike['id']}'");
}
mysql_query("DELETE FROM `nhom_bd` WHERE `id`='$id'");
mysql_query("DELETE FROM `nhom_bd` WHERE `cid`='$id'");
mysql_query("DELETE FROM `nhom_like` WHERE `id`='$id'");

$img = @getimagesize('files/anh_'.$bv['time'].'.jpg');
if(is_array($img)) {
@unlink('files/anh_'.$bv['time'].'.jpg'); }

$ur = 'page.php?id='.$req['sid'].'';
header("Location: $ur");
} else {
echo '<div class="phdr"><b>Xoá bài đăng</b></div><div class="list1"><form method="post">Bạn muốn xoá bài viết?<br/><input type="submit" name="sub" value="Xoá" />&#160;&#160;&#160;<a href="page.php?id='.$req['sid'].'"><input type="button" value="Hủy" /></a></form></div></div>';
}
break;
case 'post':
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `id`='".$id."'"),0);
if($dem == 0) {
echo '<div class="rmenu">Bài đăng không tồn tại hoặc đã bị xoá!</div>';
require('../incfiles/end.php');
exit;
}
$req = mysql_fetch_array(mysql_query("SELECT `id`,`sid`,`text`,`user_id`,`time`,`type` FROM `nhom_bd` WHERE `id`='$id'"));
$text =functions::checkout($req['text'], 1, 1);
$text = functions::smileys(bbcode::tags($text));
echo head_nhom($req['sid'],$user_id);
echo '<div class="phdr"><b>Chi tiết bài đăng</b></div><div class="rfb"><div class="mod-stt">'.ten_nick($req['user_id'],1,$req['sid']).'<img src="/images/clock.png" />&#160;<span class="gray">'.functions::thoigian($req['time']).'</span><br />';
if($req['type'] == 2){
                    $GetImageSize = GetImageSize('files/anh_'.$req['time'].'.jpg');
                    $imgx = $GetImageSize[0];
                    $imgy = $GetImageSize[1];
                    if($imgx <= $imgy && $imgx >= 150){
                        echo '<div align="center"><a href="cmt.php?id='.$req['id'].'"><img src="files/anh_'.$req['time'].'.jpg" width="150" height="auto" alt="image" /></a></div>';
                    }else if($imgx >= $imgy && $imgy >= 210) {
                        echo '<div align="center"><a href="cmt.php?id='.$req['id'].'"><img src="files/anh_'.$req['time'].'.jpg" width="210" height="auto" alt="image" /></a></div>';
                    }else{
                        echo '<div align="center"><a href="cmt.php?id='.$req['id'].'"><img src="files/anh_'.$req['time'].'.jpg" alt="image" style="max-width: 160px; height: auto;" /></a></div>';
                    }
}
echo $text.'</div></div>';
break;
        case 'xoanhom':
            $dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom` WHERE `id`='$id'"),0);
            if($dem == 0) {
                echo '<div class="rmenu">Nhóm không tồn tại hoặc đã bị xoá!</div>';
                require('../incfiles/end.php');
                exit;
            }
            $req = mysql_fetch_array(mysql_query("SELECT `user_id` FROM `nhom` WHERE `id`='$id'"));
            $kt = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='".$id."'AND `user_id`='".$user_id."' AND `duyet`='1'") ,0);

            if($user_id != $req['user_id'] && $rights < 9) {
                echo '<br/><div class="rmenu">Bạn không đủ quyền!</div>';
                require('../incfiles/end.php');
                exit;
            }
            if(isset($_POST['sub'])) {
                $rp = mysql_query("SELECT `time` FROM `nhom_bd` WHERE `sid`='$id' AND `type`='2'");
                while($n = mysql_fetch_array($rp)){
                    $img = @getimagesize('files/anh_'.$n['time'].'.jpg');
                    if(is_array($img)) {
                        @unlink('files/anh_'.$n['time'].'.jpg');
                    }
                }
                $avt = @getimagesize('avatar/'.$id.'.png');
                if(is_array($avt)) {
                    @unlink('avatar/'.$id.'.png');
                }
                $nl = mysql_query("SELECT `id` FROM `nhom_bd` WHERE `sid`='$id'");
                while($dlike = mysql_fetch_array($nl)){
                    mysql_query("DELETE FROM `nhom_like` WHERE `id`='{$dlike['id']}'");
                }
                mysql_query("DELETE FROM `nhom` WHERE `id`='$id'");
                mysql_query("DELETE FROM `nhom_user` WHERE `id`='$id'");

                mysql_query("DELETE FROM `nhom_bd` WHERE `sid`='$id'");







                header("Location: index.php");
            } else {
                echo '<div class="phdr"><b>Xóa nhóm</b></div><div class="list1"><form method="post">Bạn thực sự muốn xóa nhóm này?<br/><input type="submit" name="sub" value="Xóa"/>&#160;&#160;&#160;&#160;&#160;<a href="index.php"><input type="button" value="Hủy"/></a></form></div></div>';
            }
        break;
    }
    require('../incfiles/end.php');
?>