<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Restricted access');

    function tagtv($var){
        $ra = null;
        $var = mysql_real_escape_string($var[1]);
        $db = mysql_fetch_array(mysql_query("select * from users where name = '$var'"));
        if(mysql_num_rows(mysql_query("select * from users where name = '$var'")) == 0){
            $ra = '@'.$var.'';
        }else{
            $ra = '<a href="/users/profile.php?user='.$db['id'].'" class="nick"><img src="/avatar/' . $db['id'] . '-24-48.png" style ="width: 24px; height: 24px;" alt="'.$db['name'].'" /> '.functions::nickcolor($db['id']).'</a>';
        }

        return $ra;
    }

class bbcode extends core
{
    /*
    -----------------------------------------------------------------
    Обработка тэгов и ссылок
    -----------------------------------------------------------------
    */
    public static function tags($var)
    {
        $var = self::parse_time($var);               // Обработка тэга времени
        $var = self::highlight_code($var);           // Подсветка кода
        $var = self::soundcloud($var);
        $var = self::googlemap($var);
        $var = preg_replace('#\[br]#si', '<br />', $var); // tag br
        $var = preg_replace('#\[hr]#si', '<hr />', $var); // tag hr
        $var = self::sthoigian($var);
        $var = self::cnick($var);
        $var = self::tagnick($var);
        $var = self::highlight_bb($var);               // Обработка ссылок
        $var = self::highlight_url($var);            // Обработка ссылок
        $var = self::highlight_bbcode_url($var);       // Обработка ссылок в BBcode

        $var = self::zingmp3($var);
        $var = self::base($var);
        $var = preg_replace_callback('#@([a-zA-Z0-9\-\@\*\(\)\?\!\~\_\=\[\]]+)#si', 'tagtv', str_replace("]\n", ']', $var)); // tag tv
        return $var;
    }

    private static function cnick($var)
    {
        return preg_replace_callback(
            '#\[cnick\](.+?)\[\/cnick\]#s',
            function ($matches) {
                if(preg_match('/^[0-9]+$/', $matches[1])){
                    return functions::nickcolor($matches[1]);
                } else {
                    return $matches[1];
                }
            },
            $var
        );
    }

    private static function tagnick($var)
    {
        return preg_replace_callback(
            '#\[\@(.+?)\]#s',
            function ($matches) {
                $var_n = trim($matches[1]);
                $db = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `name` = '".mysql_real_escape_string($var_n)."' "));
                if(!$db){
                    $ra = '[@'.$matches[1].']';
                }else{
                    $ra = '<a href="/users/profile.php?user='.$db['id'].'" class="nick"><img src="/avatar/' . $db['id'] . '-24-48.png" style ="width: 24px; height: 24px;" alt="" /> '.functions::nickcolor($db['id']).'</a>';
                }
                return $ra;
            },
            $var
        );
    }

    private static function sthoigian($var)
    {
        return preg_replace_callback(
            '#\[stime\](.+?)\[\/stime\]#s',
            function ($matches) {
                $shift = (core::$system_set['timeshift'] + core::$user_set['timeshift']) * 3600;
                if (($out = strtotime($matches[1])) !== false) {
                    return functions::display_date($out + $shift);
                } else {
                    return $matches[1];
                }
            },
            $var
        );
    }

    private static function base($var)
    {
        return preg_replace_callback(
            '#\[base\](.+?)\[\/base\]#s',
            function ($matches) {
                return $matches[1];
            },
            $var
        );
    }


