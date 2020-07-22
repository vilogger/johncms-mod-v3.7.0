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

require_once("../incfiles/head.php");

$im = array();
$delimag = opendir("$filesroot/graftemp");
while ($imd = readdir($delimag)) {
    if ($imd != "." && $imd != ".." && $imd != "index.php") {
        $im[] = $imd;
    }
}
closedir($delimag);
$totalim = count($im);
for ($imi = 0; $imi < $totalim; $imi++) {
    $filtime[$imi] = filemtime("$filesroot/graftemp/$im[$imi]");
    $tim = time();
    $ftime1 = $tim - 10;
    if ($filtime[$imi] < $ftime1) {
        @unlink("$filesroot/graftemp/$im[$imi]");
    }
}
if ($_GET['file'] == '') {
    echo functions::display_error($lng_dl['file_not_selected'], '<a href="index.php">' . $lng['back'] . '</a>');
    require_once('../incfiles/end.php');
    exit;
}
$file = intval(trim($_GET['file']));
$file1 = mysql_query("select * from `download` where type = 'file' and id = '" . $file . "';");
$file2 = mysql_num_rows($file1);
$adrfile = mysql_fetch_array($file1);
if (($file1 == 0) || (!is_file("$adrfile[adres]/$adrfile[name]"))) {
    echo functions::display_error($lng_dl['file_select_error'], '<a href="index.php">' . $lng['back'] . '</a>');
    require_once('../incfiles/end.php');
    exit;
}
$_SESSION['downl'] = rand(1000, 9999);
$siz = filesize("$adrfile[adres]/$adrfile[name]");
$siz = round($siz / 1024, 2);
$filtime = filemtime("$adrfile[adres]/$adrfile[name]");
$filtime = date("d.m.Y", $filtime);

$dnam = mysql_query("select * from `download` where type = 'cat' and id = '" . $adrfile['refid'] . "';");
$dnam1 = mysql_fetch_array($dnam);
$dirname = "$dnam1[text]";
$dirid = "$dnam1[id]";
$nadir = $adrfile['refid'];
echo '<div class="phdr"><a href="index.php"><b>' . $lng['downloads'] . '</b></a>';
// Получаем структуру каталогов
while ($nadir != "" && $nadir != "0") {
    echo ' | <a href="?cat=' . $nadir . '">' . $dirname . '</a><br/>';
    $dnamm = mysql_query("select * from `download` where type = 'cat' and id = '" . $nadir . "';");
    $dnamm1 = mysql_fetch_array($dnamm);
    $dnamm2 = mysql_query("select * from `download` where type = 'cat' and id = '" . $dnamm1['refid'] . "';");
    $dnamm3 = mysql_fetch_array($dnamm2);
    $nadir = $dnamm1['refid'];
    $dirname = $dnamm3['text'];
}
echo '</div><div class="menu"><p>';
echo '<b>' . $lng_dl['file'] . ': <span class="red">' . $adrfile['name'] . '</span></b><br/>' .
    '<b>' . $lng_dl['uploaded'] . ':</b> ' . $filtime . '<br/>';

$graf = array
(
    "gif",
    "jpg",
    "png"
);
$prg = strtolower(functions::format($adrfile['name']));
if (in_array($prg, $graf)) {
    $sizsf = GetImageSize("$adrfile[adres]/$adrfile[name]");
    $widthf = $sizsf[0];
    $heightf = $sizsf[1];
    #  !предпросмотр!
    $namefile = $adrfile['name'];
    $infile = "$adrfile[adres]/$namefile";
    if (!empty($_SESSION['razm'])) {
        $razm = $_SESSION['razm'];
    } else {
        $razm = 110;
    }
    $sizs = GetImageSize($infile);
    $width = $sizs[0];
    $height = $sizs[1];
    $quality = 100;
    $x_ratio = $razm / $width;
    $y_ratio = $razm / $height;
    if (($width <= $razm) && ($height <= $razm)) {
        $tn_width = $width;
        $tn_height = $height;
    } else if (($x_ratio * $height) < $razm) {
        $tn_height = ceil($x_ratio * $height);
        $tn_width = $razm;
    } else {
        $tn_width = ceil($y_ratio * $width);
        $tn_height = $razm;
    }
    switch ($prg) {
        case "gif":
            $im = ImageCreateFromGIF($infile);
            break;

        case "jpg":
            $im = ImageCreateFromJPEG($infile);
            break;

        case "jpeg":
            $im = ImageCreateFromJPEG($infile);
            break;

        case "png":
            $im = ImageCreateFromPNG($infile);
            break;
    }
    $im1 = ImageCreateTrueColor($tn_width, $tn_height);
    imagecopyresized($im1, $im, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);
    $path = "$filesroot/graftemp";
    $imagnam = "$path/$namefile.temp.png";
    imageJpeg($im1, $imagnam, $quality);
    echo "<p><img src='" . $imagnam . "' alt=''/></p>";
    imagedestroy($im);
    imagedestroy($im1);
    @chmod("$imagnam", 0644);
    echo $widthf . ' x ' . $heightf . 'px<br/>';
}

