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

class functions extends core
{
    /**
     * Антифлуд
     * Режимы работы:
     *   1 - Адаптивный
     *   2 - День / Ночь
     *   3 - День
     *   4 - Ночь
     *
     * @return int|bool
     */
    public static function antiflood()
    {
        $default = array(
            'mode' => 2,
            'day' => 10,
            'night' => 30,
            'dayfrom' => 10,
            'dayto' => 22
        );
        $af = isset(self::$system_set['antiflood']) ? unserialize(self::$system_set['antiflood']) : $default;
        switch ($af['mode']) {
            case 1:
                // Адаптивный режим
                $adm = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `rights` > 0 AND `lastdate` > " . (time() - 300)), 0);
                $limit = $adm > 0 ? $af['day'] : $af['night'];
                break;
            case 3:
                // День
                $limit = $af['day'];
                break;
            case 4:
                // Ночь
                $limit = $af['night'];
                break;
            default:
                // По умолчанию день / ночь
                $c_time = date('G', time());
                $limit = $c_time > $af['day'] && $c_time < $af['night'] ? $af['day'] : $af['night'];
        }
        if (self::$user_rights > 0)
            $limit = 4; // Для Администрации задаем лимит в 4 секунды
        $flood = self::$user_data['lastpost'] + $limit - time();
        if ($flood > 0)
            return $flood;
        else
            return FALSE;
    }

    /**
     * Маскировка ссылок в тексте
     *
     * @param $var
     *
     * @return string
     */
    public static function antilink($var)
    {
        $var = preg_replace('~\\[url=(https?://.+?)\\](.+?)\\[/url\\]|(https?://(www.)?[0-9a-z\.-]+\.[0-9a-z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', '###', $var);
        $replace = array(
            '.ru' => '***',
            '.com' => '***',
            '.biz' => '***',
            '.cn' => '***',
            '.in' => '***',
            '.net' => '***',
            '.org' => '***',
            '.info' => '***',
            '.mobi' => '***',
            '.wen' => '***',
            '.kmx' => '***',
            '.h2m' => '***'
        );

        return strtr($var, $replace);
    }

    /**
     * Фильтрация строк
     *
     * @param string $str
     *
     * @return string
     */
    public static function checkin($str)
    {
        if (function_exists('iconv')) {
            $str = iconv("UTF-8", "UTF-8", $str);
        }

        // Фильтруем невидимые символы
        $str = preg_replace('/[^\P{C}\n]+/u', '', $str);

        return trim($str);
    }

    /**
     * Обработка текстов перед выводом на экран
     *
     * @param string $str
     * @param int $br   Параметр обработки переносов строк
     *                     0 - не обрабатывать (по умолчанию)
     *                     1 - обрабатывать
     *                     2 - вместо переносов строки вставляются пробелы
     * @param int $tags Параметр обработки тэгов
     *                     0 - не обрабатывать (по умолчанию)
     *                     1 - обрабатывать
     *                     2 - вырезать тэги
     *
     * @return string
     */
    public static function checkout($str, $br = 0, $tags = 0)
    {
        $str = htmlentities(trim($str), ENT_QUOTES, 'UTF-8');
        if ($br == 1) {
            // Вставляем переносы строк
            $str = nl2br($str);
        } elseif ($br == 2) {
            $str = str_replace("\r\n", ' ', $str);
        }
        if ($tags == 1) {
            $str = bbcode::tags($str);
        } elseif ($tags == 2) {
            $str = bbcode::notags($str);
        }

        return trim($str);
    }

    /**
     * Показ различных счетчиков внизу страницы
     */
    public static function display_counters()
    {
        global $headmod;
        $req = mysql_query("SELECT * FROM `cms_counters` WHERE `switch` = '1' ORDER BY `sort` ASC");
        if (mysql_num_rows($req) > 0) {
            while (($res = mysql_fetch_array($req)) !== FALSE) {
                $link1 = ($res['mode'] == 1 || $res['mode'] == 2) ? $res['link1'] : $res['link2'];
                $link2 = $res['mode'] == 2 ? $res['link1'] : $res['link2'];
                $count = ($headmod == 'mainpage') ? $link1 : $link2;
                if (!empty($count))
                    echo $count;
            }
        }
    }

    /**
     * Показываем дату с учетом сдвига времени
     *
     * @param int $var Время в Unix формате
     *
     * @return string Отформатированное время
     */
    public static function display_date($var)
    {
        $shift = (self::$system_set['timeshift'] + self::$user_set['timeshift']) * 3600;
        if (date("N", $var + $shift) == 1) {
            $thu_time = 'Thứ Hai';
        }else if (date("N", $var + $shift) == 2) {
            $thu_time = 'Thứ Ba';
        }else if (date("N", $var + $shift) == 3) {
            $thu_time = 'Thứ Tư';
        }else if (date("N", $var + $shift) == 4) {
            $thu_time = 'Thứ Năm';
        }else if (date("N", $var + $shift) == 5) {
            $thu_time = 'Thứ Sáu';
        }else if (date("N", $var + $shift) == 6) {
            $thu_time = 'Thứ Bảy';
        }else if (date("N", $var + $shift) == 7) {
            $thu_time = 'Chủ Nhật';
        }
        if (date('Y', $var) == date('Y', time())) {
            $jun = round((time()-$var)/60);
            if ($jun < 1) {
                return 'Vừa xong';
            }else if($jun >= 1 && $jun < 60){
                return $jun . ' phút trước';
            }else{
                if (date('z', $var + $shift) == date('z', time() + $shift)) {
                    return self::$lng['today'] . ' lúc ' . date("H:i", $var + $shift);
                }else if (date('z', $var + $shift) == date('z', time() + $shift) - 1) {
                    return self::$lng['yesterday'] . ' lúc ' . date("H:i", $var + $shift);
                }else if (date('z', $var + $shift) == date('z', time() + $shift) - 2) {
                    return $thu_time . ' lúc ' . date("H:i", $var + $shift);
                }else if (date('z', $var + $shift) == date('z', time() + $shift) - 3) {
                    return $thu_time . ' lúc ' . date("H:i", $var + $shift);
                }else{
                    return date("j", $var + $shift) . ' tháng ' . date("n", $var + $shift) . ' lúc ' . date("H:i", $var + $shift);
                }
            }
        }

        return $thu_time . ', '.date("j", $var + $shift) . ' tháng ' . date("n", $var + $shift) . ' ' . date("Y", $var + $shift);
    }

    /**
     * Сообщения об ошибках
     *
     * @param string|array $error Сообщение об ошибке (или массив с сообщениями)
     * @param string $link  Необязательная ссылка перехода
     *
     * @return bool|string
     */
    public static function display_error($error = '', $link = '')
    {
        if (!empty($error)) {
            return '<div class="rmenu"><p><b>' . self::$lng['error'] . '!</b><br />' .
            (is_array($error) ? implode('<br />', $error) : $error) . '</p>' .
            (!empty($link) ? '<p>' . $link . '</p>' : '') . '</div>';
        } else {
            return FALSE;
        }
    }

    /**
     * Отображение различных меню
     *
     * @param array $val
     * @param string $delimiter Разделитель между пунктами
     * @param string $end_space Выводится в конце
     *
     * @return string
     */
    public static function display_menu($val = array(), $delimiter = ' | ', $end_space = '')
    {
        return implode($delimiter, array_diff($val, array(''))) . $end_space;
    }