    private static function zingmp3($var)
    {
        return preg_replace_callback(
            '#\[zingmp3\](.+?)\[\/zingmp3\]#s',
            function ($matches) {
                $link = $matches[1];
                $arr_z = @explode("/",$link);
                $id_z = @str_replace(".html","",$arr_z[count($arr_z)-1]);
                $link_z = "http://api.mp3.zing.vn/api/mobile/song/getsonginfo?requestdata={\"id\":\"$id_z\"}";
                $data_z = @json_decode(@file_get_contents($link_z), true);
                //data
                $casi = $data_z['artist']; // Tên ca sĩ
                $baihat = $data_z['title']; // Tên bài hát
                $linkb = $data_z['source']['128']; // Link bài hát
                $tai = $data_z['link_download']['128'];                 // Tải bài hát
                $bhm = $data_z['link_download']['320']; // Tải bài hát 320kbs
                $lossless = $data_z['link_download']['lossless']; // Tải bài hát lossless
                $avatar = $data_z['thumbnail'];
                if(!$linkb && !$baihat && !$casi){
                    return '[zingmp3]'.$link.'[/zingmp3]';
                }else{
                    $data = '<div class="phieubac-media">Song - PhieuBac.Ga</div><div class="phieubac-media-list"><img onclick="StartSong(this)" src="'.(empty($avatar) ? '/images/ms.png' : 'http://image.mp3.zdn.vn/thumb/165_165/'.$avatar).'" width="80" height="80" class="playButton"><fieldset class="lbl-group" style="border: 3px solid #ddd;"><div style="color: #5bcf80;"><i class="fa fa-play"></i></div><label>'.$baihat.'</label></fieldset><fieldset class="lbl-group switch"><div style="color: #ff014a;"><i class="fa fa-heart"></i></div><label>'.$casi.'</label></fieldset><br /><span style="display:none;" id="norber">'.$linkb.'</span><span style="display:none;" id="xftitle">'.$baihat.'</span><div class="mp3Placeholder"></div></div>';

                    return $data;
                }
            },
            $var
        );
    }

    private static function soundcloud($var)
    {
        return preg_replace_callback(
            '#\[soundcloud\](.+?)\[\/soundcloud\]#s',
            function ($matches) {
                $f = $matches[1];
                if (preg_match('/(soundcloud\.com)/', $f)) {
                    return '<div class="soundcloud-wrapper" align="center">
                        <iframe frameborder="0" src="https://w.soundcloud.com/player/?url=' . $f . '&amp;color=f07b22" width="100%"></iframe>
                    </div>';
                }else{
                    return $matches[1];
                }
            },
            $var
        );
    }

    private static function googlemap($var)
    {
        return preg_replace_callback(
            '#\[map\](.+?)\[\/map\]#s',
            function ($matches) {
                $f = $matches[1];
                $data = html_entity_decode($f, ENT_QUOTES, 'UTF-8');
                $ok = urlencode($data);
                    return '<div class="google-map-viewer-wrapper" align="center">
                        <img src="http://maps.googleapis.com/maps/api/staticmap?center='.$data.'&zoom=auto&size=600x300&maptype=roadmap&markers=color:red%7C'.$data.'" width="100%" alt="'.$f.'">
                    </div>';
            },
            $var
        );
    }

    /**
     * Обработка тэга [time]
     *
     * @param string $var
     * @return string
     */
    private static function parse_time($var)
    {
        return preg_replace_callback(
            '#\[time\](.+?)\[\/time\]#s',
            function ($matches) {
                $shift = (core::$system_set['timeshift'] + core::$user_set['timeshift']) * 3600;
                if (($out = strtotime($matches[1])) !== false) {
                    return date("d.m.Y / H:i", $out + $shift);
                } else {
                    return $matches[1];
                }
            },
            $var
        );
    }

