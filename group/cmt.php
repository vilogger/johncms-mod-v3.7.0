<?php
/*///////////////////////
//@Tac gia: Nguyen Ary
//@Site: gochep.net
//@Facebook: facebook.com/tia.chophht
///////////////////////*/
define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');
$textl= 'Bình luận bài đăng';
require('../incfiles/head.php');
require('func.php');
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `id`='".$id."'"),0);
if($dem == 0) {
echo '<br /><div class="rmenu">Bài đăng không tồn tại hoặc đã bị xoá!</div>';
require('../incfiles/end.php');
exit;
}
$req = mysql_fetch_array(mysql_query("SELECT `sid` FROM `nhom_bd` WHERE `id`='$id'"));
$kt = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='".$req['sid']."'AND `user_id`='".$user_id."' AND `duyet`='1'") ,0);
$bl = mysql_fetch_array(mysql_query("SELECT * FROM `nhom_bd` WHERE `id`='$id'"));
$nhom = nhom($bl['sid']);
$tnhom = mysql_fetch_array(mysql_query("SELECT * FROM `nhom` WHERE `id`='".$bl['sid']."'"));
if($kt == 0 && $nhom['set'] > 0) {
echo '<div class="rmenu">Phải là thành viên của nhóm!</div>';
require('../incfiles/end.php');
exit;
}

echo head_nhom($bl['sid'],$user_id);
if($bl['type'] == 1){
echo '<div class="phdr">Lời bình luận</div>';
}else{
echo '<div class="phdr">Bài đăng</div>';
}

$text = @thugon($bl['text'],$id);
echo '<div class="list2" style=" box-shadow: 0px 1px 1px #ccc;
-moz-box-shadow: 0px 1px 1px #ccc;
-webkit-box-shadow: 0px 1px 1px #ccc; background-color: #f9f9f9;">'.ten_nick($bl['user_id'],1,$bl['sid']).'';

