<?php
    session_name('SESS');
    @session_start();
    $sess = session_id();
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $time = time();
    $time_new = (time() - 86400);
    $folder_level ='';
    $count = substr_count($_SERVER['SCRIPT_NAME'], '/');
    for ($i = 1; $i < $count; $i++){
        $folder_level .= "../";
    }
    define("H", $folder_level);

    ini_set('error_reporting', true);
    ini_set('display_errors',true);
    ini_set('register_globals', false);
    ini_set('session.use_cookies', true);
    ini_set('session.use_trans_sid', false);
    ini_set('arg_separator.output', "&amp;");
    ini_set('arg_separator.input', "&amp;");
    ini_set('magic_quotes_gpc', false);
    ini_set('mbstring.internal_encoding', 'UTF-8');
    ini_set('iconv.set.encoding', 'UTF-8');
    ob_start();

    function antihack($var){
        if(is_array($var)) array_walk($var, 'antihack');
        else $var = htmlspecialchars(stripslashes(mysql_real_escape_string($var)), ENT_QUOTES, 'UTF-8');
    }
    foreach(array('_SERVER', '_GET', '_POST', '_COOKIE', '_REQUEST') as $v){
        if(!empty(${$v})) array_walk(${$v}, 'antihack');
    }
    function css(){}


    function msg($t){
        echo '<div class="gmenu">'.$t.'</div>';
    }
    function page(){
        global $k_page;
        if (isset($_GET['page'])) {
            if ($_GET['page'] == 'end') $page = $k_page;
            else $page = abs(intval($_GET['page']));
        } else $page = 1;

        if ($page > $k_page) $page = $k_page;
        return $page;
    }
    function str($link='?',$k_page,$page){
        if($k_page==1 OR isset($_GET['from'])) echo'<div class="topmenu">';
        else echo'<div class="topmenu">';
        if ($page != 1 ) echo '<a class="pagenav" href="/'.$link.'page='.($page-1).'">&lt;&lt;</a> ';
        for($i=1;$i<=$k_page;$i++){
            if($i < $page){
                echo '<a class="pagenav" href="/'.$link.'page='.$i.'">'.$i.'</a> ';
            }
            if($i == $page){
                echo '<span class="currentpage"><b>'.$i.'</b></span>';
            }
            if($i > $page){
                echo ' <a class="pagenav" href="/'.$link.'page='.$i.'">'.$i.'</a>';
            }
        }
        if ($page != $k_page) echo ' <a class="pagenav" href="/'.$link.'page='.($page+1).'">&gt;&gt;</a>';
        echo "<br/><form action=\"/".$link."\" method=\"/POST\" enctype=\"/multipart/form-data\">";
        echo '<input type="text" name="page" size="4" class="dn" />';
        echo '<input type="submit" value="Đi đến"/><br></form>';
        echo '</div>';
    }
    function display_error($t){
        echo '<div class="list1">'.$t.'</div>';
    }
    $banthan = @mysql_query("SELECT * FROM `users` WHERE id='$user_id'");
    while ($ur = @mysql_fetch_array($banthan)){
        $tien = $ur['balans'];
        $op = $ur['fermer_oput'];
        $neve = $ur['fermer_level'];
    }
    if($op >= 0 && $op <= 150){
        $level = 0;
    } else if($op >= 151 && $op <= 300){
        $level = 1;
    } else if($op >= 301 && $op <= 800){
        $level = 2;
    } else if($op >= 801 && $op <= 1300){
        $level = 3;
    } else if($op >= 1301 && $op <= 2000){
        $level = 4;
    } else if($op >= 2001 && $op <= 3000){
        $level = 5;
    } else if($op >= 3001 && $op <= 4500){
        $level = 6;
    } else if($op >= 4501 && $op <= 6000){
        $level = 7;
    } else if($op >= 6001 && $op <= 9000){
        $level = 8;
    } else if($op >= 9001 && $op <= 12000){
        $level = 9;
    } else if($op >= 12001 && $op <= 16000){
        $level = 10;
    } else if($op >= 16001 && $op <= 20000){
        $level = 11;
    } else if($op >= 20001 && $op <= 25000){
        $level = 12;
    } else if($op >= 25001 && $op <= 30000){
        $level = 13;
    } else if($op >= 30001 && $op <= 35000){
        $level = 14;
    } else if($op >= 35001 && $op <= 40000){
        $level = 15;
    } else if($op >= 40001 && $op <= 50000){
        $level = 16;
    } else if($op >= 50001 && $op <= 60000){
        $level = 17;
    } else if($op >= 60001 && $op <= 70000){
        $level = 18;
    } else if($op >= 70001 && $op <= 85000){
        $level = 19;
    } else if($op >= 85001 && $op <= 90000){
        $level = 20;
    } else if($op >= 90001 && $op <= 105000){
        $level = 21;
    } else if($op >= 105001 && $op <= 115000){
        $level = 22;
    } else if($op >= 115001 && $op <= 130000){
        $level = 23;
    } else if($op >= 130001 && $op <= 155000){
        $level = 24;
    } else if($op >= 155001 && $op <= 170000){
        $level = 25;
    } else if($op >= 170001 && $op <= 190000){
        $level = 26;
    } else if($op >= 190001 && $op <= 210000){
        $level = 27;
    } else if($op >= 210001 && $op <= 230000){
        $level = 28;
    } else if($op >= 230001 && $op <= 250000){
        $level = 29;
    } else if($op >= 250001 && $op <= 270000){
        $level = 30;
    } else if($op >= 270001 && $op <= 290000){
        $level = 31;
    } else if($op >= 290001 && $op <= 320000){
        $level = 32;
    } else if($op >= 320001 && $op <= 340000){
        $level = 33;
    } else if($op >= 340001 && $op <= 360000){
        $level = 34;
    } else if($op >= 360001 && $op <= 400000){
        $level = 35;
    } else if($op >= 400001 && $op <= 450000){
        $level = 36;
    } else if($op >= 450001 && $op <= 500000){
        $level = 37;
    } else if($op >= 500001 && $op <= 550000){
        $level = 38;
    } else if($op >= 550001 && $op <= 600000){
        $level = 39;
    } else if($op >= 600001 && $op <= 650000){
        $level = 40;
    } else if($op >= 650001 && $op <= 700000){
        $level = 41;
    } else if($op >= 700001 && $op <= 750000){
        $level = 42;
    } else if($op >= 750001 && $op <= 800000){
        $level = 43;
    } else if($op >= 800001 && $op <= 850000){
        $level = 44;
    } else if($op >= 850001 && $op <= 900000){
        $level = 45;
    } else if($op >= 950001 && $op <= 1000000){
        $level = 46;
    } else if($op >= 1000001 && $op <= 1100000){
        $level = 47;
    } else if($op >= 1100001 && $op <= 1200000){
        $level = 48;
    } else if($op >= 1200001 && $op <= 1300000){
        $level = 49;
    } else if($op >= 1300001 && $op <= 1600001){
        $level = 50;
    } else if($op >= 1600001 && $op <= 2000001){
        $level = 51;
    } else if($op >= 2000001 && $op <= 2600001){
        $level = 52;
    } else if($op >= 2600001 && $op <= 3500001){
        $level = 53;
    } else if($op >= 3500001 && $op <= 4500001){
        $level = 54;
    } else if($op >= 4500001 && $op <= 6000001){
        $level = 55;
    } else if($op >= 6000001 && $op <= 8000001){
        $level = 56;
    } else if($op >= 8000001 && $op <= 10000001){
        $level = 57;
    } else if($op >= 10000001 && $op <= 13000001){
        $level = 58;
    } else if($op >= 13000001 && $op <= 16000001){
        $level = 59;
    } else if($op >= 16000001 && $op <= 19000001){
        $level = 60;
    } else if($op >= 19000001 && $op <= 23000001){
        $level = 61;
    } else if($op >= 23000001 && $op <= 27000001){
        $level = 62;
    } else if($op >= 27000001 && $op <= 30000001){
        $level = 63;
    } else if($op >= 30000001 && $op <= 35000001){
        $level = 64;
    } else if($op >= 35000001 && $op <= 40000001){
        $level = 65;
    } else if($op >= 40000001 && $op <= 45000001){
        $level = 66;
    } else if($op >= 45000001 && $op <= 50000001){
        $level = 67;
    } else if($op >= 50000001 && $op <= 56000001){
        $level = 68;
    } else if($op >= 56000001 && $op <= 70000001){
        $level = 69;
    } else if($op >= 70000001){
        $level = 70;
    }
    if($neve != $level) mysql_query("UPDATE `users` SET `fermer_level` = '".$level."' WHERE `id` = $user_id LIMIT 1");

    $i = mysql_query("SELECT * FROM `fermer_gr` WHERE `kol` = '0' AND `semen` > 0");
    while ($ii = mysql_fetch_array($i)){
        $semenk = mysql_fetch_array(mysql_query("select * from `fermer_name` WHERE  `id` = '$ii[semen]'  LIMIT 1"));
        $pk = mysql_fetch_array(mysql_query("select * from `fermer_gr` WHERE  `id` = '$ii[id]'  LIMIT 1"));
        if($pk['semen'] != 0 && $time > $pk['time'] && $pk['kol'] == 0){
            $pt = rand($semenk['rand1'], $semenk['rand2']);
            if($ii['woter'] == 0) $pt = floor($pt-(1/5*$pt));
            if($ii['co'] == 1) $pt = floor($pt-(1/5*$pt));
            if($ii['sau'] == 1) $pt = floor($pt-(1/5*$pt));
            mysql_query("UPDATE `fermer_gr` SET `kol` = $pt WHERE `id` = '$ii[id]' LIMIT 1");
        }
        $vremja = $ii['time'] - $time;
        if($ii['semen'] != 0){
            if($ii['vu'] == 1){
                if($vremja <= 3/5*$semenk['time'] && $vremja > 2/5*$semenk['time']){
                    if($ii['data_c'] == 0){
                        mysql_query("UPDATE `fermer_gr` SET `co` = '1' WHERE `id` = $ii[id] LIMIT 1");
                        mysql_query("UPDATE `fermer_gr` SET `data_c` = '1' WHERE `id` = $ii[id] LIMIT 1");
                    }
                    if($ii['data_n'] == 0){
                        mysql_query("UPDATE `fermer_gr` SET `woter` = '0' WHERE `id` = $ii[id] LIMIT 1");
                        mysql_query("UPDATE `fermer_gr` SET `data_n` = '1' WHERE `id` = $ii[id] LIMIT 1");
                    }
                } else if($vremja <= 2/5*$semenk['time'] && $vremja > 1/5*$semenk['time']){
                    if($ii['data_s'] == 0){
                        mysql_query("UPDATE `fermer_gr` SET `sau` = '1' WHERE `id` = $ii[id] LIMIT 1");
                        mysql_query("UPDATE `fermer_gr` SET `data_s` = '1' WHERE `id` = $ii[id] LIMIT 1");

                    }
                }
            }
        }
    }

    $ivn = mysql_query("SELECT * FROM `fermer_gr_VN` WHERE `kol` = '0' AND `semen` > 0");
    while ($iivn = mysql_fetch_array($ivn)){
        $semenkvn = mysql_fetch_array(mysql_query("select * from `fermer_name_VN` WHERE  `id` = '$iivn[semen]'  LIMIT 1"));
        $pkvn = mysql_fetch_array(mysql_query("select * from `fermer_gr_VN` WHERE  `id` = '$iivn[id]'  LIMIT 1"));
        if($pkvn['semen'] != 0 && $time > $pkvn['time'] && $pkvn['kol'] == 0){
            $ptvn = rand($semenkvn['rand1'], $semenkvn['rand2']);
            if($iivn['choan'] == 0) $ptvn = floor($ptvn-(1/5*$ptvn));
            mysql_query("UPDATE `fermer_gr_VN` SET `kol` = $ptvn WHERE `id` = '$iivn[id]' LIMIT 1");
        }
        if($iivn['timechoan'] < $time && $iivn['choan'] == 1){
            mysql_query("UPDATE `fermer_gr_VN` SET `choan` = '0' WHERE `id` = $iivn[id] LIMIT 1");
            mysql_query("UPDATE `fermer_gr_VN` SET `timechoan` = NULL WHERE `id` = $iivn[id] LIMIT 1");
        }
        if($iivn['semen'] != 0 && $iivn['songtrong'] < $time){
            mysql_query("UPDATE `fermer_gr_VN` SET `semen` = '0' WHERE `id` = $iivn[id] LIMIT 1");
            mysql_query("UPDATE `fermer_gr_VN` SET `time` = NULL WHERE `id` = $iivn[id] LIMIT 1");
            mysql_query("UPDATE `fermer_gr_VN` SET `timechoan` = NULL WHERE `id` = $iivn[id] LIMIT 1");
            mysql_query("UPDATE `fermer_gr_VN` SET `songtrong` = NULL WHERE `id` = $iivn[id] LIMIT 1");
            mysql_query("UPDATE `fermer_gr_VN` SET `choan` = '0' WHERE `id` = $iivn[id] LIMIT 1");
            mysql_query("UPDATE `fermer_gr_VN` SET `kol` = '0' WHERE `id` = $iivn[id] LIMIT 1");
        }
    }

    $reqdog = mysql_query("SELECT * FROM `fermer_dog` WHERE `time` > 0");
    while ($rdog = mysql_fetch_array($reqdog)){
        if($rdog['time'] < $time){
            mysql_query("UPDATE `fermer_dog` SET `time` = NULL WHERE `id_user` = $rdog[id_user] LIMIT 1");
        }
    }
?>