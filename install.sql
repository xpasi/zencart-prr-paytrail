CREATE TABLE IF NOT EXISTS `prr_suomenpankit` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` int(11) NOT NULL DEFAULT '0',
  `referid` bigint(20) NOT NULL DEFAULT '0',
  `session_id` varchar(32) DEFAULT NULL,
  `method` varchar(32) NOT NULL,
 PRIMARY KEY  (`id`)
)
