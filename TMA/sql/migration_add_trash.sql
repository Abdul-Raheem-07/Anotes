-- Use this INSTEAD of schema.sql if you already have `notes` and
-- `reminders` tables created and don't want to lose your data.
-- Run it once in phpMyAdmin's SQL tab (select the `notes` database first).

ALTER TABLE `notes`
  ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME DEFAULT NULL;

ALTER TABLE `reminders`
  ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME DEFAULT NULL;

-- If your MySQL/MariaDB version doesn't support "ADD COLUMN IF NOT EXISTS"
-- (older versions), run these two lines instead and ignore any
-- "duplicate column" error if you've already run this once:
-- ALTER TABLE `notes` ADD COLUMN `deleted_at` DATETIME DEFAULT NULL;
-- ALTER TABLE `reminders` ADD COLUMN `deleted_at` DATETIME DEFAULT NULL;