if ($prg == "mp3") {
    $getID3 = new getID3;
    $ThisFileInfo = $getID3->analyze("$adrfile[adres]/$adrfile[name]");
    getid3_lib::CopyTagsToComments($ThisFileInfo);
    $fileformat = $ThisFileInfo['fileformat'];
    if(preg_match('/mp3/', $fileformat)){
        $f = "$adrfile[adres]/$adrfile[name]";
        $picture = $ThisFileInfo['comments']['picture']['0']['data'];
        $picture_mime = $ThisFileInfo['comments']['picture']['0']['image_mime'];
        echo '<p>';
        if (!empty($ThisFileInfo['comments']['artist']['0']))
            echo '<div><b>' . $lng_dl['artist'] . ':</b> ' . $ThisFileInfo['comments']['artist']['0'] . '</div>';
        if (!empty($ThisFileInfo['comments']['album']['0']))
            echo '<div><b>' . $lng_dl['album'] . ':</b> ' . $ThisFileInfo['comments']['album']['0'] . '</div>';
        if (!empty($ThisFileInfo['comments']['year']['0']))
            echo '<div><b>' . $lng_dl['released'] . ':</b> ' . $ThisFileInfo['comments']['year']['0'] . '</div>';
        if (!empty($ThisFileInfo['comments']['title']['0']))
            echo '<div><b>' . $lng['title'] . ':</b> ' . $ThisFileInfo['comments']['title']['0'] . '</div>';
        echo '</p>';
        if ($ThisFileInfo['bitrate']) {
            echo '<b>' . $lng_dl['bitrate'] . ':</b> ' . BitrateText($ThisFileInfo['bitrate'] / 1000) . '<br/>' .
            '<b>' . $lng_dl['duration'] . ':</b> ' . $ThisFileInfo['playtime_string'] . '<br/>';
        }
                        $data = '<img onclick="StartSong(this)" src="'.(!$picture ? '/images/ms.png' : 'data:'.$picture_mime.';base64,'.base64_encode($picture)).'" width="80" height="80" class="playButton"><fieldset class="lbl-group" style="border: 3px solid #ddd;"><div style="color: #5bcf80;"><i class="icon-play"></i></div><label>'.$ThisFileInfo['comments']['title']['0'].'</label></fieldset><fieldset class="lbl-group switch"><div style="color: #ff014a;"><i class="icon-heart"></i></div><label>'.$ThisFileInfo['comments']['artist']['0'].'</label></fieldset><br /><span style="display:none;" id="norber">'.$f.'</span><span style="display:none;" id="xftitle">'.$ThisFileInfo['comments']['title']['0'].'</span><div class="mp3Placeholder"></div>';

        echo '<div style="background: #6998c8; color:#fff; border: 0px; font-weight: bold; font-size: 13px; padding: 5px 12px 5px 12px; cursor: pointer;">Song - PhieuBac.Ga</div><div style="background-color: #FFF; border-color: #6998c8; border-style: solid; border-width:  0px 1px 1px 1px; padding: 6px;">'.$data.'</div>';
    }
}
if (!empty($adrfile['text'])) {
    echo "<p>Mô tả:<br/>$adrfile[text]</p>";
}

