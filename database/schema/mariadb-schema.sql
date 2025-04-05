/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `account_activations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_activations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(191) NOT NULL,
  `account_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `account_activations_account_id_foreign` (`account_id`),
  KEY `account_activations_token_index` (`token`),
  CONSTRAINT `account_activations_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `account_balance_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_balance_transactions` (
  `balance_transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `balance_before` int(10) unsigned NOT NULL,
  `balance_after` int(10) unsigned NOT NULL,
  `transaction_amount` int(11) NOT NULL,
  `reason` varchar(191) NOT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`balance_transaction_id`),
  KEY `account_balance_transactions_account_id_foreign` (`account_id`),
  CONSTRAINT `account_balance_transactions_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `account_email_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_email_changes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `token` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `account_email_changes_token_email_previous_email_new_index` (`token`,`email`),
  KEY `account_email_changes_account_id_foreign` (`account_id`),
  CONSTRAINT `account_email_changes_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `account_password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_password_resets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at_tmp` timestamp NULL DEFAULT NULL,
  `account_id` int(10) unsigned NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_password_resets_email_index` (`email`),
  KEY `account_password_resets_account_id_foreign` (`account_id`),
  CONSTRAINT `account_password_resets_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounts` (
  `account_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(60) DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `username` varchar(191) DEFAULT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT 0,
  `totp_secret` text DEFAULT NULL,
  `totp_backup_code` text DEFAULT NULL,
  `is_totp_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `totp_last_used` int(11) DEFAULT NULL,
  `stripe_id` varchar(191) DEFAULT NULL,
  `pm_type` varchar(191) DEFAULT NULL,
  `pm_last_four` varchar(4) DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `balance` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`account_id`),
  KEY `accounts_stripe_id_index` (`stripe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(191) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(191) DEFAULT NULL,
  `event` varchar(191) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `causer_type` varchar(191) DEFAULT NULL,
  `causer_id` bigint(20) unsigned DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` uuid DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `badges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `display_name` varchar(191) NOT NULL,
  `unicode_icon` varchar(191) NOT NULL,
  `list_hidden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `badges_pivot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `badges_pivot` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `badge_id` varchar(191) NOT NULL,
  `account_id` varchar(191) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ban_appeals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ban_appeals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `game_ban_id` int(10) unsigned NOT NULL,
  `is_account_verified` tinyint(1) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `explanation` text NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `decision_note` text DEFAULT NULL,
  `decided_at` timestamp NULL DEFAULT NULL,
  `decider_player_minecraft_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ban_appeals_game_ban_id_foreign` (`game_ban_id`),
  KEY `ban_appeals_decider_player_minecraft_id_foreign` (`decider_player_minecraft_id`),
  CONSTRAINT `ban_appeals_decider_player_minecraft_id_foreign` FOREIGN KEY (`decider_player_minecraft_id`) REFERENCES `players_minecraft` (`player_minecraft_id`),
  CONSTRAINT `ban_appeals_game_ban_id_foreign` FOREIGN KEY (`game_ban_id`) REFERENCES `game_player_bans` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `builder_rank_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `builder_rank_applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `minecraft_alias` varchar(191) NOT NULL,
  `current_builder_rank` varchar(191) NOT NULL,
  `build_location` varchar(191) NOT NULL,
  `build_description` text NOT NULL,
  `additional_notes` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `denied_reason` text DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `donation_perks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donation_perks` (
  `donation_perks_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `donation_id` int(10) unsigned NOT NULL,
  `donation_tier_id` int(10) unsigned DEFAULT NULL,
  `account_id` int(10) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `is_lifetime_perks` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`donation_perks_id`),
  KEY `donation_perks_donation_id_foreign` (`donation_id`),
  KEY `donation_perks_account_id_foreign` (`account_id`),
  KEY `donation_perks_donation_tier_id_foreign` (`donation_tier_id`),
  CONSTRAINT `donation_perks_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`),
  CONSTRAINT `donation_perks_donation_id_foreign` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`donation_id`),
  CONSTRAINT `donation_perks_donation_tier_id_foreign` FOREIGN KEY (`donation_tier_id`) REFERENCES `donation_tiers` (`donation_tier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `donation_tiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donation_tiers` (
  `donation_tier_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`donation_tier_id`),
  KEY `donation_tiers_group_id_foreign` (`group_id`),
  CONSTRAINT `donation_tiers_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `donations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donations` (
  `donation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned DEFAULT NULL,
  `amount` double NOT NULL COMMENT 'Amount donated in dollars',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`donation_id`),
  KEY `donations_perks_end_at_amount_index` (`amount`),
  KEY `donations_account_id_foreign` (`account_id`),
  CONSTRAINT `donations_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `game_ip_bans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `game_ip_bans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `banner_player_id` int(10) unsigned NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `reason` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `unbanned_at` timestamp NULL DEFAULT NULL,
  `unbanner_player_id` int(10) unsigned DEFAULT NULL,
  `unban_type` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `game_ip_bans_unbanner_player_id_foreign` (`unbanner_player_id`),
  KEY `game_ip_bans_ip_address_index` (`ip_address`),
  CONSTRAINT `game_ip_bans_unbanner_player_id_foreign` FOREIGN KEY (`unbanner_player_id`) REFERENCES `players_minecraft` (`player_minecraft_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `game_player_bans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `game_player_bans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `server_id` int(10) unsigned DEFAULT NULL,
  `banned_player_id` int(10) unsigned NOT NULL,
  `banned_alias_at_time` varchar(191) NOT NULL COMMENT 'Alias of the player at ban time for logging purposes',
  `banner_player_id` int(10) unsigned DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Date that this ban auto-expires on',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `unbanned_at` timestamp NULL DEFAULT NULL,
  `unbanner_player_id` int(10) unsigned DEFAULT NULL,
  `unban_type` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `game_network_bans_unbanner_player_id_foreign` (`unbanner_player_id`),
  CONSTRAINT `game_network_bans_unbanner_player_id_foreign` FOREIGN KEY (`unbanner_player_id`) REFERENCES `players_minecraft` (`player_minecraft_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `group_scopes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `group_scopes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(191) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_scopes_scope_index` (`scope`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `group_scopes_pivot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `group_scopes_pivot` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL,
  `scope_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_scopes_pivot_group_id_foreign` (`group_id`),
  KEY `group_scopes_pivot_scope_id_foreign` (`scope_id`),
  CONSTRAINT `group_scopes_pivot_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
  CONSTRAINT `group_scopes_pivot_scope_id_foreign` FOREIGN KEY (`scope_id`) REFERENCES `group_scopes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `groups` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `alias` varchar(191) DEFAULT NULL,
  `is_build` tinyint(1) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_staff` tinyint(1) NOT NULL DEFAULT 0,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `minecraft_name` varchar(191) DEFAULT NULL,
  `minecraft_display_name` varchar(191) NOT NULL,
  `minecraft_hover_text` varchar(191) NOT NULL,
  `display_priority` int(11) DEFAULT NULL,
  `discord_name` varchar(191) DEFAULT NULL,
  `can_access_panel` tinyint(1) NOT NULL DEFAULT 0,
  `group_type` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `groups_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `groups_accounts` (
  `groups_accounts_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL,
  `account_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`groups_accounts_id`),
  KEY `groups_accounts_group_id_foreign` (`group_id`),
  KEY `groups_accounts_account_id_foreign` (`account_id`),
  CONSTRAINT `groups_accounts_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE,
  CONSTRAINT `groups_accounts_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `minecraft_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `minecraft_config` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`config`)),
  `version` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `minecraft_registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `minecraft_registrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `minecraft_uuid` varchar(191) NOT NULL,
  `minecraft_alias` varchar(191) NOT NULL,
  `code` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `minecraft_warps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `minecraft_warps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `world` varchar(191) NOT NULL,
  `x` double NOT NULL,
  `y` double NOT NULL,
  `z` double NOT NULL,
  `pitch` double NOT NULL,
  `yaw` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `minecraft_warps_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `payment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned DEFAULT NULL,
  `stripe_price` varchar(191) DEFAULT NULL,
  `stripe_product` varchar(191) DEFAULT NULL,
  `amount_paid_in_cents` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `is_subscription_payment` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `payments_stripe_price_stripe_product_index` (`stripe_price`,`stripe_product`),
  KEY `payments_account_id_foreign` (`account_id`),
  CONSTRAINT `payments_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `player_warnings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_warnings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warned_player_id` int(10) unsigned NOT NULL,
  `warner_player_id` int(10) unsigned DEFAULT NULL,
  `reason` text NOT NULL,
  `additional_info` text DEFAULT NULL,
  `weight` int(11) NOT NULL COMMENT 'How many points the infraction is worth',
  `is_acknowledged` tinyint(1) NOT NULL DEFAULT 0,
  `acknowledged_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `players_minecraft`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `players_minecraft` (
  `player_minecraft_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(60) NOT NULL,
  `alias` varchar(191) DEFAULT NULL,
  `account_id` int(10) unsigned DEFAULT NULL,
  `last_synced_at` datetime DEFAULT NULL,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`player_minecraft_id`),
  UNIQUE KEY `players_minecraft_uuid_unique` (`uuid`),
  KEY `players_minecraft_uuid_index` (`uuid`),
  KEY `players_minecraft_account_id_foreign` (`account_id`),
  CONSTRAINT `players_minecraft_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `players_minecraft_aliases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `players_minecraft_aliases` (
  `players_minecraft_alias_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_minecraft_id` int(10) unsigned NOT NULL,
  `alias` varchar(191) NOT NULL,
  `registered_at` timestamp NULL DEFAULT NULL COMMENT 'The actual datetime they changed their alias to this',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`players_minecraft_alias_id`),
  KEY `players_minecraft_aliases_alias_index` (`alias`),
  KEY `players_minecraft_aliases_player_minecraft_id_foreign` (`player_minecraft_id`),
  CONSTRAINT `players_minecraft_aliases_player_minecraft_id_foreign` FOREIGN KEY (`player_minecraft_id`) REFERENCES `players_minecraft` (`player_minecraft_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `server_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `server_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(191) NOT NULL,
  `server_id` int(10) unsigned NOT NULL,
  `description` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `server_tokens_server_id_foreign` (`server_id`),
  KEY `server_tokens_token_index` (`token`),
  CONSTRAINT `server_tokens_server_id_foreign` FOREIGN KEY (`server_id`) REFERENCES `servers` (`server_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servers` (
  `server_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `ip` varchar(191) NOT NULL,
  `ip_alias` varchar(191) DEFAULT NULL COMMENT 'An alternative address to connect to the server',
  `port` varchar(191) DEFAULT NULL,
  `web_port` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`server_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `showcase_warps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `showcase_warps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `creators` varchar(191) DEFAULT NULL,
  `location_world` varchar(191) NOT NULL,
  `location_x` double NOT NULL,
  `location_y` double NOT NULL,
  `location_z` double NOT NULL,
  `location_pitch` double NOT NULL,
  `location_yaw` double NOT NULL,
  `built_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `showcase_warps_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stripe_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stripe_products` (
  `price_id` varchar(191) NOT NULL,
  `product_id` varchar(191) NOT NULL,
  `donation_tier_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`price_id`),
  KEY `stripe_products_donation_tier_id_foreign` (`donation_tier_id`),
  CONSTRAINT `stripe_products_donation_tier_id_foreign` FOREIGN KEY (`donation_tier_id`) REFERENCES `donation_tiers` (`donation_tier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscription_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint(20) unsigned NOT NULL,
  `stripe_id` varchar(191) NOT NULL,
  `stripe_product` varchar(191) NOT NULL,
  `stripe_price` varchar(191) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_items_subscription_id_stripe_price_unique` (`subscription_id`,`stripe_price`),
  KEY `subscription_items_stripe_id_index` (`stripe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_account_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `stripe_id` varchar(191) NOT NULL,
  `stripe_status` varchar(191) NOT NULL,
  `stripe_price` varchar(191) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_account_account_id_stripe_status_index` (`account_account_id`,`stripe_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2017_09_15_131358_create_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2017_09_15_154714_create_servers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2017_09_17_142032_create_bans',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2017_10_03_124225_create_donations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2017_10_14_134236_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2018_03_21_102044_create_user_register_emails',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2018_03_22_153113_create_account_oauth',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2018_03_27_142354_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2018_04_20_141003_alter_donations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2018_06_30_090859_create_email_change',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2018_07_09_032541_alter_account_links_add_email',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2018_08_17_173709_create_payments',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2018_08_22_153055_create_player_warnings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2018_08_23_141229_delete_server_tokens',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2018_09_02_034512_allow_null_ban_staff',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2018_09_09_032458_create_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2019_05_26_081858_create_minecraft_auth_codes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2019_07_27_183009_add_username_to_accounts',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2019_07_28_125054_add_username_to_unactivated_accounts',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2019_08_27_121924_combine_accounts_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2019_09_04_153202_add_discourse_group_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2019_10_06_045949_create_donation_perks',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2019_10_14_122946_create_stripe_sessions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2020_03_22_113534_add_panel_access_column_to_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2020_12_23_210328_delete_players_minecraft_playtime',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2020_12_23_210456_rename_players_minecraft_last_seen_at',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2020_12_23_210633_make_nullable_players_minecraft_last_synced_at',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2020_12_24_163432_add_2fa_fields_to_accounts',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2021_07_28_132343_add_minecraft_group_names',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2021_07_31_134903_create_donation_tiers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2021_07_31_134904_migrate_lifetime_donors',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2021_08_02_180000_create_customer_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2021_08_02_180001_create_subscriptions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2021_08_02_180002_create_subscription_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2021_08_04_163409_create_account_payments',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2021_08_20_130638_create_minecraft_loot_boxes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2022_04_11_103651_remove_loot_boxes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2022_04_12_145615_add_account_balance',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2022_04_12_152853_add_balance_transactions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2022_04_19_120626_add_last_balance_date',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2022_04_20_060549_create_product_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2022_05_09_132856_create_pages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2022_05_13_170120_remove_server_status_players',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2022_05_15_134438_create_rank_applications',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2022_05_16_154731_add_builder_group_col',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2022_05_22_154628_create_ban_appeals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2022_05_28_164701_create_server_tokens',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2022_06_02_163732_delete_account_links_and_ban_logs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2022_06_03_132514_create_group_scopes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2022_06_05_153600_create_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2022_06_05_153601_add_event_column_to_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2022_06_05_153602_add_batch_uuid_column_to_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2022_06_14_143430_add_player_last_seen_col',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2022_06_19_140057_remove_game_account_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2022_08_08_125414_add_badges',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2022_08_13_090330_rename_decider_account_to_decider_player',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2022_08_15_151557_add_warp_showcase',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2022_08_29_053247_cascade_account_deletion',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2022_09_06_031358_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2022_09_09_044922_add_unban_cols_to_ban_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2022_09_09_045726_move_unbans_to_bans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2022_09_10_044245_drop_server_keys',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2022_09_12_034018_change_warnings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2022_09_15_070606_create_ip_ban_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2022_09_19_052638_rename_staff_player_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2024_08_18_155246_delete_pages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2024_08_19_113119_delete_server_categories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2024_09_03_133144_account_activation_tokens',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2024_09_04_151106_add_password_reset_expiry',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2024_09_07_101052_update_email_change',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2024_09_15_120334_add_badge_visibility',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2024_10_02_100137_create_minecraft_registration',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2024_10_07_100758_remove_alias_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2024_10_07_144916_remove_default_group_members',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2024_10_18_100146_add_server_query_port',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2024_10_25_083122_create_minecraft_config',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2024_10_28_110159_create_warps',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2024_10_30_110257_add_minecraft_group_cols',1);
