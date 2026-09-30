<?php
require_once('_class/baglan.php');
require_once('_class/fonksiyon.php');
require_once('_class/class.upload.php');
require_once('_class/exchange_rates.php');
require_once('_class/Bildirim.php');

$exchange_rates = ExchangeRates::getRates(['USD', 'EUR']);
$aktif_odeme = 'vakifbank';
$hizli_bagis_id = null;
$kayit = null;

if(isset($_GET['bagis_id'])) {
    $hizli_bagis_id = intval($_GET['bagis_id']);
    $cek = $db->prepare("SELECT * FROM bagis_odeme WHERE id = ?");
    $cek->execute([$hizli_bagis_id]);
    $kayit = $cek->fetch(PDO::FETCH_ASSOC);

    if(!$kayit){
        header("Location: bagis.php?hata=kayitbulunamadi");
        exit;
    }

$guncel_tutar = isset($_POST['tutar']) ? $_POST['tutar'] : $kayit['tutar'];

    $_POST = array_merge([
        'modul_id'             => $kayit['modul_id'],
        'ad'                   => $kayit['ad'],
        'soyad'                => $kayit['soyad'],
        'email'                => $kayit['email'],
        'telefon'              => $kayit['telefon'],
        'bagis_tipi'           => $kayit['bagis_tipi'],
        'tutar'                => $guncel_tutar,
        'para_birimi'          => $kayit['para_birimi'] ?? 'TRY',
        'ulke'                 => $kayit['ulke'] ?? 'TR',
        'kategori_id'          => $kayit['kategori_id'],
        'sponsortip'           => $kayit['sponsortip'] ?? 'bireysel',
        'grup_adi'             => $kayit['grup_adi'],
        'kurum_unvani'         => $kayit['kurum_unvani'],
        'sorumlu_ad_soyad'     => $kayit['sorumlu_ad_soyad'],
        'sorumlu_telefon'      => $kayit['sorumlu_telefon'],
        'sorumlu_email'        => $kayit['sorumlu_email'],
        'odeme_tipi'           => $kayit['odeme_tipi'] ?? 'tek_seferlik',
        'sponsorluk_suresi_ay' => $kayit['sponsorluk_suresi_ay'],
        'odeme_gunu'           => $kayit['odeme_gunu'] ?? 1,
        'odeme_yontemi'        => $_GET['odeme_yontemi'] ?? ($kayit['odeme_yontemi'] ?? $aktif_odeme)
    ], $_POST);
}

if(empty($_POST) && isset($_GET['tutar'])) {
    $_POST = [
        'modul_id'             => $_GET['modul_id'] ?? 1,
        'ad'                   => $_GET['ad'] ?? '',
        'soyad'                => $_GET['soyad'] ?? '',
        'email'                => $_GET['email'] ?? '',
        'telefon'              => $_GET['telefon'] ?? '',
        'tutar'                => $guncel_tutar,
        'bagis_tipi'           => $_GET['bagis_tipi'] ?? 'tek',
        'para_birimi'          => $_GET['para_birimi'] ?? ($_SESSION['para_birimi'] ?? 'TRY'),
        'ulke'                 => $_GET['ulke'] ?? 'TR',
        'sponsortip'           => $_GET['sponsortip'] ?? 'bireysel',
        'grup_adi'             => $_GET['grup_adi'] ?? null,
        'kurum_unvani'         => $_GET['kurum_unvani'] ?? null,
        'odeme_yontemi'        => $_GET['odeme_yontemi'] ?? $aktif_odeme
    ];
}

