CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(15) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

CREATE TABLE `config` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` bigint(15) DEFAULT NULL,
  `key` varchar(180) CHARACTER SET utf8 DEFAULT NULL,
  `value` varchar(180) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_id` (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `friends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(15) NOT NULL DEFAULT '0',
  `friend_user_id` bigint(15) NOT NULL DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `invites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
  `email` varchar(50) DEFAULT '',
  `code` varchar(11) DEFAULT NULL,
  `result` int(1) DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

CREATE TABLE `items` (
  `content` text default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(15) NOT NULL DEFAULT '0',
  `title` varchar(140) DEFAULT NULL,
  `image` varchar(140) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

CREATE TABLE `likes` (
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(15) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

CREATE TABLE `log` (
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(15) NOT NULL DEFAULT '0',
  `object_type` varchar(50) DEFAULT NULL,
  `object_id` varchar(50) DEFAULT NULL,
  `action` varchar(50) NOT NULL DEFAULT '',
  `params` varchar(50) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

CREATE TABLE `users` (
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
  `id` bigint(15) NOT NULL AUTO_INCREMENT,
  `username` varchar(180) DEFAULT NULL,
  `email` varchar(180) DEFAULT NULL,
  `full_name` varchar(180) DEFAULT NULL,
  `bio` varchar(180) DEFAULT NULL,
  `url` varchar(180) DEFAULT NULL,
  `points` int(9) DEFAULT '0',
  `invites` int(9) DEFAULT '2',
  `password` varchar(180) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_joined` datetime DEFAULT NULL,

CREATE TABLE `users_email_notifications` (
  `user_id` int(11) unsigned NOT NULL,
  `notification` varchar(255) DEFAULT NULL,
  `value` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`, `notification`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `users_password_reset` (
  `user_id` bigint(15) unsigned NOT NULL,
  `reset_code` char(32) DEFAULT NULL,
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