    /**
     * Парсинг ссылок
     * За основу взята доработанная функция от форума phpBB 3.x.x
     *
     * @param $text
     * @return mixed
     */
    public static function highlight_url($text)
    {
        if (!function_exists('url_callback')) {
            function url_callback($type, $whitespace, $url, $relative_url)
            {
                $orig_url = $url;
                $orig_relative = $relative_url;
                $url = htmlspecialchars_decode($url);
                $relative_url = htmlspecialchars_decode($relative_url);
                $text = '';
                $chars = array('<', '>', '"');
                $split = false;
                foreach ($chars as $char) {
                    $next_split = strpos($url, $char);
                    if ($next_split !== false) {
                        $split = ($split !== false) ? min($split, $next_split) : $next_split;
                    }
                }
                if ($split !== false) {
                    $url = substr($url, 0, $split);
                    $relative_url = '';
                } else {
                    if ($relative_url) {
                        $split = false;
                        foreach ($chars as $char) {
                            $next_split = strpos($relative_url, $char);
                            if ($next_split !== false) {
                                $split = ($split !== false) ? min($split, $next_split) : $next_split;
                            }
                        }
                        if ($split !== false) {
                            $relative_url = substr($relative_url, 0, $split);
                        }
                    }
                }
                $last_char = ($relative_url) ? $relative_url[strlen($relative_url) - 1] : $url[strlen($url) - 1];
                switch ($last_char) {
                    case '.':
                    case '?':
                    case '!':
                    case ':':
                    case ',':
                        $append = $last_char;
                        if ($relative_url) {
                            $relative_url = substr($relative_url, 0, -1);
                        } else {
                            $url = substr($url, 0, -1);
                        }
                        break;

                    default:
                        $append = '';
                        break;
                }
                $short_url = (mb_strlen($url) > 40) ? mb_substr($url, 0, 30) . ' ... ' . mb_substr($url, -5) : $url;
                switch ($type) {
                    case 1:
                        $relative_url = preg_replace('/[&?]sid=[0-9a-f]{32}$/', '', preg_replace('/([&?])sid=[0-9a-f]{32}&/', '$1', $relative_url));
                        $url = $url . '/' . $relative_url;
                        $text = $relative_url;
                        if (!$relative_url) {
                            return $whitespace . $orig_url . '/' . $orig_relative;
                        }
                        break;

                    case 2:
                        $url2 = htmlspecialchars($url);
                        $append2 = htmlspecialchars($append);
                        $name = preg_replace('/([^A-Za-z0-9_\-\.]+)/i', '', $url2);
                        $url_ext = $url2;
                        if (($qs_ext_pos = strrpos($url2, '?')) !== false) {
                            $url_ext = substr($url2, 0, $qs_ext_pos);
                        }
                        $dot_ext_pos = strrpos($url_ext, '.');
                        $url_ext = strtolower(substr($url_ext, $dot_ext_pos + 1, strlen($url_ext) - $dot_ext_pos));
                        if (preg_match('/^(jpg|jpeg|png|gif)$/', $url_ext)) {
                            $redata = 1;
                            $nameimg = basename($url2);
                            $doiten = str_replace($nameimg, rawurlencode($nameimg), $url2);
                            if(GetImageSize($url2)){
                                $GetImageSize = GetImageSize($url2);
                                $iok = $url2;
                            }else if(GetImageSize($doiten)){
                                $GetImageSize = GetImageSize($doiten);
                                $iok = $doiten;
                            }
                            if($GetImageSize){
                                $udata = $whitespace . '<div style="text-align: center; padding: 0; margin: 0;"><a href="'.$iok.'"><img src="'.$iok.'" alt="'.basename($url2).'" style="max-width: 100%;" /></a></div>' . $append2;
                            }else{
                                $redata = 2;
                                $text = $short_url;
                                if (!isset(core::$user_set['direct_url']) || !core::$user_set['direct_url']) {
                                    $url = core::$system_set['homeurl'] . '/go.php?url=' . rawurlencode($url);
                                }
                            }

                        }else if (preg_match('/^(3gp|mp4|flv|avi)$/', $url_ext)) {
                            $redata = 1;
                    $random = mt_rand(99999, 111111);
                    $base = basename($url2);
                    $fomatfile = functions::format($base);
                    $d1 = '<script>$(document).ready(function() {$("#myVideo'.$random.'").html5_video({source : {"video/'.$fomatfile.'"  : "'.$url2.'",},title: "Player - PhieuBac.Ga",color: "#3a7d57",width: false,buffering_text: "Đang đệm...",autoplay: false,play_control: true,time_indicator: true,volume_control: true,share_control: true,fullscreen_control: true,dblclick_fullscreen: true,volume: 0.7,show_controls_on_load: true,show_controls_on_pause: true,});});</script>';

                    $udata = $whitespace.$d1.'<div class="phieubac-media">Player - PhieuBac.Ga</div><div class="phieubac-media-list" style="padding: 15px;"><div style="box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);" id="myVideo'.$random.'"></div></div>'.$append2;
                        }else if (preg_match('/^(mp3|mid|midi|wav|aac)$/', $url_ext)) {
                            $redata = 1;
                            $random = mt_rand(99999, 111111);
                    $source = @file_get_contents($url2);
                    $ten = $random.'_'.basename($url2);
                    $rc = @file_put_contents($ten, $source);
                    $getID3 = new getID3;
                    $ThisFileInfo = $getID3->analyze($ten);
                    getid3_lib::CopyTagsToComments($ThisFileInfo);
                    $fileformat = $ThisFileInfo['fileformat'];
                    if($rc){
                        $fname = basename($url2);
                         $c_title = $ThisFileInfo['comments']['title']['0'];
                         $c_artist = $ThisFileInfo['comments']['artist']['0'];
                        $picture = $ThisFileInfo['comments']['picture']['0']['data'];
                        $picture_mime = $ThisFileInfo['comments']['picture']['0']['image_mime'];
                        @unlink($ten);

                        $udata = $whitespace.'<div class="phieubac-media">Song - PhieuBac.Ga</div><div class="phieubac-media-list"><img onclick="StartSong(this)" src="'.(!$picture ? '/images/ms.png' : 'data:'.$picture_mime.';base64,'.base64_encode($picture)).'" width="80" height="80" class="playButton"><fieldset class="lbl-group" style="border: 3px solid #ddd;"><div style="color: #5bcf80;"><i class="fa fa-play"></i></div><label>'.(!empty($c_title) ? $c_title : $fname).'</label></fieldset>'.(!empty($c_artist) ? '<fieldset class="lbl-group switch"><div style="color: #ff014a;"><i class="fa fa-heart"></i></div><label>'.$c_artist.'</label></fieldset>' : '').'<br /><span style="display:none;" id="norber">'.htmlspecialchars($url).'</span><span style="display:none;" id="xftitle">'.(!empty($c_title) ? $c_title : $fname).'</span><div class="mp3Placeholder"></div></div>'.$append2;
                    } else {
                        @unlink($ten);
                            $redata = 2;
                            $text = $short_url;
                            if (!isset(core::$user_set['direct_url']) || !core::$user_set['direct_url']) {
                                $url = core::$system_set['homeurl'] . '/go.php?url=' . rawurlencode($url);
                            }
                    }
                        }else if(preg_match('/(http|https)\:\/\/(www\.)?(youtube\.com|youtu\.be)/', $url2)){
                            $redata = 1;
                            $values = explode('=', $url2);
                            $valuesto = explode('&', $values[1]);
 
                            $udata = $whitespace.'<div style="text-align: center;"><iframe src="http://www.youtube.com/embed/'. $valuesto[0] . '?ap=%2526fmt%3D18&disablekb=1&autohide=1&theme=light&color=red&rel=0" style="width: 100%; height: 480px; border: 0;" allowfullscreen></iframe></div>'.$append2;
                        }else{
                            $redata = 2;
                            $text = $short_url;
                            if (!isset(core::$user_set['direct_url']) || !core::$user_set['direct_url']) {
                                $url = core::$system_set['homeurl'] . '/go.php?url=' . rawurlencode($url);
                            }
                        }
                        break;

                    case 4:
                        $text = $short_url;
                        $url = 'mailto:' . $url;
                        break;
                }
                if($type == 2){
                    if($redata == 1){
                        return $udata;
                    }
                }
                $url = htmlspecialchars($url);
                $text = htmlspecialchars($text);
                $append = htmlspecialchars($append);

                return $whitespace . '<a target="_blank" href="' . $url . '">' . $text . '</a>' . $append;
            }
        }

        // Обработка внутренних ссылок
        $text = preg_replace_callback(
            '#(^|[\n\t (>.])(' . preg_quote(core::$system_set['homeurl'],
                '#') . ')/((?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@|]+|%[\dA-F]{2})*(?:/(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@|]+|%[\dA-F]{2})*)*(?:\?(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@/?|]+|%[\dA-F]{2})*)?(?:\#(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@/?|]+|%[\dA-F]{2})*)?)#iu',
            function ($matches) {
                return url_callback(1, $matches[1], $matches[2], $matches[3]);
            },
            $text
        );

        // Обработка обычных ссылок типа xxxx://aaaaa.bbb.cccc. ...
        $text = preg_replace_callback(
            '#(^|[\n\t (>.])([a-z][a-z\d+]*:/{2}(?:(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@|]+|%[\dA-F]{2})+|[0-9.]+|\[[a-zа-яё0-9.]+:[a-zа-яё0-9.]+:[a-zа-яё0-9.:]+\])(?::\d*)?(?:/(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@|]+|%[\dA-F]{2})*)*(?:\?(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@/?|]+|%[\dA-F]{2})*)?(?:\#(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@/?|]+|%[\dA-F]{2})*)?)#iu',
            function ($matches) {
                return url_callback(2, $matches[1], $matches[2], '');
            },
            $text
        );

        return $text;
    }

