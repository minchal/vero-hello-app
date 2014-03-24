
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `autologin` (
  `id` varchar(40) NOT NULL,
  `time` int(11) NOT NULL,
  `user` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
