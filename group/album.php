<?php
/*///////////////////////
//@Tac gia: Nguyen Ary
//@Site: gochep.net
//@Facebook: facebook.com/tia.chophht
///////////////////////*/
define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');
$textl= 'Album hình ảnh';
require('../incfiles/head.php');
require('func.php');
$id= intval(abs($_GET['id']));
$dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom` WHERE `id`='$id'"),0);
if(!isset($id) || $dem == 0) {
    echo '<div class="rmenu">Nhóm không tồn tại hoặc đã bị xoá!</div>';
    require('../incfiles/end.php');
    exit;
}
    $nhom = mysql_fetch_array(mysql_query("SELECT * FROM `nhom` WHERE `id`='".$id."'"));
    $ktviet = mysql_result( mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `user_id`='$user_id' AND `id`='$id' AND `duyet`='1'"),0);
    if($ktviet == 0 && $nhom['set'] == 1 || $ktviet == 0 && $nhom['set'] == 2) {
        echo '<div class="rmenu">Chỉ dành cho thành viên của nhóm</div>';
        require('../incfiles/end.php');
        exit;
    }
    echo head_nhom($id,$user_id);
    echo '<div class="phdr"><b>Album hình ảnh</b></div>';
    $tong =mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `sid`='$id' AND `type`='2'"),0);
    if($tong) {
        echo '<div class="rfb">';
        $req =mysql_query("SELECT * FROM `nhom_bd` WHERE `sid`='$id' AND `type`='2' ORDER BY `time` DESC LIMIT $start,$kmess");
        while($res=mysql_fetch_array($req)) {
            echo '<div class="mod-stt">'.ten_nick($res['user_id'],0,$id).' <span class="gray" style="font-size: x-small">('.functions::thoigian($res['time']).')</span>';
            if($res['type']==2) {
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
            $like = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_like` WHERE `id`='".$res['id']."' AND `type` != '1'"),0);
            $bl = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_bd` WHERE `cid`='".$res['id']."' AND `type`='1'"),0);
            echo '<div class="sub">'.($like > 0 ? '<a href="more.php?act=like&id='.$res['id'].'"><img src="img/l.png" alt="l" /> '.$like.'</a> · ':'').'<a href="cmt.php?id='.$res['id'].'">Bình luận ('.$bl.')</a></div></div>';
        }
        echo '</div>';
        if ($tong> $kmess){
            echo '<divclass="topmenu">' . functions::display_pagination('album.php?id='.$id.'&', $start, $tong, $kmess) . '</div>';
        }
    } else {
        echo '<div class="rmenu">Không có hình ảnh nào</div>';
    }
    echo '<div class="list2"><a href="page.php?id='.$id.'">Trở về nhóm >></a></div>';
require('../incfiles/end.php');
?>