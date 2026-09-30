-- Test Kutuları (Adım bazlı seçenek kutuları)
-- Her adımda 3 kutu, her kutuda birden fazla madde var

CREATE TABLE IF NOT EXISTS `test_kutulari` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id` int(11) NOT NULL,
  `adim` tinyint(1) NOT NULL COMMENT 'Adım numarası (1,2,3)',
  `harf` char(1) NOT NULL COMMENT 'Kutu harfi (A,B,C,X,Y,Z,K,L,M)',
  `maddeler` text NOT NULL COMMENT 'Kutudaki maddeler, satır satır',
  `enneagram_tipi` tinyint(2) NOT NULL COMMENT 'Eşleşen enneagram tipi (1-9)',
  `sira` tinyint(1) NOT NULL DEFAULT 1,
  `durum` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `test_id` (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Data
INSERT INTO `test_kutulari` (`test_id`, `adim`, `harf`, `maddeler`, `enneagram_tipi`, `sira`) VALUES
-- Adım 1: A / B / C
(1, 1, 'A', 'Ciddi ve akılcı davranan\nİlişkilerinde mantıklı ve kuralcı\nMantıklı ve kontrollü davranan\nAyrıntıcı ve eleştirel\nHatalara müdahale eden\nPrensipli ve ilkelerine çok bağlı', 1, 1),
(1, 1, 'B', 'Çok meraklı, yenilikçi\nKeşfetmeyi seven\nNeşeli ve iyimser\nPratik davranan\nOlumsuzluğa pek takılmayan\nHeyecan ve coşku arayan', 7, 2),
(1, 1, 'C', 'Uyum ve denge odaklı\nÇatışmadan kaçınan\nHuzur ve sükûnet arayan\nAcele etmeyen\nAlçak gönüllü ve esnek\nYumuşak huylu, sabırlı', 9, 3),

-- Adım 2: X / Y / Z
(1, 2, 'X', 'Bilgiye derinlemesine meraklı\nAkılcı, soğukkanlı\nİlişkilerinde mesafeli\nKendine yetmeye çalışan\nDuygusallıktan kaçınan\nMüdahale etmeden uyaran', 5, 1),
(1, 2, 'Y', 'Cesur, kararlı ve baskın\nKendinden emin\nMeydan okuyan\nSahiplenici ve otoriter\nRiskten kaçınmayan\nÇatışmadan kaçınmayan', 8, 2),
(1, 2, 'Z', 'İlişkilerinde sıcak ve samimi\nYardım etme gereği duyan\nDuygusal paylaşımı seven\nArkadaş canlısı, sıcakkanlı\nÇabuk duygulanan-alınan\nArkadaşlık ve iletişimi seven', 2, 3),

-- Adım 3: K / L / M
(1, 3, 'K', 'Güven ve emniyet odaklı\nZihinsel netlik arayan\nRiski sevmeyen, kontrolcü\nTemkinli ve tedbirli\nSorgulayıcı\nKontrollü davranan\nBelirsizlikten rahatsız olan', 6, 1),
(1, 3, 'L', 'Başarı odaklı, hedef koyan\nKendini motive eden\nHırslı ve rekabetçi\nÖnde çıkmayı seven\nİmajını önemseyen\nDiplomatik olabilen', 3, 2),
(1, 3, 'M', 'Anlam ve derinlik arayan\nSezgilerini önemseyen\nAnlaşılmak isteyen, özgün\nDuyguları yoğun yaşayan\nİç dünyasını gözlemleyen\nHassas ve dost canlısı', 4, 3);
