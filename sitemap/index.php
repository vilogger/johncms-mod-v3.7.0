<?php
    define('_IN_JOHNCMS', 1);
    require('../incfiles/core.php');
    require('../incfiles/head.php');

    $rArticle = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='t' AND `close` != '1'"), 0);
    $qArticle = mysql_query("SELECT * FROM `forum` WHERE `type` = 't' AND `close` != '1' ORDER BY `time` DESC LIMIT $rArticle");
    $xmlCt = file_get_contents($rootpath.'sitemap/sitemap.xml.tpl');
    $htmlCt = str_replace(array('{home}', '{copyright}', '{keywords}', '{description}', '{result}'), array($home, $set['copyright'], functions::createTags($sKeywords), $set['meta_desc'], $rArticle), file_get_contents($rootpath.'sitemap/sitemap.html.tpl'));
    $tab = '    ';
    $outXml = '';
    $outHtml = '';
    if($rArticle > 0) while(($fArticle = mysql_fetch_assoc($qArticle)) != false){
        $nameArt = $fArticle['text'];
        $urlArt = $home.'/forum/' . $fArticle['id'] . '/' . $fArticle['seo'] . '.html';
        $outXml .= "$tab<url>\n$tab$tab<loc>$urlArt</loc>\n$tab$tab<changefreq>daily</changefreq>\n$tab$tab<priority>1.00</priority>\n$tab</url>\n";
        $outHtml .= "\n$tab$tab$tab<div class=\"list\"><a href=\"$urlArt\" title=\"$nameArt\">$nameArt</a></div>";
    }
    mysql_free_result($qArticle);
    $sitemapXml = $rootpath.'sitemap.xml';
    $sitemapHtml = $rootpath.'sitemap.html';
    @unlink($sitemapXml);
    @unlink($sitemapHtml);
    file_put_contents($sitemapXml, str_replace('/*DataInsert*/', $outXml, $xmlCt));
    file_put_contents($sitemapHtml, str_replace('/*DataInsert*/', $outHtml, $htmlCt));

    echo '<div class="phdr">»Sitemap</div>' .
    '<div class="list1 center">Cập nhật <span style="color: red;">'.$rArticle.'</span> bài viết xong.!</div>';

    require('../incfiles/end.php');
