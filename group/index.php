<?php
/*///////////////////////
//@Tac gia: Nguyen Ary
//@Site: gochep.net
//@Facebook: facebook.com/tia.chophht
///////////////////////*/
    define('_IN_JOHNCMS', 1);
    require('../incfiles/core.php');
    $textl = 'Hội nhóm - Clan';
    require('../incfiles/head.php');
    require('func.php');
    echo '<a href="tao.php"><b>Tạo nhóm</b></a><div class="phdr"><b>Nhóm đã tham gia</b></div>';
    $dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom_user` WHERE `user_id`='$user_id' AND `duyet`='1'"),0);
    if($dem) {
        $req = mysql_query("SELECT * FROM `nhom_user` WHERE `user_id`='$user_id' AND `duyet`='1' ORDER BY `stime` DESC LIMIT $start, $kmess");
        while($res = mysql_fetch_array($req)) {
            $nhom = nhom($res['id']);
            echo '<div class="list1"><table cellpadding="0" cellspacing="0"><tr><td>';
            $url = @getimagesize('avatar/'.$res['id'].'.png');
            if(is_array($url)){
                echo '<img src="avatar/'.$res['id'].'.png" width="35" height="35" alt="" />';
            }else{
                echo '<img src="avatar/noavatar.png" width="35" height="35" alt="" />';
            }





            echo '</td><td style="padding: 0px 0px 0px 4px;"><a href="page.php?id='.$res['id'].'"><b>'.$nhom['name'].'</b></a></td></tr></table></div>';
        }
        if($dem > $kmess)
        echo '<div class="topmenu"><a href="more.php">Xem thêm... >></a></div>';
    } else {
        echo '<div class="rmenu">Chưa tham gia nhóm nào!</div>';
    }
    echo '<div class="phdr"><b>Nhóm ngẫu nhiên</b></div>';

    $dem = mysql_result(mysql_query("SELECT COUNT(*) FROM `nhom`"),0);
    if($dem) {
        $req = mysql_query("SELECT * FROM `nhom` ORDER BY RAND() LIMIT $start, $kmess");
        while($res = mysql_fetch_array($req)) {
            $nhom = nhom($res['id']);
            echo '<div class="list1"><table cellpadding="0" cellspacing="0"><tr><td>';
            $url = @getimagesize('avatar/'.$res['id'].'.png');
            if(is_array($url)){
                echo '<img src="avatar/'.$res['id'].'.png" width="35" height="35" alt="" />';
            }else{
                echo '<img src="avatar/noavatar.png" width="35" height="35" alt="" />';
            }
            echo '</td><td style="padding: 0px 0px 0px 4px;"><a href="page.php?id='.$res['id'].'"><b>'.$nhom['name'].'</b></a></td></tr></table></div>';
        }
        if($dem > $kmess)
        echo '<div class="topmenu"><a href="more.php?act=nhom">Xem thêm... >></a></div>';
    } else {
        echo '<div class="rmenu">Chưa có nhóm nào!</div>';
    }

    require('../incfiles/end.php');
?>