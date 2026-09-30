-- Yerel calisma ayarlari. Canli yedegi degistirmez.
-- HTTPS yonlendirmesini kapatir ve site adresini yerel sunucuya ceker.

UPDATE moduller SET alan21 = '0' WHERE id = 1;
UPDATE ayarlar SET site_url = 'http://127.0.0.1:8080/' WHERE id = 1;
UPDATE sabit_url SET durum = 0 WHERE id = 1;
