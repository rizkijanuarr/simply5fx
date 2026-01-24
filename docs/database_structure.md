# Database Structure S5FX

## Tables

### account_trades
CREATE TABLE `account_trades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` decimal(15,2) NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_trades_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

### hits
CREATE TABLE `hits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hits_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

### lot_sizes
CREATE TABLE `lot_sizes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `size` decimal(10,2) NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lot_sizes_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

### markets
CREATE TABLE `markets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `markets_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

### positions
CREATE TABLE `positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `positions_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

### risk_to_rewards
CREATE TABLE `risk_to_rewards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ratio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `risk_to_rewards_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

### transactions
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_trade_id` bigint unsigned NOT NULL,
  `position_id` bigint unsigned NOT NULL,
  `market_id` bigint unsigned NOT NULL,
  `risk_to_reward_id` bigint unsigned NOT NULL,
  `lot_size_id` bigint unsigned NOT NULL,
  `hit_id` bigint unsigned DEFAULT NULL,
  `harga_entry` decimal(15,5) NOT NULL,
  `harga_sl` decimal(15,5) NOT NULL,
  `harga_tp` decimal(15,5) NOT NULL,
  `account_balance` decimal(15,2) DEFAULT NULL,
  `profit_or_loss` decimal(15,2) DEFAULT NULL,
  `equity` decimal(15,2) DEFAULT NULL,
  `account_change` decimal(15,2) DEFAULT NULL,
  `cummulative_account_change` decimal(15,2) DEFAULT NULL,
  `screenshot_before` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screenshot_after` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transactions_sku_unique` (`sku`),
  KEY `transactions_account_trade_id_foreign` (`account_trade_id`),
  KEY `transactions_position_id_foreign` (`position_id`),
  KEY `transactions_market_id_foreign` (`market_id`),
  KEY `transactions_risk_to_reward_id_foreign` (`risk_to_reward_id`),
  KEY `transactions_lot_size_id_foreign` (`lot_size_id`),
  KEY `transactions_hit_id_foreign` (`hit_id`),
  CONSTRAINT `transactions_account_trade_id_foreign` FOREIGN KEY (`account_trade_id`) REFERENCES `account_trades` (`id`),
  CONSTRAINT `transactions_hit_id_foreign` FOREIGN KEY (`hit_id`) REFERENCES `hits` (`id`),
  CONSTRAINT `transactions_lot_size_id_foreign` FOREIGN KEY (`lot_size_id`) REFERENCES `lot_sizes` (`id`),
  CONSTRAINT `transactions_market_id_foreign` FOREIGN KEY (`market_id`) REFERENCES `markets` (`id`),
  CONSTRAINT `transactions_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`),
  CONSTRAINT `transactions_risk_to_reward_id_foreign` FOREIGN KEY (`risk_to_reward_id`) REFERENCES `risk_to_rewards` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