if($bl['type'] == 2) {
                    $GetImageSize = GetImageSize('files/anh_'.$bl['time'].'.jpg');
                    $imgx = $GetImageSize[0];
                    $imgy = $GetImageSize[1];
                    if($imgx <= $imgy && $imgx >= 150){
                        echo '<div align="center"><a href="cmt.php?id='.$bl['id'].'"><img src="files/anh_'.$bl['time'].'.jpg" width="150" height="auto" alt="image" /></a></div>';
                    }else if($imgx >= $imgy && $imgy >= 210) {
                        echo '<div align="center"><a href="cmt.php?id='.$bl['id'].'"><img src="files/anh_'.$bl['time'].'.jpg" width="210" height="auto" alt="image" /></a></div>';
                    }else{
                        echo '<div align="center"><a href="cmt.php?id='.$bl['id'].'"><img src="files/anh_'.$bl['time'].'.jpg" alt="image" style="max-width: 160px; height: auto;" /></a></div>';
                    }
}
echo '<div style="margin-top:4px;"></div>'.$text.'<div class="gray" style="font-size: x-small;">(' . functions::thoigian($bl['time']) . ')</div>';
//Phan menu bai dang
$kt = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `id`='".$bl['sid']."'AND `user_id`='".$user_id."' AND `duyet`='1'") ,0);
$like = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_like` WHERE `id`='".$id."'"),0);
$klike = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_like` WHERE `id`='".$id."' AND `user_id`='".$user_id."'"),0);
$xoa = mysql_fetch_array(mysql_query("SELECT `rights` FROM `nhom_user` WHERE `id`='".$bl['sid']."' AND `user_id`='".$bl['user_id']."'"));
$xoa2 = mysql_fetch_array(mysql_query("SELECT * FROM `nhom_user` WHERE `id`='".$bl['sid']."' AND `user_id`='".$user_id."'"));
echo '<div class="sub">'.($like> 0 ? '<a href="more.php?act=like&id='.$id.'"><img src="img/l.png" alt="l" />'.$like.'</a>'.($kt >= 1 ? ' · ' : '').'':'').''.($kt >= 1 ? ''.($klike == 0 ? '<a href="action.php?act=like&id='.$id.'">Thích</a>':'<a href="action.php?act=dislike&id='.$id.'">Bỏ thích</a>').'' : '').''.($xoa2['rights']> $xoa['rights'] || $res['user_id'] == $user_id || $rights == 9 ? ' · <a href="action.php?act=del&id='.$id.'">Xoá</a>':'').'</div>';
echo '</div>';
if($bl['type'] != 1){
//phan dang bai


echo '<div class="phdr"><b>Bình luận</b></div>';
if($kt>=1) {
$text = functions::checkin(trim($_POST['text']));
$trungt = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `text`='{$text}' AND `cid`='{$id}' AND `type`='1'"), 0);
if(isset($_POST['submit'])) {
if(!empty($ban)) {
echo '<div class="rmenu">Tài khoản của bạn đang bị khoá nên không thể sử dụng chức năng này!</div>';
}else if(empty($text)) {
echo '<div class="rmenu">Chưa nhập nội dung!</div>';


} else if(strlen($text) > 5000) {
echo '<div class="rmenu">Nội dung quá dài. Tối đa 5000 kí tự!</div>';
} else if(($datauser['lastpost'] + 5) > time()) {
echo '<div class="rmenu">Đợi <b>'.(($datauser['lastpost']+5) - time()).'s</b> nữa để đăng tiếp!</div>';
} else {
if($user_id != $bl['user_id']){
    mysql_query("INSERT INTO `cms_mail` SET `user_id` = '$user_id', `from_id`='".$bl['user_id']."', `them`='1', `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã bình luận về bài viết của bạn trong nhóm [url=".$home."/group/cmt.php?id=".$id."]".$tnhom['name']."[/url]', `sys`='1', `time`='".time()."'");
}
mysql_query("INSERT INTO `nhom_bd` SET `sid`='".$bl['sid']."', `cid`='".$id."', `user_id`='".$user_id."', `time`='".$time."', `stime`='".$time."', `text`='".mysql_real_escape_string($text)."', `type`='1'");
mysql_query("UPDATE `users` SET `lastpost`='$time' WHERE `id`='$user_id'");
mysql_query("UPDATE `users` SET `postgroup`=`postgroup`+'1' WHERE `id` = '$user_id' ");
mysql_query("UPDATE `nhom_bd` SET `stime`='$time' WHERE `id`='$id'");
            $reqp = mysql_query("SELECT DISTINCT `user_id` FROM `nhom_bd` WHERE `cid`='$id' AND `user_id` != '$user_id' AND `user_id` != '{$bl['user_id']}'");
            while ($resp = mysql_fetch_array($reqp)) {
                            mysql_query("INSERT INTO `cms_mail` SET `user_id` = '$user_id', `from_id`='".$resp['user_id']."', `them`='1', `text` = '[url=".$home."/users/profile.php?user=".$user_id."][cnick]".$user_id."[/cnick][/url] đã bình luận về bài viết của nhóm [url=".$home."/group/cmt.php?id=".$id."]".$tnhom['name']."[/url]', `sys`='1', `time`='".time()."'");
            }
$trave = isset($_POST['trave']) ? base64_decode($_POST['trave']) : 'cmt.php?id='.$id.'';
header("Location: $trave");
exit;
}
}
$trave = base64_encode($_SERVER['REQUEST_URI']);
echo '<div class="list2"><form method="post"><textarea name="text" cows="3"></textarea><input type="hidden" name="trave" value="'.$trave.'" /><br /><input type="submit" name="submit" value="Đăng" /></form></div>';
}

$tong =mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `cid`='$id' AND `type`='1'"),0);
if($tong) {

    $req =mysql_query("SELECT * FROM `nhom_bd` WHERE `cid`='$id' AND `type`='1' ORDER BY `time` DESC LIMIT $start,$kmess");
    while($res = mysql_fetch_array($req)) {
        $var = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id`='".$res['user_id']."'"));
        $vad = mysql_fetch_array(mysql_query("SELECT * FROM `nhom_user` WHERE `id`='".$res['sid']."'AND `user_id`='".$res['user_id']."'"));
        echo '<div class="list1" style="background-color: #f9f9f9;">';
        echo (time() > $var['lastdate']+300 ? '<span style="color:red;">&#8226;</span>' : '<span style="color:green;">&#8226;</span>').' <a href="/users/profile.php?user='.$res['user_id'].'"><b>'.$var['name'].'</b></a> <span class="gray" style="font-size: x-small;">(' . functions::thoigian($res['time']) . ')</span><br />';
        echo thugon($res['text'],$res['id']).'<br />';
        //Phan menu bai dang
        $like = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_like` WHERE `id`='".$res['id']."' AND `type`='1'"),0);
        $klike = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_like` WHERE `id`='".$res['id']."' AND `user_id`='".$user_id."' AND `type`='1'"),0);
        $xoa = mysql_fetch_array(mysql_query("SELECT `rights` FROM `nhom_user` WHERE `id`='".$bl['sid']."' AND `user_id`='".$res['user_id']."'"));
        $xoa2 = mysql_fetch_array(mysql_query("SELECT * FROM `nhom_user` WHERE `id`='".$bl['sid']."' AND `user_id`='".$user_id."'"));
        echo '<div class="sub">'.($like> 0 ? '<a href="more.php?act=like&id='.$res['id'].'"><img src="img/l.png" alt="l"/>'.$like.'</a>'.($kt >= 1 ? ' · ' : '').'':'').''.($kt >= 1 ? ''.($klike == 0 ? '<a href="action.php?act=like&id='.$res['id'].'">Thích</a>':'<a href="action.php?act=dislike&id='.$res['id'].'">Bỏ thích</a>').'' : '').''.($xoa2['rights']> $xoa['rights'] || $res['user_id'] == $user_id || $rights == 9 ? ' · <a href="action.php?act=del&id='.$res['id'].'">Xoá</a>':'').'</div>';
        echo '</div>';
    }

if ($tong> $kmess){echo '<divclass="topmenu">' . functions::display_pagination('cmt.php?id='.$id.'&', $start, $tong, $kmess) . '</div>';
}
} else {
echo '<div class="rmenu">Chưa có bình luận nào!</div>';
}

echo '<div class="phdr">Tổng: '.$tong.'</div>';
}else{
echo '<div class="list1"><a href="cmt.php?id='.$bl['cid'].'">Xem trong bài đăng</a></div>';
}
require('../incfiles/end.php');
?>