    /*
    -----------------------------------------------------------------
    Удаление bbCode из текста
    -----------------------------------------------------------------
    */
    static function notags($var = '')
    {
        $var = preg_replace('#\[color=(.+?)\](.+?)\[/color]#si', '$2', $var);
        $var = preg_replace('#\[code=(.+?)\](.+?)\[/code]#si', '$2', $var);
        $var = preg_replace('!\[bg=(#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z\-]+)](.+?)\[/bg]!is', '$2', $var);
        $var = preg_replace('#\[spoiler=(.+?)\]#si', '$2', $var);
        $replace = array(
            '[br]' => '',
            '[hr]' => '',
            '[small]' => '',
            '[/small]' => '',
            '[big]' => '',
            '[/big]' => '',
            '[green]' => '',
            '[/green]' => '',
            '[red]' => '',
            '[/red]' => '',
            '[blue]' => '',
            '[/blue]' => '',
            '[b]' => '',
            '[/b]' => '',
            '[i]' => '',
            '[/i]' => '',
            '[u]' => '',
            '[/u]' => '',
            '[s]' => '',
            '[/s]' => '',
            '[quote]' => '',
            '[/quote]' => '',
            '[php]' => '',
            '[/php]' => '',
            '[c]' => '',
            '[/c]' => '',
            '[*]' => '',
            '[/*]' => ''
        );

        return strtr($var, $replace);
    }

