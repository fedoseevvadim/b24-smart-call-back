create table if not exists scb_stat
(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `query_id` int(11) DEFAULT '0',
  `status_id` int(1) DEFAULT '0',
  `status_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type_id` int(1) DEFAULT '0',
  `type_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_create` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `utm_source` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `utm_medium` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `utm_campaign` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `utm_term` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `utm_content` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `utm_updated` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `record_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `duration` int(5) DEFAULT '0',
  `record_written` int(1) DEFAULT '0',
  `lead` int(11) DEFAULT '0',
  `deal` int(11) DEFAULT '0',
  `id_record_bx` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
);
