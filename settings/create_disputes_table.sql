CREATE TABLE IF NOT EXISTS `disputes` (
  `dispute_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('open','resolved','rejected') DEFAULT 'open',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dispute_id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `vendor_id` (`vendor_id`),
  CONSTRAINT `disputes_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `disputes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `disputes_ibfk_3` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
