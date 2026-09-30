CREATE TABLE IF NOT EXISTS `karakter_programlari` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sira` int(11) NOT NULL DEFAULT 0,
  `baslik` varchar(255) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `detay_aciklama` longtext DEFAULT NULL,
  `resim` varchar(255) DEFAULT NULL,
  `seo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `durum` tinyint(1) NOT NULL DEFAULT 1,
  `dil` int(11) NOT NULL DEFAULT 1,
  `tarih` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- sabit_url tablosuna URL alanlarını ekle
ALTER TABLE sabit_url ADD COLUMN IF NOT EXISTS karakterprogramlariurl VARCHAR(255) DEFAULT 'karakter-programlari';
ALTER TABLE sabit_url ADD COLUMN IF NOT EXISTS karakterprogramdetayurl VARCHAR(255) DEFAULT 'karakter-program-detay';
UPDATE sabit_url SET karakterprogramlariurl = 'karakter-programlari', karakterprogramdetayurl = 'karakter-program-detay' WHERE karakterprogramlariurl IS NULL OR karakterprogramlariurl = '';
