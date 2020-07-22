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

// Рекламный блок сайта
if (!empty($cms_ads[2])) {
    echo '<div class="gmenu">' . $cms_ads[2] . '</div>';
}

echo '</div>';
if($headmod != 'online'){
    echo '<div class="catRow">Trực tuyến: ' . counters::online();
    $totalend = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `lastdate` > '" . (time() - 30) . "'"), 0);
    if ($totalend) {
        echo '<br />';
        $reqend = mysql_query("SELECT * FROM `users` WHERE `preg`='1' and `lastdate` > '" . (time() - 30) . "' ORDER BY `name` ASC LIMIT 1000");
        while ($resend = mysql_fetch_assoc($reqend)) {
            echo ($user_id && $resend['id'] != $user_id ? '<a href="' . $home . '/users/profile.php?user='.($resend['id']).'">'.functions::nickcolor($resend['id']).'</a>' : functions::nickcolor($resend['id']));
            echo ', ';
            ++$l;
        }
    }else{
    }
    echo '</div>';
}
echo '<div class="phdr" style="text-align:center">'.date('Y').' © <span class="uppercase">phieubac.ga</span><br/>JOHNCMS 6.2.0 - Mod V3.6.0 </div>';

echo '<div class="content" style="text-align:center"><strong>'.$set['meta_desc'].'</strong><br /><hr />Sitemap: <a href="'.$home.'/sitemap.xml">xml</a> | <a href="'.$home.'/sitemap.html">html</a></div>';

// Счетчики каталогов
functions::display_counters();

// Рекламный блок сайта
if (!empty($cms_ads[3])) {
    echo '<br />' . $cms_ads[3];
}

/*
-----------------------------------------------------------------
ВНИМАНИЕ!!!
Данный копирайт нельзя убирать в течение 90 дней с момента установки скриптов
-----------------------------------------------------------------
ATTENTION!!!
The copyright could not be removed within 90 days of installation scripts
-----------------------------------------------------------------
*/
?>

<script type="text/javascript" src="/js/modernizr.js"></script>
<script type="text/javascript" src="/js/mousetrap.min.js"></script>
<script type="text/javascript" src="/js/common.js"></script>

<a id="toTop" href="javascript:;"><span id="toTopHover"></span><img width="40" height="40" alt="To Top" src="/images/to-top@2x.png"></a>
<script type="text/javascript">
$(document).ready(function() {
    function UItoTop(){
        containerID: 'toTop',
        containerHoverID: 'toTopHover',
        scrollSpeed: 1200,
        easingType: 'linear'};
});
</script>
<?php
echo '</div></body></html>';