<?php
/**
 * IP'den ülke tespiti yapan sınıf
 * Uses ipapi.co API for geolocation
 */

class CountryDetector {
    private static $cache_file = '_class/ip_country_cache.json';
    private static $cache_duration = 86400; // 24 saat cache
    
    /**
     * IP adresinden ülke kodunu bul
     * @param string $ip IP adresi
     * @return string Ülke kodu (örn: TR, US, DE)
     */
    public static function getCountryCode($ip) {
        // Localhost için varsayılan
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'TR';
        }
        
        // Cache kontrolü
        $cached = self::getCachedCountry($ip);
        if ($cached) {
            return $cached;
        }
        
        // API'den ülke bilgisini al
        $country_code = self::fetchFromAPI($ip);
        
        // Cache'e kaydet
        if ($country_code) {
            self::cacheCountry($ip, $country_code);
        }
        
        return $country_code ?: 'TR'; // Varsayılan Türkiye
    }
    
    /**
     * IP'den ülke bilgisini API'den al
     */
    private static function fetchFromAPI($ip) {
        try {
            $url = "https://ipapi.co/{$ip}/country/";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_USERAGENT, 'DernekYeni/1.0');
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code === 200 && $response) {
                $country_code = trim($response);
                if (strlen($country_code) === 2) {
                    return strtoupper($country_code);
                }
            }
        } catch (Exception $e) {
            error_log("Ülke tespiti hatası: " . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Cache'den ülke kodunu al
     */
    private static function getCachedCountry($ip) {
        if (!file_exists(self::$cache_file)) {
            return null;
        }
        
        $data = json_decode(file_get_contents(self::$cache_file), true);
        if (!is_array($data) || !isset($data[$ip])) {
            return null;
        }
        
        $cache_time = $data[$ip]['timestamp'];
        if (time() - $cache_time > self::$cache_duration) {
            return null; // Cache süresi dolmuş
        }
        
        return $data[$ip]['country'];
    }
    
    /**
     * Ülke kodunu cache'e kaydet
     */
    private static function cacheCountry($ip, $country_code) {
        $data = [];
        
        if (file_exists(self::$cache_file)) {
            $existing = json_decode(file_get_contents(self::$cache_file), true);
            if (is_array($existing)) {
                $data = $existing;
            }
        }
        
        $data[$ip] = [
            'country' => $country_code,
            'timestamp' => time()
        ];
        
        file_put_contents(self::$cache_file, json_encode($data));
    }
    
    /**
     * Ülke kodundan bayrak emoji'si al
     */
    public static function getFlagEmoji($country_code) {
        $flags = [
            'TR' => '🇹🇷',
            'US' => '🇺🇸',
            'DE' => '🇩🇪',
            'GB' => '🇬🇧',
            'FR' => '🇫🇷',
            'NL' => '🇳🇱',
            'AT' => '🇦🇹',
            'BE' => '🇧🇪',
            'CH' => '🇨🇭',
            'DK' => '🇩🇰',
            'SE' => '🇸🇪',
            'NO' => '🇳🇴',
            'FI' => '🇫🇮',
            'CA' => '🇨🇦',
            'AU' => '🇦🇺',
            'IT' => '🇮🇹',
            'ES' => '🇪🇸',
            'PL' => '🇵🇱',
            'CZ' => '🇨🇿',
            'HU' => '🇭🇺',
            'RO' => '🇷🇴',
            'BG' => '🇧🇬',
            'GR' => '🇬🇷',
            'RU' => '🇷🇺',
            'UA' => '🇺🇦',
            'JP' => '🇯🇵',
            'KR' => '🇰🇷',
            'CN' => '🇨🇳',
            'IN' => '🇮🇳',
            'PK' => '🇵🇰',
            'BD' => '🇧🇩',
            'ID' => '🇮🇩',
            'MY' => '🇲🇾',
            'SG' => '🇸🇬',
            'TH' => '🇹🇭',
            'VN' => '🇻🇳',
            'PH' => '🇵🇭',
            'EG' => '🇪🇬',
            'SA' => '🇸🇦',
            'AE' => '🇦🇪',
            'QA' => '🇶🇦',
            'KW' => '🇰🇼',
            'OM' => '🇴🇲',
            'BH' => '🇧🇭',
            'JO' => '🇯🇴',
            'LB' => '🇱🇧',
            'SY' => '🇸🇾',
            'IQ' => '🇮🇶',
            'IR' => '🇮🇷',
            'IL' => '🇮🇱',
            'TR' => '🇹🇷'
        ];
        
        return $flags[strtoupper($country_code)] ?? '🏳️'; // Varsayılan beyaz bayrak
    }
    
    /**
     * Ülke kodundan ülke adı al
     */
    public static function getCountryName($country_code) {
        $countries = [
            'TR' => 'Türkiye',
            'US' => 'Amerika Birleşik Devletleri',
            'DE' => 'Almanya',
            'GB' => 'Birleşik Krallık',
            'FR' => 'Fransa',
            'NL' => 'Hollanda',
            'AT' => 'Avusturya',
            'BE' => 'Belçika',
            'CH' => 'İsviçre',
            'DK' => 'Danimarka',
            'SE' => 'İsveç',
            'NO' => 'Norveç',
            'FI' => 'Finlandiya',
            'CA' => 'Kanada',
            'AU' => 'Avustralya',
            'IT' => 'İtalya',
            'ES' => 'İspanya',
            'PL' => 'Polonya',
            'CZ' => 'Çekya',
            'HU' => 'Macaristan',
            'RO' => 'Romanya',
            'BG' => 'Bulgaristan',
            'GR' => 'Yunanistan',
            'RU' => 'Rusya',
            'UA' => 'Ukrayna',
            'JP' => 'Japonya',
            'KR' => 'Güney Kore',
            'CN' => 'Çin',
            'IN' => 'Hindistan',
            'PK' => 'Pakistan',
            'BD' => 'Bangladeş',
            'ID' => 'Endonezya',
            'MY' => 'Malezya',
            'SG' => 'Singapur',
            'TH' => 'Tayland',
            'VN' => 'Vietnam',
            'PH' => 'Filipinler',
            'EG' => 'Mısır',
            'SA' => 'Suudi Arabistan',
            'AE' => 'Birleşik Arap Emirlikleri',
            'QA' => 'Katar',
            'KW' => 'Kuveyt',
            'OM' => 'Umman',
            'BH' => 'Bahreyn',
            'JO' => 'Ürdün',
            'LB' => 'Lübnan',
            'SY' => 'Suriye',
            'IQ' => 'Irak',
            'IR' => 'İran',
            'IL' => 'İsrail'
        ];
        
        return $countries[strtoupper($country_code)] ?? 'Bilinmeyen Ülke';
    }
}
?>