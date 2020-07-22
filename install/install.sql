
SET time_zone = '+7:00';

--
-- Table structure for table `commentlikes`
--
DROP TABLE IF EXISTS `commentlikes`;
CREATE TABLE IF NOT EXISTS `commentlikes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` int(1) NOT NULL DEFAULT '0',
  `post_id` int(11) NOT NULL,
  `time` int(12) NOT NULL,
  `timeline_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `timeline_id` (`timeline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--
DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` int(1) NOT NULL DEFAULT '0',
  `media_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `time` int(12) NOT NULL,
  `timeline_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `timeline_id` (`timeline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `postlikes`
--
DROP TABLE IF EXISTS `postlikes`;
CREATE TABLE IF NOT EXISTS `postlikes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` int(1) NOT NULL DEFAULT '0',
  `post_id` int(11) NOT NULL DEFAULT '0',
  `reaction` enum('Like','Love','Haha','Hihi','Woww','Cry','Angry') NOT NULL,
  `time` int(12) NOT NULL,
  `timeline_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `timeline_id` (`timeline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--
DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` int(1) NOT NULL DEFAULT '0',
  `activity_text` text COLLATE utf8_unicode_ci NOT NULL,
  `hidden` int(1) NOT NULL DEFAULT '0',
  `post_id` int(11) NOT NULL DEFAULT '0',
  `privacy` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'public',
  `recipient_id` int(11) NOT NULL DEFAULT '0',
  `shared` int(1) NOT NULL DEFAULT '0',
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `time` int(12) NOT NULL,
  `timeline_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `timeline_id` (`timeline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `postshares`
--
DROP TABLE IF EXISTS `postshares`;
CREATE TABLE IF NOT EXISTS `postshares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` int(1) NOT NULL DEFAULT '0',
  `post_id` int(11) NOT NULL DEFAULT '0',
  `time` int(12) NOT NULL,
  `timeline_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `timeline_id` (`timeline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `cms_image`
--
DROP TABLE IF EXISTS `cms_image`;
CREATE TABLE IF NOT EXISTS `cms_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT '0',
  `size` text NOT NULL,
  `url` text NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `bot`
--
DROP TABLE IF EXISTS `bot`;
CREATE TABLE `bot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `key` varchar(100) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `txt1` varchar(500) NOT NULL,
  `txt2` varchar(500) NOT NULL,
  `txt3` varchar(500) NOT NULL,
  `txt4` varchar(500) NOT NULL,
  `txt5` varchar(500) NOT NULL,
  `time` int(15) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Структура таблицы `cms_ads`
--
DROP TABLE IF EXISTS `cms_ads`;
CREATE TABLE `cms_ads` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `type`       TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `view`       TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `layout`     TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `count`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `count_link` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `name`       TEXT                NOT NULL,
  `link`       TEXT                NOT NULL,
  `to`         INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `color`      VARCHAR(10)         NOT NULL DEFAULT '',
  `time`       INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `day`        INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `mesto`      TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `bold`       TINYINT(1)          NOT NULL DEFAULT '0',
  `italic`     TINYINT(1)          NOT NULL DEFAULT '0',
  `underline`  TINYINT(1)          NOT NULL DEFAULT '0',
  `show`       TINYINT(1)          NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_album_cat`
--
DROP TABLE IF EXISTS `cms_album_cat`;
CREATE TABLE `cms_album_cat` (
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`     INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `sort`        INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `name`        VARCHAR(40)         NOT NULL DEFAULT '',
  `description` TEXT                NOT NULL,
  `password`    VARCHAR(20)         NOT NULL DEFAULT '',
  `access`      TINYINT(4) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `access` (`access`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_album_comments`
--
DROP TABLE IF EXISTS `cms_album_comments`;
CREATE TABLE `cms_album_comments` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sub_id`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `time`       INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_id`    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `text`       TEXT             NOT NULL,
  `reply`      TEXT             NOT NULL,
  `attributes` TEXT             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_id` (`sub_id`),
  KEY `user_id` (`user_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_album_downloads`
--
DROP TABLE IF EXISTS `cms_album_downloads`;
CREATE TABLE `cms_album_downloads` (
  `user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `file_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `time`    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`, `file_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_album_files`
--
DROP TABLE IF EXISTS `cms_album_files`;
CREATE TABLE `cms_album_files` (
  `id`              INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`         INT(10) UNSIGNED    NOT NULL,
  `album_id`        INT(10) UNSIGNED    NOT NULL,
  `description`     TEXT                NOT NULL,
  `img_name`        VARCHAR(100)        NOT NULL DEFAULT '',
  `tmb_name`        VARCHAR(100)        NOT NULL DEFAULT '',
  `time`            INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `comments`        TINYINT(1)          NOT NULL DEFAULT '1',
  `comm_count`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `access`          TINYINT(4) UNSIGNED NOT NULL DEFAULT '0',
  `vote_plus`       INT(11)             NOT NULL DEFAULT '0',
  `vote_minus`      INT(11)             NOT NULL DEFAULT '0',
  `views`           INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `downloads`       INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `unread_comments` TINYINT(1)          NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `album_id` (`album_id`),
  KEY `access` (`access`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_album_views`
--
DROP TABLE IF EXISTS `cms_album_views`;
CREATE TABLE `cms_album_views` (
  `user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `file_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `time`    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`, `file_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_album_votes`
--
DROP TABLE IF EXISTS `cms_album_votes`;
CREATE TABLE `cms_album_votes` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `file_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `vote`    TINYINT(2)       NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `file_id` (`file_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_ban_ip`
--
DROP TABLE IF EXISTS `cms_ban_ip`;
CREATE TABLE `cms_ban_ip` (
  `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip1`      BIGINT(11)       NOT NULL DEFAULT '0',
  `ip2`      BIGINT(11)       NOT NULL DEFAULT '0',
  `ban_type` TINYINT(4)       NOT NULL DEFAULT '0',
  `link`     VARCHAR(100)     NOT NULL,
  `who`      VARCHAR(25)      NOT NULL,
  `reason`   TEXT             NOT NULL,
  `date`     INT(11)          NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip1` (`ip1`),
  UNIQUE KEY `ip2` (`ip2`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_ban_users`
--
DROP TABLE IF EXISTS `cms_ban_users`;
CREATE TABLE `cms_ban_users` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT(11)          NOT NULL DEFAULT '0',
  `ban_time`   INT(11)          NOT NULL DEFAULT '0',
  `ban_while`  INT(11)          NOT NULL DEFAULT '0',
  `ban_type`   TINYINT(4)       NOT NULL DEFAULT '1',
  `ban_who`    VARCHAR(30)      NOT NULL DEFAULT '',
  `ban_ref`    INT(11)          NOT NULL DEFAULT '0',
  `ban_reason` TEXT             NOT NULL,
  `ban_raz`    VARCHAR(30)      NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ban_time` (`ban_time`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_contact`
--
DROP TABLE IF EXISTS `cms_contact`;
CREATE TABLE IF NOT EXISTS `cms_contact` (
  `id`      INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `from_id` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `time`    INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `type`    TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `friends` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `ban`     TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `man`     TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_user` (`user_id`, `from_id`),
  KEY `time` (`time`),
  KEY `ban` (`ban`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_counters`
--
DROP TABLE IF EXISTS `cms_counters`;
CREATE TABLE `cms_counters` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort`   INT(10)          NOT NULL DEFAULT '1',
  `name`   VARCHAR(30)      NOT NULL,
  `link1`  TEXT             NOT NULL,
  `link2`  TEXT             NOT NULL,
  `mode`   TINYINT(4)       NOT NULL DEFAULT '1',
  `switch` TINYINT(1)       NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_forum_files`
--
DROP TABLE IF EXISTS `cms_forum_files`;
CREATE TABLE `cms_forum_files` (
  `id`       INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `cat`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `subcat`   INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `topic`    INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `post`     INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `time`     INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `filename` TEXT                NOT NULL,
  `filetype` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `dlcount`  INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `del`      TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cat` (`cat`),
  KEY `subcat` (`subcat`),
  KEY `topic` (`topic`),
  KEY `post` (`post`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_forum_rdm`
--
DROP TABLE IF EXISTS `cms_forum_rdm`;
CREATE TABLE `cms_forum_rdm` (
  `topic_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_id`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `time`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`topic_id`, `user_id`),
  KEY `time` (`time`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_forum_vote`
--
DROP TABLE IF EXISTS `cms_forum_vote`;
CREATE TABLE `cms_forum_vote` (
  `id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`  INT(2)           NOT NULL DEFAULT '0',
  `time`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `topic` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `name`  VARCHAR(200)     NOT NULL,
  `count` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `topic` (`topic`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_forum_vote_users`
--
DROP TABLE IF EXISTS `cms_forum_vote_users`;
CREATE TABLE `cms_forum_vote_users` (
  `id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user`  INT(11)          NOT NULL DEFAULT '0',
  `topic` INT(11)          NOT NULL,
  `vote`  INT(11)          NOT NULL,
  PRIMARY KEY (`id`),
  KEY `topic` (`topic`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_mail`
--
DROP TABLE IF EXISTS `cms_mail`;
CREATE TABLE IF NOT EXISTS `cms_mail` (
  `id`        INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`   INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `from_id`   INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `text`      TEXT                NOT NULL,
  `time`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `read`      TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `sys`       TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `type_mod` VARCHAR(100)        NOT NULL DEFAULT '',
  `post_id` int(11) NOT NULL,
  `delete`    INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `file_name` VARCHAR(100)        NOT NULL DEFAULT '',
  `count`     INT(10)             NOT NULL DEFAULT '0',
  `size`      INT(10)             NOT NULL DEFAULT '0',
  `them`      VARCHAR(100)        NOT NULL DEFAULT '',
  `spam`      TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `from_id` (`from_id`),
  KEY `time` (`time`),
  KEY `read` (`read`),
  KEY `sys` (`sys`),
  KEY `delete` (`delete`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_sessions`
--
DROP TABLE IF EXISTS `cms_sessions`;
CREATE TABLE `cms_sessions` (
  `session_id`   CHAR(32)             NOT NULL DEFAULT '',
  `ip`           BIGINT(11)           NOT NULL DEFAULT '0',
  `ip_via_proxy` BIGINT(11)           NOT NULL DEFAULT '0',
  `browser`      VARCHAR(255)         NOT NULL DEFAULT '',
  `lastdate`     INT(10) UNSIGNED     NOT NULL DEFAULT '0',
  `sestime`      INT(10) UNSIGNED     NOT NULL DEFAULT '0',
  `views`        INT(10) UNSIGNED     NOT NULL DEFAULT '0',
  `movings`      SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `place`        VARCHAR(100)         NOT NULL DEFAULT '',
  PRIMARY KEY (`session_id`),
  KEY `lastdate` (`lastdate`),
  KEY `place` (`place`(10))
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_settings`
--
DROP TABLE IF EXISTS `cms_settings`;
CREATE TABLE `cms_settings` (
  `key` TINYTEXT NOT NULL,
  `val` TEXT     NOT NULL,
  PRIMARY KEY (`key`(30))
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

INSERT INTO `cms_settings` (`key`, `val`) VALUES
  ('active', '1'),
  ('admp', 'panel'),
  ('antiflood', 'a:5:{s:4:"mode";i:2;s:3:"day";i:10;s:5:"night";i:30;s:7:"dayfrom";i:10;s:5:"dayto";i:22;}'),
  ('clean_time', '1'),
  ('copyright', 'Powered by JohnCMS'),
  ('email', ''),
  ('flsz', '4000'),
  ('gzip', '1'),
  ('homeurl', ''),
  ('karma', 'a:6:{s:12:"karma_points";i:5;s:10:"karma_time";i:86400;s:5:"forum";i:20;s:4:"time";i:0;s:2:"on";i:1;s:3:"adm";i:0;}'),
  ('lng', 'vn'),
  ('mod_reg', '2'),
  ('mod_forum', '2'),
  ('mod_guest', '2'),
  ('mod_lib', '2'),
  ('mod_gal', '2'),
  ('mod_down_comm', '1'),
  ('mod_down', '2'),
  ('mod_lib_comm', '1'),
  ('mod_gal_comm', '1'),
  ('meta_desc', 'Powered by JohnCMS http://johncms.com'),
  ('meta_key', 'johncms'),
  ('news', 'a:8:{s:4:"view";i:1;s:4:"size";i:200;s:8:"quantity";i:5;s:4:"days";i:3;s:6:"breaks";i:1;s:7:"smileys";i:1;s:4:"tags";i:1;s:3:"kom";i:1;}'),
  ('reg_message', ''),
  ('setting_mail', ''),
  ('skindef', 'default'),
  ('them_message', ''),
  ('timeshift', '0'),
  ('site_access', '2');

--
-- Структура таблицы `cms_users_data`
--
DROP TABLE IF EXISTS `cms_users_data`;
CREATE TABLE `cms_users_data` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `key`     VARCHAR(30)      NOT NULL DEFAULT '',
  `val`     TEXT             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `key` (`key`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_users_guestbook`
--
DROP TABLE IF EXISTS `cms_users_guestbook`;
CREATE TABLE `cms_users_guestbook` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sub_id`     INT(10) UNSIGNED NOT NULL,
  `time`       INT(11)          NOT NULL,
  `user_id`    INT(10) UNSIGNED NOT NULL,
  `text`       TEXT             NOT NULL,
  `reply`      TEXT             NOT NULL,
  `attributes` TEXT             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_id` (`sub_id`),
  KEY `user_id` (`user_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_users_iphistory`
--
DROP TABLE IF EXISTS `cms_users_iphistory`;
CREATE TABLE `cms_users_iphistory` (
  `id`           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`      INT(10) UNSIGNED    NOT NULL,
  `ip`           BIGINT(11)          NOT NULL DEFAULT '0',
  `ip_via_proxy` BIGINT(11)          NOT NULL DEFAULT '0',
  `time`         INT(10) UNSIGNED    NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_ip` (`ip`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `download`
--
DROP TABLE IF EXISTS `download`;
CREATE TABLE `download` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `refid`  INT(11)          NOT NULL DEFAULT '0',
  `adres`  TEXT             NOT NULL,
  `time`   INT(11)          NOT NULL DEFAULT '0',
  `name`   TEXT             NOT NULL,
  `type`   VARCHAR(4)       NOT NULL DEFAULT '',
  `avtor`  VARCHAR(25)      NOT NULL DEFAULT '',
  `ip`     TEXT             NOT NULL,
  `soft`   TEXT             NOT NULL,
  `text`   TEXT             NOT NULL,
  `screen` TEXT             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `refid` (`refid`),
  KEY `time` (`time`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;


--
-- Table structure for table `shopitem`
--
DROP TABLE IF EXISTS `shopitem`;
CREATE TABLE `shopitem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lever` int(11) NOT NULL,
  `gia` int(11) NOT NULL,
  `doben` int(11) NOT NULL,
  `ten` varchar(1024) DEFAULT NULL,
  `chucnang` varchar(1024) DEFAULT NULL,
  `note` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `shopitem`
--

INSERT INTO `shopitem` (`id`, `lever`, `gia`, `doben`, `ten`, `chucnang`, `note`) VALUES
(1, 10, 2000, 17, 'mây mưa', '1', 'có khả năng tưới nước 1 loạt cho tất cả các cây, giúp bạn tưới nước rễ dàng hơn.'),
(2, 3, 1000, 15, 'phân bón C1', '2', 'khi sử dụng item, tất cả các hạt giống đã trồng sẽ tăng 15% số lượng nông sản khi thu hoạch'),
(3, 5, 2500, 16, 'phân bón C2', '3', 'giúp tăng 20% số lượng nông sản sau khi gieo hạt giống.'),
(4, 7, 3000, 13, 'phân bón C3', '4', 'giúp tăng 25% số lượng nông sản sau khi gieo hạt giống.'),
(5, 10, 4200, 12, 'phân bón C4', '5', 'giúp tăng 35% số lượng nông sản sau khi gieo hạt giống.'),
(6, 16, 6000, 10, 'phân bón C5', '6', 'giúp tăng 40% số lượng nông sản sau khi gieo hạt giống.'),
(7, 24, 8000, 9, 'phân bón C6', '7', 'giúp tăng 50% số lượng nông sản sau khi gieo hạt giống.'),
(8, 3, 900, 14, 'Phu thuốc giệt cỏ', '8', 'Giệt sạch cỏ dại cho tất cả cây trồng.'),
(9, 4, 1000, 14, 'Phun thuốc trừ sâu', '9', 'Giệt sâu hại cho tất cả cây trồng.'),
(10, 5, 950, 12, 'Xới đất tất cả', '10', 'Xới tất cả những cây trồng đã chết.');

--
-- Table structure for table `fermer_dog`
--
DROP TABLE IF EXISTS `fermer_dog`;
CREATE TABLE `fermer_dog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `time` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `fermer_gr`
--
DROP TABLE IF EXISTS `fermer_gr`;
CREATE TABLE `fermer_gr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `semen` int(11) NOT NULL,
  `woter` int(11) NOT NULL DEFAULT '0',
  `xoidat` int(11) NOT NULL DEFAULT '0',
  `vu` int(11) NOT NULL DEFAULT '1',
  `co` int(11) NOT NULL DEFAULT '0',
  `sau` int(11) NOT NULL DEFAULT '0',
  `data_n` int(11) NOT NULL DEFAULT '0',
  `data_c` int(11) NOT NULL DEFAULT '0',
  `data_s` int(11) NOT NULL DEFAULT '0',
  `kol` varchar(1024) DEFAULT NULL,
  `time` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `fermer_name`
--
DROP TABLE IF EXISTS `fermer_name`;
CREATE TABLE `fermer_name` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(1024) NOT NULL,
  `cena` varchar(1024) NOT NULL,
  `dohod` varchar(1024) NOT NULL,
  `rand1` varchar(1024) NOT NULL,
  `rand2` varchar(1024) NOT NULL,
  `oput` varchar(1024) NOT NULL,
  `time` varchar(1024) DEFAULT NULL,
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `vu` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `fermer_name`
--

INSERT INTO `fermer_name` (`id`, `name`, `cena`, `dohod`, `rand1`, `rand2`, `oput`, `time`, `level`, `vu`) VALUES
(1, 'Củ cải', '200', '13', '32', '36', '20', '18000', 0, 1),
(2, 'Ngô', '500', '28', '39', '42', '45', '25200', 2, 1),
(3, 'Cà tím', '800', '34', '51', '54', '70', '32400', 4, 1),
(4, 'Cà chua', '950', '34', '33', '35', '110', '39600', 7, 2),
(5, 'Ớt', '1100', '35', '33', '39', '170', '45000', 10, 2),
(6, 'Bí ngô', '1250', '43', '32', '36', '320', '45000', 13, 2),
(7, 'Dâu tây', '1550', '42', '40', '45', '670', '54000', 17, 2),
(8, 'Táo', '1800', '55', '36', '49', '1120', '61200', 21, 2),
(9, 'Dưa hấu', '1975', '55', '40', '42', '1356', '75600', 24, 2),
(10, 'Chuối', '2150', '56', '45', '47', '1669', '75600', 27, 1),
(11, 'Cam', '3350', '80', '47', '50', '2022', '111600', 30, 2),
(12, 'Nho', '4125', '86', '59', '62', '2250', '147600', 33, 2),
(13, 'Dưa lưới', '4600', '97', '50', '53', '2551', '147600', 36, 2),
(14, 'Dứa', '5200', '101', '56', '60', '2750', '72000', 39, 1),
(15, 'Lúa nước', '7000', '105', '110', '120', '3000', '118800', 42, 1),
(16, 'Măng cụt', '50000', '700', '100', '115', '3500', '72000', 45, 3);

--
-- Table structure for table `fermer_sclad`
--
DROP TABLE IF EXISTS `fermer_sclad`;
CREATE TABLE `fermer_sclad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `semen` int(11) NOT NULL,
  `kol` int(11) NOT NULL,
  `time` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `fermer_udobr`
--
DROP TABLE IF EXISTS `fermer_udobr`;
CREATE TABLE `fermer_udobr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `udobr` int(11) NOT NULL,
  `kol` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `fermer_udobr_name`
--
DROP TABLE IF EXISTS `fermer_udobr_name`;
CREATE TABLE `fermer_udobr_name` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(1024) NOT NULL,
  `cena` varchar(1024) NOT NULL,
  `time` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `fermer_udobr_name`
--

INSERT INTO `fermer_udobr_name` (`id`, `name`, `cena`, `time`) VALUES
(1, 'Than bùn', '200', '1800'),
(2, 'Phân hữu cơ', '250', '7200'),
(3, 'Phân đạm', '500', '21600'),
(4, 'Chất khoáng', '1500', '25200'),
(5, 'Phân lân', '5000', '32400'),
(6, 'Phân nitơ', '10500', '54000'),
(7, 'Phân bột', '25000', '86400'),
(8, 'Phân cấp cao', '55000', '172800');

--
-- Table structure for table `fermer_vor`
--
DROP TABLE IF EXISTS `fermer_vor`;
CREATE TABLE `fermer_vor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `gr` int(11) DEFAULT NULL,
  `time` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `fermer_gr_VN`
--
DROP TABLE IF EXISTS `fermer_gr_VN`;
CREATE TABLE `fermer_gr_VN` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `semen` int(11) NOT NULL,
  `choan` int(11) NOT NULL DEFAULT '0',
  `timechoan` varchar(1024) DEFAULT NULL,
  `songtrong` varchar(1024) DEFAULT NULL,
  `kol` varchar(1024) DEFAULT NULL,
  `time` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `fermer_name_VN`
--
DROP TABLE IF EXISTS `fermer_name_VN`;
CREATE TABLE `fermer_name_VN` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(1024) NOT NULL,
  `cena` varchar(1024) NOT NULL,
  `dohod` varchar(1024) NOT NULL,
  `rand1` varchar(1024) NOT NULL,
  `rand2` varchar(1024) NOT NULL,
  `oput` varchar(1024) NOT NULL,
  `time` varchar(1024) DEFAULT NULL,
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `vu` varchar(1024) NOT NULL,
  `note` varchar(1024) NOT NULL,
  `donvi` varchar(1024) NOT NULL,
  `songtrong` varchar(1024) DEFAULT NULL,
  `type` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `fermer_name_VN`
--

INSERT INTO `fermer_name_VN` (`id`, `name`, `cena`, `dohod`, `rand1`, `rand2`, `oput`, `time`, `level`, `vu`, `note`, `donvi`, `songtrong`, `type`) VALUES
(1, 'Gà', '200', '13', '20', '30', '520', '86400', 15, 1, 'Trứng Gà', 'quả', '432000', '1'),
(2, 'Heo', '200', '28', '80', '120', '50', '172800', 22, 1,'Thịt Heo', 'kg', '604800', '2'),
(3, 'Bò sữa', '200', '34', '51', '54', '75', '129600', 33, 1, 'Sữa Bò', 'lít', '604800', '3'),
(4, 'Cừu', '200', '43', '32', '36', '800', '216000', 40, 1, 'Lông Cừu', 'kg', '864000', '4');

--
-- Table structure for table `fermer_sclad_VN`
--
DROP TABLE IF EXISTS `fermer_sclad_VN`;
CREATE TABLE `fermer_sclad_VN` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `semen` int(11) NOT NULL,
  `kol` int(11) NOT NULL,
  `time` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `fermer_mua`
--
DROP TABLE IF EXISTS `fermer_mua`;
CREATE TABLE `fermer_mua` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `semen` int(11) NOT NULL,
  `kol` int(11) NOT NULL,
  `time` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `fermer_mua_VN`
--
DROP TABLE IF EXISTS `fermer_mua_VN`;
CREATE TABLE `fermer_mua_VN` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `semen` int(11) NOT NULL,
  `kol` int(11) NOT NULL,
  `time` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `fermer_TAVN`
--
DROP TABLE IF EXISTS `fermer_TAVN`;
CREATE TABLE `fermer_TAVN` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `udobr` int(11) NOT NULL,
  `kol` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `fermer_TAVN_name`
--
DROP TABLE IF EXISTS `fermer_TAVN_name`;
CREATE TABLE `fermer_TAVN_name` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(1024) NOT NULL,
  `cena` varchar(1024) NOT NULL,
  `time` varchar(1024) DEFAULT NULL,
  `note` varchar(1024) NOT NULL,
  `type` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `fermer_TAVN_name`
--

INSERT INTO `fermer_TAVN_name` (`id`, `name`, `cena`, `time`, `note`, `type`) VALUES
(1, 'Thức ăn cho Gà', '50', '600', 'Thức ăn dành giêng cho Gà', '1'),
(2, 'Thức ăn cho Heo', '70', '600', 'Thức ăn dành giêng cho Heo', '2'),
(3, 'Thức ăn cho Bò', '90', '600', 'Thức ăn dành giêng cho Bò sữa', '3'),
(4, 'Thức ăn cho Cừu', '110', '600', 'Thức ăn dành giêng cho Cừu', '4');



--
-- Table structure for table `fermer_vor_VN`
--
DROP TABLE IF EXISTS `fermer_vor_VN`;
CREATE TABLE `fermer_vor_VN` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `gr` int(11) DEFAULT NULL,
  `time` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `g`
--
DROP TABLE IF EXISTS `g`;
CREATE TABLE `g` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ua` text NOT NULL,
  `sess` text NOT NULL,
  `time` int(11) NOT NULL,
  `iplong` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `khodo`
--
DROP TABLE IF EXISTS `khodo`;
CREATE TABLE `khodo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_do` int(11) NOT NULL,
  `ten` varchar(1024) DEFAULT NULL,
  `lv` int(11) NOT NULL,
  `hong` int(11) NOT NULL,
  `chucnang` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Структура таблицы `forum`
--
DROP TABLE IF EXISTS `forum`;
CREATE TABLE `forum` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `refid`        INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `type`         CHAR(1)          NOT NULL DEFAULT '',
  `time`         INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_id`      INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `from`         VARCHAR(25)      NOT NULL DEFAULT '',
  `realid`       INT(3)           NOT NULL DEFAULT '0',
  `ip`           BIGINT(11)       NOT NULL DEFAULT '0',
  `ip_via_proxy` BIGINT(11)       NOT NULL DEFAULT '0',
  `soft`         TEXT             NOT NULL,
  `text`         TEXT             NOT NULL,
  `tags`         TEXT             NOT NULL,
  `close`        TINYINT(1)       NOT NULL DEFAULT '0',
  `close_who`    VARCHAR(25)      NOT NULL DEFAULT '',
  `vip`          TINYINT(1)       NOT NULL DEFAULT '0',
  `edit`         TEXT             NOT NULL,
  `tedit`        INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `kedit`        INT(2) UNSIGNED  NOT NULL DEFAULT '0',
  `curators`     TEXT             NOT NULL,
  `seo` varchar(500) NOT NULL,
  `view` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `refid` (`refid`),
  KEY `type` (`type`),
  KEY `time` (`time`),
  KEY `close` (`close`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `text` (`text`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Table structure for table `forum_thank`
--
DROP TABLE IF EXISTS `forum_thank`;
CREATE TABLE `forum_thank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT '0',
  `topic` int(11) NOT NULL,
  `userthank` int(11) NOT NULL,
  `chude` int(11) NOT NULL,
  `time` text NOT NULL,
  `reaction_type` enum('Like','Love','Haha','Hihi','Woww','Cry','Angry') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_msg_id_reaction_type` (`topic`,`reaction_type`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Структура таблицы `gallery`
--
DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `refid` INT(11)          NOT NULL DEFAULT '0',
  `time`  INT(11)          NOT NULL DEFAULT '0',
  `type`  VARCHAR(2)       NOT NULL DEFAULT '',
  `avtor` VARCHAR(25)      NOT NULL DEFAULT '',
  `text`  TEXT             NOT NULL,
  `name`  TEXT             NOT NULL,
  `user`  BINARY(1)        NOT NULL DEFAULT '\0',
  `ip`    TEXT             NOT NULL,
  `soft`  TEXT             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `refid` (`refid`),
  KEY `type` (`type`),
  KEY `time` (`time`),
  KEY `avtor` (`avtor`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `guest`
--
DROP TABLE IF EXISTS `guest`;
CREATE TABLE `guest` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `adm`        TINYINT(1)          NOT NULL DEFAULT '0',
  `time`       INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `user_id`    INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `name`       VARCHAR(25)         NOT NULL DEFAULT '',
  `text`       TEXT                NOT NULL,
  `ip`         BIGINT(11)          NOT NULL DEFAULT '0',
  `browser`    TINYTEXT            NOT NULL,
  `admin`      VARCHAR(25)         NOT NULL DEFAULT '',
  `otvet`      TEXT                NOT NULL,
  `otime`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `edit_who`   VARCHAR(20)         NOT NULL DEFAULT '',
  `edit_time`  INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `edit_count` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `time` (`time`),
  KEY `ip` (`ip`),
  KEY `adm` (`adm`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `karma_users`
--
DROP TABLE IF EXISTS `karma_users`;
CREATE TABLE `karma_users` (
  `id`         INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`    INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `name`       VARCHAR(50)         NOT NULL DEFAULT '',
  `karma_user` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `points`     TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `type`       TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `time`       INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `text`       TEXT                NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `karma_user` (`karma_user`),
  KEY `type` (`type`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `library_cats`
--
DROP TABLE IF EXISTS `library_cats`;
CREATE TABLE `library_cats` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent`      INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `name`        VARCHAR(200)     NOT NULL DEFAULT '',
  `description` TEXT             NOT NULL,
  `dir`         TINYINT(1)       NOT NULL DEFAULT '0',
  `pos`         INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_add`    TINYINT(1)       NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `library_texts`
--
DROP TABLE IF EXISTS `library_texts`;
CREATE TABLE `library_texts` (
  `id`             INT(10) UNSIGNED  NOT NULL AUTO_INCREMENT,
  `cat_id`         INT(10)  UNSIGNED NOT NULL DEFAULT '0',
  `text`           MEDIUMTEXT        NOT NULL,
  `name`           VARCHAR(255)      NOT NULL DEFAULT '',
  `announce`       TEXT,
  `uploader`       VARCHAR(100)      NOT NULL DEFAULT '',
  `uploader_id`    INT(10)  UNSIGNED NOT NULL DEFAULT '0',
  `count_views`    INT(10) UNSIGNED  NOT NULL DEFAULT '0',
  `premod`         TINYINT(1)        NOT NULL DEFAULT '0',
  `comments`       TINYINT(1)        NOT NULL DEFAULT '0',
  `count_comments` INT(10)  UNSIGNED NOT NULL DEFAULT '0',
  `time`           INT(10) UNSIGNED  NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `text` (`text`, `name`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `library_tags`
--
DROP TABLE IF EXISTS `library_tags`;
CREATE TABLE `library_tags` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lib_text_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `tag_name`    VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `lib_text_id` (`lib_text_id`),
  KEY `tag_name` (`tag_name`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_library_comments`
--
DROP TABLE IF EXISTS `cms_library_comments`;
CREATE TABLE `cms_library_comments` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sub_id`     INT(11) UNSIGNED NOT NULL,
  `time`       INT(11)          NOT NULL,
  `user_id`    INT(11) UNSIGNED NOT NULL,
  `text`       TEXT             NOT NULL,
  `reply`      TEXT,
  `attributes` TEXT             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_id` (`sub_id`),
  KEY `user_id` (`user_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Структура таблицы `cms_library_rating`
--
DROP TABLE IF EXISTS `cms_library_rating`;  
CREATE TABLE IF NOT EXISTS `cms_library_rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `st_id` int(11) NOT NULL,
  `point` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`st_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;  

--
-- Структура таблицы `news`
--
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `avt`  VARCHAR(25)      NOT NULL DEFAULT '',
  `name` TEXT             NOT NULL,
  `text` TEXT             NOT NULL,
  `seo` varchar(500) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kom`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

--
-- Structure de la table `status`
--
DROP TABLE IF EXISTS `status`;
CREATE TABLE `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `time` int(11) NOT NULL,
  `stime` int(11) NOT NULL,
  `img` int(1) NOT NULL,
  `share` int(11) NOT NULL DEFAULT '0',
  `gshare` int(11) NOT NULL DEFAULT '0',
  `tshare` int(11) NOT NULL DEFAULT '0',
  `wall` int(11) NOT NULL,
  `tid` int(1) NOT NULL,
  `riengtu` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Structure de la table `status_bl`
--
DROP TABLE IF EXISTS `status_bl`;
CREATE TABLE `status_bl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `type` varchar(1) NOT NULL,
  `time` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `view` int(11) NOT NULL DEFAULT '0',
  `user_fr` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Structure de la table `status_cmt`
--
DROP TABLE IF EXISTS `status_cmt`;
CREATE TABLE `status_cmt` (
  `id` int(11) NOT NULL,
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Structure de la table `status_like`
--
DROP TABLE IF EXISTS `status_like`;
CREATE TABLE `status_like` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Structure de la table `status_user`
--
DROP TABLE IF EXISTS `status_user`;
CREATE TABLE `status_user` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Structure de la table `nhom`
--
DROP TABLE IF EXISTS `nhom`;
CREATE TABLE `nhom` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `gt` text NOT NULL,
  `set` INT(1) NOT NULL,
  `time` INT NOT NULL,
  `user_id` INT NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Structure de la table `nhom_user`
--
DROP TABLE IF EXISTS `nhom_user`;
CREATE TABLE `nhom_user` (
  `user_id` INT NOT NULL,
  `id` INT NOT NULL,
  `time` INT NOT NULL,
  `stime` INT NOT NULL,
  `view` int(11) NOT NULL DEFAULT '0',
  `rights` INT(1) NOT NULL,
  `theo` INT(1) NOT NULL,
  `duyet` INT(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Structure de la table `nhom_bd`
--
DROP TABLE IF EXISTS `nhom_bd`;
CREATE TABLE `nhom_bd` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `sid` INT NOT NULL,
  `cid` INT NOT NULL,
  `user_id` INT NOT NULL,
  `time` INT NOT NULL,
  `stime` INT NOT NULL,
  `text` TEXT NOT NULL,
  `type` INT(1) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Structure de la table `nhom_like`
--
DROP TABLE IF EXISTS `nhom_like`;
CREATE TABLE `nhom_like` (
  `user_id` INT NOT NULL,
  `id` INT NOT NULL,
  `time` INT NOT NULL,
  `type` INT(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Структура таблицы `users`
--
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`            INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(40)         NOT NULL DEFAULT '',
  `name_lat`      VARCHAR(40)         NOT NULL DEFAULT '',
  `password`      VARCHAR(32)         NOT NULL DEFAULT '',
  `rights`        TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `failed_login`  TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `imname`        VARCHAR(50)         NOT NULL DEFAULT '',
  `sex`           VARCHAR(2)          NOT NULL DEFAULT '',
  `facebook_ID`          TEXT    NOT NULL,
  `facebook_Link`          TEXT    NOT NULL,
  `facebook_Update`       INT(10) UNSIGNED NOT NULL,
  `google_ID`          TEXT    NOT NULL,
  `google_Link`          TEXT    NOT NULL,
  `google_Update`       INT(10) UNSIGNED NOT NULL,
  `komm`          INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `postforum`     INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `postguest`     INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `poststatus` int(11) NOT NULL DEFAULT '0',
  `postgroup` int(11) NOT NULL DEFAULT '0',
  `blstatus` int(11) NOT NULL DEFAULT '0',
  `yearofbirth`   INT(4)              NOT NULL DEFAULT '0',
  `datereg`       INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `lastdate`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `mail`          VARCHAR(50)         NOT NULL DEFAULT '',
  `icq`           INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `skype`         VARCHAR(50)         NOT NULL DEFAULT '',
  `jabber`        VARCHAR(50)         NOT NULL DEFAULT '',
  `www`           VARCHAR(50)         NOT NULL DEFAULT '',
  `about`         TEXT                NOT NULL,
  `live`          VARCHAR(100)        NOT NULL DEFAULT '',
  `mibile`        VARCHAR(50)         NOT NULL DEFAULT '',
  `status`        VARCHAR(100)        NOT NULL DEFAULT '',
  `ip`            BIGINT(11)          NOT NULL DEFAULT '0',
  `ip_via_proxy`  BIGINT(11)          NOT NULL DEFAULT '0',
  `browser`       TEXT                NOT NULL,
  `preg`          TINYINT(1)          NOT NULL DEFAULT '0',
  `regadm`        VARCHAR(25)         NOT NULL DEFAULT '',
  `mailvis`       TINYINT(1)          NOT NULL DEFAULT '0',
  `dayb`          INT(2)              NOT NULL DEFAULT '0',
  `monthb`        INT(2)              NOT NULL DEFAULT '0',
  `sestime`       INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `total_on_site` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `lastpost`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `rest_code`     VARCHAR(32)         NOT NULL DEFAULT '',
  `rest_time`     INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `movings`       INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `place`         VARCHAR(30)         NOT NULL DEFAULT '',
  `set_user`      TEXT                NOT NULL,
  `set_forum`     TEXT                NOT NULL,
  `set_mail`      TEXT                NOT NULL,
  `set_anhbia`      TEXT                NOT NULL,
  `karma_plus`    INT(11)             NOT NULL DEFAULT '0',
  `karma_minus`   INT(11)             NOT NULL DEFAULT '0',
  `karma_time`    INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `karma_off`     TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `comm_count`    INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `comm_old`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `smileys`       TEXT                NOT NULL,
  `stt`       TEXT                NOT NULL,
  `bl` int(255) NOT NULL DEFAULT '0',
  `vgold` int(255) NOT NULL DEFAULT '10000',
  `balans` varchar(1024) NOT NULL,
  `thank_duoc` int(11) NOT NULL DEFAULT '0',
  `thank_di` int(11) NOT NULL DEFAULT '0',
  `fermer_oput` int(255) NOT NULL DEFAULT '0',
  `fermer_level` int(255) NOT NULL DEFAULT '0',
  `active` int(1) NOT NULL DEFAULT '0',
  `avatar_id` int(11) NOT NULL DEFAULT '0',
  `avatar_extension` varchar(4) NOT NULL DEFAULT 'none',
  `cover_id` int(11) NOT NULL DEFAULT '0',
  `cover_extension` varchar(4) NOT NULL DEFAULT 'none',
  `cover_position` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name_lat` (`name_lat`),
  KEY `lastdate` (`lastdate`),
  KEY `place` (`place`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;