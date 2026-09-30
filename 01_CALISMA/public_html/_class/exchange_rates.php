<?php
/**
 * Exchange Rates Helper Class
 * Fetches exchange rates from free APIs and caches them for performance
 */

class ExchangeRates {
    private static $cache_file = __DIR__ . '/exchange_rates_cache.json';
    private static $cache_duration = 3600; // 1 hour cache
    
    /**
     * Get exchange rates for specified currencies
     * @param array $currencies List of currency codes to get rates for
     * @return array Associative array of currency rates with TRY as base
     */
    public static function getRates($currencies = ['USD', 'EUR']) {
        // Check if we have valid cached data
        $cached_data = self::getCachedRates();
        if ($cached_data && self::isCacheValid()) {
            return $cached_data;
        }
        
        // Try to fetch from API
        $rates = self::fetchFromAPI($currencies);
        
        // If API fails, use cached data even if expired
        if (!$rates && $cached_data) {
            return $cached_data;
        }
        
        // If we have rates, cache them
        if ($rates) {
            self::cacheRates($rates);
            return $rates;
        }
        
        // Fallback to hardcoded rates if everything fails
        return self::getDefaultRates();
    }
    
    /**
     * Fetch exchange rates from European Central Bank API
     * @param array $currencies List of currency codes to get rates for
     * @return array|false Associative array of currency rates or false on failure
     */
    private static function fetchFromAPI($currencies) {
        // ECB API provides rates against EUR, so we need to convert to TRY rates
        $ecb_url = 'https://api.exchangerate-api.com/v4/latest/TRY';
        
        // Try to fetch from ECB API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ecb_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'DernekYeni/1.0');
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code !== 200 || !$response) {
            return false;
        }
        
        $data = json_decode($response, true);
        
        if (!isset($data['rates']) || !is_array($data['rates'])) {
            return false;
        }
        
        $rates = ['TRY' => 1]; // Base currency (TRY)
        
        // Extract requested currencies
        // API'den gelen veriler: 1 TRY = X USD (örnek: 0.033)
        // Bizim ihtiyacımız: 1 USD = Y TRY (örnek: 30.30)
        // Yani: 1 / X = Y
        foreach ($currencies as $currency) {
            if ($currency !== 'TRY' && isset($data['rates'][$currency])) {
                // API'den gelen rate: 1 TRY = data['rates'][$currency] USD
                // Bizim ihtiyacımız: 1 USD = 1 / data['rates'][$currency] TRY
                // Ama biz TRY base çalışıyoruz, yani:
                // rates[$currency] = kaç TRY = 1 $currency
                $rates[$currency] = round(1 / $data['rates'][$currency], 4);
            }
        }
        
        return $rates;
    }
    
    /**
     * Get cached exchange rates
     * @return array|false Cached rates or false if not available
     */
    private static function getCachedRates() {
        if (!file_exists(self::$cache_file)) {
            return false;
        }
        
        $data = json_decode(file_get_contents(self::$cache_file), true);
        
        if (!is_array($data) || !isset($data['rates'])) {
            return false;
        }
        
        return $data['rates'];
    }
    
    /**
     * Check if cache is still valid
     * @return bool True if cache is valid, false otherwise
     */
    private static function isCacheValid() {
        if (!file_exists(self::$cache_file)) {
            return false;
        }
        
        $cache_time = filemtime(self::$cache_file);
        $current_time = time();
        
        return ($current_time - $cache_time) < self::$cache_duration;
    }
    
    /**
     * Cache exchange rates
     * @param array $rates Exchange rates to cache
     * @return bool True on success, false on failure
     */
    private static function cacheRates($rates) {
        $data = [
            'timestamp' => time(),
            'rates' => $rates
        ];
        
        return file_put_contents(self::$cache_file, json_encode($data)) !== false;
    }
    
    /**
     * Get default hardcoded exchange rates as fallback
     * @return array Default exchange rates
     */
    private static function getDefaultRates() {
        return [
            'TRY' => 1,
            'USD' => 30.50,
            'EUR' => 33.20
        ];
    }
    
    /**
     * Convert amount from one currency to another
     * @param float $amount Amount to convert
     * @param string $from_currency Source currency code
     * @param string $to_currency Target currency code
     * @param array $rates Exchange rates array
     * @return float Converted amount
     */
    public static function convert($amount, $from_currency, $to_currency, $rates) {
        // If same currency, no conversion needed
        if ($from_currency === $to_currency) {
            return $amount;
        }
        
        // Check if we have rates for both currencies
        if (!isset($rates[$from_currency]) || !isset($rates[$to_currency])) {
            return $amount; // Return original amount if rates not available
        }
        
        // Convert to base currency (TRY) first, then to target currency
        $amount_in_try = $amount * $rates[$from_currency];
        $converted_amount = $amount_in_try / $rates[$to_currency];
        
        return round($converted_amount, 2);
    }
}
?>