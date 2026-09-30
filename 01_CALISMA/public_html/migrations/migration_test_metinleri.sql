-- Testler tablosuna giris ve sonuc metni alanlari ekleme
ALTER TABLE testler ADD COLUMN giris_metni TEXT DEFAULT NULL AFTER aciklama;
ALTER TABLE testler ADD COLUMN sonuc_metni TEXT DEFAULT NULL AFTER giris_metni;
