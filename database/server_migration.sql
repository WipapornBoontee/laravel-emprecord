-- SQL Migration & Seed Script for Company Holidays & Attendance Adjustments
-- Generated for Server / Production Database Import

CREATE TABLE IF NOT EXISTS `company_holidays` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'ชื่อวันหยุด',
  `holiday_date` date NOT NULL COMMENT 'วันที่หยุด',
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'หยุดซ้ำทุกปีหรือไม่ (1=ใช่, 0=ไม่)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_holidays_holiday_date_index` (`holiday_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attendance_adjustments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `attendance_id` bigint(20) unsigned DEFAULT NULL,
  `target_date` date NOT NULL,
  `requested_check_in` time DEFAULT NULL,
  `requested_check_out` time DEFAULT NULL,
  `reason` text NOT NULL,
  `attachment_url` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approver_id` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `reject_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_adjustments_user_id_foreign` (`user_id`),
  KEY `attendance_adjustments_attendance_id_foreign` (`attendance_id`),
  KEY `attendance_adjustments_approver_id_foreign` (`approver_id`),
  KEY `attendance_adjustments_target_date_index` (`target_date`),
  KEY `attendance_adjustments_status_index` (`status`),
  CONSTRAINT `attendance_adjustments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_adjustments_attendance_id_foreign` FOREIGN KEY (`attendance_id`) REFERENCES `attendances` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendance_adjustments_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ข้อมูลวันหยุดประเพณีเริ่มต้น (Default Seed Data for Company Holidays)
INSERT IGNORE INTO `company_holidays` (`name`, `holiday_date`, `is_recurring`, `created_at`, `updated_at`) VALUES
('วันขึ้นปีใหม่', CONCAT(YEAR(CURDATE()), '-01-01'), 1, NOW(), NOW()),
('วันจักรี', CONCAT(YEAR(CURDATE()), '-04-06'), 1, NOW(), NOW()),
('วันสงกรานต์', CONCAT(YEAR(CURDATE()), '-04-13'), 1, NOW(), NOW()),
('วันสงกรานต์', CONCAT(YEAR(CURDATE()), '-04-14'), 1, NOW(), NOW()),
('วันสงกรานต์', CONCAT(YEAR(CURDATE()), '-04-15'), 1, NOW(), NOW()),
('วันแรงงานแห่งชาติ', CONCAT(YEAR(CURDATE()), '-05-01'), 1, NOW(), NOW()),
('วันฉัตรมงคล', CONCAT(YEAR(CURDATE()), '-05-04'), 1, NOW(), NOW()),
('วันเฉลิมพระชนมพรรษาสมเด็จพระนางเจ้าฯ พระบรมราชินี', CONCAT(YEAR(CURDATE()), '-06-03'), 1, NOW(), NOW()),
('วันเฉลิมพระชนมพรรษาพระบาทสมเด็จพระเจ้าอยู่หัว', CONCAT(YEAR(CURDATE()), '-07-28'), 1, NOW(), NOW()),
('วันแม่แห่งชาติ', CONCAT(YEAR(CURDATE()), '-08-12'), 1, NOW(), NOW()),
('วันคล้ายวันสวรรคต ร.9 (วันนวมินทรมหาราช)', CONCAT(YEAR(CURDATE()), '-10-13'), 1, NOW(), NOW()),
('วันปิยมหาราช', CONCAT(YEAR(CURDATE()), '-10-23'), 1, NOW(), NOW()),
('วันพ่อแห่งชาติ', CONCAT(YEAR(CURDATE()), '-12-05'), 1, NOW(), NOW()),
('วันรัฐธรรมนูญ', CONCAT(YEAR(CURDATE()), '-12-10'), 1, NOW(), NOW()),
('วันสิ้นปี', CONCAT(YEAR(CURDATE()), '-12-31'), 1, NOW(), NOW());
