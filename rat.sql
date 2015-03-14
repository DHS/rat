CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(15) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `config` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(180) NOT NULL DEFAULT 'Ratter',
  `tagline` varchar(180) DEFAULT 'Ratter is an app to demonstrate the functionality of <a href="http://github.com/DHS/rat">Rat</a>',
  `beta` tinyint(1) NOT NULL DEFAULT '1',
  `private` tinyint(1) NOT NULL DEFAULT '1',
  `signup_email_notifications` tinyint(1) NOT NULL DEFAULT '0',
  `items` varchar(10000) DEFAULT '{"name":"post","name_plural":"posts","titles":{"enabled":1,"name":"Title","name_plural":"Titles"},"content":{"enabled":1,"name":"Content","name_plural":"Contents"},"uploads":{"enabled":1,"name":"Image","directory":"uploads","max_size":"5242880","mime_types":"image/jpeg,image/png,image/gif,image/pjpeg","aws_s3_bucket":"rat-uploads"},"comments":{"enabled":1,"name":"Comment","name_plural":"Comments"},"likes":{"enabled":1,"name":"Like","name_plural":"Likes","opposite_name":"Unlike","past_tense":"Liked by"}}',
  `timezone` varchar(180) NOT NULL DEFAULT 'Europe/London',
  `invites` varchar(1000) NOT NULL DEFAULT '{"enabled":1}',
  `friends` varchar(1000) NOT NULL DEFAULT '{"enabled":1}',
  `admin_users` varchar(180) NOT NULL DEFAULT '1',
  `theme` varchar(180) NOT NULL DEFAULT 'bootstrap',
  `plugins` varchar(1000) NOT NULL DEFAULT '{"log":1,"gravatar":1,"points":0,"analytics":0}',
  `send_emails` tinyint(1) NOT NULL DEFAULT '0',
  `send_emails_from` varchar(180) DEFAULT NULL,
  `encryption_salt` varchar(180) NOT NULL DEFAULT 'hw9e46',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
INSERT INTO `config` (`name`) VALUES ('Ratter');

CREATE TABLE `friends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(15) NOT NULL DEFAULT '0',
  `friend_user_id` bigint(15) NOT NULL DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `friend_user_id` (`friend_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `invites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(15) NOT NULL,
  `email` varchar(50) DEFAULT '',
  `code` varchar(11) DEFAULT NULL,
  `result` int(1) DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `items` (
  `content` text default NULL,
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(15) NOT NULL DEFAULT '0',
  `title` varchar(140) DEFAULT NULL,
  `image` varchar(140) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(15) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(15) NOT NULL DEFAULT '0',
  `object_type` varchar(50) DEFAULT NULL,
  `object_id` varchar(50) DEFAULT NULL,
  `action` varchar(50) NOT NULL DEFAULT '',
  `params` varchar(50) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `users` (
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
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
