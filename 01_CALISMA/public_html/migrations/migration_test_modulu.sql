-- Mizaç Testi Modülü - Veritabanı Tabloları ve Seed Data

-- Test ana tablosu
CREATE TABLE IF NOT EXISTS `testler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adi` varchar(255) NOT NULL,
  `seo` varchar(255) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `durum` tinyint(1) NOT NULL DEFAULT 1,
  `dil` int(11) NOT NULL DEFAULT 1,
  `tarih` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Test soruları
CREATE TABLE IF NOT EXISTS `test_sorulari` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id` int(11) NOT NULL,
  `soru` text NOT NULL,
  `sira` int(11) NOT NULL DEFAULT 0,
  `durum` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `test_id` (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Test cevapları (her cevap bir mizaç tipine işaret eder)
CREATE TABLE IF NOT EXISTS `test_cevaplari` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `soru_id` int(11) NOT NULL,
  `cevap` text NOT NULL,
  `mizac_tipi` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Demevi, 1=Safravi, 2=Balgami, 3=Sevdavi, 4=Dengeli',
  `sira` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `soru_id` (`soru_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mizaç tipleri açıklamaları
CREATE TABLE IF NOT EXISTS `test_mizac_tipleri` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id` int(11) NOT NULL,
  `mizac_tipi` tinyint(1) NOT NULL COMMENT '0=Demevi, 1=Safravi, 2=Balgami, 3=Sevdavi, 4=Dengeli',
  `baslik` varchar(255) NOT NULL,
  `etiket` varchar(100) DEFAULT NULL,
  `aciklama` text NOT NULL,
  `renk` varchar(20) DEFAULT '#333333',
  PRIMARY KEY (`id`),
  KEY `test_id` (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Test sonuçları (kullanıcı sonuçlarını kaydet)
CREATE TABLE IF NOT EXISTS `test_sonuclari` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id` int(11) NOT NULL,
  `sonuc_json` text NOT NULL,
  `dominant_mizac` tinyint(1) NOT NULL,
  `ip_adresi` varchar(45) DEFAULT NULL,
  `tarih` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `test_id` (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- SEED DATA: Mizaç Testi
-- =============================================

INSERT INTO `testler` (`id`, `adi`, `seo`, `aciklama`, `durum`, `dil`) VALUES
(1, 'Mizaç Testi', 'mizac-testi', 'Mizacınızı keşfedin! 30 soruluk bu test ile baskın mizaç tipinizi öğrenin.', 1, 1);

-- Mizaç Tipleri Açıklamaları
INSERT INTO `test_mizac_tipleri` (`test_id`, `mizac_tipi`, `baslik`, `etiket`, `aciklama`, `renk`) VALUES
(1, 0, 'Sıcak ve nemli (Demevi) mizaca sahipsiniz.', 'Sıcak · Nemli', 'Demevi mizaç sahipleri en uyumlu ve yapıcı kişilerdir. Orta kilolu, pembemsi bir cilt yapısına sahiptirler. Kolay hastalanırlar ancak hızlı iyileşirler. Sosyal, neşeli ve iyimser bir yapıları vardır. İnsanlarla iyi geçinir, çevrelerine pozitif enerji yayarlar. Karaciğer ve kalp-damar hastalıklarına yatkındırlar. Fiziksel olarak enerjik ve hareketlidirler.', '#7ED321'),
(1, 1, 'Sıcak ve kuru (Safravi) mizaca sahipsiniz.', 'Sıcak · Kuru', 'Safravi mizaç sahipleri keskin kişilik kurallarına sahiptir. Vücut ısıları yüksektir, daha az hastalanırlar ancak iyileşmeleri yavaştır. Lider ruhlu, kararlı ve hırslıdırlar. Hedeflerine ulaşmak için azimle çalışırlar. Sindirim sistemi rahatsızlıkları ve Alzheimer gibi hastalıklara yatkındırlar. Sabırsız olabilirler ancak iş bitirici bir yapıları vardır.', '#F5A623'),
(1, 2, 'Soğuk ve nemli (Balgami) mizaca sahipsiniz.', 'Soğuk · Nemli', 'Balgami mizaç sahipleri kontrol edilebilir bir kişilik yapısına sahip olup, keskin kuralları olmayan bir yapıya sahiptirler. Fiziksel olarak, kilolu beyaz tenli bir cilt yapısına sahiptirler ve beden ısıları düşüktür. Hastalanma eğilimleri yüksektir, ancak tedaviye hızlı cevap verirler. Romatizmal hastalıklar, akciğer hastalıkları ve Parkinson gibi hastalıklarla sık karşılaşırlar.', '#4A90D9'),
(1, 3, 'Soğuk ve kuru (Sevdavi) mizaca sahipsiniz.', 'Soğuk · Kuru', 'Sevdavi mizaç sahipleri inatçı ve endişeli bir yapıya sahiptirler. Genellikle yeseler de zayıf kalırlar, kolay üşürler. Tedaviye yavaş cevap verirler. Detaycı, analitik ve mükemmeliyetçi kişilerdir. Sanatsal yetenekleri güçlüdür. Karamsar olabilirler ancak derin düşünce yapısına sahiptirler. Eklem ve kemik hastalıklarına yatkındırlar.', '#4A6CF7'),
(1, 4, 'Dengeli bir mizaca sahipsiniz.', 'Dengeli', 'Dengeli mizaç sahiplerinde sıcak-soğuk ve nemli-kuru dengesi uyumludur. Sağlıklı, canlı bir görünüme sahiptirler. Sindirimleri düzenlidir. Kişilikleri istikrarlıdır. Bağışıklık sistemleri güçlüdür. Hastalıklara karşı dirençlidirler. Duygusal ve fiziksel olarak dengeli bir yaşam sürerler. Her ortama kolayca uyum sağlarlar.', '#9B9B9B');

-- =============================================
-- 30 SORU ve CEVAPLARI
-- Şık sıralaması: Balgami(2), Sevdavi(3), Dengeli(4), Safravi(1), Demevi(0)
-- =============================================

-- Soru 1
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (101, 1, 'Günlük enerjini nasıl tanımlarsın?', 1, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(101, 'Gün boyu düşük, ağır hareket ederim.', 2, 1),
(101, 'Sakinim; hızlanmam zor olur.', 3, 2),
(101, 'Güne ve ortama göre değişir.', 4, 3),
(101, 'Genelde enerjik ve atılımlıyım.', 1, 4),
(101, 'Sürekli hareket halindeyim, durmak zor.', 0, 5);

-- Soru 2
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (102, 1, 'Soğuk havalarda nasıl hissedersin?', 2, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(102, 'Hemen üşür, halsizleşirim.', 2, 1),
(102, 'Üşürüm ama idare ederim.', 3, 2),
(102, 'Çok etkilenmem.', 4, 3),
(102, 'Soğuk beni canlandırır.', 1, 4),
(102, 'Soğukta enerjim artar.', 0, 5);

-- Soru 3
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (103, 1, 'Sıcak havalarda kendini nasıl hissedersin?', 3, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(103, 'Sıcakta halsiz ve tahammülsüz olurum.', 2, 1),
(103, 'Terlerim; dikkatimi toplamak zorlaşır.', 3, 2),
(103, 'Genelde rahatsız etmez.', 4, 3),
(103, 'Sıcakta rahatlarım.', 1, 4),
(103, 'Sıcak hava beni motive eder.', 0, 5);

-- Soru 4
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (104, 1, 'Karar verirken genellikle nasıl davranırsın?', 4, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(104, 'Karar vermem uzun sürer.', 2, 1),
(104, 'Uzun uzun analiz ederim.', 3, 2),
(104, 'Duruma göre orta hızda karar veririm.', 4, 3),
(104, 'Hızlı karar veririm.', 1, 4),
(104, 'Anlık içgüdülerle karar alırım.', 0, 5);

-- Soru 5
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (105, 1, 'Yemek yeme alışkanlığın nasıldır?', 5, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(105, 'Yavaş yerim; yemek sonrası ağırlık olur.', 2, 1),
(105, 'Ağır/yağlı yemekleri severim.', 3, 2),
(105, 'Dengeli yemeye çalışırım.', 4, 3),
(105, 'Hızlı yerim ama sık acıkmam.', 1, 4),
(105, 'Hafif ve taze yiyecekleri seçerim.', 0, 5);

-- Soru 6
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (106, 1, 'Uyku düzenin nasıldır?', 6, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(106, 'Uzun uyurum; uykusuz kalamam.', 2, 1),
(106, 'Gün içinde sık kestiririm.', 3, 2),
(106, 'Düzenli ve orta sürede uyurum.', 4, 3),
(106, 'Az uyusam da dinç kalkarım.', 1, 4),
(106, 'Hafif uykuyla bile enerjik olurum.', 0, 5);

-- Soru 7
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (107, 1, 'Zihinsel çalışma tarzın nasıldır?', 7, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(107, 'Yavaş ama derin düşünürüm.', 2, 1),
(107, 'Tek konuya uzun süre odaklanırım.', 3, 2),
(107, 'Dengeli ve esnek düşünürüm.', 4, 3),
(107, 'Hızlı düşünür, çabuk sonuca giderim.', 1, 4),
(107, 'Sürekli fikir üretir, hızla geçiş yaparım.', 0, 5);

-- Soru 8
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (108, 1, 'Stres altında nasıl tepki verirsin?', 8, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(108, 'Sessizleşir, yalnız kalmak isterim.', 2, 1),
(108, 'İçime kapanırım.', 3, 2),
(108, 'Sakin kalmaya çalışırım.', 4, 3),
(108, 'Agresif/atak davranabilirim.', 1, 4),
(108, 'Çabuk parlayıp tepki verebilirim.', 0, 5);

-- Soru 9
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (109, 1, 'Sosyal ortamlarda kendini nasıl hissedersin?', 9, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(109, 'Kalabalıktan hoşlanmam; geri planda kalırım.', 2, 1),
(109, 'Dinleyici olurum; az konuşurum.', 3, 2),
(109, 'Duruma göre esnek davranırım.', 4, 3),
(109, 'Kolay iletişim kurarım.', 1, 4),
(109, 'Ortamın enerjisini yükseltirim.', 0, 5);

-- Soru 10
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (110, 1, 'Vücudun hava değişimlerine nasıl tepki verir?', 10, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(110, 'Hemen etkilenir; halsizleşirim.', 2, 1),
(110, 'Mevsim geçişlerinde sık etkilenirim.', 3, 2),
(110, 'Orta derecede etkilenirim.', 4, 3),
(110, 'Genelde rahatsız etmez.', 1, 4),
(110, 'Pek etkilenmem.', 0, 5);

-- Soru 11
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (111, 1, 'Cildini nasıl tanımlarsın?', 11, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(111, 'Soğuk ve nemli/parlak.', 2, 1),
(111, 'Yumuşak, yağlanmaya eğilimli.', 3, 2),
(111, 'Normal/dengeli.', 4, 3),
(111, 'Ilık-kuru hisli.', 1, 4),
(111, 'Soğuk ve kuru.', 0, 5);

-- Soru 12
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (112, 1, 'Terleme eğilimin nasıldır?', 12, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(112, 'Çok az terlerim.', 2, 1),
(112, 'Kolay terlerim.', 3, 2),
(112, 'Orta düzeyde terlerim.', 4, 3),
(112, 'Zor terlerim.', 1, 4),
(112, 'Sıcakta sürekli terlerim.', 0, 5);

-- Soru 13
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (113, 1, 'Sindirim sistemin nasıl çalışır?', 13, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(113, 'Yavaş; sık şişkinlik olur.', 2, 1),
(113, 'Ağır yemekler çabuk dokunur.', 3, 2),
(113, 'Genelde sorunsuzdur.', 4, 3),
(113, 'Hızlıdır; çabuk acıkırım.', 1, 4),
(113, 'Hassas ama hızlı çalışır.', 0, 5);

-- Soru 14
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (114, 1, 'Zaman yönetiminde nasılsın?', 14, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(114, 'Plan yaparım ama uygulamam zor olur.', 2, 1),
(114, 'Erteleme eğilimim yüksektir.', 3, 2),
(114, 'Dengeli planlarım.', 4, 3),
(114, 'Hedef odaklı ve disiplinliyimdir.', 1, 4),
(114, 'Hızlı adımlar atarım.', 0, 5);

-- Soru 15
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (115, 1, 'Ruh halin genellikle nasıldır?', 15, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(115, 'Karamsar ve düşünceliyim.', 2, 1),
(115, 'Sessiz, içe dönüğüm.', 3, 2),
(115, 'Dengeli/istikrarlı.', 4, 3),
(115, 'Neşeli ve canlıyım.', 1, 4),
(115, 'Hızlı değişir; heyecanlıyım.', 0, 5);

-- Soru 16
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (116, 1, 'Fiziksel dayanıklılığını nasıl tanımlarsın?', 16, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(116, 'Çabuk yorulurum.', 2, 1),
(116, 'Zorlayınca çabuk pes ederim.', 3, 2),
(116, 'Orta seviyede dayanıklıyım.', 4, 3),
(116, 'Uzun süre yorulmam.', 1, 4),
(116, 'Çok yüksek dayanıklılığım var.', 0, 5);

-- Soru 17
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (117, 1, 'Bir işi bitirme tarzın nasıldır?', 17, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(117, 'Yavaş ama çok dikkatliyim.', 2, 1),
(117, 'Mükemmeliyetçi; uzun sürer.', 3, 2),
(117, 'Dengeli ilerlerim.', 4, 3),
(117, 'Hızlı ve etkili bitiririm.', 1, 4),
(117, 'Pratik, ani kararlarla tamamlarım.', 0, 5);

-- Soru 18
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (118, 1, 'Soğuk algınlığına yatkın mısın?', 18, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(118, 'Çok sık olurum.', 2, 1),
(118, 'Mevsim geçişlerinde sık olur.', 3, 2),
(118, 'Ara sıra olur.', 4, 3),
(118, 'Nadir olur.', 1, 4),
(118, 'Çok nadirdir.', 0, 5);

-- Soru 19
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (119, 1, 'Aç kaldığında davranışın nasıl değişir?', 19, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(119, 'Halsizleşir; ağırlaşırım.', 2, 1),
(119, 'Uykum gelir; yavaşlarım.', 3, 2),
(119, 'Pek etkilenmem.', 4, 3),
(119, 'Sinirlenebilirim.', 1, 4),
(119, 'Daha aktifleşirim.', 0, 5);

-- Soru 20
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (120, 1, 'Günün hangi saatinde en verimlisin?', 20, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(120, 'Sabah geç saatler.', 2, 1),
(120, 'Öğleden sonra.', 3, 2),
(120, 'Gün ortası.', 4, 3),
(120, 'Sabah erken.', 1, 4),
(120, 'Gece geç saatler.', 0, 5);

-- Soru 21
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (121, 1, 'Sıcakta ne kadar su içersin?', 21, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(121, 'Az içerim.', 2, 1),
(121, 'Hatırlayınca içerim.', 3, 2),
(121, 'Normal düzeyde içerim.', 4, 3),
(121, 'Sık içerim.', 1, 4),
(121, 'Çok fazla su içerim.', 0, 5);

-- Soru 22
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (122, 1, 'Rutin değişikliklerine nasıl tepki verirsin?', 22, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(122, 'Rahatsız olurum.', 2, 1),
(122, 'Uymakta zorlanırım.', 3, 2),
(122, 'Duruma göre uyum sağlarım.', 4, 3),
(122, 'Hızla adapte olurum.', 1, 4),
(122, 'Değişiklik beni motive eder.', 0, 5);

-- Soru 23
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (123, 1, 'Planlı mı yoksa spontane mi yaşarsın?', 23, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(123, 'Her şeyi planlarım.', 2, 1),
(123, 'Önceden uzun uzun düşünürüm.', 3, 2),
(123, 'Duruma göre değişir.', 4, 3),
(123, 'Çoğunlukla spontane davranırım.', 1, 4),
(123, 'İçgüdülerimle hemen hareket ederim.', 0, 5);

-- Soru 24
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (124, 1, 'Yeni bir ortama girdiğinde nasıl davranırsın?', 24, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(124, 'Önce gözlemler, konuşmam.', 2, 1),
(124, 'Sessiz kalırım.', 3, 2),
(124, 'Duruma göre açılırım.', 4, 3),
(124, 'Hemen iletişim kurarım.', 1, 4),
(124, 'Ortamın enerjisini yükseltirim.', 0, 5);

-- Soru 25
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (125, 1, 'Hayal kurma eğilimin nasıldır?', 25, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(125, 'Sık sık dalar, hayal kurarım.', 2, 1),
(125, 'Gerçeklerden kopmadan hayal kurarım.', 3, 2),
(125, 'Dengeli bir hayal gücüm var.', 4, 3),
(125, 'Nadir hayal kurarım.', 1, 4),
(125, 'Genelde somut/pratik düşünürüm.', 0, 5);

-- Soru 26
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (126, 1, 'Ağrılara karşı dayanıklılığın nasıldır?', 26, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(126, 'Hafif ağrılar bile etkiler.', 2, 1),
(126, 'Orta düzeyde dayanıklıyım.', 3, 2),
(126, 'Çoğunu önemsemem.', 4, 3),
(126, 'Yüksek dayanıklılığım var.', 1, 4),
(126, 'Çok yüksek eşiğim var.', 0, 5);

-- Soru 27
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (127, 1, 'Kilo alma eğilimin nasıldır?', 27, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(127, 'Kolay kilo alırım.', 2, 1),
(127, 'Zor kilo veririm.', 3, 2),
(127, 'Dengeli tutulur.', 4, 3),
(127, 'Zor kilo alırım.', 1, 4),
(127, 'Kilo tutmakta zorlanırım.', 0, 5);

-- Soru 28
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (128, 1, 'Hedeflerine yaklaşım tarzın nasıldır?', 28, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(128, 'Sabırlı ama yavaş ilerlerim.', 2, 1),
(128, 'Doğru anı beklerim.', 3, 2),
(128, 'Dengeli ilerlerim.', 4, 3),
(128, 'Hızlı adımlar atarım.', 1, 4),
(128, 'Risk alır, çabuk sonuç isterim.', 0, 5);

-- Soru 29
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (129, 1, 'Eleştiri aldığında nasıl davranırsın?', 29, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(129, 'İçime kapanırım.', 2, 1),
(129, 'Uzun süre düşünürüm.', 3, 2),
(129, 'Kabul eder, değerlendiririm.', 4, 3),
(129, 'Hızlı tepki veririm.', 1, 4),
(129, 'Hemen karşı argüman üretirim.', 0, 5);

-- Soru 30
INSERT INTO test_sorulari (id, test_id, soru, sira, durum) VALUES (130, 1, 'Yeni bilgileri öğrenirken yaklaşımın nasıldır?', 30, 1);
INSERT INTO test_cevaplari (soru_id, cevap, mizac_tipi, sira) VALUES
(130, 'Temkinli ve kuşkucuyum.', 2, 1),
(130, 'Önce gözlemler, sonra denerim.', 3, 2),
(130, 'Mantıklıysa kabul ederim.', 4, 3),
(130, 'Hızlı öğrenir, uygularım.', 1, 4),
(130, 'Deneyerek hemen uygularım.', 0, 5);