    /*
    -----------------------------------------------------------------
    Подсветка кода
    -----------------------------------------------------------------
    */
    private static function highlight_code($var)
    {
        $var = preg_replace_callback('#\[php\](.+?)\[\/php\]#s', 'self::phpCodeCallback', $var);
        $var = preg_replace_callback('#\[code=(.+?)\](.+?)\[\/code]#is', 'self::codeCallback', $var);

        return $var;
    }

    private static $geshi;

    private static function phpCodeCallback($code)
    {
        return self::codeCallback(array(1 => 'php', 2 => $code[1]));
    }

    private static function codeCallback($code)
    {
        $parsers = array(
            'php'  => 'php',
            'css'  => 'css',
            'html' => 'html5',
            'js'   => 'javascript',
            'sql'  => 'sql',
            'xml'  => 'xml',
        );

        $parser = isset($code[1]) && isset($parsers[$code[1]]) ? $parsers[$code[1]] : 'php';

        if (null === self::$geshi) {
            require_once 'geshi.php';
            self::$geshi = new \GeSHi;
            self::$geshi->set_link_styles(GESHI_LINK, 'text-decoration: none');
            self::$geshi->set_link_target('_blank');
            self::$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 2);
            self::$geshi->set_line_style('background: rgba(255, 255, 255, 0.6)', 'background: rgba(255, 255, 255, 0.35)', false);
            self::$geshi->set_code_style('padding-left: 6px; white-space: pre-wrap');
        }

