-- Docker geliştirme ortamı için ayar overrides
-- Bu dosya ana SQL import'undan SONRA çalışır (z- prefix sayesinde)

USE ailevenesilderne_vt;

-- HTTPS zorlamasını kapat (alan21: SSL redirect kontrolü)
UPDATE moduller SET alan21 = '0' WHERE id = 1;

-- Dosya Yönetimi modülü tablosu
CREATE TABLE IF NOT EXISTS `dosyalar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sira` int(11) DEFAULT 0,
  `baslik` varchar(255) NOT NULL,
  `dosya` varchar(255) DEFAULT NULL,
  `orijinal_ad` varchar(255) DEFAULT NULL,
  `dosya_boyut` varchar(50) DEFAULT NULL,
  `dosya_tip` varchar(20) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `aciklama` text DEFAULT NULL,
  `durum` tinyint(1) DEFAULT 1,
  `dil` int(11) DEFAULT 1,
  `tarih` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- sabit_url tablosuna dosyalar URL'i ekle
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ailevenesilakade_vt' AND TABLE_NAME = 'sabit_url' AND COLUMN_NAME = 'dosyalarurl');
SET @sql = IF(@col_exists = 0, "ALTER TABLE sabit_url ADD COLUMN dosyalarurl varchar(255) DEFAULT 'dosyalarimiz'", 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
UPDATE sabit_url SET dosyalarurl = 'dosyalarimiz' WHERE id = 1 AND (dosyalarurl IS NULL OR dosyalarurl = '');

-- Instagram username alanı ekle
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ailevenesilakade_vt' AND TABLE_NAME = 'ayarlar' AND COLUMN_NAME = 'instagram_username');
SET @sql = IF(@col_exists = 0, "ALTER TABLE ayarlar ADD COLUMN instagram_username varchar(255) DEFAULT ''", 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
