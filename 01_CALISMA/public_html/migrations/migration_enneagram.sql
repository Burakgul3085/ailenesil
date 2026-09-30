-- Enneagram Testi - DB Güncellemeleri
-- test_sonuclari tablosuna veli/öğrenci bilgi alanları ekle

ALTER TABLE `test_sonuclari` ADD COLUMN `veli_ad` varchar(255) DEFAULT NULL AFTER `test_id`;
ALTER TABLE `test_sonuclari` ADD COLUMN `ogrenci_ad` varchar(255) DEFAULT NULL AFTER `veli_ad`;
ALTER TABLE `test_sonuclari` ADD COLUMN `ogrenci_yas` int(3) DEFAULT NULL AFTER `ogrenci_ad`;
ALTER TABLE `test_sonuclari` ADD COLUMN `telefon` varchar(20) DEFAULT NULL AFTER `ogrenci_yas`;
ALTER TABLE `test_sonuclari` ADD COLUMN `email` varchar(255) DEFAULT NULL AFTER `telefon`;

-- Testler tablosunu güncelle
UPDATE `testler` SET `adi` = 'Enneagram Testi', `aciklama` = 'Hızlı Enneagram testi ile çocuğunuzun kişilik tipini keşfedin.' WHERE `id` = 1;

-- Eski mizaç tiplerini sil, yeni 9 Enneagram tipi ekle
DELETE FROM `test_mizac_tipleri` WHERE `test_id` = 1;

INSERT INTO `test_mizac_tipleri` (`test_id`, `mizac_tipi`, `baslik`, `etiket`, `aciklama`, `renk`) VALUES
(1, 1, 'Tip 1 - Reformcu (Mükemmeliyetçi)', 'İlkeli · Düzenli · Sorumlu', 'Çocuğunuz ilkeli, düzenli ve mükemmeliyetçi bir yapıya sahiptir. Doğru olanı yapmak ister, eleştirel ve ayrıntıcı bir bakış açısına sahiptir. Adalet duygusu güçlüdür. Kurallara uyar ve çevresinden de aynısını bekler. Sorumluluk sahibidir ve kendini sürekli geliştirmek ister.', '#4A90D2'),
(1, 2, 'Tip 2 - Yardımsever', 'Sıcak · İlgili · Fedakâr', 'Çocuğunuz sıcak, ilgili ve fedakâr bir yapıya sahiptir. Başkalarına yardım etmekten büyük keyif alır. İlişki odaklı, duygusal ve empatik bir kişiliği vardır. Sevgi dolu ve arkadaş canlısıdır. Çevresindeki insanların ihtiyaçlarını fark etmede yeteneklidir.', '#E74C3C'),
(1, 3, 'Tip 3 - Başarıcı', 'Hedef Odaklı · Hırslı · Enerjik', 'Çocuğunuz hedef odaklı, hırslı ve uyumlu bir yapıya sahiptir. Başarılı olmak ve takdir görmek ister. Enerjik, üretken ve motivasyonu yüksektir. Rekabetçi bir ruhu vardır. Liderlik özellikleri taşır ve çevresini motive etme yeteneğine sahiptir.', '#F39C12'),
(1, 4, 'Tip 4 - Bireyci (Romantik)', 'Özgün · Duygusal · Yaratıcı', 'Çocuğunuz özgün, duygusal ve yaratıcı bir yapıya sahiptir. Anlamlı ve derin bağlantılar kurmak ister. İç dünyası zengin, sezgisel ve hassas bir kişiliği vardır. Sanatsal yetenekleri güçlüdür. Kendini ifade etmeye ve anlaşılmaya büyük önem verir.', '#9B59B6'),
(1, 5, 'Tip 5 - Araştırmacı (Gözlemci)', 'Meraklı · Analitik · Bağımsız', 'Çocuğunuz meraklı, analitik ve bağımsız bir yapıya sahiptir. Bilgiye derinlemesine ulaşmak ister. Gözlemci, mantıklı ve kendi alanını koruyan bir kişiliği vardır. Soğukkanlı ve düşünceli davranır. Öğrenme isteği ve araştırma tutkusu yüksektir.', '#2ECC71'),
(1, 6, 'Tip 6 - Sadık (Sorgulayıcı)', 'Güvenilir · Sorumlu · Tedbirli', 'Çocuğunuz güvenilir, sorumlu ve tedbirli bir yapıya sahiptir. Güvenlik ve emniyet arayışındadır. Sorgulayıcı, sadık ve topluluğuna bağlı bir kişiliği vardır. Temkinli ve kontrollü davranır. Belirsizliklerden rahatsız olur, önceden hazırlıklı olmayı tercih eder.', '#3498DB'),
(1, 7, 'Tip 7 - Maceracı (Coşkulu)', 'Enerjik · İyimser · Çok Yönlü', 'Çocuğunuz enerjik, iyimser ve çok yönlü bir yapıya sahiptir. Yeni deneyimler ve heyecan arar. Pratik, yaratıcı ve neşeli bir kişiliği vardır. Olumsuzluklara pek takılmaz. Keşfetmeyi ve öğrenmeyi sever, coşkulu ve meraklı bir ruh hali taşır.', '#F1C40F'),
(1, 8, 'Tip 8 - Lider (Meydan Okuyan)', 'Güçlü · Kararlı · Koruyucu', 'Çocuğunuz güçlü, kararlı ve koruyucu bir yapıya sahiptir. Kontrolü elinde tutmak ve bağımsız olmak ister. Cesur, doğrudan ve sahiplenici bir kişiliği vardır. Meydan okumaktan çekinmez. Riskten kaçınmaz ve çatışmadan korkmaz. Liderlik özellikleri güçlüdür.', '#E67E22'),
(1, 9, 'Tip 9 - Barışçıl (Uzlaştırıcı)', 'Uyumlu · Sabırlı · Sakin', 'Çocuğunuz uyumlu, sabırlı ve kabullenici bir yapıya sahiptir. Huzur ve denge arayışındadır. Sakin, alçak gönüllü ve uzlaştırıcı bir kişiliği vardır. Çatışmadan kaçınır ve çevresine huzur verir. Esnek ve yumuşak huylu bir yapıya sahiptir.', '#1ABC9C');