    /**
     * Постраничная навигация
     * За основу взята доработанная функция от форума SMF 2.x.x
     *
     * @param string $url
     * @param int $start
     * @param int $total
     * @param int $kmess
     *
     * @return string
     */
    public static function display_pagination($url, $start, $total, $kmess)
    {
        $neighbors = 2;
        if ($start >= $total)
            $start = max(0, $total - (($total % $kmess) == 0 ? $kmess : ($total % $kmess)));
        else
            $start = max(0, (int)$start - ((int)$start % (int)$kmess));
        $base_link = '<a href="' . strtr($url, array('%' => '%%')) . 'page=%d' . '"><span class="pagenav">%s</span></a>';
        $out[] = $start == 0 ? '' : sprintf($base_link, $start / $kmess, '&lt;&lt;');
        if ($start > $kmess * $neighbors)
            $out[] = sprintf($base_link, 1, '1');
        if ($start > $kmess * ($neighbors + 1))
            $out[] = '<span class="tpage">...</span>';
        for ($nCont = $neighbors; $nCont >= 1; $nCont--)
            if ($start >= $kmess * $nCont) {
                $tmpStart = $start - $kmess * $nCont;
                $out[] = sprintf($base_link, $tmpStart / $kmess + 1, $tmpStart / $kmess + 1);
            }
        $out[] = '<span class="currentpage"><b>' . ($start / $kmess + 1) . '</b></span>';
        $tmpMaxPages = (int)(($total - 1) / $kmess) * $kmess;
        for ($nCont = 1; $nCont <= $neighbors; $nCont++)
            if ($start + $kmess * $nCont <= $tmpMaxPages) {
                $tmpStart = $start + $kmess * $nCont;
                $out[] = sprintf($base_link, $tmpStart / $kmess + 1, $tmpStart / $kmess + 1);
            }
        if ($start + $kmess * ($neighbors + 1) < $tmpMaxPages)
            $out[] = '<span class="tpage">...</span>';
        if ($start + $kmess * $neighbors < $tmpMaxPages)
            $out[] = sprintf($base_link, $tmpMaxPages / $kmess + 1, $tmpMaxPages / $kmess + 1);
        if ($start + $kmess < $total) {
            $display_page = ($start + $kmess) > $total ? $total : ($start / $kmess + 2);
            $out[] = sprintf($base_link, $display_page, '&gt;&gt;');
        }

        return implode(' ', $out).'<br />';
    }

    /**
     * Показываем местоположение пользователя
     *
     * @param int $user_id
     * @param string $place
     *
     * @return mixed|string
     */
    public static function display_place($user_id = 0, $place = '')
    {
        global $headmod;
        $place = explode(",", $place);
        $placelist = parent::load_lng('places');
        if (array_key_exists($place[0], $placelist)) {
            if ($place[0] == 'profile') {
                if ($place[1] == $user_id) {
                    return '<a href="' . self::$system_set['homeurl'] . '/users/profile.php?user=' . $place[1] . '">' . $placelist['profile_personal'] . '</a>';
                } else {
                    $user = self::get_user($place[1]);

                    return $placelist['profile'] . ': <a href="' . self::$system_set['homeurl'] . '/users/profile.php?user=' . $user['id'] . '">' . $user['name'] . '</a>';
                }
            } elseif ($place[0] == 'online' && isset($headmod) && $headmod == 'online') {
                return $placelist['here'];
            } else {
                return str_replace('#home#', self::$system_set['homeurl'], $placelist[$place[0]]);
            }
        }

        return '<a href="' . self::$system_set['homeurl'] . '/index.php">' . $placelist['homepage'] . '</a>';
    }

