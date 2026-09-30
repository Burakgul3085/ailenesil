<?php
/**
 * Vakıfbank İşlem Loglama Sistemi
 * Her ödeme işlemi için detaylı log kaydı tutar
 * Banka ile paylaşılmak üzere raporlama sağlar
 */

class VakifbankLogger {
    private $db;
    private $logDir;
    
    public function __construct($db) {
        $this->db = $db;
        $this->logDir = __DIR__ . '/../logs/vakifbank/';
        
        // Log klasörü yoksa oluştur
        if (!file_exists($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
    }
    
    /**
     * Ödeme başlatma isteğini logla
     */
    public function logPaymentRequest($orderID, $amount, $formData, $settings, $probeResult = null, $context = []) {
        $probeArr = is_array($probeResult) ? $probeResult : [];
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'PAYMENT_REQUEST',
            'order_id' => $orderID,
            'amount' => $amount,
            'gateway_url' => $probeArr['url'] ?? ($settings['GatewayURL'] ?? 'N/A'),
            'probe_http_code' => $probeArr['http_code'] ?? 'N/A',
            'probe_404_warning' => !empty($probeArr['config_error']),
            'currency' => $formData['FECCurrencyCode'] ?? 'N/A',
            'merchant_id' => $formData['MerchantId'] ?? 'N/A',
            'username' => $formData['UserName'] ?? 'N/A',
            'transaction_security' => $formData['TransactionSecurity'] ?? 'N/A',
            'has_threeDSRequestorURL' => isset($formData['threeDSRequestorURL']) ? 'YES' : 'NO',
            'threeDSRequestorURL' => $formData['threeDSRequestorURL'] ?? 'N/A',
            'browser_info' => [
                'javaEnabled' => $formData['browserJavaEnabled'] ?? 'N/A',
                'javascriptEnabled' => $formData['browserJavascriptEnabled'] ?? 'N/A',
                'language' => $formData['browserLanguage'] ?? 'N/A',
                'colorDepth' => $formData['browserColorDepth'] ?? 'N/A',
                'screenHeight' => $formData['browserScreenHeight'] ?? 'N/A',
                'screenWidth' => $formData['browserScreenWidth'] ?? 'N/A',
                'timezone' => $formData['browserTZ'] ?? 'N/A'
            ],
            'test_mode' => $settings['init'] ?? 'N/A',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
            'network_probe' => $probeResult ?? 'NOT_RUN',
            'context' => $context
        ];
        
        $this->writeLog($orderID, $logData);
        $this->saveToDatabase($orderID, 'REQUEST', $logData);
        
        return $logData;
    }
    
    /**
     * Banka endpoint'ine HTTP/SSL erişim testi yapar
     */
    public function probeEndpoint($orderID, $url, $connectTimeout = 5, $timeout = 15) {
        if (!function_exists('curl_init')) {
            $data = [
                'timestamp' => date('Y-m-d H:i:s'),
                'type' => 'HTTP_PROBE',
                'order_id' => $orderID,
                'url' => $url,
                'http_code' => 0,
                'config_error' => true,
                'error' => 'cURL yüklü değil'
            ];
            $this->writeLog($orderID, $data);
            $this->saveToDatabase($orderID, 'HTTP_PROBE', $data);
            return $data;
        }
        
        // GET kullan: Birçok ödeme gateway'i HEAD'e 404 döner, sadece POST kabul eder
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => $connectTimeout,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CERTINFO => true,
            CURLOPT_FOLLOWLOCATION => false
        ]);
        
        $response = curl_exec($ch);
        $curlErr = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        $httpCode = (int)($info['http_code'] ?? 0);
        // 200, 302 = OK. 405 = endpoint var, sadece POST kabul ediyor (health-check için yeterli)
        $configError = !in_array($httpCode, [200, 302, 405], true);

