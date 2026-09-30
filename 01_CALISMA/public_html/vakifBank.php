<?php

class vakifBank
{
    // Vakıf Katılım SanalPOS API Dökümanı v2.7 - 3DSecure Ödeme İşlemi Adresi
    public const GATEWAY_URL = 'https://boa.vakifkatilim.com.tr/VirtualPOS.Gateway/Home/ThreeDModelPayGate';
    
    private $settings = [];

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    /**
     * XML formatında 3DS ödeme isteği - banka XML bekliyor (form POST değil)
     * Kart bilgileri $payment['CardNumber'], ['CardExpireYear'], ['CardExpireMonth'], ['CardCVV2'], ['CardHolderName'] ile gelmeli
     */
    public function paymentXml($payment)
    {
        $postUrl = !empty($this->settings['GatewayURL']) ? trim($this->settings['GatewayURL']) : self::GATEWAY_URL;
        
        $merchantId = $this->settings['HostMerchantId'];
        $customerId = $this->settings['CustomerId'] ?? $this->settings['HostTerminalId'];
        $userName = (!empty($this->settings['UserName'])) ? $this->settings['UserName'] : $this->settings['HostTerminalId'];
        $password = $this->settings['MerchantPassword'];
        
        $hashPassword = '';
        $maybeB64 = base64_decode($password, true);
        if ($maybeB64 !== false && strlen($maybeB64) === 20) {
            $hashPassword = $password;
        } elseif (preg_match('/^[a-f0-9]{40}$/i', $password)) {
            $hashPassword = base64_encode(hex2bin($password));
        } else {
            $hashPassword = base64_encode(sha1($password, true));
        }
        
        $orderId = $payment['TransactionId'];
        $amount = (float)$payment['Amount'];
        $amountKurus = (int)round($amount * 100);
        $displayAmount = number_format($amount, 2, '.', '');
        
        $okUrl = preg_replace('/^(https?:\/\/)www\./i', '$1', $this->settings['SuccessURL']);
        $failUrl = preg_replace('/^(https?:\/\/)www\./i', '$1', $this->settings['FailureURL']);
        
        $hashString = $merchantId . $orderId . $amountKurus . $okUrl . $failUrl . $userName . $hashPassword;
        $hashData = base64_encode(sha1($hashString, true));
        
        $currencyCode = isset($payment['AmountCode']) ? $payment['AmountCode'] : '0949';
        if (strlen($currencyCode) == 3) $currencyCode = '0' . $currencyCode;
        
        $cardNumber = preg_replace('/\s+/', '', $payment['CardNumber'] ?? '');
        $cardExpYear = $payment['CardExpireYear'] ?? '';
        $cardExpMonth = str_pad($payment['CardExpireMonth'] ?? '', 2, '0', STR_PAD_LEFT);
        $cardCvv = $payment['CardCVV2'] ?? '';
        $cardHolder = $payment['CardHolderName'] ?? '';
        
        $name = ($payment['CardHolderName'] ?? '') ?: (trim(($payment['FirstName'] ?? '') . ' ' . ($payment['LastName'] ?? '')));
        $email = $payment['Email'] ?? 'musteri@example.com';
        $phone = $payment['Phone'] ?? '5000000000';
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<VPosMessageContract xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . "\n";
        $xml .= '<OkUrl>' . htmlspecialchars($okUrl, ENT_XML1, 'UTF-8') . '</OkUrl>' . "\n";
        $xml .= '<FailUrl>' . htmlspecialchars($failUrl, ENT_XML1, 'UTF-8') . '</FailUrl>' . "\n";
        $xml .= '<HashData>' . $hashData . '</HashData>' . "\n";
        $xml .= '<MerchantId>' . htmlspecialchars($merchantId, ENT_XML1, 'UTF-8') . '</MerchantId>' . "\n";
        $xml .= '<SubMerchantId>0</SubMerchantId>' . "\n";
        $xml .= '<CustomerId>' . htmlspecialchars($customerId, ENT_XML1, 'UTF-8') . '</CustomerId>' . "\n";
        $xml .= '<UserName>' . htmlspecialchars($userName, ENT_XML1, 'UTF-8') . '</UserName>' . "\n";
        $xml .= '<HashPassword>' . htmlspecialchars($hashPassword, ENT_XML1, 'UTF-8') . '</HashPassword>' . "\n";
        $xml .= '<MerchantOrderId>' . htmlspecialchars($orderId, ENT_XML1, 'UTF-8') . '</MerchantOrderId>' . "\n";
        $xml .= '<InstallmentCount>0</InstallmentCount>' . "\n";
        $xml .= '<Amount>' . $amountKurus . '</Amount>' . "\n";
        $xml .= '<DisplayAmount>' . $displayAmount . '</DisplayAmount>' . "\n";
        $xml .= '<FECAmount>0</FECAmount>' . "\n";
        $xml .= '<FECCurrencyCode>' . $currencyCode . '</FECCurrencyCode>' . "\n";
        $xml .= '<AdditionalData><AdditionalDataList></AdditionalDataList></AdditionalData>' . "\n";
        $xml .= '<Addresses><VPosAddressContract>';
        $xml .= '<Type>1</Type><Name>' . htmlspecialchars($name, ENT_XML1, 'UTF-8') . '</Name>';
        $xml .= '<PhoneNumber>' . htmlspecialchars($phone, ENT_XML1, 'UTF-8') . '</PhoneNumber>';
        $xml .= '<OrderId>0</OrderId><AddressId>12</AddressId>';
        $xml .= '<Email>' . htmlspecialchars($email, ENT_XML1, 'UTF-8') . '</Email>';
        $xml .= '</VPosAddressContract></Addresses>' . "\n";
        $xml .= '<APIVersion>1.0.0</APIVersion>' . "\n";
        $xml .= '<CardNumber>' . htmlspecialchars($cardNumber, ENT_XML1, 'UTF-8') . '</CardNumber>' . "\n";
        $xml .= '<CardExpireDateYear>' . htmlspecialchars($cardExpYear, ENT_XML1, 'UTF-8') . '</CardExpireDateYear>' . "\n";
        $xml .= '<CardExpireDateMonth>' . $cardExpMonth . '</CardExpireDateMonth>' . "\n";
        $xml .= '<CardCVV2>' . htmlspecialchars($cardCvv, ENT_XML1, 'UTF-8') . '</CardCVV2>' . "\n";
        $xml .= '<CardHolderName>' . htmlspecialchars($cardHolder, ENT_XML1, 'UTF-8') . '</CardHolderName>' . "\n";
        $xml .= '<PaymentType>1</PaymentType>' . "\n";
        $xml .= '<DebtId>0</DebtId>' . "\n";
        $xml .= '<SurchargeAmount>0</SurchargeAmount>' . "\n";
        $xml .= '<SGKDebtAmount>0</SGKDebtAmount>' . "\n";
        $xml .= '<InstallmentMaturityCommisionFlag>0</InstallmentMaturityCommisionFlag>' . "\n";
        $xml .= '<TransactionSecurity>3</TransactionSecurity>' . "\n";
        $xml .= '</VPosMessageContract>';
        
        $ch = curl_init($postUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $xml,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/xml; charset=UTF-8',
                'Content-Length: ' . strlen($xml)
            ],
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);
        