    /**
     * Отображения личных данных пользователя
     *
     * @param int $user Массив запроса в таблицу `users`
     * @param array $arg  Массив параметров отображения
     *                    [lastvisit] (boolean)   Дата и время последнего визита
     *                    [stshide]   (boolean)   Скрыть статус (если есть)
     *                    [iphide]    (boolean)   Скрыть (не показывать) IP и UserAgent
     *                    [iphist]    (boolean)   Показывать ссылку на историю IP
     *
     *                    [header]    (string)    Текст в строке после Ника пользователя
     *                    [body]      (string)    Основной текст, под ником пользователя
     *                    [sub]       (string)    Строка выводится вверху области "sub"
     *                    [footer]    (string)    Строка выводится внизу области "sub"
     *
     * @return string
     */
    public static function display_user($user = 0, $arg = array())
    {
        global $mod;
        $out = FALSE;

        if (!$user['id']) {
            $out = '<b>' . self::$lng['guest'] . '</b>';
            if (!empty($user['name']))
                $out .= ': ' . $user['name'];
        } else {
            if (self::$user_set['avatar']) {
                $out .= '<table cellpadding="0" cellspacing="0"><tr><td>';
                    $out .= '<img src="' . self::$system_set['homeurl'] . '/avatar/' . $user['id'] . '-24-48.png" alt="" />&#160;';
                $out .= '</td><td>';
            }
            if ($user['sex']){
                if(time() > $user['lastdate'] + 30){
                    $out .= functions::image(($user['sex'] == 'm' ? 'user/man_of' : 'user/j_of') . '.png', array('class' => 'icon-inline'));
                }else{
                    $out .= functions::image(($user['sex'] == 'm' ? 'm' : 'w') . ($user['datereg'] > time() - 86400 ? '_new' : '') . '.png', array('class' => 'icon-inline'));
                }
            }else{
                $out .= functions::image('del.png');
            }
            $out .= !self::$user_id || self::$user_id == $user['id'] ? '<b>' . functions::nickcolor($user['id']) . '</b>' : '<a href="' . self::$system_set['homeurl'] . '/users/profile.php?user=' . $user['id'] . '"><b>' . functions::nickcolor($user['id']) . '</b></a>';
            $rank = array(
                0 => '',
                1 => '(GMod)',
                2 => '(CMod)',
                3 => '(FMod)',
                4 => '(DMod)',
                5 => '(LMod)',
                6 => '(Smd)',
                7 => '(Adm)',
                9 => '(SV!)'
            );
            $rights = isset($user['rights']) ? $user['rights'] : 0;
            if (isset($arg['addinfo'])) {
                $out .= functions::finfo($user['id']);
                $out .= functions::binfo($user['id']);
            }
            if (!isset($arg['stshide']) && !empty($user['status']))
                $out .= '<div class="status">' . functions::image('label.png', array('class' => 'icon-inline')) . $user['status'] . '</div>';
            if (self::$user_set['avatar'])
                $out .= '</td></tr></table>';
        }
        if (!empty($arg['header']))
            $out .= ' ' . $arg['header'];

        if (isset($arg['body']))
            $out .= '<div>' . $arg['body'] . '</div>';
        $ipinf = !isset($arg['iphide']) && self::$user_rights ? 1 : 0;
        $lastvisit = time() > $user['lastdate'] + 300 && isset($arg['lastvisit']) ? self::display_date($user['lastdate']) : FALSE;
        if ($ipinf || $lastvisit || isset($arg['sub']) && !empty($arg['sub']) || isset($arg['footer'])) {
            $out .= '<div class="sub">';
            if (isset($arg['sub'])) {
                $out .= '<div>' . $arg['sub'] . '</div>';
            }
            if ($lastvisit) {
                $out .= '<div><span class="gray">' . self::$lng['last_visit'] . ':</span> ' . $lastvisit . '</div>';
            }
            $iphist = '';
            if ($ipinf) {
                $out .= '<div><span class="gray">' . self::$lng['browser'] . ':</span> ' . htmlspecialchars($user['browser']) . '</div>' .
                    '<div><span class="gray">' . self::$lng['ip_address'] . ':</span> ';
                $hist = $mod == 'history' ? '&amp;mod=history' : '';
                $ip = long2ip($user['ip']);
                if (self::$user_rights && isset($user['ip_via_proxy']) && $user['ip_via_proxy']) {
                    $out .= '<b class="red"><a href="' . self::$system_set['homeurl'] . '/' . self::$system_set['admp'] . '/index.php?act=search_ip&amp;ip=' . $ip . $hist . '">' . $ip . '</a></b>';
                    $out .= '&#160;[<a href="' . self::$system_set['homeurl'] . '/' . self::$system_set['admp'] . '/index.php?act=ip_whois&amp;ip=' . $ip . '">?</a>]';
                    $out .= ' / ';
                    $out .= '<a href="' . self::$system_set['homeurl'] . '/' . self::$system_set['admp'] . '/index.php?act=search_ip&amp;ip=' . long2ip($user['ip_via_proxy']) . $hist . '">' . long2ip($user['ip_via_proxy']) . '</a>';
                    $out .= '&#160;[<a href="' . self::$system_set['homeurl'] . '/' . self::$system_set['admp'] . '/index.php?act=ip_whois&amp;ip=' . long2ip($user['ip_via_proxy']) . '">?</a>]';
                } elseif (self::$user_rights) {
                    $out .= '<a href="' . self::$system_set['homeurl'] . '/' . self::$system_set['admp'] . '/index.php?act=search_ip&amp;ip=' . $ip . $hist . '">' . $ip . '</a>';
                    $out .= '&#160;[<a href="' . self::$system_set['homeurl'] . '/' . self::$system_set['admp'] . '/index.php?act=ip_whois&amp;ip=' . $ip . '">?</a>]';
                } else {
                    $out .= $ip . $iphist;
                }
                if (isset($arg['iphist'])) {
                    $iptotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_users_iphistory` WHERE `user_id` = '" . $user['id'] . "'"), 0);
                    $out .= '<div><span class="gray">' . self::$lng['ip_history'] . ':</span> <a href="' . self::$system_set['homeurl'] . '/users/profile.php?act=ip&amp;user=' . $user['id'] . '">[' . $iptotal . ']</a></div>';
                }
                $out .= '</div>';
            }
            if (isset($arg['footer']))
                $out .= $arg['footer'];
            $out .= '</div>';
        }

        return $out;
    }

    /**
     * Форматирование имени файла
     *
     * @param string $name
     *
     * @return string
     */
    public static function format($name)
    {
        $f1 = strrpos($name, ".");
        $f2 = substr($name, $f1 + 1, 999);
        $fname = strtolower($f2);

        return $fname;
    }

    /**
     * Получаем данные пользователя
     *
     * @param int $id Идентификатор пользователя
     *
     * @return array|bool
     */
    public static function get_user($id = 0)
    {
        if ($id && $id != self::$user_id) {
            $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id'");
            if (mysql_num_rows($req)) {
                return mysql_fetch_assoc($req);
            } else {
                return FALSE;
            }
        } else {
            return self::$user_data;
        }
    }

    public static function image($name, $args = array())
    {
        if (is_file(ROOTPATH . 'theme/' . core::$user_set['skin'] . '/images/' . $name)) {
            $src = core::$system_set['homeurl'] . '/theme/' . core::$user_set['skin'] . '/images/' . $name;
        } elseif (is_file(ROOTPATH . 'images/' . $name)) {
            $src = core::$system_set['homeurl'] . '/images/' . $name;
        } else {
            return false;
        }

        return '<img src="' . $src . '" alt="' . (isset($args['alt']) ? $args['alt'] : '') . '"' .
        (isset($args['width']) ? ' width="' . $args['width'] . '"' : '') .
        (isset($args['height']) ? ' height="' . $args['height'] . '"' : '') .
        ' class="' . (isset($args['class']) ? $args['class'] : 'icon') . '"/>';
    }

    /**
     * Является ли выбранный юзер другом?
     *
     * @param int $id   Идентификатор пользователя, которого проверяем
     *
     * @return bool
     */
    public static function is_friend($id = 0)
    {
        static $user_id = NULL;
        static $return = FALSE;

        if (!self::$user_id && !$id) {
            return FALSE;
        }

        if (is_null($user_id) || $id != $user_id) {
            $query = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact` WHERE `type` = '2' AND ((`from_id` = '$id' AND `user_id` = '" . self::$user_id . "') OR (`from_id` = '" . self::$user_id . "' AND `user_id` = '$id'))"), 0);
            $return = $query == 2 ? TRUE : FALSE;
        }

        return $return;
    }

    /**
     * Находится ли выбранный пользователь в контактах и игноре?
     *
     * @param int $id Идентификатор пользователя, которого проверяем
     *
     * @return int Результат запроса:
     *             0 - не в контактах
     *             1 - в контактах
     *             2 - в игноре у меня
     */
    public static function is_contact($id = 0)
    {
        static $user_id = NULL;
        static $return = 0;

        if (!self::$user_id && !$id) {
            return 0;
        }

        if (is_null($user_id) || $id != $user_id) {
            $user_id = $id;
            $req_1 = mysql_query("SELECT * FROM `cms_contact` WHERE `user_id` = '" . self::$user_id . "' AND `from_id` = '$id'");
            if (mysql_num_rows($req_1)) {
                $res_1 = mysql_fetch_assoc($req_1);
                if ($res_1['ban'] == 1) {
                    $return = 2;
                } else {
                    $return = 1;
                }
            } else {
                $return = 0;
            }
        }

        return $return;
    }

    /**
     * Проверка на игнор у получателя
     *
     * @param $id
     *
     * @return bool
     */
    public static function is_ignor($id)
    {
        static $user_id = NULL;
        static $return = FALSE;

        if (!self::$user_id && !$id) {
            return FALSE;
        }

        if (is_null($user_id) || $id != $user_id) {
            $user_id = $id;
            $req_2 = mysql_query("SELECT * FROM `cms_contact` WHERE `user_id` = '$id' AND `from_id` = '" . self::$user_id . "'");
            if (mysql_num_rows($req_2)) {
                $res_2 = mysql_fetch_assoc($req_2);
                if ($res_2['ban'] == 1) {
                    $return = TRUE;
                }
            }
        }

        return $return;
    }

    /*
    -----------------------------------------------------------------
    Транслитерация с Русского в латиницу
    -----------------------------------------------------------------
    */
    public static function rus_lat($str)
    {
        $replace = array(
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'e',
            'ж' => 'j',
            'з' => 'z',
            'и' => 'i',
            'й' => 'i',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sch',
            'ъ' => "",
            'ы' => 'y',
            'ь' => "",
            'э' => 'ye',
            'ю' => 'yu',
            'я' => 'ya'
        );

        return strtr($str, $replace);
    }

    /*
    -----------------------------------------------------------------
    Обработка смайлов
    -----------------------------------------------------------------
    */
    public static function smileys($str, $adm = FALSE)
    {
        static $smileys_cache = array();
        if (empty($smileys_cache)) {
            $file = ROOTPATH . 'files/cache/smileys.dat';
            if (file_exists($file) && ($smileys = file_get_contents($file)) !== FALSE) {
                $smileys_cache = unserialize($smileys);

                return strtr($str, ($adm ? array_merge($smileys_cache['usr'], $smileys_cache['adm']) : $smileys_cache['usr']));
            } else {
                return $str;
            }
        } else {
            return strtr($str, ($adm ? array_merge($smileys_cache['usr'], $smileys_cache['adm']) : $smileys_cache['usr']));
        }
    }

    /*
    -----------------------------------------------------------------
    Функция пересчета на дни, или часы
    -----------------------------------------------------------------
    */
    public static function timecount($var)
    {
        global $lng;
        if ($var < 0) $var = 0;
        $day = ceil($var / 86400);
        if ($var > 345600) return $day . ' ' . $lng['timecount_days'];
        if ($var >= 172800) return $day . ' ' . $lng['timecount_days_r'];
        if ($var >= 86400) return '1 ' . $lng['timecount_day'];

        return date("G:i:s", mktime(0, 0, $var));
    }

    /*
    -----------------------------------------------------------------
    Транслитерация текста
    -----------------------------------------------------------------
    */
    public static function trans($str)
    {
        $replace = array(
            'a' => 'а',
            'b' => 'б',
            'v' => 'в',
            'g' => 'г',
            'd' => 'д',
            'e' => 'е',
            'yo' => 'ё',
            'zh' => 'ж',
            'z' => 'з',
            'i' => 'и',
            'j' => 'й',
            'k' => 'к',
            'l' => 'л',
            'm' => 'м',
            'n' => 'н',
            'o' => 'о',
            'p' => 'п',
            'r' => 'р',
            's' => 'с',
            't' => 'т',
            'u' => 'у',
            'f' => 'ф',
            'h' => 'х',
            'c' => 'ц',
            'ch' => 'ч',
            'w' => 'ш',
            'sh' => 'щ',
            'q' => 'ъ',
            'y' => 'ы',
            'x' => 'э',
            'yu' => 'ю',
            'ya' => 'я',
            'A' => 'А',
            'B' => 'Б',
            'V' => 'В',
            'G' => 'Г',
            'D' => 'Д',
            'E' => 'Е',
            'YO' => 'Ё',
            'ZH' => 'Ж',
            'Z' => 'З',
            'I' => 'И',
            'J' => 'Й',
            'K' => 'К',
            'L' => 'Л',
            'M' => 'М',
            'N' => 'Н',
            'O' => 'О',
            'P' => 'П',
            'R' => 'Р',
            'S' => 'С',
            'T' => 'Т',
            'U' => 'У',
            'F' => 'Ф',
            'H' => 'Х',
            'C' => 'Ц',
            'CH' => 'Ч',
            'W' => 'Ш',
            'SH' => 'Щ',
            'Q' => 'Ъ',
            'Y' => 'Ы',
            'X' => 'Э',
            'YU' => 'Ю',
            'YA' => 'Я'
        );

        return strtr($str, $replace);
    }

    /*
    -----------------------------------------------------------------
    Старая функция проверки переменных.
    В новых разработках не применять!
    Вместо данной функции использовать checkin()
    -----------------------------------------------------------------
    */
    public static function check($str)
    {
        $str = htmlentities(trim($str), ENT_QUOTES, 'UTF-8');
        $str = self::checkin($str);
        $str = nl2br($str);
        $str = mysql_real_escape_string($str);

        return $str;
    }

    public static function seourl($var){
        $var = preg_replace('/(â|ầ|ầ|ấ|ấ|ậ|ậ|ẩ|ẩ|ẫ|ẫ|ă|ằ|ằ|ắ|ắ|ặ|ặ|ẳ|ẳ|ẵ|ẵ|à|à|á|á|ạ|ạ|ả|ả|ã|ã)/', 'a', $var);
        $var = preg_replace('/(ê|ề|ề|ế|ế|ệ|ệ|ể|ể|ễ|ễ|è|è|é|é|ẹ|ẹ|ẻ|ẻ|ẽ|ẽ)/', 'e', $var);
        $var = preg_replace('/(ì|ì|í|í|ị|ị|ỉ|ỉ|ĩ|ĩ)/', 'i', $var);
        $var = preg_replace('/(ô|ồ|ồ|ố|ố|ộ|ộ|ổ|ổ|ỗ|ỗ|ơ|ờ|ờ|ớ|ớ|ợ|ợ|ở|ở|ỡ|ỡ|ò|ò|ó|ó|ọ|ọ|ỏ|ỏ|õ|õ)/', 'o', $var);
        $var = preg_replace('/(ư|ừ|ừ|ứ|ứ|ự|ự|ử|ử|ữ|ữ|ù|ù|ú|ú|ụ|ụ|ủ|ủ|ũ|ũ)/', 'u', $var);
        $var = preg_replace('/(ỳ|ỳ|ý|ý|ỵ|ỵ|ỷ|ỷ|ỹ|ỹ)/', 'y', $var);
        $var = preg_replace('/(đ)/', 'd', $var);
        $var = preg_replace('/(B)/', 'b', $var);
        $var = preg_replace('/(C)/', 'c', $var);
        $var = preg_replace('/(D)/', 'd', $var);
        $var = preg_replace('/(F)/', 'f', $var);
        $var = preg_replace('/(G)/', 'g', $var);
        $var = preg_replace('/(H)/', 'h', $var);
        $var = preg_replace('/(J)/', 'j', $var);
        $var = preg_replace('/(K)/', 'k', $var);
        $var = preg_replace('/(L)/', 'l', $var);
        $var = preg_replace('/(M)/', 'm', $var);
        $var = preg_replace('/(N)/', 'n', $var);
        $var = preg_replace('/(P)/', 'p', $var);
        $var = preg_replace('/(Q)/', 'q', $var);
        $var = preg_replace('/(R)/', 'r', $var);
        $var = preg_replace('/(S)/', 's', $var);
        $var = preg_replace('/(T)/', 't', $var);
        $var = preg_replace('/(V)/', 'v', $var);
        $var = preg_replace('/(W)/', 'w', $var);
        $var = preg_replace('/(X)/', 'x', $var);
        $var = preg_replace('/(Z)/', 'z', $var);
        $var = preg_replace('/(Â|Ầ|Ầ|Ấ|Ấ|Ậ|Ậ|A|Ẩ|Ẩ|Ẫ|Ẫ|Ă|Ắ|Ằ|Ằ|Ắ|Ặ|Ặ|Ẳ|Ẳ|Ẵ|Ẵ|À|À|Á|Á|Ạ|Ạ|Ả|Ả|Ã|Ã)/', 'a', $var);
        $var = preg_replace('/(Ẽ|Ẽ|Ê|Ề|E|Ề|Ế|Ế|Ệ|Ệ|Ể|Ể|Ễ|Ễ|È|È|É|É|Ẹ|Ẹ|Ẻ|Ẻ)/', 'e', $var);
        $var = preg_replace('/(Ì|Ì|Í|Í|Ị|Ị|I|Ỉ|Ỉ|Ĩ|Ĩ)/', 'i', $var);
        $var = preg_replace('/(Ô|Ồ|Ồ|Ố|Ố|O|Ộ|Ộ|Ổ|Ổ|Ỗ|Ỗ|Ờ|Ơ|Ờ|Ớ|Ớ|Ợ|Ợ|Ở|Ở|Ỡ|Ỡ|Ò|Ò|Ó|Ó|Ọ|Ọ|Ỏ|Ỏ|Õ|Õ)/', 'o', $var);
        $var = preg_replace('/(Ư|Ừ|Ừ|U|Ứ|Ứ|Ự|Ự|Ử|Ử|Ữ|Ữ|Ù|Ù|Ú|Ú|Ụ|Ụ|Ủ|Ủ|Ũ|Ũ)/', 'u', $var);
        $var = preg_replace('/(Ỳ|Ỳ|Ý|Ý|Ỵ|Y|Ỵ|Ỷ|Ỷ|Ỹ|Ỹ)/', 'y', $var);
        $var = preg_replace('/(́|̀|̉|̃||̣)/', '', $var);
        $var = preg_replace('/(Đ)/', 'd', $var);
        $var = htmlspecialchars_decode($var);
        $var = str_replace(',', '', $var);
        $var = str_ireplace(array('&ETH;', '&Eth;', '&eth;'), '-', $var);
        $var = preg_replace('/[\W]+/s', '-', $var);
        $var = preg_replace('/-{2,}/', '-', $var);

        return $var;
    }
    public static function display_pagination2($base_url, $start, $max_value, $num_per_page)
    {
        $neighbors = 2;
        if ($start >= $max_value)
            $start = max(0, (int)$max_value - (((int)$max_value % (int)$num_per_page) == 0 ? $num_per_page : ((int)$max_value % (int)$num_per_page)));
        else
            $start = max(0, (int)$start - ((int)$start % (int)$num_per_page));
        $base_link = '<a href="' . strtr($base_url, array('%' => '%%')) . '_p%d.html' . '"><span class="pagenav">%s</span></a>';
        $out[] = $start == 0 ? '' : sprintf($base_link, $start / $num_per_page, '&lt;&lt;');
        if ($start > $num_per_page * $neighbors)
            $out[] = sprintf($base_link, 1, '1');
        if ($start > $num_per_page * ($neighbors + 1))
            $out[] = '<span class="tpage">...</span>';
        for ($nCont = $neighbors; $nCont >= 1; $nCont--)
            if ($start >= $num_per_page * $nCont) {
                $tmpStart = $start - $num_per_page * $nCont;
                $out[] = sprintf($base_link, $tmpStart / $num_per_page + 1, $tmpStart / $num_per_page + 1);
            }
        $out[] = '<span class="currentpage"><b>' . ($start / $num_per_page + 1) . '</b></span>';
        $tmpMaxPages = (int)(($max_value - 1) / $num_per_page) * $num_per_page;
        for ($nCont = 1; $nCont <= $neighbors; $nCont++)
            if ($start + $num_per_page * $nCont <= $tmpMaxPages) {
                $tmpStart = $start + $num_per_page * $nCont;
                $out[] = sprintf($base_link, $tmpStart / $num_per_page + 1, $tmpStart / $num_per_page + 1);
            }
        if ($start + $num_per_page * ($neighbors + 1) < $tmpMaxPages)
            $out[] = '<span class="tpage">...</span>';
        if ($start + $num_per_page * $neighbors < $tmpMaxPages)
            $out[] = sprintf($base_link, $tmpMaxPages / $num_per_page + 1, $tmpMaxPages / $num_per_page + 1);
        if ($start + $num_per_page < $max_value) {
            $display_page = ($start + $num_per_page) > $max_value ? $max_value : ($start / $num_per_page + 2);
            $out[] = sprintf($base_link, $display_page, '&gt;&gt;');
        }
        return implode(' ', $out).'<br />';
    }
    public static function createTags($name, $type = 0){
        global $home;
        if(stristr($name, ' ')){
            $explode = explode(' ', $name);
            unset($name);
            $count = count($explode);
            for($i = 0; $i < $count; $i++){
                $getEx = str_replace(',', '', trim($explode[$i]));
                $getEx = html_entity_decode(trim($getEx), ENT_QUOTES, 'UTF-8');
                $getEx = str_replace('[', '', $getEx);
                $getEx = str_replace(']', '', $getEx);
                $getEx = str_replace('>', '', $getEx);
                $getEx = str_replace('<', '', $getEx);
                $getEx = str_replace('!', '', $getEx);
                $getEx = str_replace('?', '', $getEx);
                $getEx = str_replace('.', '', $getEx);
                $getEx = str_replace(',', '', $getEx);
                $getEx = str_replace(':', '', $getEx);
                $getEx = str_replace("'", '', $getEx);
                $getEx = str_replace('"', '', $getEx);
                $getEx = str_replace('&', '', $getEx);
                $getEx = str_replace('(', '', $getEx);
                $getEx = str_replace(')', '', $getEx);

                $name .= ($type == 1 ? "<a href=\"$home/forum/timkiem.html?search=$getEx\" class=\"post-tag\" rel=\"nofollow\">$getEx</a>" : $getEx.',');
            }
         }elseif($type == 1) $name = "<a href=\"$home/forum/timkiem.html?search=$name\" class=\"post-tag\" rel=\"nofollow\">$name</a>";
         return $name;
    }
    public static function shareLink($link){
        global $home;
        $out = '<span class="mrt-social" data-show="0"><a rel="nofollow" href="https://plus.google.com/share?url='.$link.'" class="mrt-social-button mrt-social-google fa fa-google-plus" title="Share on Google+" target="_blank"></a><a rel="nofollow" href="https://twitter.com/home?status='.$link.'" class="mrt-social-button mrt-social-twitter fa fa-twitter" title="Share on twitter" target="_blank"></a><a rel="nofollow" href="https://www.facebook.com/sharer/sharer.php?u='.$link.'" class="mrt-social-button mrt-social-facebook fa fa-facebook" title="Share on facebook" target="_blank"></a></span>';
        return $out;
    }

    public static function nickcolor($id, $mod = false) {
        $ban = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '" . $id . "' AND `ban_time` > '" . time() . "'"), 0);
        $user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '" . $id . "'"));
        if($ban > 0) {
            $out .= '<span style="color:black">'.($mod == 1 ? '<small>' : '<b>').'<s>' . $user['name'] . '</s>'.($mod == 1 ? '</small>' : '</b>').'</span>';
        } else {
            if($user['rights'] > 0) {
                if($user['rights'] == 1) {
                    $span = '<span style="color: #eba501">';
                }
                if($user['rights'] == 2) {
                    $span = '<span style="color: #9932CC">';
                }
                if($user['rights'] == 3) {
                     $span = '<span style="color: #0000FF">';
                }
                if($user['rights'] == 4) {
                    $span = '<span style="color: #0896c3">';
                }
                if($user['rights'] == 5) {
                    $span = '<span style="color: #228622">';
                }
                if($user['rights'] == 6) {
                    $span = '<span style="color: #7192A8">';
                }
                if($user['rights'] == 7) {
                    $span = '<span style="color: #F267AB">';
                }
                if($user['rights'] == 8) {
                    $span = '<span style="color:red;">';
                }
                if($user['rights'] == 9) {
                    $span = '<span style="color:red;">';
                }
                if($user['rights'] == 10) {
                    $span = '<span style="color: #7192a8">';
                }
                $out .= ''.$span.'' . $user['name'] . '</span>';
            } else {
                $out .= '<span style="color:black">' . $user['name'] . '</span>';
            }
        }
        return $out;
    }
    public static function thoigian($gio){
        $time = time();
        $jun = round(($time-$gio)/60);
        //làm tròn thời gian lấy time hiện tại trừ time bài viết gửi đem chia cho 60 giây
        if($jun < 1){
            $jun='Vừa Xong';
        }
        // nếu thời gian làm tròn < 1 => vừa xong
        if($jun >= 1 && $jun < 60){
            $jun="$jun Phút Trước";
        }
        if($jun >= 60 && $jun < 1440){
            $jun=round($jun/60);
            $jun="$jun Giờ Trước";
        }
        if($jun >= 1440 && $jun < 43200){
            $jun = round($jun/60/24);
            $jun = "$jun Ngày Trước";
        }
        if($jun >= 43200 && $jun < 518400){
            $jun = round($jun/60/24/30);
            $jun = "$jun Tháng Trước";
        }
        if($jun >= 518400){
            $jun = round($jun/60/24/30/12);
            $jun = "$jun Năm Trước";
        }
        return $jun;
    }
    public static function cattu($str, $bd, $len) {
        if (mb_strlen($str , 'UTF-8') > $len * 5) {
            $str = mb_substr($str, $bd, $len * 5, 'UTF-8');
            $str = mb_substr($str, $bd , mb_strrpos($str ," " , 'UTF-8'), 'UTF-8' );
            $str = '' . strip_tags(implode(' ', array_slice(explode(' ', $str), $bd, $len))) . ' ....';
        }
        return $str ;
    }
    public static function rights($var) {
        $rights = mysql_fetch_array(mysql_query("SELECT `rights` FROM `users` WHERE `id`='".$var."'"));
        $rank = array(0 => '(Member)',
         1 => '(<span style="color: #eba501">V.I.P</span>)',
         2 => '(<span style="color: #9932CC">Auto</span>)',
         3 => '(<span style="color: #0000FF">JavaMaster</span>)',
         4 => '(<span style="color: #0896c3">WapMaster</span>)',
         5 => '(<span style="color: #228622">S.W.A.T</span>)',
         6 => '(<span style="color: #7192A8">Mod</span>)',
         7 => '(<span style="color: #F267AB">SMod</span>)',
         8 => '(<span style="color:red;">Admin</span>)',
         9 => '(<span style="color:red;">Sáng Lập</span>)');
        $out = ''.$rank[$rights['rights']];
        return $out;
    }
    public static function finfo($var) {
        $user_u = $var;
        $req_u = mysql_query("SELECT * FROM `users` WHERE `id` = '$user_u' LIMIT 1");
        $res_u = mysql_fetch_array($req_u);
        $exp = $res_u['postforum']*155;
        $op = $res_u['fermer_oput'];
        $level = $res_u['fermer_level'];
        $tien = $res_u['balans'];
        $tvgold = $res_u['vgold'];

        if ($exp >= 0 && $exp <3000){

            $chucdanh = '<img src="/forum/level/gacon.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 3000 && $exp <5250){
            $chucdanh = '<img src="/forum/level/buago.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 5250 && $exp <8250){
            $chucdanh = '<img src="/forum/level/buagodoi.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 8250 && $exp <12750){
            $chucdanh = '<img src="/forum/level/buada.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 12750 && $exp <19500){
            $chucdanh = '<img src="/forum/level/buadadoi.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 19500 && $exp <31500){
            $chucdanh = '<img src="/forum/level/riusat.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 31500 && $exp <46500){
            $chucdanh = '<img src="/forum/level/riusatdoi.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 46500 && $exp <70500){
            $chucdanh = '<img src="/forum/level/riubac.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 70500 && $exp <102000){
            $chucdanh = '<img src="/forum/level/riubacdoi.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 102000 && $exp <165000){
            $chucdanh = '<img src="/forum/level/riuvang.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 165000 && $exp <240000){
            $chucdanh = '<img src="/forum/level/riuvangdoi.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 240000 && $exp <330000){
            $chucdanh = '<img src="/forum/level/riuchiensat.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 330000 && $exp <435000){
            $chucdanh = '<img src="/forum/level/riuchiensatcham.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 435000 && $exp <585000){
            $chucdanh = '<img src="/forum/level/riuchienbac.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 585000 && $exp <765000){
            $chucdanh = '<img src="/forum/level/riuchienbaccham.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 765000 && $exp <1140000){
            $chucdanh = '<img src="/forum/level/riuchienvang.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 1140000 && $exp <1650000){
            $chucdanh = '<img src="/forum/level/riuchienvangcham.gif" width="25" height="15" alt="" />';
        }
        if ($exp >= 1650000){
            $chucdanh = '<img src="/forum/level/vip.gif" width="25" height="15" alt="" />';
        }

        if($op >= 0 && $op <= 150) $level='<img src="/nongtrai/level/0.png" width="25" height="25" alt="" />';
        else if($op >= 151 && $op <= 300) $level = '<img src="/nongtrai/level/1.png" width="25" height="25" alt="" />';
        else if($op >= 301 && $op <= 800) $level = '<img src="/nongtrai/level/2.png" width="25" height="25" alt="" />';
        else if($op >= 801 && $op <= 1300) $level = '<img src="/nongtrai/level/3.png" width="25" height="25" alt="" />';
        else if($op >= 1301 && $op <= 2000) $level = '<img src="/nongtrai/level/4.png" width="25" height="25" alt="" />';
        else if($op >= 2001 && $op <= 3000) $level = '<img src="/nongtrai/level/5.png" width="25" height="25" alt="" />';
        else if($op >= 3001 && $op <= 4500) $level = '<img src="/nongtrai/level/6.png" width="25" height="25" alt="" />';
        else if($op >= 4501 && $op <= 6000) $level = '<img src="/nongtrai/level/7.png" width="25" height="25" alt="" />';
        else if($op >= 6001 && $op <= 9000) $level = '<img src="/nongtrai/level/8.png" width="25" height="25" alt="" />';
        else if($op >= 9001 && $op <= 12000) $level = '<img src="/nongtrai/level/9.png" width="25" height="25" alt="" />';
        else if($op >= 12001 && $op <= 16000) $level = '<img src="/nongtrai/level/10.png" width="25" height="25" alt="" />';
        else if($op >= 16001 && $op <= 20000) $level = '<img src="/nongtrai/level/11.png" width="25" height="25" alt="" />';
        else if($op >= 20001 && $op <= 25000) $level = '<img src="/nongtrai/level/12.png" width="25" height="25" alt="" />';
        else if($op >= 25001 && $op <= 30000) $level = '<img src="/nongtrai/level/13.png" width="25" height="25" alt="" />';
        else if($op >= 30001 && $op <= 35000) $level = '<img src="/nongtrai/level/14.png" width="25" height="25" alt="" />';
        else if($op >= 35001 && $op <= 40000) $level = '<img src="/nongtrai/level/15.png" width="25" height="25" alt="" />';
        else if($op >= 40001 && $op <= 50000) $level = '<img src="/nongtrai/level/16.png" width="25" height="25" alt="" />';
        else if($op >= 50001 && $op <= 60000) $level = '<img src="/nongtrai/level/17.png" width="25" height="25" alt="" />';
        else if($op >= 60001 && $op <= 70000) $level = '<img src="/nongtrai/level/18.png" width="25" height="25" alt="" />';
        else if($op >= 70001 && $op <= 85000) $level = '<img src="/nongtrai/level/19.png" width="25" height="25" alt="" />';
        else if($op >= 85001 && $op <= 90000) $level = '<img src="/nongtrai/level/20.png" width="25" height="25" alt="" />';
        else if($op >= 90001 && $op <= 105000) $level = '<img src="/nongtrai/level/21.png" width="25" height="25" alt="" />';
        else if($op >= 105001 && $op <= 115000) $level = '<img src="/nongtrai/level/22.png" width="25" height="25" alt="" />';
        else if($op >= 115001 && $op <= 130000) $level = '<img src="/nongtrai/level/23.png" width="25" height="25" alt="" />';
        else if($op >= 130001 && $op <= 155000) $level = '<img src="/nongtrai/level/24.png" width="25" height="25" alt="" />';
        else if($op >= 155001 && $op <= 170000) $level = '<img src="/nongtrai/level/25.png" width="25" height="25" alt="" />';
        else if($op >= 170001 && $op <= 190000) $level = '<img src="/nongtrai/level/26.png" width="25" height="25" alt="" />';
        else if($op >= 190001 && $op <= 210000) $level = '<img src="/nongtrai/level/27.png" width="25" height="25" alt="" />';
        else if($op >= 210001 && $op <= 230000) $level = '<img src="/nongtrai/level/28.png" width="25" height="25" alt="" />';
        else if($op >= 230001 && $op <= 250000) $level = '<img src="/nongtrai/level/29.png" width="25" height="25" alt="" />';
        else if($op >= 250001 && $op <= 270000) $level = '<img src="/nongtrai/level/30.png" width="25" height="25" alt="" />';
        else if($op >= 270001 && $op <= 290000) $level = '<img src="/nongtrai/level/31.png" width="25" height="25" alt="" />';
        else if($op >= 290001 && $op <= 320000) $level = '<img src="/nongtrai/level/32.png" width="25" height="25" alt="" />';
        else if($op >= 320001 && $op <= 340000) $level = '<img src="/nongtrai/level/33.png" width="25" height="25" alt="" />';
        else if($op >= 340001 && $op <= 360000) $level = '<img src="/nongtrai/level/34.png" width="25" height="25" alt="" />';
        else if($op >= 360001 && $op <= 400000) $level = '<img src="/nongtrai/level/35.png" width="25" height="25" alt="" />';
        else if($op >= 400001 && $op <= 450000) $level = '<img src="/nongtrai/level/36.png" width="25" height="25" alt="" />';
        else if($op >= 450001 && $op <= 500000) $level = '<img src="/nongtrai/level/37.png" width="25" height="25" alt="" />';
        else if($op >= 500001 && $op <= 550000) $level = '<img src="/nongtrai/level/38.png" width="25" height="25" alt="" />';
        else if($op >= 550001 && $op <= 600000) $level = '<img src="/nongtrai/level/39.png" width="25" height="25" alt="" />';
        else if($op >= 600001 && $op <= 650000) $level = '<img src="/nongtrai/level/40.png" width="25" height="25" alt="" />';
        else if($op >= 650001 && $op <= 700000) $level = '<img src="/nongtrai/level/41.png" width="25" height="25" alt="" />';
        else if($op >= 700001 && $op <= 750000) $level = '<img src="/nongtrai/level/42.png" width="25" height="25" alt="" />';
        else if($op >= 750001 && $op <= 800000) $level = '<img src="/nongtrai/level/43.png" width="25" height="25" alt="" />';
        else if($op >= 800001 && $op <= 850000) $level = '<img src="/nongtrai/level/44.png" width="25" height="25" alt="" />';
        else if($op >= 850001 && $op <= 900000) $level = '<img src="/nongtrai/level/45.png" width="25" height="25" alt="" />';
        else if($op >= 950001 && $op <= 1000000) $level = '<img src="/nongtrai/level/46.png" width="25" height="25" alt="" />';
        else if($op >= 1000001 && $op <= 1100000) $level = '<img src="/nongtrai/level/47.png" width="25" height="25" alt="" />';
        else if($op >= 1100001 && $op <= 1200000) $level = '<img src="/nongtrai/level/48.png" width="25" height="25" alt="" />';
        else if($op >= 1200001 && $op <= 1300000) $level = '<img src="/nongtrai/level/49.png" width="25" height="25" alt="" />';
        else if($op >= 1300001 && $op <= 1600001) $level = '<img src="/nongtrai/level/50.png" width="25" height="25" alt="" />';
        else if($op >= 1600001 && $op <= 2000001) $level = '<img src="/nongtrai/level/51.png" width="25" height="25" alt="" />';
        return '<div class="status" style="padding-left: 5px;">'.$chucdanh.'&#160;' .$res_u['postforum'] . '&#160;&#160;&#160;<img src="/images/like.png" alt="" width="13" height="15" />&#160;' . $res_u['thank_duoc'].'&#160;&#160;&#160;'.$level.'<br />Xu: '.$tien.'&#160;Gold: '.$tvgold.'</div>';
    }

    public static function binfo($var) {
        global $user_id, $home;
        $out = FALSE;
        $user_u = $var;
        $hehe_u = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact` WHERE `type`='2' AND ((`from_id`='$user_u' AND `user_id`='$user_id') OR (`from_id`='$user_id' AND `user_id`='$user_u'))"), 0);
        $ycct = mysql_fetch_array(mysql_query("SELECT * FROM `cms_contact` WHERE `from_id`='$user_u' AND `user_id`='$user_id' LIMIT 1"));
        $yckb = mysql_fetch_array(mysql_query("SELECT * FROM `cms_contact` WHERE `from_id`='$user_id' AND `user_id`='$user_u' LIMIT 1"));

        if ($user_id && $user_id != $user_u) {
            if ($hehe_u != 2){
                if ($ycct['type'] == 2 && $yckb['type'] == 1){
                    $out = '<div class="status">' . functions::image('add.gif', array('class' => 'icon-inline')) . '[Đang chờ] [<a href="'.$home.'/mail/index.php?act=write&id='.$user_u.'">PM</a>]</div>';
                } else if ($ycct['type'] == 1 && $yckb['type'] == 2){
                    $out = '<div class="status">' . functions::image('add.gif', array('class' => 'icon-inline')) . '[Kết bạn] [<a href="'.$home.'/mail/index.php?act=write&id='.$user_u.'">PM</a>]</div>';
                } else {
                    $out = '<div class="status">' . functions::image('add.gif', array('class' => 'icon-inline')) . '[<a href="'.$home.'/users/profile.php?act=friends&do=add&id='.$user_u.'">Kết bạn</a>] [<a href="'.$home.'/mail/index.php?act=write&id='.$user_u.'">PM</a>]</div>';
                }
            } else {
                $out = '<div class="status">' . functions::image('add.gif', array('class' => 'icon-inline')) . '[Đã kết bạn] [<a href="'.$home.'/mail/index.php?act=write&id='.$user_u.'">PM</a>]</div>';
            }
        }
        return $out;
    }

    public static function sex($var) {
        $idus = $var;
        $out = NULL;
        $req_u = mysql_query("SELECT * FROM `users` WHERE `id` = '$idus' LIMIT 1");
        $res_u = mysql_fetch_array($req_u);
        $sex = $res_u['sex'];
        if($sex == 'm') {
            $out = 'của anh ấy ';
        }else{
            $out = 'của cô ấy ';
        }
        return $out;
    }

    /*Like Validate Check*/
    public static function Like_Check($msg_id, $uid, $reaction_type)
    {
        $msg_id = mysql_real_escape_string($msg_id); // Reactioned message id
        $uid = mysql_real_escape_string($uid);  // Reactioner user id
        $reaction_type = mysql_real_escape_string($reaction_type); // Reaction type
        //Check reaction type for user
        $q = mysql_query("SELECT id FROM forum_thank WHERE  userthank='$uid' and topic='$msg_id' AND reaction_type='$reaction_type'");
        // Output the result
        if(mysql_num_rows($q) != 0) {
            return true;
        } else {
            return false;
        }
    }

    /*Unlike*/
    public static function Unlike($msg_id, $uid,$reaction_type)
    {
        $msg_id=mysql_real_escape_string($msg_id); // Reactioned message id
        $uid=mysql_real_escape_string($uid);  // Reactioner user id
        $q=mysql_query("SELECT id FROM forum_thank WHERE userthank='$uid' and topic='$msg_id'") or die(mysql_error());

        if(mysql_num_rows($q)>0) {
            //If user react the message id then delete the reaction type from forum_thank table
            mysql_query("DELETE FROM forum_thank WHERE topic='$msg_id' and userthank='$uid'") or die(mysql_error());
            //Prepare the statement
            $thongkethank = mysql_query("SELECT COUNT(*) FROM `forum_thank` WHERE `topic`='" . $msg_id . "'");
            $thongkethanks = mysql_result($thongkethank, 0);

            return $thongkethanks;
        } else {
            return false;
        }
    }
    /*Like Message*/
    public static function Like($msg_id, $uid, $reaction_type)
    {
        $msg_id=mysql_real_escape_string($msg_id); // Reactioned message id
        $uid=mysql_real_escape_string($uid); // Reactioner user id
        $reaction_type = mysql_real_escape_string($reaction_type); // Reaction type
        // Select the message id from forum_thank table
        $q=mysql_query("SELECT id FROM forum_thank WHERE  userthank='$uid' and topic='$msg_id'");
        // 1 row means there's a Like already, so Unlike() it.
        if(mysql_num_rows($q)==1) {
            functions::Unlike($msg_id, $uid, $reaction_type);
        }
        $q = mysql_query("SELECT * FROM `forum` WHERE `id` = '$msg_id'");
        $r = mysql_fetch_array($q);
        $ouid = $r['user_id'];
        $reid = $r['refid'];
        $time = time();
        // then insert the like from message like table
        mysql_query("INSERT INTO `forum_thank` SET `user` = '".$ouid."', `topic` = '".$msg_id."' , `time` = '$time', `userthank` = '$uid', `chude` = '".$reid."', `reaction_type` = '".$reaction_type."' ");
        // Prepare the statement
        $thongkethank = mysql_query("SELECT COUNT(*) FROM `forum_thank` WHERE `topic`='" . $msg_id . "'");
        $thongkethanks = mysql_result($thongkethank, 0);

        return $thongkethanks;
    }
    /*Like Count Test*/
    public static function Like_CountT($msg_id, $uid, $reaction_type)
    {
        $msg_id = mysql_real_escape_string($msg_id);
        $reaction_type = mysql_real_escape_string($reaction_type);
        $q = mysql_query("SELECT COUNT(*) AS reaction_count FROM forum_thank WHERE topic = '$msg_id' AND reaction_type = '$reaction_type'") or die(mysql_error());
        $row = mysql_fetch_array($q);
        if ($row) {
            return $row['reaction_count'];
        } else return 0;
    }
    /*Like Count Test*/
    public static function Like_CountTotal($msg_id, $uid, $reaction_type)
    {
        $msg_id = mysql_real_escape_string($msg_id);
        $reaction_type = mysql_real_escape_string($reaction_type);
        $q = mysql_query("SELECT reaction_type, COUNT(*) AS reaction_count FROM forum_thank WHERE topic = '$msg_id' GROUP BY reaction_type") or die(mysql_error());
        $row = mysql_fetch_array($q);
        if ($row) {
            return $row['reaction_count'];
        } else return 0;
    }


    /*Like Validate Check*/
    public static function STTLike_Check($msg_id, $uid, $reaction_type)
    {
        $msg_id = mysql_real_escape_string($msg_id);
        $uid = mysql_real_escape_string($uid);
        $reaction_type = mysql_real_escape_string($reaction_type);
        $q = mysql_query("SELECT `id` FROM `postlikes` WHERE  `timeline_id`='$uid' AND `post_id`='$msg_id' AND `reaction`='$reaction_type'");
        if(mysql_num_rows($q) != 0) {
            return true;
        } else {
            return false;
        }
    }

    /*Unlike*/
    public static function STTUnlike($msg_id, $uid,$reaction_type)
    {
        $msg_id=mysql_real_escape_string($msg_id);
        $uid=mysql_real_escape_string($uid);
        $q=mysql_query("SELECT `id` FROM `postlikes` WHERE `timeline_id`='$uid' AND `post_id`='$msg_id'") or die(mysql_error());

        if(mysql_num_rows($q)>0) {
            mysql_query("DELETE FROM `postlikes` WHERE `post_id`='$msg_id' AND `timeline_id`='$uid'") or die(mysql_error());
            $thongkethank = mysql_query("SELECT COUNT(*) FROM `postlikes` WHERE `post_id`='" . $msg_id . "'");
            $thongkethanks = mysql_result($thongkethank, 0);

            return $thongkethanks;
        } else {
            return false;
        }
    }
    /*Like Message*/
    public static function STTLike($msg_id, $uid, $reaction_type)
    {
        $msg_id=mysql_real_escape_string($msg_id); // Reactioned message id
        $uid=mysql_real_escape_string($uid); // Reactioner user id
        $reaction_type = mysql_real_escape_string($reaction_type); // Reaction type
        // Select the message id from forum_thank table
        $q=mysql_query("SELECT `id` FROM `postlikes` WHERE  `timeline_id`='$uid' AND `post_id`='$msg_id'");
        // 1 row means there's a Like already, so Unlike() it.
        if(mysql_num_rows($q)==1) {
            functions::STTUnlike($msg_id, $uid, $reaction_type);
        }
        $q = mysql_query("SELECT * FROM `posts` WHERE `id` = '$msg_id'");
        $r = mysql_fetch_array($q);
        $ouid = $r['timeline_id'];
        $time = time();
        // then insert the like from message like table
        mysql_query("INSERT INTO `postlikes` SET `timeline_id` = '".$uid."', `post_id` = '".$msg_id."' , `time` = '$time', `reaction` = '".$reaction_type."' ");
        // Prepare the statement
        $thongkethank = mysql_query("SELECT COUNT(*) FROM `postlikes` WHERE `post_id`='" . $msg_id . "'");
        $thongkethanks = mysql_result($thongkethank, 0);

        return $thongkethanks;
    }
    /*Like Count Test*/
    public static function STTLike_CountT($msg_id, $uid, $reaction_type)
    {
        $msg_id = mysql_real_escape_string($msg_id);
        $reaction_type = mysql_real_escape_string($reaction_type);
        $q = mysql_query("SELECT COUNT(*) AS `reaction_count` FROM `postlikes` WHERE `post_id` = '$msg_id' AND `reaction` = '$reaction_type'") or die(mysql_error());
        $row = mysql_fetch_array($q);
        if ($row) {
            return $row['reaction_count'];
        } else return 0;
    }
    /*Like Count Test*/
    public static function STTLike_CountTotal($msg_id, $uid, $reaction_type)
    {
        $msg_id = mysql_real_escape_string($msg_id);
        $reaction_type = mysql_real_escape_string($reaction_type);
        $q = mysql_query("SELECT `reaction`, COUNT(*) AS `reaction_count` FROM `postlikes` WHERE `post_id` = '$msg_id' GROUP BY `reaction`") or die(mysql_error());
        $row = mysql_fetch_array($q);
        if ($row) {
            return $row['reaction_count'];
        } else return 0;
    }
}