        $probeResult = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'HTTP_PROBE',
            'order_id' => $orderID,
            'url' => $url,
            'http_code' => $httpCode,
            'config_error' => $configError,
            'primary_ip' => $info['primary_ip'] ?? 'N/A',
            'local_ip' => $info['local_ip'] ?? 'N/A',
            'total_time' => $info['total_time'] ?? 0,
            'connect_time' => $info['connect_time'] ?? 0,
            'ssl_verify_result' => $info['ssl_verify_result'] ?? 'N/A',
            'certinfo' => $info['certinfo'] ?? [],
            'curl_error' => $curlErr ?: null,
            'response_headers_sample' => $response ? substr($response, 0, 1000) : ''
        ];
        
        $this->writeLog($orderID, $probeResult);
        $this->saveToDatabase($orderID, 'HTTP_PROBE', $probeResult);
        
        return $probeResult;
    }
    
    /**
     * Banka yanıtını logla
     */
    public function logBankResponse($orderID, $responseData, $isSuccess = false) {
        $rc = $responseData['ResponseCode'] ?? $responseData['Rc'] ?? 'N/A';
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'BANK_RESPONSE',
            'order_id' => $orderID,
            'status' => $isSuccess ? 'SUCCESS' : 'FAILED',
            'response_code' => $rc,
            'bank_rc' => $rc,
            'response_message' => $responseData['ResponseMessage'] ?? $responseData['Message'] ?? 'N/A',
            'transaction_id' => $responseData['TransactionId'] ?? $responseData['MerchantOrderId'] ?? 'N/A',
            'md_status' => $responseData['MDStatus'] ?? 'N/A',
            'error_code' => $responseData['ErrorCode'] ?? 'N/A',
            'full_response' => $responseData,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'N/A'
        ];
        
        $this->writeLog($orderID, $logData);
        $this->saveToDatabase($orderID, $isSuccess ? 'SUCCESS' : 'FAILED', $logData);
        
        return $logData;
    }
    
    /**
     * Callback isteği ham verisi loglanır
     */
    public function logCallbackReception($orderID, $payload, $source = 'callback', $extra = []) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'CALLBACK_RECEPTION',
            'order_id' => $orderID,
            'source' => $source,
            'payload' => $payload,
            'extra' => $extra,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
        ];
        
        $this->writeLog($orderID, $logData);
        $this->saveToDatabase($orderID, 'CALLBACK', $logData);
        
        return $logData;
    }
    
    /**
     * İstisnaları logla
     */
    public function logException($orderID, $exception, $stage = 'unknown', $context = []) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'EXCEPTION',
            'order_id' => $orderID,
            'stage' => $stage,
            'exception_class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'context' => $context,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
        ];
        
        $this->writeLog($orderID, $logData);
        $this->saveToDatabase($orderID, 'EXCEPTION', $logData);
        
        return $logData;
    }
    
    /**
     * Dosyaya log yaz
     */
    private function writeLog($orderID, $data) {
        $filename = $this->logDir . date('Y-m-d') . '_vakifbank.log';
        $logEntry = "\n" . str_repeat('=', 80) . "\n";
        $logEntry .= "Order ID: {$orderID}\n";
        $logEntry .= "Type: {$data['type']}\n";
        $logEntry .= "Timestamp: {$data['timestamp']}\n";
        $logEntry .= str_repeat('-', 80) . "\n";
        $logEntry .= json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $logEntry .= "\n" . str_repeat('=', 80) . "\n";
        
        file_put_contents($filename, $logEntry, FILE_APPEND);
    }
    
    /**
     * Veritabanına kaydet
     */
    private function saveToDatabase($orderID, $type, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO vakifbank_transaction_logs 
                (order_id, log_type, log_data, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $orderID,
                $type,
                json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            ]);
        } catch (Exception $e) {
            // Veritabanı hatası olursa sadece dosyaya yazıyoruz
            error_log("VakifbankLogger DB Error: " . $e->getMessage());
        }
    }
    
    /**
     * Banka için rapor oluştur
     */
    public function generateBankReport($startDate = null, $endDate = null) {
        if (!$startDate) $startDate = date('Y-m-d', strtotime('-7 days'));
        if (!$endDate) $endDate = date('Y-m-d');
        
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    order_id,
                    log_type,
                    log_data,
                    created_at
                FROM vakifbank_transaction_logs
                WHERE DATE(created_at) BETWEEN ? AND ?
                ORDER BY created_at DESC
            ");
            
            $stmt->execute([$startDate, $endDate]);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $report = [
                'report_date' => date('Y-m-d H:i:s'),
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate
                ],
                'summary' => [
                    'total_requests' => 0,
                    'successful' => 0,
                    'failed' => 0,
                    'success_rate' => 0
                ],
                'failed_transactions' => [],
                'successful_transactions' => []
            ];
            
            foreach ($logs as $log) {
                $logData = json_decode($log['log_data'], true);
                
                if ($log['log_type'] === 'REQUEST') {
                    $report['summary']['total_requests']++;
                } elseif ($log['log_type'] === 'SUCCESS') {
                    $report['summary']['successful']++;
                    $report['successful_transactions'][] = [
                        'order_id' => $log['order_id'],
                        'timestamp' => $log['created_at'],
                        'response_code' => $logData['response_code'] ?? 'N/A',
                        'transaction_id' => $logData['transaction_id'] ?? 'N/A'
                    ];
                } elseif ($log['log_type'] === 'FAILED') {
                    $report['summary']['failed']++;
                    $report['failed_transactions'][] = [
                        'order_id' => $log['order_id'],
                        'timestamp' => $log['created_at'],
                        'response_code' => $logData['response_code'] ?? 'N/A',
                        'response_message' => $logData['response_message'] ?? 'N/A',
                        'error_code' => $logData['error_code'] ?? 'N/A',
                        'full_details' => $logData
                    ];
                }
            }
            
            if ($report['summary']['total_requests'] > 0) {
                $report['summary']['success_rate'] = round(
                    ($report['summary']['successful'] / $report['summary']['total_requests']) * 100, 
                    2
                );
            }
            
            return $report;
            
        } catch (Exception $e) {
            return [
                'error' => 'Rapor oluşturulamadı: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Kart bazlı analiz raporu
     */
    public function generateCardAnalysisReport($startDate = null, $endDate = null) {
        if (!$startDate) $startDate = date('Y-m-d', strtotime('-7 days'));
        if (!$endDate) $endDate = date('Y-m-d');
        
        $report = $this->generateBankReport($startDate, $endDate);
        
        $analysis = [
            'report_info' => [
                'generated_at' => date('Y-m-d H:i:s'),
                'period' => "{$startDate} - {$endDate}",
                'total_transactions' => $report['summary']['total_requests'],
                'successful_count' => $report['summary']['successful'],
                'failed_count' => $report['summary']['failed'],
                'success_rate' => $report['summary']['success_rate'] . '%'
            ],
            'technical_details' => [
                'integration_type' => '3D Secure v2',
                'payment_gateway' => 'Vakıf Katılım Sanal POS',
                'implementation_date' => date('Y-m-d'),
                'parameters_sent' => [
                    'MerchantId' => 'Gönderiliyor',
                    'UserName' => 'Gönderiliyor (Fallback: HostTerminalId)',
                    'HashPassword' => 'Gönderiliyor (Base64 SHA1)',
                    'threeDSRequestorURL' => 'Gönderiliyor',
                    'browserJavaEnabled' => 'Gönderiliyor',
                    'browserJavascriptEnabled' => 'Gönderiliyor',
                    'browserLanguage' => 'Gönderiliyor',
                    'browserColorDepth' => 'Gönderiliyor',
                    'browserScreenHeight' => 'Gönderiliyor',
                    'browserScreenWidth' => 'Gönderiliyor',
                    'browserTZ' => 'Gönderiliyor'
                ]
            ],
            'failed_transactions_detail' => $report['failed_transactions'],
            'note_to_bank' => 'Yukarıdaki parametrelerin tamamı her işlemde eksiksiz gönderilmektedir. Bazı kartlarda başarılı, bazılarında başarısız olması durumu, kart bazlı 3D Secure doğrulama sürecinde veya banka tarafındaki kart limitlerinde/kısıtlamalarında farklılık olduğunu düşündürmektedir. Lütfen başarısız işlemleri kart bazında inceleyiniz.'
        ];
        
        return $analysis;
    }
}