if($_POST){
    // Kart formu gönderildi - XML ödeme işlemi (banka XML bekliyor)
    if (!empty($_POST['vkb_siparis_no']) && !empty(trim($_POST['card_number'] ?? ''))) {
        require_once('vakifBank.php');
        require_once('_class/vakifbank_logger.php');
        require_once('_class/exchange_rates.php');
        $exchange_rates = ExchangeRates::getRates(['USD', 'EUR']);
        $vakifLogger = new VakifbankLogger($db);
        $vakifbank_ayar = $db->query("SELECT * FROM vakifbank WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
        if (!$vakifbank_ayar) { header("Location: bagis.php?hata=vakifbank_ayar_eksik"); exit; }
        $stmt = $db->prepare("SELECT * FROM bagis_odeme WHERE spno = ?");
        $stmt->execute([$_POST['vkb_siparis_no']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) { header("Location: bagis.php?hata=kayitbulunamadi"); exit; }
        $vakif_tutar = (float)$row['tutar'];
        $para_birimi = $row['para_birimi'] ?? 'TRY';
        if ($para_birimi != 'TRY' && isset($exchange_rates[$para_birimi])) $vakif_tutar = round($vakif_tutar * $exchange_rates[$para_birimi], 2);
        $currency_codes = ['TRY'=>'0949','USD'=>'0840','EUR'=>'0978'];
        $amountCode = $currency_codes[$para_birimi] ?? '0949';
        $protocol = (!empty($_SERVER['HTTPS'])?'https://':'http://');
        $host = preg_replace('/^www\./i','',$_SERVER['HTTP_HOST']??'');
        $base_url = $protocol.$host;
        $gatewayUrl = (isset($vakifbank_ayar['gateway_url']) && trim($vakifbank_ayar['gateway_url'])!=='') ? trim($vakifbank_ayar['gateway_url']) : vakifBank::GATEWAY_URL;
        $userName = !empty($vakifbank_ayar['user_name'])?$vakifbank_ayar['user_name']:$vakifbank_ayar['host_terminal_id'];
        $setting = ['HostMerchantId'=>$vakifbank_ayar['host_merchant_id'],'MerchantPassword'=>$vakifbank_ayar['merchant_password'],'HostTerminalId'=>$vakifbank_ayar['host_terminal_id'],'CustomerId'=>$vakifbank_ayar['host_terminal_id'],'UserName'=>$userName,'GatewayURL'=>$gatewayUrl,'SuccessURL'=>$base_url.'/vakifbank_callback.php','FailureURL'=>$base_url.'/vakifbank_callback.php','AmountCode'=>$amountCode];
        $orderID = str_replace('BAGIS','',$_POST['vkb_siparis_no']);
        $cardNum = preg_replace('/\s+/','',$_POST['card_number']);
        $exp = preg_replace('/\D/','',$_POST['card_expiry']??'');
        $expMonth = strlen($exp)>=2 ? substr($exp,0,2) : str_pad($exp,2,'0',STR_PAD_LEFT);
        $expYear = strlen($exp)>=4 ? substr($exp,2,2) : (strlen($exp)==2 ? $exp : '');
        $paymentData = ['TransactionId'=>$orderID,'Amount'=>number_format($vakif_tutar,2,'.',''),'AmountCode'=>$amountCode,'CardNumber'=>$cardNum,'CardExpireMonth'=>$expMonth,'CardExpireYear'=>$expYear,'CardCVV2'=>$_POST['card_cvv']??'','CardHolderName'=>trim($_POST['card_holder']??''),'Email'=>$row['email']??'','Phone'=>$row['telefon']??$row['tel']??'','FirstName'=>$row['ad']??'','LastName'=>$row['soyad']??''];
        $vakifBank = new vakifBank($setting);
        $paymentResult = $vakifBank->paymentXml($paymentData);
        if ($paymentResult['status'] && !empty($paymentResult['html_response'])) {
            $vakifLogger->logPaymentRequest($orderID, $paymentData['Amount'], ['type'=>'XML_3DS'], $setting, null, ['entrypoint'=>'paytr_tekil_bagis_xml']);
            echo $paymentResult['html_response'];
            exit;
        }
        $vakifLogger->logBankResponse($orderID, ['ResponseCode'=>$paymentResult['ResponseCode']??'ERR','ResponseMessage'=>$paymentResult['ErrorCode']??''], false);
        header("Location: bagis.php?hata=vakifbank&msg=".urlencode($paymentResult['ErrorCode']??'Ödeme başarısız'));
        exit;
    }

    $modul_id = $_POST['modul_id'];
    $ad = trim($_POST['ad']);
    $soyad = trim($_POST['soyad']);
    $email = trim($_POST['email']);
    $telefon = trim($_POST['telefon']);
    $bagis_tipi = $_POST['bagis_tipi'] ?? 'tek';
    $para_birimi = $_POST['para_birimi'] ?? (isset($_SESSION['para_birimi']) ? $_SESSION['para_birimi'] : 'TRY');
    $odeme_yontemi = $_POST['odeme_yontemi'] ?? ($_GET['odeme_yontemi'] ?? $aktif_odeme);
    $ulke = $_POST['ulke'] ?? (!empty($kayit) && isset($kayit['ulke']) ? $kayit['ulke'] : 'TR');
    $not_bilgisi = $_POST['not_bilgisi'] ?? '';
    $anonim_bagis = isset($_POST['anonim_bagis']) ? 1 : 0;
    $haber_bulteni = isset($_POST['haber_bulteni']) ? 1 : 0;
    $hediye_bagis = isset($_POST['hediye_bagis']) ? 1 : 0;
    $hediye_adi = $_POST['hediye_adi'] ?? '';
    $hediye_email = $_POST['hediye_email'] ?? '';
    $hediye_telefon = $_POST['hediye_telefon'] ?? '';
    $hediye_turu = !empty($_POST['hediye_turu']) ? intval($_POST['hediye_turu']) : null;
    $tutar = floatval($_POST['tutar'] ?? $_POST['ozel_tutar'] ?? 0);
    
    if($tutar <= 0){
        header("Location: bagis.php?hata=tutar");
        exit;
    }

    $Ksorgu = $db->prepare("SELECT * FROM bagis_moduller WHERE id = ?");
    $Ksorgu->execute([$modul_id]);
    $Kampanya = $Ksorgu->fetch(PDO::FETCH_ASSOC);

    if(!$Kampanya){
        header("Location: bagis.php?hata=kampanya");
        exit;
    }

    $siparis_no = "BAGIS".date("YmdHis").rand(1000,9999);
    $kategori_id = 0;
    $bagis_id_for_cat = $_POST['bagis_id'] ?? $_POST['bagis_id_param'] ?? null;
    
    if(!empty($bagis_id_for_cat) && $bagis_id_for_cat > 0) {
        $st_cat = $db->prepare("SELECT kategori FROM bagislar WHERE id = ?");
        $st_cat->execute([$bagis_id_for_cat]);
        $kategori_id = $st_cat->fetchColumn() ?: 0;
    }
    
    if($kategori_id == 0 && !empty($kayit) && !empty($kayit['kategori_id'])) {
        $kategori_id = intval($kayit['kategori_id']);
    }
    
    if($kategori_id == 0 && !empty($kayit) && !empty($kayit['sepet'])) {
        $se_arr = json_decode($kayit['sepet'], true);
        if(!empty($se_arr) && is_array($se_arr) && !empty($se_arr[0]['id'])) {
            $st_cat = $db->prepare("SELECT kategori FROM bagislar WHERE id = ?");
            $st_cat->execute([$se_arr[0]['id']]);
            $kategori_id = $st_cat->fetchColumn() ?: 0;
        }
    }
    
    if($kategori_id == 0 && !empty($_POST['sepet'])) {
        $sepet_data = is_string($_POST['sepet']) ? json_decode($_POST['sepet'], true) : $_POST['sepet'];
        if(!empty($sepet_data) && is_array($sepet_data) && !empty($sepet_data[0]['id'])) {
            $st_cat = $db->prepare("SELECT kategori FROM bagislar WHERE id = ?");
            $st_cat->execute([$sepet_data[0]['id']]);
            $kategori_id = $st_cat->fetchColumn() ?: 0;
        }
    }

    $sponsortip = $_POST['sponsortip'] ?? 'bireysel';
    $sorumlu_ad_soyad = $_POST['sorumlu_ad_soyad'] ?? ($ad . ' ' . $soyad);
    $sorumlu_telefon = $_POST['sorumlu_telefon'] ?? $telefon;
    $sorumlu_email = $_POST['sorumlu_email'] ?? $email;
    $grup_adi = $_POST['grup_adi'] ?? null;
    $kurum_unvani = $_POST['kurum_unvani'] ?? null;
    $odeme_tipi = $_POST['odeme_tipi'] ?? 'tek_seferlik';
    $sponsorluk_suresi_ay = $_POST['sponsorluk_suresi_ay'] ?? null;
    $odeme_gunu = $_POST['odeme_gunu'] ?? 1;

    if($hizli_bagis_id){
        $mevcutSepet = $kayit['sepet'] ?? null;
        if(!empty($mevcutSepet)) {
            try {
                $sepet_array = json_decode($mevcutSepet, true);
                if(!empty($sepet_array) && is_array($sepet_array) && !empty($sepet_array[0])) {
                    $sepet_array[0]['tutar'] = floatval($tutar);
                    $mevcutSepet = json_encode($sepet_array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } catch(Exception $e) {}
        }
        
        $sorgu = $db->prepare("UPDATE bagis_odeme SET
            spno = ?, adi = ?, ad = ?, soyad = ?, email = ?, tel = ?, telefon = ?, 
            aciklama = ?, not_bilgisi = ?, anonim_bagis = ?, haber_bulteni = ?, 
            hediye_bagis = ?, hediye_adi = ?, hediye_email = ?, hediye_telefon = ?, 
            hediye_turu = ?, bagis_tipi = ?, fiyat = ?, tutar = ?, kampanya_adi = ?, 
            para_birimi = ?, ip = ?, odeme_yontemi = ?, tarih = NOW(), durum = 'beklemede', 
            sepet = ?, ulke = ?, kategori_id = ?, sponsortip = ?, sorumlu_ad_soyad = ?, 
            sorumlu_telefon = ?, sorumlu_email = ?, grup_adi = ?, kurum_unvani = ?, 
            odeme_tipi = ?, sponsorluk_suresi_ay = ?, odeme_gunu = ?
            WHERE id = ?");
        $sorgu->execute([
            $siparis_no, $ad.' '.$soyad, $ad, $soyad, $email, $telefon, $telefon,
            $not_bilgisi, $not_bilgisi, $anonim_bagis, $haber_bulteni,
            $hediye_bagis, $hediye_adi, $hediye_email, $hediye_telefon, $hediye_turu,
            $bagis_tipi, $tutar, $tutar, $Kampanya['adi'], $para_birimi,
            $_SERVER['REMOTE_ADDR'], $odeme_yontemi, $mevcutSepet, $ulke, $kategori_id,
            $sponsortip, $sorumlu_ad_soyad, $sorumlu_telefon, $sorumlu_email, $grup_adi, 
            $kurum_unvani, $odeme_tipi, $sponsorluk_suresi_ay, $odeme_gunu, $hizli_bagis_id
        ]);
        $bagis_id = $hizli_bagis_id;
    } else {
        $sorgu = $db->prepare("INSERT INTO bagis_odeme SET
            modul_id = ?, spno = ?, adi = ?, ad = ?, soyad = ?, email = ?, tel = ?, telefon = ?, 
            aciklama = ?, not_bilgisi = ?, anonim_bagis = ?, haber_bulteni = ?, 
            hediye_bagis = ?, hediye_adi = ?, hediye_email = ?, hediye_telefon = ?, 
            hediye_turu = ?, bagis_tipi = ?, fiyat = ?, tutar = ?, kampanya_adi = ?, 
            para_birimi = ?, ip = ?, odeme_yontemi = ?, tarih = NOW(), durum = 'beklemede', 
            ulke = ?, kategori_id = ?, sponsortip = ?, sorumlu_ad_soyad = ?, 
            sorumlu_telefon = ?, sorumlu_email = ?, grup_adi = ?, kurum_unvani = ?, 
            odeme_tipi = ?, sponsorluk_suresi_ay = ?, odeme_gunu = ?");
        $sorgu->execute([
            $modul_id, $siparis_no, $ad.' '.$soyad, $ad, $soyad, $email, $telefon, $telefon,
            $not_bilgisi, $not_bilgisi, $anonim_bagis, $haber_bulteni,
            $hediye_bagis, $hediye_adi, $hediye_email, $hediye_telefon, $hediye_turu,
            $bagis_tipi, $tutar, $tutar, $Kampanya['adi'], $para_birimi,
            $_SERVER['REMOTE_ADDR'], $odeme_yontemi, $ulke, $kategori_id,
            $sponsortip, $sorumlu_ad_soyad, $sorumlu_telefon, $sorumlu_email, $grup_adi, 
            $kurum_unvani, $odeme_tipi, $sponsorluk_suresi_ay, $odeme_gunu
        ]);
        $bagis_id = $db->lastInsertId();
    }

    try {
        require_once(__DIR__ . '/_class/YetimSponsorlukYoneticisi.php');
        $sponsorlukYoneticisi = new YetimSponsorlukYoneticisi($db);
        $sponsorlukYoneticisi->revizeYetimSponsorlugu($bagis_id);
    } catch (Exception $e) {
        error_log("Yetim sponsorluk kontrolü hatası: " . $e->getMessage());
    }

    $odeme_yontemi = 'vakifbank';

    if($odeme_yontemi == 'vakifbank') {
        require_once('vakifBank.php');
        require_once('_class/vakifbank_logger.php');
        $vakifLogger = new VakifbankLogger($db);
        $vakifbank_ayar = $db->query("SELECT * FROM vakifbank WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
        
        if(!$vakifbank_ayar || empty($vakifbank_ayar['host_merchant_id'])) {
            header("Location: bagis.php?hata=vakifbank_ayar_eksik");
            exit;
        }
        
        $protocol = (!empty($_SERVER['HTTPS']) ? "https://" : "http://");
        $host = $_SERVER['HTTP_HOST'] ?? '';
        
        // URL standardizasyonu: www/non-www tutarlılığı (non-www standardı)
        $host = preg_replace('/^www\./i', '', $host);
        $base_url = $protocol . $host;
        
        $vakif_tutar = $tutar;
        $is_converted = false;
        
        if ($para_birimi != 'TRY' && isset($exchange_rates[$para_birimi])) {
            $vakif_tutar = round($tutar * $exchange_rates[$para_birimi], 2);
            $is_converted = true;
        }

        // ISO 4217 Currency Code Mapping (4 haneli format)
        $currency_codes = [
            'TRY' => '0949',
            'USD' => '0840',
            'EUR' => '0978'
        ];
        
        // Para birimi kodunu belirle (4 haneli format zorunlu)
        $amountCode = isset($currency_codes[$para_birimi]) ? $currency_codes[$para_birimi] : '0949';
        $taksit_sayisi = ($vakifbank_ayar['taksit'] > 1) ? $vakifbank_ayar['taksit'] : '0';
        $userName = (!empty($vakifbank_ayar['user_name'])) ? $vakifbank_ayar['user_name'] : $vakifbank_ayar['host_terminal_id'];
        
        // Ödemenin yapıldığı sayfa URL'si (threeDSRequestorURL için) - callback URL değil
        $requestorURL = $base_url . '/paytr_tekil_bagis.php';
        
        // Gateway URL: DB'den veya varsayılan (banka dokümanından teyit edin)
        $gatewayUrl = (isset($vakifbank_ayar['gateway_url']) && trim($vakifbank_ayar['gateway_url']) !== '')
            ? trim($vakifbank_ayar['gateway_url']) : vakifBank::GATEWAY_URL;
        
        $setting = [
            'init' => ($vakifbank_ayar['test_modu'] == '1') ? 'test' : 'prod',
            'HostMerchantId' => $vakifbank_ayar['host_merchant_id'],
            'MerchantPassword' => $vakifbank_ayar['merchant_password'],
            'HostTerminalId' => $vakifbank_ayar['host_terminal_id'],
            'CustomerId' => $vakifbank_ayar['host_terminal_id'],
            'UserName' => $userName,
            'GatewayURL' => $gatewayUrl,
            'SuccessURL' => $base_url.'/vakifbank_callback.php',
            'FailureURL' => $base_url.'/vakifbank_callback.php',
            'RequestorURL' => $requestorURL,
            'InstalmentCount' => $taksit_sayisi,
            'AmountCode' => $amountCode
        ];
        
        $vakifBank = new vakifBank($setting);
        $orderID = str_replace('BAGIS', '', $siparis_no);
        $bankEndpoint = $gatewayUrl;
        $probeResult = $vakifLogger->probeEndpoint($orderID, $bankEndpoint);
        // Not: 404 probe'da normal olabilir - gateway sadece POST kabul ediyor. Gerçek sonuç form POST'ta belli olur.

        $amount = number_format($vakif_tutar, 2, '.', '');
        
        $vakifLogger->logPaymentRequest($orderID, $amount, ['gateway_url'=>$gatewayUrl,'type'=>'card_form_shown'], $setting, $probeResult, ['entrypoint' => 'paytr_tekil_bagis']);
        
        // Kart formu göster - banka XML bekliyor, form POST kabul etmiyor
        $displayAmount = number_format($vakif_tutar, 2, ',', '.');
        $kampanya_adi = $Kampanya['adi'] ?? 'Bağış';
        ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Güvenli Ödeme - Kart Bilgileri</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    	body{
	    top:0px !important;
	}
		.VIpgJd-ZVi9od-ORHb {
			margin: 0;
			background-color: #E4EFFB;
			overflow: hidden;
			display: none;
		}
		.VIpgJd-ZVi9od-ORHb-OEVmcd {
			display: none;
			left: 0;
			top: 0;
			height: 39px;
			width: 100%;
			z-index: 10000001;
			position: fixed;
			border: none;
			border-bottom: 1px solid #6B90DA;
			margin: 0;
			box-shadow: 0 0 8px 1px #999;
		}
		.VIpgJd-yAWNEb-L7lbkb>div{display:none !important;}
		.VIpgJd-yAWNEb-L7lbkb {

    display: none !important;
}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Plus Jakarta Sans',-apple-system,sans-serif;background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#0f172a 100%);min-height:100vh;padding:20px;color:#1e293b}
        .page-wrap{max-width:480px;margin:0 auto}
        .header{text-align:center;padding:24px 0;animation:fadeInDown .6s ease}
        .header h1{color:#fff;font-size:1.5rem;font-weight:800;margin-bottom:8px}
        .header p{color:#94a3b8;font-size:.9rem}
        .amount-card{background:linear-gradient(135deg,#0ea5e9,#06b6d4);border-radius:20px;padding:24px;margin-bottom:24px;box-shadow:0 20px 40px rgba(6,182,212,.3);animation:fadeInUp .6s ease .1s both;position:relative;overflow:hidden}
        .amount-card::before{content:'';position:absolute;top:-50%;right:-50%;width:100%;height:100%;background:radial-gradient(circle,rgba(255,255,255,.15) 0%,transparent 70%);pointer-events:none}
        .amount-card .label{color:rgba(255,255,255,.9);font-size:.85rem;font-weight:600;margin-bottom:4px}
        .amount-card .value{color:#fff;font-size:2rem;font-weight:800;letter-spacing:-.02em}
        .amount-card .campaign{color:rgba(255,255,255,.85);font-size:.9rem;margin-top:8px}
        .form-card{background:#fff;border-radius:24px;padding:28px;box-shadow:0 25px 50px -12px rgba(0,0,0,.4);animation:fadeInUp .6s ease .2s both;margin-bottom:20px}
        .form-card h2{font-size:1.2rem;font-weight:700;color:#0f172a;margin-bottom:20px;display:flex;align-items:center;gap:10px}
        .form-card h2::before{content:'';width:4px;height:24px;background:linear-gradient(180deg,#0ea5e9,#06b6d4);border-radius:2px}
        .input-group{margin-bottom:20px}
        .input-group label{display:block;font-size:.8rem;font-weight:600;color:#64748b;margin-bottom:8px;text-transform:uppercase;letter-spacing:.05em}
        .input-group input{width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:1rem;font-family:inherit;transition:all .25s ease}
        .input-group input:focus{outline:none;border-color:#0ea5e9;box-shadow:0 0 0 4px rgba(14,165,233,.15)}
        .input-group input::placeholder{color:#94a3b8}
        .input-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
        .hint{font-size:.75rem;color:#94a3b8;margin-top:6px}
        .submit-btn{width:100%;padding:16px;background:linear-gradient(135deg,#0ea5e9,#06b6d4);color:#fff;border:none;border-radius:14px;font-size:1rem;font-weight:700;cursor:pointer;margin-top:8px;transition:all .3s ease;box-shadow:0 4px 14px rgba(6,182,212,.4)}
        .submit-btn:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(6,182,212,.5)}
        .submit-btn:active{transform:translateY(0)}
        .info-section{background:rgba(255,255,255,.06);border-radius:16px;padding:20px;animation:fadeInUp .6s ease .3s both}
        .info-section h3{color:#fff;font-size:.9rem;font-weight:700;margin-bottom:12px;display:flex;align-items:center;gap:8px}
        .info-item{display:flex;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.08)}
        .info-item:last-child{border-bottom:none}
        .info-item .icon{width:36px;height:36px;background:rgba(14,165,233,.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.1rem}
        .info-item .text{color:#94a3b8;font-size:.85rem;line-height:1.5}
        .info-item .text strong{color:#e2e8f8}
        .badges{display:flex;justify-content:center;gap:16px;margin-top:24px;flex-wrap:wrap;animation:fadeIn .6s ease .4s both}
        .badge{display:flex;align-items:center;gap:8px;color:#64748b;font-size:.8rem;font-weight:500}
        .badge svg{width:20px;height:20px;opacity:.8}
        @keyframes fadeInUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        @keyframes fadeInDown{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        @media(max-width:480px){.input-row{grid-template-columns:1fr}.form-card{padding:20px}.amount-card .value{font-size:1.6rem}}
        .lang-switcher{display:flex;justify-content:center;gap:8px;margin-bottom:20px;flex-wrap:wrap}
        .lang-btn{padding:8px 16px;border-radius:10px;border:2px solid rgba(255,255,255,.2);background:rgba(255,255,255,.05);color:#94a3b8;font-size:.85rem;font-weight:600;cursor:pointer;transition:all .25s;text-decoration:none}
        .lang-btn:hover{background:rgba(255,255,255,.1);color:#fff;border-color:rgba(255,255,255,.3)}
        .lang-btn.active{background:linear-gradient(135deg,#0ea5e9,#06b6d4);color:#fff;border-color:transparent}
        .lang-btn span{margin-left:6px}
        /* RTL - Arapça desteği */
        body[dir="rtl"]{direction:rtl;text-align:right;font-family:'Plus Jakarta Sans',-apple-system,sans-serif}
        body[dir="rtl"] *{font-size:inherit}
        body[dir="rtl"] .header,body[dir="rtl"] .form-card,body[dir="rtl"] .info-section,body[dir="rtl"] .amount-card{text-align:right}
        body[dir="rtl"] .form-card h2{flex-direction:row-reverse}
        body[dir="rtl"] .form-card h2::before{margin-right:0;margin-left:10px}
        body[dir="rtl"] .input-group input{text-align:right}
        body[dir="rtl"] .info-item{flex-direction:row-reverse;text-align:right}
        body[dir="rtl"] .lang-switcher{direction:rtl}
        body[dir="rtl"] .lang-btn span{margin-left:0;margin-right:6px}
        body[dir="rtl"] .badges{direction:rtl}
        body[dir="rtl"] .badge{flex-direction:row-reverse}
        body[dir="rtl"] h1,body[dir="rtl"] h2,body[dir="rtl"] h3,body[dir="rtl"] .amount-card .value{font-size:1.15em}
        .toast-container{position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:10px;pointer-events:none}
        .toast{background:linear-gradient(135deg,#1e293b,#0f172a);color:#e2e8f0;padding:14px 20px;border-radius:14px;box-shadow:0 10px 40px rgba(0,0,0,.4),0 0 0 1px rgba(255,255,255,.08);font-size:.9rem;line-height:1.5;max-width:340px;animation:toastIn .4s cubic-bezier(0.34,1.56,0.64,1);display:flex;align-items:flex-start;gap:12px}
        .toast-icon{width:36px;height:36px;background:rgba(14,165,233,.25);border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.1rem}
        .toast.hiding{animation:toastOut .3s ease forwards}
        @keyframes toastIn{from{opacity:0;transform:translateX(100px) scale(0.9)}to{opacity:1;transform:translateX(0) scale(1)}}
        @keyframes toastOut{from{opacity:1;transform:translateX(0)}to{opacity:0;transform:translateX(100px)}}
        body[dir="rtl"] .toast-container{right:auto;left:20px}
        body[dir="rtl"] .toast{flex-direction:row-reverse}
        @media(max-width:480px){.toast-container{left:12px;right:12px;top:12px}.toast{max-width:none}}
    </style>
    <script>
        var LANG_MSG={'tr':'Dili değiştirdikten sonraki ödeme tutarının yenilenmesi için sepete ürünü kendi dilinizde yeniden eklemelisiniz.','en':'To refresh the payment amount, please re-add the product to your cart in your language.','ar':'لتحديث مبلغ الدفع، يرجى إعادة إضافة المنتج إلى سلة التسوق بلغتك.'};
        function showLangToast(lang){var m=LANG_MSG[lang]||LANG_MSG.tr;var c=document.getElementById('toastContainer');if(!c){c=document.createElement('div');c.id='toastContainer';c.className='toast-container';document.body.appendChild(c);}var t=document.createElement('div');t.className='toast';t.innerHTML='<span class="toast-icon">ℹ️</span><span>'+m+'</span>';c.appendChild(t);setTimeout(function(){t.classList.add('hiding');setTimeout(function(){t.remove();},300);},2200);}
        function payPageGTranslate(lang){
            var domain=window.location.hostname;if(domain.indexOf('www.')===0)domain=domain.substring(4);
            document.cookie="googtrans=/tr/"+lang+"; domain=."+domain+"; path=/; expires=Thu, 01 Jan 2030 00:00:00 UTC";
            document.cookie="googtrans=/tr/"+lang+"; path=/; expires=Thu, 01 Jan 2030 00:00:00 UTC";
            document.documentElement.dir=document.body.dir=(lang==='ar'?'rtl':'ltr');
            var s=document.getElementsByTagName('select');for(var i=0;i<s.length;i++){if(s[i].className==='goog-te-combo'){s[i].value=lang;s[i].dispatchEvent(new Event('change'));return;}}
            setTimeout(function(){payPageGTranslate(lang);},300);
        }
        function googleTranslateElementInit(){new google.translate.TranslateElement({pageLanguage:'tr',includedLanguages:'tr,en,ar',autoDisplay:false},'google_translate_element');}
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async></script>
    <div id="google_translate_element" style="display:none;visibility:hidden"></div>
</head>
<body>
    <div class="page-wrap">
        <div class="lang-switcher">
            <a href="#" class="lang-btn" data-lang="tr" onclick="payPageGTranslate('tr');showLangToast('tr');document.querySelectorAll('.lang-btn').forEach(b=>b.classList.remove('active'));this.classList.add('active');return false"><span>🇹🇷</span><span>Türkçe</span></a>
            <a href="#" class="lang-btn" data-lang="en" onclick="payPageGTranslate('en');showLangToast('en');document.querySelectorAll('.lang-btn').forEach(b=>b.classList.remove('active'));this.classList.add('active');return false"><span>🇬🇧</span><span>English</span></a>
            <a href="#" class="lang-btn" data-lang="ar" onclick="payPageGTranslate('ar');showLangToast('ar');document.querySelectorAll('.lang-btn').forEach(b=>b.classList.remove('active'));this.classList.add('active');return false"><span>🇸🇦</span><span>العربية</span></a>
        </div>
        <div class="header">
            <h1>Güvenli Ödeme</h1>
            <p>Kart bilgilerinizi girin, 3D Secure ile korunan ödeme sayfasına yönlendirileceksiniz.</p>
        </div>

        <div class="amount-card">
            <div class="label">Ödeme Tutarı</div>
            <div class="value"><?php echo $displayAmount; ?> ₺</div>
            <div class="campaign"><?php echo htmlspecialchars($kampanya_adi); ?></div>
        </div>

        <div class="form-card">
            <h2>Kart Bilgileri</h2>
            <form method="post" action="" id="paymentForm">
                <input type="hidden" name="vkb_siparis_no" value="<?php echo htmlspecialchars($siparis_no); ?>">
                <input type="hidden" name="modul_id" value="<?php echo (int)$modul_id; ?>">
                <input type="hidden" name="ad" value="<?php echo htmlspecialchars($ad); ?>">
                <input type="hidden" name="soyad" value="<?php echo htmlspecialchars($soyad); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="telefon" value="<?php echo htmlspecialchars($telefon); ?>">
                <input type="hidden" name="tutar" value="<?php echo $tutar; ?>">
                <input type="hidden" name="para_birimi" value="<?php echo htmlspecialchars($para_birimi); ?>">

                <div class="input-group">
                    <label>Kart Numarası</label>
                    <input type="text" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19" required autocomplete="cc-number" inputmode="numeric" pattern="[0-9\s]*">
                    <span class="hint">Kartınızın ön yüzündeki 16 haneli numara</span>
                </div>

                <div class="input-row">
                    <div class="input-group">
                        <label>Son Kullanma (AA/YY)</label>
                        <input type="text" name="card_expiry" placeholder="MM/YY" maxlength="5" required autocomplete="cc-exp" inputmode="numeric">
                        <span class="hint">Örn: 12/28</span>
                    </div>
                    <div class="input-group">
                        <label>CVV</label>
                        <input type="text" name="card_cvv" placeholder="•••" maxlength="4" required autocomplete="cc-csc" inputmode="numeric">
                        <span class="hint">Kartın arkasındaki 3 hane</span>
                    </div>
                </div>

                <div class="input-group">
                    <label>Kart Üzerindeki İsim</label>
                    <input type="text" name="card_holder" placeholder="AD SOYAD" value="<?php echo htmlspecialchars(trim($ad.' '.$soyad)); ?>" required autocomplete="cc-name">
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">Ödemeyi Tamamla</button>
            </form>
        </div>

        <div class="info-section">
            <h3>🔒 Güvenlik Bilgisi</h3>
            <div class="info-item">
                <span class="icon">🛡️</span>
                <span class="text"><strong>3D Secure:</strong> Ödeme sırasında bankanız tarafından gönderilen SMS şifresi ile doğrulama yapılacaktır.</span>
            </div>
            <div class="info-item">
                <span class="icon">🔐</span>
                <span class="text"><strong>SSL Şifreleme:</strong> Kart bilgileriniz 256-bit SSL ile şifrelenerek iletilmektedir.</span>
            </div>
            <div class="info-item">
                <span class="icon">💳</span>
                <span class="text"><strong>Visa, Mastercard, Troy:</strong> Tüm kartlarla güvenli ödeme yapabilirsiniz.</span>
            </div>
        </div>

        <div class="badges">
            <span class="badge">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                SSL Güvenli
            </span>
            <span class="badge">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                3D Secure
            </span>
            <span class="badge">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Vakıf Katılım
            </span>
        </div>
    </div>
    <script>
        document.getElementById('paymentForm').addEventListener('submit',function(){
            var btn=document.getElementById('submitBtn');
            btn.disabled=true;
            btn.textContent='İşleniyor...';
            btn.style.opacity='0.8';
        });
        document.querySelector('[name="card_number"]').addEventListener('input',function(e){
            var v=e.target.value.replace(/\D/g,'').substr(0,16);
            e.target.value=v.replace(/(\d{4})(?=\d)/g,'$1 ').trim();
        });
        document.querySelector('[name="card_expiry"]').addEventListener('input',function(e){
            var v=e.target.value.replace(/\D/g,'');
            if(v.length>=2)e.target.value=v.substr(0,2)+'/'+v.substr(2,2);
            else e.target.value=v;
        });
        document.querySelector('[name="card_cvv"]').addEventListener('input',function(e){
            e.target.value=e.target.value.replace(/\D/g,'').substr(0,4);
        });
        (function(){
            var c=document.cookie.match(/(^|;)\s*googtrans=([^;]+)/);
            var lang='tr';
            if(c){var p=c[2].split('/');if(p.length>0)lang=p[p.length-1];}
            document.documentElement.dir=document.body.dir=(lang==='ar'?'rtl':'ltr');
            if(lang==='en'||lang==='ar'){
                setTimeout(function(){payPageGTranslate(lang);},800);
                document.querySelectorAll('.lang-btn').forEach(function(b){b.classList.toggle('active',b.getAttribute('data-lang')===lang);});
            }else{document.querySelector('.lang-btn[data-lang="tr"]').classList.add('active');}
        })();
    </script>
</body>
</html>
        <?php
            exit;
    }
}
header("Location: bagis.php");
exit;
?>