        self::$geshi->set_language($parser);
        $php = strtr($code[2], array('<br />' => ''));
        $php = html_entity_decode(trim($php), ENT_QUOTES, 'UTF-8');
        self::$geshi->set_source($php);
        $data = self::$geshi->parse_code();
        return '<div class="phpcode" style="overflow-x: auto">' . $data . '</div>';
    }

    /*
    -----------------------------------------------------------------
    Обработка URL в тэгах BBcode
    -----------------------------------------------------------------
    */
    private static function highlight_bbcode_url($var)
    {
        if (!function_exists('process_url')) {
            function process_url($url)
            {
                $home = parse_url(core::$system_set['homeurl']);
                $tmp = parse_url($url[1]);
                if ($home['host'] == $tmp['host'] || isset(core::$user_set['direct_url']) && core::$user_set['direct_url']) {
                    return '<a href="' . $url[1] . '">' . $url[2] . '</a>';
                } else {
                    return '<a href="' . core::$system_set['homeurl'] . '/go.php?url=' . urlencode(htmlspecialchars_decode($url[1])) . '">' . $url[2] . '</a>';
                }
            }
        }

        return preg_replace_callback('~\\[url=(https?://.+?)\\](.+?)\\[/url\\]~', 'process_url', $var);
    }

    /*
    -----------------------------------------------------------------
    Обработка bbCode
    -----------------------------------------------------------------
    */
    private static function highlight_bb($var)
    {
        // Список поиска
        $search = array(
            '#\[b](.+?)\[/b]#is', // Жирный
            '#\[i](.+?)\[/i]#is', // Курсив
            '#\[u](.+?)\[/u]#is', // Подчеркнутый
            '#\[s](.+?)\[/s]#is', // Зачеркнутый
            '#\[small](.+?)\[/small]#is', // Маленький шрифт
            '#\[big](.+?)\[/big]#is', // Большой шрифт
            '#\[red](.+?)\[/red]#is', // Красный
            '#\[green](.+?)\[/green]#is', // Зеленый
            '#\[blue](.+?)\[/blue]#is', // Синий
            '!\[color=(#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z\-]+)](.+?)\[/color]!is', // Цвет шрифта
            '!\[bg=(#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z\-]+)](.+?)\[/bg]!is', // Цвет фона
            '#\[(quote|c)](.+?)\-(.+?)\-(.+?)\-(.+?)\[/(quote|c)]#is', // Цитата
            '#\[(quote|c)](.+?)\[/(quote|c)]#is', // c/q
            '#\[(quote|c)=(.+?)\](.+?)\[/(quote|c)]#is', // c/q td
            '#\[\*](.+?)\[/\*]#is', // Список
            '#\[spoiler=(.+?)](.+?)\[/spoiler]#is' // Спойлер
        );
        // Список замены
        $replace = array(
            '<strong>$1</strong>',
            // Жирный
            '<span style="font-style:italic">$1</span>',
            // Курсив
            '<span style="text-decoration:underline">$1</span>',
            // Подчеркнутый
            '<span style="text-decoration:line-through">$1</span>',
            // Зачеркнутый
            '<span class="font-xs">$1</span>',
            // Маленький шрифт
            '<span style="font-size:large">$1</span>',
            // Большой шрифт
            '<span style="color:red">$1</span>',
            // Красный
            '<span style="color:green">$1</span>',
            // Зеленый
            '<span style="color:blue">$1</span>',
            // Синий
            '<span style="color:$1">$2</span>',
            // Цвет шрифта
            '<span style="background-color:$1">$2</span>',
            // Цвет фона
            '<div class="quote"><div class="qhead"><strong><span class="font-xs">$2</span></strong> <span class="gray font-xs">($3)</span><div style="float: right; display: inline;"><a rel="nofollow" href="/forum/post-$4.html" title="Link to post">[#]</a></div></div><div class="qcontent">$5</div></div>', // Цитата
            '<div class="quote"><div class="qcontent">$2</div></div>',
            '<div class="quote"><div class="qhead"><strong>$2</strong> đã nói.</div><div class="qcontent">$3</div></div>',
            // Цитата
            '<span class="bblist">$1</span>',
            // Список
            '<div><div class="spoilerhead" style="cursor:pointer;" onclick="var _n=this.parentNode.getElementsByTagName(\'div\')[1];if(_n.style.display==\'none\'){_n.style.display=\'\';}else{_n.style.display=\'none\';}">$1 (+/-)</div><div class="spoilerbody" style="display:none">$2</div></div>'
            // Спойлер
        );

        return preg_replace($search, $replace, $var);
    }

    /*
    -----------------------------------------------------------------
    Панель кнопок bbCode (для компьютеров)
    -----------------------------------------------------------------
    */
    public static function auto_bb($form, $field)
    {
        $colors = array(
            'ffffff',
            'bcbcbc',
            '708090',
            '6c6c6c',
            '454545',
            'fcc9c9',
            'fe8c8c',
            'fe5e5e',
            'fd5b36',
            'f82e00',
            'ffe1c6',
            'ffc998',
            'fcad66',
            'ff9331',
            'ff810f',
            'd8ffe0',
            '92f9a7',
            '34ff5d',
            'b2fb82',
            '89f641',
            'b7e9ec',
            '56e5ed',
            '21cad3',
            '03939b',
            '039b80',
            'cac8e9',
            '9690ea',
            '6a60ec',
            '4866e7',
            '173bd3',
            'f3cafb',
            'e287f4',
            'c238dd',
            'a476af',
            'b53dd2'
        );
        $font_color = '';
        $bg_color = '';

        foreach ($colors as $value) {
            $font_color .= '<a href="javascript:tag(\'[color=#' . $value . ']\', \'[/color]\'); show_hide(\'#color\');" style="background-color:#' . $value . ';"></a>';
            $bg_color .= '<a href="javascript:tag(\'[bg=#' . $value . ']\', \'[/bg]\'); show_hide(\'#bg\');" style="background-color:#' . $value . ';"></a>';
        }

        // Смайлы
        $smileys = !empty(self::$user_data['smileys']) ? unserialize(self::$user_data['smileys']) : '';

        if (!empty($smileys)) {
            $res_sm = '';
            $bb_smileys = '<small><a href="' . self::$system_set['homeurl'] . '/pages/faq.php?act=my_smileys">' . self::$lng['edit_list'] . '</a></small><br />';
            foreach ($smileys as $value) {
                $res_sm .= '<a href="javascript:tag(\':' . $value . '\', \':\'); show_hide(\'#sm\');">:' . $value . ':</a> ';
            }
            $bb_smileys .= functions::smileys($res_sm, self::$user_data['rights'] >= 1 ? 1 : 0);
        } else {
            $bb_smileys = '<small><a href="' . self::$system_set['homeurl'] . '/pages/faq.php?act=smileys">' . self::$lng['add_smileys'] . '</a></small>';
        }

        // Код
        $code = array(
            'php',
            'css',
            'js',
            'html',
            'sql',
            'xml',
        );

        $codebtn = '';
        foreach ($code as $val) {
            $codebtn .= '<a href="javascript:tag(\'[code=' . $val . ']\', \'[/code]\'); show_hide(\'#code\');">' . strtoupper($val) . '</a>';
        }

        $out = '<style>
.codepopup {margin-top: 3px;}
.codepopup a {
border: 1px solid #a7a7a7;
border-radius: 3px;
background-color: #dddddd;
color: black;
font-weight: bold;
padding: 2px 6px 2px 6px;
display: inline-block;
margin-right: 6px;
margin-bottom: 3px;
text-decoration: none;
}
</style>
            <script language="JavaScript" type="text/javascript">
            function tag(text1, text2) {
              if ((document.selection)) {
                document.' . $form . '.' . $field . '.focus();
                document.' . $form . '.document.selection.createRange().text = text1+document.' . $form . '.document.selection.createRange().text+text2;
              } else if(document.forms[\'' . $form . '\'].elements[\'' . $field . '\'].selectionStart!=undefined) {
                var element = document.forms[\'' . $form . '\'].elements[\'' . $field . '\'];
                var str = element.value;
                var start = element.selectionStart;
                var length = element.selectionEnd - element.selectionStart;
                element.value = str.substr(0, start) + text1 + str.substr(start, length) + text2 + str.substr(start + length);
              } else {
                document.' . $form . '.' . $field . '.value += text1+text2;
              }
            }
            function show_hide(elem) {
                input_wrapper = $(elem);
                group_id = input_wrapper.attr(\'data-group\');
                if (input_wrapper.css(\'display\') == "none") {
                    $(\'.input-wrapper[data-group=\' + group_id + \']\')
                        .slideUp()
                        .find(\'input\').val(\'\').show()
                        .end()
                        .find(\'.result-container\').remove()
                        .end()
                        .find(\'.remove-btn\').remove();
                    input_wrapper.slideDown();
                } else {
                    $(\'.input-wrapper[data-group=\' + group_id + \']\').slideUp();
                }
            }
            </script>
            <div class="more-wrapper"><ul class="redactor_toolbar"><li class="redactor_btn_group"><span class="option" onclick="tag(\'[b]\', \'[/b]\')"><img src="' . self::$system_set['homeurl'] . '/images/bb/bold.gif" alt="b" title="' . self::$lng['tag_bold'] . '" border="0"/></span>
            <span class="option" onclick="tag(\'[i]\', \'[/i]\')"><img src="' . self::$system_set['homeurl'] . '/images/bb/italics.gif" alt="i" title="' . self::$lng['tag_italic'] . '" border="0"/></span>
            <span class="option" onclick="tag(\'[u]\', \'[/u]\')"><img src="' . self::$system_set['homeurl'] . '/images/bb/underline.gif" alt="u" title="' . self::$lng['tag_underline'] . '" border="0"/></span>
            <span class="option" onclick="tag(\'[s]\', \'[/s]\')"><img src="' . self::$system_set['homeurl'] . '/images/bb/strike.gif" alt="s" title="' . self::$lng['tag_strike'] . '" border="0"/></span></li><li class="redactor_btn_group">
            <span class="option" onclick="tag(\'[*]\', \'[/*]\')"><img src="' . self::$system_set['homeurl'] . '/images/bb/list.gif" alt="s" title="' . self::$lng['tag_list'] . '" border="0"/></span>
            <span class="option" onclick="tag(\'[spoiler=]\', \'[/spoiler]\');"><img src="' . self::$system_set['homeurl'] . '/images/bb/sp.gif" alt="spoiler" title="Спойлер" border="0"/></span>
            <span class="option" onclick="tag(\'[c]\', \'[/c]\')"><img src="' . self::$system_set['homeurl'] . '/images/bb/quote.gif" alt="quote" title="' . self::$lng['tag_quote'] . '" border="0"/></span>
            <span class="option" onclick="tag(\'[url=]\', \'[/url]\')"><img src="' . self::$system_set['homeurl'] . '/images/bb/link.gif" alt="url" title="' . self::$lng['tag_link'] . '" border="0"/></span>
            </li><li class="redactor_btn_group">
            <span class="option" onclick="toggleMediaGroup(\'.story-publisher-box #code\');"><img src="' . self::$system_set['homeurl'] . '/images/bb/php.gif" title="' . Code . '" border="0"/></span>
            <span class="option" onclick="toggleMediaGroup(\'.story-publisher-box #color\');"><img src="' . self::$system_set['homeurl'] . '/images/bb/color.gif" title="' . self::$lng['color_text'] . '" border="0"/></span>
            <span class="option" onclick="toggleMediaGroup(\'.story-publisher-box #bg\');"><img src="' . self::$system_set['homeurl'] . '/images/bb/color_bg.gif" title="' . self::$lng['color_bg'] . '" border="0"/></span>';

        if (self::$user_id) {
            $out .= '<span class="option" onclick="toggleMediaGroup(\'.story-publisher-box #sm\');"><img src="' . self::$system_set['homeurl'] . '/images/bb/smileys.gif" alt="sm" title="' . self::$lng['smileys'] . '" border="0"/></span>';
        }
        $out .= '</li><li class="redactor_btn_group">
                    <span class="option" onclick="toggleMediaGroup(\'.story-publisher-box .google-map-wrapper\');"><i class="fa fa-map-marker fbleft"></i></span>
                    <span class="option" onclick="toggleMediaGroup(\'.soundcloud-search-wrapper\');"><i class="fa fa-music fbleft"></i></span>
                    <span class="option" onclick="toggleMediaGroup(\'.youtube-search-wrapper\');"><i class="fa fa-film fbleft"></i></span>
        </li></ul></div>';

        $out .= '<div class="input-wrapper google-map-wrapper" data-group="B"><i class="fa fa-map-marker fbleft"></i>
                    <input class="gmap-input" type="text" value="" placeholder="Bạn đang ở đâu?" data-placeholder="Bạn đang ở đâu?" name="google-map">
                </div>

                <div class="input-wrapper soundcloud-search-wrapper" data-group="A">
                    <i class="fa fa-music fbleft"></i>

                    <input class="soundcloud-input" type="text" onkeyup="searchSoundcloud(this.value);" value="" placeholder="Tìm bài hát?" data-placeholder="Tìm bài hát?">

                    <div class="input-result-wrapper"></div>
                </div>

                <div class="input-wrapper youtube-search-wrapper" data-group="A">
                    <i class="fa fa-film fbleft"></i>

                    <input class="youtube-input" type="text" onkeyup="searchYoutube(this.value);" value="" placeholder="Tìm video?" data-placeholder="Tìm video?">
                    <div class="input-result-wrapper"></div>
                </div>';

        if (self::$user_id) {
            $out .= '<div id="sm" class="input-wrapper" data-group="E"><table><tr><td>' . $bb_smileys . '</td></tr></table></div>';
        }


        $out .= '<div id="code" class="input-wrapper" data-group="D"><div class="codepopup">' . $codebtn . '</div></div>' .
            '<div id="color" class="input-wrapper" data-group="C"><div class="bbpopup"><img src="' . self::$system_set['homeurl'] . '/images/bb/color.gif" title="' . self::$lng['color_text'] . '" border="0"/> | ' . $font_color . '</div></div>' .
            '<div id="bg" class="input-wrapper" data-group="C"><div class="bbpopup"><img src="' . self::$system_set['homeurl'] . '/images/bb/color_bg.gif" title="' . self::$lng['color_bg'] . '" border="0"/> | ' . $bg_color . '</div></div>';

        return $out;
    }
}