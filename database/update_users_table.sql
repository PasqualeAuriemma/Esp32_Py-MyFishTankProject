-- Aggiungi colonne per reset password se non esistono
ALTER TABLE `users`
ADD COLUMN `reset_token` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `reset_token_expiry` DATETIME DEFAULT NULL;

-- Opzionale: aggiungi altri campi utili
ALTER TABLE `users`
ADD COLUMN `username` VARCHAR(100) DEFAULT NULL,
ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
