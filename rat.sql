CREATE TABLE `comments` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` bigint(15) NOT NULL default '0',
  `item_id` int(11) NOT NULL default '0',
  `content` text NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

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
  `id` int(11) NOT NULL auto_increment,
  `user_id` bigint(15) NOT NULL,
  `email` varchar(50) default '',
  `code` varchar(11) default NULL,
  `result` int(1) default '0',
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `items` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` bigint(15) NOT NULL default '0',
  `title` varchar(140) default NULL,
  `content` text default NULL,
  `image` varchar(140) default NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `likes` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` bigint(15) NOT NULL default '0',
  `item_id` int(11) NOT NULL default '0',
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `log` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` bigint(15) NOT NULL default '0',
  `object_type` varchar(50) default NULL,
  `object_id` varchar(50) default NULL,
  `action` varchar(50) NOT NULL default '',
  `params` varchar(50) default NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `users` (
  `id` bigint(15) NOT NULL auto_increment,
  `username` varchar(180) default NULL,
  `email` varchar(180) default NULL,
  `full_name` varchar(180) default NULL,
  `bio` varchar(180) default NULL,
  `url` varchar(180) default NULL,
  `points` int(9) default '0',
  `invites` int(9) default '2',
  `password` varchar(180) default NULL,
  `date_added` datetime default NULL,
  `date_joined` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `users_email_notifications` (
  `user_id` int(11) unsigned NOT NULL,
  `notification` varchar(255) DEFAULT NULL,
  `value` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`, `notification`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `users_password_reset` (
  `user_id` bigint(15) unsigned NOT NULL,
  `reset_code` char(32) DEFAULT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