        if ($curlErr) {
            return [
                'status' => false,
                'ErrorCode' => 'CURL: ' . $curlErr,
                'is_html_form' => false
            ];
        }
        
        if (stripos($response, '<?xml') === 0 || stripos($response, '<VPosTransactionResponseContract') !== false) {
            $xmlResp = @simplexml_load_string($response);
            if ($xmlResp) {
                $rc = (string)($xmlResp->ResponseCode ?? '');
                $msg = (string)($xmlResp->ResponseMessage ?? '');
                if ($rc !== '00' && $rc !== '') {
                    return [
                        'status' => false,
                        'ErrorCode' => $rc . ' - ' . $msg,
                        'ResponseCode' => $rc,
                        'ResponseMessage' => $msg,
                        'raw_response' => $response,
                        'is_html_form' => false
                    ];
                }
            }
        }
        
        if (stripos($response, '<html') !== false || stripos($response, '<form') !== false) {
            return [
                'status' => true,
                'html_response' => $response,
                'is_html_form' => false,
                'is_3ds_html' => true
            ];
        }
        
        return [
            'status' => true,
            'html_response' => $response,
            'is_html_form' => false,
            'is_3ds_html' => true
        ];
    }

    public function payment($payment)
    {
        // Gateway URL: settings'ten veya varsayılan const (banka dokümanından teyit edin)
        $postUrl = !empty($this->settings['GatewayURL']) ? trim($this->settings['GatewayURL']) : self::GATEWAY_URL;
        
        $merchantId = $this->settings['HostMerchantId'];
        $userName = (!empty($this->settings['UserName'])) ? $this->settings['UserName'] : $this->settings['HostTerminalId'];
        $password = $this->settings['MerchantPassword'];
        
        // Şifre Hashleme İşlemi
        $hashPassword = '';
        $maybeB64 = base64_decode($password, true);
        if ($maybeB64 !== false && strlen($maybeB64) === 20) {
            $hashPassword = $password;
        } elseif (preg_match('/^[a-f0-9]{40}$/i', $password)) {
            $hashPassword = base64_encode(hex2bin($password));
        } else {
            $hashPassword = base64_encode(sha1($password, true));
        }
        
        $orderId = $payment['TransactionId'];
        
        // Tutar Kuruş cinsine çevrilmeli
        $amount = (float)$payment['Amount'];
        $amountKurus = (int)round($amount * 100);
        
        $okUrl = $this->settings['SuccessURL'];
        $failUrl = $this->settings['FailureURL'];
        
        // 3DS2 zorunlu: Ödemenin yapıldığı sayfa URL'si (callback URL değil!)
        // Sadece settings'teki RequestorURL kullanılır - callback URL asla kullanılmaz
        if (empty($this->settings['RequestorURL'])) {
            throw new \InvalidArgumentException('threeDSRequestorURL zorunludur. Ödeme sayfası URL\'sini (paytr_tekil_bagis veya bagis-odeme) RequestorURL olarak gönderin.');
        }
        $threeDSRequestorURL = $this->settings['RequestorURL'];
        
        // URL standardizasyonu: www/non-www tutarlılığı
        // www ile başlıyorsa kaldır, protocol'ü koru (non-www standardı)
        $threeDSRequestorURL = preg_replace('/^(https?:\/\/)www\./i', '$1', $threeDSRequestorURL);
        $okUrl = preg_replace('/^(https?:\/\/)www\./i', '$1', $okUrl);
        $failUrl = preg_replace('/^(https?:\/\/)www\./i', '$1', $failUrl);

        // HashData Hesaplama 
        // Not: Eğer hala hata alırsanız banka dökümanına göre bu string'e yeni alanlar eklenmiş olabilir.
        $hashString = $merchantId . $orderId . $amountKurus . $okUrl . $failUrl . $userName . $hashPassword;
        $hashData = base64_encode(sha1($hashString, true));
        
        // Para birimi kodu: ISO 4217 formatında 4 haneli olmalı (TRY=0949, USD=0840, EUR=0978)
        // paymentData'dan AmountCode gelirse onu kullan, yoksa varsayılan TRY (0949)
        $currencyCode = isset($payment['AmountCode']) ? $payment['AmountCode'] : '0949';
        
        // Eğer 3 haneli gelirse 4 haneliye çevir (949 -> 0949)
        if (strlen($currencyCode) == 3) {
            $currencyCode = '0' . $currencyCode;
        }
        
        $transactionSecurity = '3'; // 3D Secure

        // Form verilerini hazırla
        $formData = [
            'MerchantId'          => $merchantId,
            'MerchantOrderId'     => $orderId,
            'Amount'              => $amountKurus,
            'UserName'            => $userName,
            'HashPassword'        => $hashPassword,
            'HashData'            => $hashData,
            'OkUrl'               => $okUrl,
            'FailUrl'             => $failUrl,
            'TransactionSecurity' => $transactionSecurity,
            'FECCurrencyCode'     => $currencyCode,
            'PaymentType'         => '1', // Satış
            'Lang'                => 'TR',
            'Description'         => $payment['OrderDescription'] ?? '',
            // 3DS2 ZORUNLU ALANLAR - Vakıf Katılım dokümanına birebir uyumlu
            'threeDSRequestorURL' => $threeDSRequestorURL, // Ödemenin yapıldığı sayfa URL'si (callback değil)
            'DeviceCategory'      => '1' // 1: Browser (Web işlemleri için zorunlu)
        ];

        return [
            'status'      => true,
            'form_data'   => $formData,
            'post_url'    => $postUrl,
            'is_html_form' => true
        ];
    }

    public function callback($callback)
    {
        $responseCode = $callback['ResponseCode'] ?? '';
        $responseMessage = $callback['ResponseMessage'] ?? '';
        $orderId = $callback['MerchantOrderId'] ?? '';
        
        // Vakıf Katılım genellikle "00" dönerse başarılı sayar.
        if ($responseCode == "00") {
            return [
                'Rc'            => '00',
                'Message'       => 'İşlem Başarılı',
                'TransactionId' => $orderId,
                'ErrorCode'     => ''
            ];
        } else {
            return [
                'Rc'            => $responseCode,
                'Message'       => $responseMessage,
                'TransactionId' => $orderId,
                'ErrorCode'     => $responseCode . ' - ' . $responseMessage
            ];
        }
    }
}