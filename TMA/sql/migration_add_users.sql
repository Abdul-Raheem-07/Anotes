-- ============================================================
-- Adds multi-user support to ANote.
-- Safe to run once against your existing `notes` database
-- (phpMyAdmin -> select `notes` database -> SQL tab -> paste -> Go).
--
-- What this does, in order:
--   1. Creates the `users` table.
--   2. Creates a default Admin account so your existing notes
--      and reminders have somewhere to go (they currently belong
--      to nobody).
--   3. Adds a nullable `user_id` to `notes` and `reminders`.
--   4. Assigns every existing row to the Admin account.
--   5. Locks `user_id` down: NOT NULL + foreign key + index.
--
-- Default admin login (change the password after your first login):
--   email:    admin@anote.local
--   password: Admin123!
-- ============================================================

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- This hash was generated with a real bcrypt implementation (not typed
-- by hand) and verifies correctly against PHP's password_verify().
INSERT INTO `users` (`name`, `email`, `password`)
VALUES ('Admin', 'admin@anote.local', '$2b$12$FMoG27xKJPUTzvNfCQLm6eFc5KJZalGnC3e8dmSPxoXZ1yXKLkwtC')
ON DUPLICATE KEY UPDATE `email` = `email`;

-- Add nullable user_id columns first (existing rows have no owner yet)
ALTER TABLE `notes` ADD COLUMN `user_id` INT NULL AFTER `sno`;
ALTER TABLE `reminders` ADD COLUMN `user_id` INT NULL AFTER `id`;

-- Assign every existing note/reminder to the Admin account
UPDATE `notes`
SET `user_id` = (SELECT id FROM `users` WHERE email = 'admin@anote.local')
WHERE `user_id` IS NULL;

UPDATE `reminders`
SET `user_id` = (SELECT id FROM `users` WHERE email = 'admin@anote.local')
WHERE `user_id` IS NULL;

-- Now that every row has an owner, make it required and enforce it
ALTER TABLE `notes` MODIFY `user_id` INT NOT NULL;
ALTER TABLE `reminders` MODIFY `user_id` INT NOT NULL;

ALTER TABLE `notes`
  ADD CONSTRAINT `fk_notes_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `reminders`
  ADD CONSTRAINT `fk_reminders_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;

ALTER TABLE `notes` ADD INDEX `idx_notes_user` (`user_id`);
ALTER TABLE `reminders` ADD INDEX `idx_reminders_user` (`user_id`);