if ((!in_array($prg, $graf)) && ($prg != "mp3")) {
    if (!empty($adrfile['screen'])) {
        $infile = "$screenroot/$adrfile[screen]";
        if (!empty($_SESSION['razm'])) {
            $razm = $_SESSION['razm'];
        } else {
            $razm = 110;
        }
        $sizs = GetImageSize($infile);
        $width = $sizs[0];
        $height = $sizs[1];
        $quality = 100;
        $angle = 0;
        $fontsiz = 20;
        $tekst = $set['copyright'];
        $x_ratio = $razm / $width;
        $y_ratio = $razm / $height;
        if (($width <= $razm) && ($height <= $razm)) {
            $tn_width = $width;
            $tn_height = $height;
        } else if (($x_ratio * $height) < $razm) {
            $tn_height = ceil($x_ratio * $height);
            $tn_width = $razm;
        } else {
            $tn_width = ceil($y_ratio * $width);
            $tn_height = $razm;
        }
        $format = functions::format($infile);
        switch ($format) {
            case "gif":
                $im = ImageCreateFromGIF($infile);
                break;

            case "jpg":
                $im = ImageCreateFromJPEG($infile);
                break;

            case "jpeg":
                $im = ImageCreateFromJPEG($infile);
                break;

            case "png":
                $im = ImageCreateFromPNG($infile);
                break;
        }
        $color = imagecolorallocate($im, 55, 255, 255);
        $fontdir = opendir("$filesroot/fonts");
        while ($ttf = readdir($fontdir)) {
            if ($ttf != "." && $ttf != ".." && $ttf != "index.php") {
                $arr[] = $ttf;
            }
        }
        $it = count($arr);
        $ii = rand(0, $it - 1);
        $fontus = "$filesroot/fonts/$arr[$ii]";
        $font_size = ceil(($width + $height) / 15);
        @imagettftext($im, $font_size, $angle, '10', $height - 10, $color, $fontus, $tekst);
        $im1 = imagecreatetruecolor($tn_width, $tn_height);
        $namefile = "$adrfile[name]";
        imagecopyresized($im1, $im, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);
        $path = "$filesroot/graftemp";
        switch ($format) {
            case "gif":
                $imagnam = "$path/$namefile.temp.gif";
                ImageGif($im1, $imagnam, $quality);
                echo "<p><img src='" . $imagnam . "' alt=''/></p>";
                break;

            case "jpg":
                $imagnam = "$path/$namefile.temp.jpg";
                imageJpeg($im1, $imagnam, $quality);
                echo "<p><img src='" . $imagnam . "' alt=''/></p>";
                break;

            case "jpeg":
                $imagnam = "$path/$namefile.temp.jpg";
                imageJpeg($im1, $imagnam, $quality);
                echo "<p><img src='" . $imagnam . "' alt=''/></p>";

                break;

            case "png":
                $imagnam = "$path/$namefile.temp.png";
                imagePng($im1, $imagnam, $quality);
                echo "<p><img src='" . $imagnam . "' alt=''/></p>";
                break;
        }
        imagedestroy($im);
        imagedestroy($im1);
    }
}

// Ссылка на скачивание файла
$dl_count = !empty($adrfile['ip']) ? intval($adrfile['ip']) : 0;
echo '</p></div><div class="gmenu"><p>' .
    '<h3 class="red">' .
    '<a href="index.php?act=down&amp;id=' . $file . '"><img src="../images/file.gif" border="0" alt=""/></a>&#160;' .
    '<a href="index.php?act=down&amp;id=' . $file . '">' . $lng['download'] . '</a></h3><br />' .
    '<small><span class="gray">' . $lng_dl['size'] . ':</span> <b>' . $siz . '</b> kB<br />';
if ($prg == "zip") {
    echo "<a href='?act=zip&amp;file=" . $file . "'>Xem lưu trữ</a><br/>";
}
echo '<span class="gray">' . $lng_dl['downloads'] . ':</span> <b>' . $dl_count . '</b>';

if (!empty($adrfile['soft'])) {
    $rating = unserialize($adrfile['soft']);
    $rat = $rating['vote'] / $rating['count'];
    $rat = round($rat, 2);
    echo '<br /><span class="gray">' . $lng_dl['average_rating'] . ':</span> <b>' . $rat . '</b>' .
        '<br /><span class="gray">' . $lng_dl['vote_count'] . ':</span> <b>' . $rating['count'] . '</b>';
}

echo '</small></p>';

// Рейтинг файла
echo '<p><form action="index.php?act=rat&amp;id=' . $file . '" method="post"><select name="rat" style="font-size: x-small;">';
for ($i = 10; $i >= 1; --$i) {
    echo "<option>$i</option>";
}
echo '</select><input type="submit" value="' . $lng_dl['rate'] . '" style="font-size: x-small;"/></form></p>';

if ($set['mod_down_comm'] || $rights >= 7) {
    $totalkomm = mysql_result(mysql_query("SELECT COUNT(*) FROM `download` WHERE `type` = 'komm' AND `refid` = '$file'"), 0);
    echo '<p><small><a href="index.php?act=komm&amp;id=' . $file . '">' . $lng['comments'] . '</a> (' . $totalkomm . ')</small></p>';
}

echo '</div>';
if (($rights == 4 || $rights >= 6) && (!empty($_GET['file']))) {
    echo '<p>';
    if ((!in_array($prg, $graf)) && ($prg != "mp3")) {
        echo '<a href="index.php?act=screen&amp;file=' . $file . '">' . $lng_dl['change_screenshot'] . '</a><br/>';
    }
    echo '<a href="index.php?act=opis&amp;file=' . $file . '">' . $lng_dl['change_description'] . '</a><br/>';
    echo '<a href="index.php?act=dfile&amp;file=' . $file . '">' . $lng_dl['delete_file'] . '</a>';
    echo '</p>';
}