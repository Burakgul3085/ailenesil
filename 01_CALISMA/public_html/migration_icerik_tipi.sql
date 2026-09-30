-- Migration: Add content type and responsive image fields to program_icerikleri
-- Run this SQL on the database before using the new features

ALTER TABLE `program_icerikleri`
  ADD COLUMN `icerik_tipi` TINYINT NOT NULL DEFAULT 0 COMMENT '0=normal metin, 1=tek görsel' AFTER `gorsel_format`,
  ADD COLUMN `gorsel_desktop` VARCHAR(255) DEFAULT NULL COMMENT 'Desktop görseli dosya adı' AFTER `icerik_tipi`,
  ADD COLUMN `gorsel_mobil` VARCHAR(255) DEFAULT NULL COMMENT 'Mobil görseli dosya adı' AFTER `gorsel_desktop`;
