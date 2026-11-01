SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS `layisi_shoe_centre`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `layisi_shoe_centre`;

DROP TABLE IF EXISTS `loyalty_points`;
DROP TABLE IF EXISTS `newsletter_subscribers`;
DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `wishlist`;
DROP TABLE IF EXISTS `site_settings`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `inventory_history`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `product_descriptions`;
DROP TABLE IF EXISTS `product_size_stock`;
DROP TABLE IF EXISTS `product_stock`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `product_prices`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `rate_limit_attempts`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `user_roles`;
DROP TABLE IF EXISTS `profiles`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id`            CHAR(36)      NOT NULL DEFAULT (UUID()),
  `email`         VARCHAR(255)  NOT NULL,
  `password_hash` VARCHAR(255)  NOT NULL,
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `profiles` (
  `id`         CHAR(36)     NOT NULL DEFAULT (UUID()),
  `user_id`    CHAR(36)     NOT NULL,
  `full_name`  VARCHAR(255) NULL,
  `phone`      VARCHAR(32)  NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_profiles_user_id` (`user_id`),
  CONSTRAINT `fk_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_roles` (
  `id`      CHAR(36)                 NOT NULL DEFAULT (UUID()),
  `user_id` CHAR(36)                 NOT NULL,
  `role`    ENUM('admin','user')     NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_roles` (`user_id`, `role`),
  KEY `idx_user_roles_user` (`user_id`),
  CONSTRAINT `fk_user_roles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_stock` (
  `id`         CHAR(36)    NOT NULL DEFAULT (UUID()),
  `product_id` VARCHAR(64) NOT NULL,
  `quantity`   INT         NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_product_stock_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_size_stock` (
  `id`         CHAR(36)    NOT NULL DEFAULT (UUID()),
  `product_id` VARCHAR(64) NOT NULL,
  `size`       VARCHAR(8)  NOT NULL,
  `quantity`   INT         NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_product_size_stock` (`product_id`, `size`),
  CONSTRAINT `fk_size_stock_product` FOREIGN KEY (`product_id`) REFERENCES `product_stock` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
  `id`         CHAR(36)     NOT NULL DEFAULT (UUID()),
  `name`       VARCHAR(100) NOT NULL,
  `slug`       VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_name` (`name`),
  UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_prices` (
  `product_id`     VARCHAR(64)   NOT NULL,
  `price`          DECIMAL(12,2) NOT NULL,
  `original_price` DECIMAL(12,2) NULL,
  `discount`       INT           NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_images` (
  `id`         CHAR(36)     NOT NULL DEFAULT (UUID()),
  `product_id` VARCHAR(64)  NOT NULL,
  `image_url`  VARCHAR(500) NOT NULL,
  `sort_order` INT          NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product_images_product` (`product_id`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rate_limit_attempts` (
  `id`         CHAR(36)     NOT NULL DEFAULT (UUID()),
  `bucket`     VARCHAR(150) NOT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rate_limit_bucket_time` (`bucket`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `id`             CHAR(36)      NOT NULL DEFAULT (UUID()),
  `name`           VARCHAR(150)  NOT NULL,
  `brand`          VARCHAR(100)  NOT NULL,
  `price`          DECIMAL(12,2) NOT NULL,
  `original_price` DECIMAL(12,2) NULL,
  `image`          VARCHAR(500)  NOT NULL,
  `category`       VARCHAR(100)  NOT NULL,
  `is_new`         TINYINT(1)    NOT NULL DEFAULT 1,
  `discount`       INT           NULL,
  `created_at`     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_descriptions` (
  `id`          CHAR(36)    NOT NULL DEFAULT (UUID()),
  `product_id`  VARCHAR(64) NOT NULL,
  `description` TEXT        NOT NULL,
  `created_at`  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_product_descriptions_product` (`product_id`),
  CONSTRAINT `fk_descriptions_stock` FOREIGN KEY (`product_id`) REFERENCES `product_stock` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `id`                    CHAR(36)       NOT NULL DEFAULT (UUID()),
  `user_id`               CHAR(36)       NOT NULL,
  `total_amount`          DECIMAL(12,2)  NOT NULL,
  `payment_method`        VARCHAR(32)    NOT NULL,
  `payment_phone`         VARCHAR(32)    NOT NULL,
  `status`                VARCHAR(32)    NOT NULL DEFAULT 'pending',
  `payment_status`        VARCHAR(32)    NOT NULL DEFAULT 'pending',
  `transaction_id`        VARCHAR(128)   NULL,
  `payment_initiated_at`  TIMESTAMP      NULL,
  `shipping_name`         VARCHAR(255)   NULL,
  `shipping_address`      TEXT           NULL,
  `shipping_city`         VARCHAR(128)   NULL,
  `shipping_phone`        VARCHAR(32)    NULL,
  `created_at`            TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_orders_user`   (`user_id`),
  KEY `idx_orders_status` (`status`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
  `id`            CHAR(36)       NOT NULL DEFAULT (UUID()),
  `order_id`      CHAR(36)       NOT NULL,
  `product_id`    VARCHAR(64)    NOT NULL,
  `quantity`      INT            NOT NULL DEFAULT 1,
  `price_at_sale` DECIMAL(12,2)  NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order` (`order_id`),
  KEY `idx_order_items_product` (`product_id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `product_stock` (`product_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inventory_history` (
  `id`                CHAR(36)    NOT NULL DEFAULT (UUID()),
  `product_id`        VARCHAR(64) NOT NULL,
  `previous_quantity` INT         NOT NULL,
  `new_quantity`      INT         NOT NULL,
  `change_amount`     INT         NOT NULL,
  `change_type`       VARCHAR(32) NOT NULL,
  `notes`             TEXT        NULL,
  `changed_by`        CHAR(36)    NULL,
  `created_at`        TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_inventory_history_product` (`product_id`),
  CONSTRAINT `fk_inventory_history_product` FOREIGN KEY (`product_id`) REFERENCES `product_stock` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_inventory_history_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `comments` (
  `id`                  CHAR(36)    NOT NULL DEFAULT (UUID()),
  `product_id`          VARCHAR(64) NOT NULL,
  `user_id`             CHAR(36)    NOT NULL,
  `content`             TEXT        NOT NULL,
  `rating`              TINYINT     NULL,
  `status`              ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `helpful_count`       INT         NOT NULL DEFAULT 0,
  `not_helpful_count`   INT         NOT NULL DEFAULT 0,
  `images`              TEXT        NULL,
  `created_at`          TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_comments_product` (`product_id`),
  KEY `idx_comments_user`    (`user_id`),
  CONSTRAINT `fk_comments_product` FOREIGN KEY (`product_id`) REFERENCES `product_stock` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wishlist` (
  `id`         CHAR(36)    NOT NULL DEFAULT (UUID()),
  `user_id`    CHAR(36)    NOT NULL,
  `product_id` VARCHAR(64) NOT NULL,
  `created_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wishlist_user_product` (`user_id`,`product_id`),
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `product_stock` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contact_messages` (
  `id`         CHAR(36)     NOT NULL DEFAULT (UUID()),
  `user_id`    CHAR(36)     NULL,
  `name`       VARCHAR(255) NOT NULL,
  `email`      VARCHAR(255) NOT NULL,
  `subject`    VARCHAR(255) NULL,
  `message`    TEXT         NOT NULL,
  `is_read`    TINYINT      NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_contact_messages_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `newsletter_subscribers` (
  `id`         CHAR(36)     NOT NULL DEFAULT (UUID()),
  `email`      VARCHAR(255) NOT NULL UNIQUE,
  `is_active`  TINYINT      NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `loyalty_points` (
  `id`         CHAR(36)     NOT NULL DEFAULT (UUID()),
  `user_id`    CHAR(36)     NOT NULL,
  `points`     INT          NOT NULL DEFAULT 0,
  `tier`       ENUM('bronze','silver','gold','platinum') NOT NULL DEFAULT 'bronze',
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_loyalty_user` (`user_id`),
  CONSTRAINT `fk_loyalty_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `site_settings` (
  `setting_key`   VARCHAR(100) NOT NULL,
  `setting_value` TEXT         NOT NULL,
  `updated_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_orders_created_at ON orders(created_at);
CREATE INDEX idx_comments_status ON comments(status);
CREATE INDEX idx_comments_product_id ON comments(product_id);
CREATE INDEX idx_wishlist_user_id ON wishlist(user_id);
CREATE INDEX idx_inventory_history_product_id ON inventory_history(product_id);

SET FOREIGN_KEY_CHECKS = 1;
