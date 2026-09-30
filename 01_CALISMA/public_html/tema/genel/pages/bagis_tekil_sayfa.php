<?php
// Tekil Sayfa Bağış Formu - 4 Aşamalı - Tailwind CSS
if(!isset($_GET['id']) || empty($_GET['id']))
{
    echo $dil['txt502'];
    exit;
}

// Döviz kurları - Dinamik API entegrasyonu
require_once('_class/exchange_rates.php');
$kurlar = ExchangeRates::getRates(['USD', 'EUR']);

// Dil ID'sine göre otomatik para birimi ayarlama
$dilParaBirimiMap = [
    1 => 'TRY',  // Türkçe -> TRY
    2 => 'USD',  // İngilizce -> USD
    3 => 'USD',  // Arapça -> USD
];

// Eğer dil değiştiyse ve para birimi ayarlanmamışsa, dil'e göre otomatik ayarla
$mevcutDilId = isset($_SESSION['k_dil']) ? (int)$_SESSION['k_dil'] : 1;
if(isset($dilParaBirimiMap[$mevcutDilId]) && !isset($_SESSION['para_birimi'])) {
    $_SESSION['para_birimi'] = $dilParaBirimiMap[$mevcutDilId];
}

// Varsayılan para birimi - önce session'dan, sonra dil'e göre, son olarak TRY
$seciliParaBirimi = isset($_SESSION['para_birimi']) ? $_SESSION['para_birimi'] : (isset($dilParaBirimiMap[$mevcutDilId]) ? $dilParaBirimiMap[$mevcutDilId] : 'TRY');
$paraBirimiSembolleri = [
    'TRY' => '₺',
    'USD' => '$',
    'EUR' => '€'
];

$Sorgu = $db->prepare("SELECT * FROM bagis_moduller WHERE seo = ? AND durum = 1");
$Sorgu->execute(array($_GET['id']));
if($Sorgu->rowCount() == 0)
{
    echo $dil['txt503'];
    exit;
}
$Kampanya = $Sorgu->fetch(PDO::FETCH_ASSOC);

// Hediye türlerini veritabanından çek (eğer tablo yoksa varsayılan değerler kullan)
$hediyeTurleri = [];
try {
    $hediyeTurleriSorgu = $db->query("SELECT * FROM hediye_turleri WHERE durum = 1 ORDER BY sira ASC");
    $hediyeTurleri = $hediyeTurleriSorgu->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    // Tablo yoksa varsayılan değerler
    $hediyeTurleri = [
        ['id' => 1, 'adi' => $dil['txt598'] ?? 'Öğretmene Hediye Bağış', 'sira' => 1],
        ['id' => 2, 'adi' => $dil['txt599'] ?? 'Arkadaşa Hediye Bağış', 'sira' => 2]
    ];
}

// Tutarları seçili para birimine çevir
$hedefTutar = $Kampanya['hedef_tutar'];
$toplananTutar = $Kampanya['toplanan_tutar'];
if ($seciliParaBirimi != 'TRY') {
    $hedefTutar = ExchangeRates::convert($hedefTutar, 'TRY', $seciliParaBirimi, $kurlar);
    $toplananTutar = ExchangeRates::convert($toplananTutar, 'TRY', $seciliParaBirimi, $kurlar);
}

// İlerleme yüzdesi hesapla
$ilerleme = $hedefTutar > 0 ? ($toplananTutar / $hedefTutar) * 100 : 0;
$ilerleme = min($ilerleme, 100);

// Kalan gün hesapla
$kalan_gun = 0;
if($Kampanya['bitis_tarihi'] && $Kampanya['bitis_tarihi'] != '0000-00-00')
{
    $bitis = new DateTime($Kampanya['bitis_tarihi']);
    $simdi = new DateTime();
    if($bitis > $simdi)
    {
        $kalan_gun = $simdi->diff($bitis)->days;
    }
}

// İstatistik Gösterim Durumu
$istatistikAktif = isset($Kampanya['istatistik_durum']) ? $Kampanya['istatistik_durum'] : 1;
?>



    <!-- Font Awesome (Sadece ödeme logoları için) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');
        
        :root {
            --primary-color: #2c5f5f;
            --gray-50: #fafafa;
            --gray-100: #f5f5f5;
            --gray-200: #e0e0e0;
            --gray-400: #9e9e9e;
            --gray-600: #616161;
            --gray-700: #424242;
            --gray-900: #212121;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
        }
        
        .step-progress {
            counter-reset: step;
            display: flex;
            justify-content: space-between;
            padding: 0;
            margin: 0;
            list-style: none;
        }
        
        .step-progress li {
            position: relative;
            flex: 1;
            text-align: center;
        }
        
        .step-progress li:before {
            content: counter(step);
            counter-increment: step;
            width: 32px;
            height: 32px;
            line-height: 32px;
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-400);
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 50%;
            margin: 0 auto 8px;
            position: relative;
            z-index: 2;
        }
        
        .step-progress li:after {
            content: '';
            width: 100%;
            height: 1px;
            background: var(--gray-200);
            position: absolute;
            left: 50%;
            top: 16px;
            z-index: 1;
        }
        
        .step-progress li:last-child:after {
            display: none;
        }
        
        .step-progress li.active:before {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .step-progress li.completed:before {
            content: "✓";
            background: var(--gray-200);
            border-color: var(--gray-200);
            color: var(--gray-600);
            font-size: 12px;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        input[type="text"], 
        input[type="email"], 
        input[type="tel"],
        input[type="number"], 
        input[type="date"], 
        textarea,
        select {
            border: 1px solid var(--gray-200);
            border-radius: 6px;
            padding: 12px 16px;
            width: 100%;
            outline: none;
            transition: border-color 0.2s;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            background: white;
        }

        input:focus, 
        textarea:focus,
        select:focus {
            border-color: var(--primary-color);
        }
        
        input[type="radio"], 
        input[type="checkbox"] {
            accent-color: var(--primary-color);
        }
        
        label {
            font-weight: 500;
            color: var(--gray-700);
            font-size: 14px;
        }
        
        /* Responsive Layout */
        @media (min-width: 992px) {
            section[style*="padding: 2rem"] > div > div[style*="display: grid"] {
                grid-template-columns: <?php echo $istatistikAktif ? '280px 1fr 300px' : '1fr 300px'; ?> !important;
                gap: 2rem !important;
            }
            aside[style*="order: 2"] {
                order: 1 !important;
            }
            div[style*="order: 1"] {
                order: 2 !important;
            }
            aside[style*="order: 3"] {
                order: 3 !important;
            }
        }
        
        @media (max-width: 991px) {
            aside[style*="order: 2"] > div,
            aside[style*="order: 3"] > div {
                position: relative !important;
                top: 0 !important;
            }
        }
                
                .sayfaalaniicin{
background: #ffffff;
    padding: 2rem 0;
    min-height: auto;
    margin-top: 118px;
        }
        @media (max-width: 768px) {

        .sayfaalaniicin{
                background: #ffffff;
    padding: 2rem 0;
    min-height: auto;
    margin-top: auto !important;
        }
        }
    </style>




<!-- BAĞIŞ FORMU -->
<section class="sayfaalaniicin" style="background: #ffffff;padding: 2rem 0;min-height: auto;margin-top: 118px;">
    <div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
        <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
            
               <!-- Sol Sidebar - İstatistikler -->
            <?php if($istatistikAktif): ?>
            <aside style="order: 2;">
                <div style="background: white; border-radius: 8px; border: 1px solid var(--gray-200); padding: 1.5rem; position: sticky; top: 2rem;">
                    <h3 style="font-size: 16px; font-weight: 600; color: var(--gray-900); margin: 0 0 1.5rem 0;"><?=@$dil['txt504'];?></h3>
                    
                    <!-- İstatistikler -->
                    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="padding-bottom: 1rem; border-bottom: 1px solid var(--gray-200);">
                            <div style="font-size: 12px; color: var(--gray-600); margin-bottom: 0.5rem;"><?=@$dil['txt505'];?></div>
                            <div style="font-size: 1.5rem; font-weight: 600; color: var(--gray-900);">
                                <?php echo number_format($hedefTutar, 0);?> <?php echo $paraBirimiSembolleri[$seciliParaBirimi];?>
                            </div>
                        </div>
                        
                        <div style="padding-bottom: 1rem; border-bottom: 1px solid var(--gray-200);">
                            <div style="font-size: 12px; color: var(--gray-600); margin-bottom: 0.5rem;"><?=@$dil['txt494'];?></div>
                            <div style="font-size: 1.5rem; font-weight: 600; color: var(--gray-900);">
                                <?php echo number_format($toplananTutar, 0);?> <?php echo $paraBirimiSembolleri[$seciliParaBirimi];?>
                            </div>
                        </div>
                        
                        <div style="padding-bottom: 1rem; border-bottom: 1px solid var(--gray-200);">
                            <div style="font-size: 12px; color: var(--gray-600); margin-bottom: 0.5rem;"><?=@$dil['txt495'];?></div>
                            <div style="font-size: 1.5rem; font-weight: 600; color: var(--gray-900);">
                                %<?php echo number_format($ilerleme, 0);?>
                            </div>
                        </div>
                        
                        <div>
                            <div style="font-size: 12px; color: var(--gray-600); margin-bottom: 0.5rem;"><?php echo $kalan_gun > 0 ? $dil['txt497'] . ' ' . $dil['txt496'] : $dil['txt436'];?></div>
                            <div style="font-size: 1.5rem; font-weight: 600; color: var(--gray-900);">
                                <?php echo $kalan_gun > 0 ? $kalan_gun : '∞';?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- İlerleme Çubuğu -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <span style="font-size: 12px; font-weight: 500; color: var(--gray-700);"><?=@$dil['txt506'];?></span>
                            <span style="font-size: 12px; font-weight: 600; color: var(--gray-900);">
                                %<?php echo number_format($ilerleme, 1);?>
                            </span>
                        </div>
                        <div style="width: 100%; background: var(--gray-200); border-radius: 4px; height: 8px; overflow: hidden;">
                            <div style="height: 100%; background: var(--primary-color); border-radius: 4px; transition: width 1s ease;" 
                                 data-width="<?php echo $ilerleme;?>%"></div>
                        </div>
                    </div>
                </div>
            </aside>
            <?php endif; ?>
            
            <!-- Orta Ana İçerik - Form -->
            <div style="order: 1;">
                <!-- Form Container -->
                <div style="background: white; border-radius: 8px; border: 1px solid var(--gray-200);">
            <!-- Başlık -->
            <div style="padding: 2rem; border-bottom: 1px solid var(--gray-200);">
                <h2 style="font-size: 1.5rem; font-weight: 600; color: var(--gray-900); margin: 0 0 0.5rem 0;"><?=@$dil['txt423'];?></h2>
                <p style="color: var(--gray-600); margin: 0; font-size: 14px;"><?php echo $Kampanya['adi'];?></p>
            </div>
            
            <!-- Step Indicator -->
            <div style="padding: 1.5rem 2rem; background: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
                <ul class="step-progress">
                    <li class="active" id="step-indicator-1">
                        <span style="font-size: 11px; font-weight: 500; color: var(--gray-600);"><?=@$dil['txt507'];?></span>
                    </li>
                    <li id="step-indicator-2">
                        <span style="font-size: 11px; font-weight: 500; color: var(--gray-600);"><?=@$dil['txt508'];?></span>
                    </li>
                    <li id="step-indicator-3">
                        <span style="font-size: 11px; font-weight: 500; color: var(--gray-600);"><?=@$dil['txt510'];?></span>
                    </li>
                </ul>
            </div>
            
            <!-- Form -->
            <form id="bagisForm" method="post" action="paytr_tekil_bagis.php" style="padding: 2rem;">
                <input type="hidden" name="modul_id" value="<?php echo $Kampanya['id'];?>">
                <input type="hidden" name="modul_adi" value="<?php echo $Kampanya['adi'];?>">
                <input type="hidden" name="kampanya_seo" value="<?php echo $Kampanya['seo'];?>">
                <input type="hidden" name="para_birimi" value="<?php echo $seciliParaBirimi;?>">
                
                <!-- AŞAMA 1: Bağış Tutarı -->
                <div class="step-content" id="step1" style="animation: fadeIn 0.3s;">
                    <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--gray-900); margin-bottom: 1.5rem;"><?=@$dil['txt511'];?></h3>
                    
                    <!-- Tek Seferlik / Sürekli -->
                    <div style="display: none; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 2rem;">
                        <label style="cursor: pointer;">
                            <input type="radio" name="bagis_tipi" value="tek" style="display: none;" checked>
                            <div class="bagis-tipi-card" style="border: 1px solid var(--gray-200); border-radius: 6px; padding: 1rem; text-align: center; transition: all 0.2s; background: white;">
                                <div style="font-weight: 500; font-size: 14px; color: var(--gray-700);"><?=@$dil['txt512'];?></div>
                            </div>
                        </label>
                        
                        <label style="cursor: pointer;">
                            <input type="radio" name="bagis_tipi" value="surekli" style="display: none;">
                            <div class="bagis-tipi-card" style="border: 1px solid var(--gray-200); border-radius: 6px; padding: 1rem; text-align: center; transition: all 0.2s; background: white;">
                                <div style="font-weight: 500; font-size: 14px; color: var(--gray-700);"><?=@$dil['txt513'];?></div>
                            </div>
                        </label>
                    </div>
                    
                    <style>
                    input[name="bagis_tipi"]:checked + .bagis-tipi-card {
                        border-color: var(--primary-color) !important;
                        background: var(--gray-50) !important;
                    }
                    .bagis-tipi-card:hover {
                        border-color: var(--primary-color);
                    }
                    </style>
                    
                    <!-- Hızlı Seçim -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: var(--gray-700); margin-bottom: 0.75rem;"><?=@$dil['txt514'];?></label>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;">
                            <label style="cursor: pointer;">
                                <input type="radio" name="tutar" value="<?=@$dil['txt603'];?>" class="tutar-radio" style="display: none;">
                                <div class="tutar-card" style="border: 1px solid var(--gray-200); border-radius: 6px; padding: 1rem; text-align: center; transition: all 0.2s; background: white;">
                                    <div style="font-size: 1.25rem; font-weight: 600; color: var(--gray-900);"><?=@$dil['txt603'];?> ₺</div>
                                </div>
                            </label>
                            
                            <label style="cursor: pointer;">
                                <input type="radio" name="tutar" value="<?=@$dil['txt600'];?>" class="tutar-radio" style="display: none;">
                                <div class="tutar-card" style="border: 1px solid var(--gray-200); border-radius: 6px; padding: 1rem; text-align: center; transition: all 0.2s; background: white;">
                                    <div style="font-size: 1.25rem; font-weight: 600; color: var(--gray-900);"><?=@$dil['txt600'];?> ₺</div>
                                </div>
                            </label>
                            
                            <label style="cursor: pointer;">
                                <input type="radio" name="tutar" value="<?=@$dil['txt601'];?>" class="tutar-radio" style="display: none;">
                                <div class="tutar-card" style="border: 1px solid var(--gray-200); border-radius: 6px; padding: 1rem; text-align: center; transition: all 0.2s; background: white;">
                                    <div style="font-size: 1.25rem; font-weight: 600; color: var(--gray-900);"><?=@$dil['txt601'];?> ₺</div>
                                </div>
                            </label>
                            
                            <label style="cursor: pointer;">
                                <input type="radio" name="tutar" value="<?=@$dil['txt602'];?>" class="tutar-radio" style="display: none;">
                                <div class="tutar-card" style="border: 1px solid var(--gray-200); border-radius: 6px; padding: 1rem; text-align: center; transition: all 0.2s; background: white;">
                                    <div style="font-size: 1.25rem; font-weight: 600; color: var(--gray-900);"><?=@$dil['txt602'];?> ₺</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <style>
                    input[name="tutar"]:checked + .tutar-card {
                        border-color: var(--primary-color) !important;
                        background: var(--gray-50) !important;
                    }
                    .tutar-card:hover {
                        border-color: var(--primary-color);
                    }
                    </style>
                    
                    <!-- Özel Tutar -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem;"><?=@$dil['txt515'];?></label>
                        <div style="position: relative;">
                            <input type="number" name="ozel_tutar" id="ozel_tutar" 
                                   placeholder="<?=@$dil['txt516'];?>" min="1" step="0.01">
                            <span style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: var(--gray-400); font-size: 14px;">₺</span>
                        </div>
                    </div>
                    
                    <!-- Hediye Bağışı -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 0.75rem; background: var(--gray-50); border-radius: 6px; border: 1px solid var(--gray-200);">
                            <input type="checkbox" id="hediye_bagis" name="hediye_bagis" style="width: 18px; height: 18px; margin-right: 10px;">
                            <span style="color: var(--gray-700); font-size: 14px; font-weight: 400;"><?=@$dil['txt517'];?></span>
                        </label>
                    </div>
                    
                    <!-- Hediye Bilgileri -->
                    <div id="hediye_bilgileri" style="display: none; margin-bottom: 1.5rem; padding: 1.25rem; background: var(--gray-50); border-radius: 6px; border: 1px solid var(--gray-200);">
                        <h4 style="font-size: 14px; font-weight: 600; color: var(--gray-900); margin-bottom: 1rem;"><?=@$dil['txt518'];?></h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem;"><?=@$dil['txt519'];?></label>
                                <input type="text" name="hediye_adi">
                            </div>
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem;"><?=@$dil['txt520'];?></label>
                                <input type="email" name="hediye_email">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem;"><?=@$dil['txt596'];?></label>
                                <input type="tel" name="hediye_telefon">
                            </div>
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem;"><?=@$dil['txt597'];?></label>
                                <select name="hediye_turu" id="hediye_turu">
                                    <option value=""><?=@$dil['txt597'];?> Seçiniz</option>
                                    <?php foreach($hediyeTurleri as $tur): ?>
                                    <option value="<?php echo $tur['id'];?>"><?php echo htmlspecialchars($tur['adi']);?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" onclick="nextStep(2)" 
                            style="width: 100%; background: var(--primary-color); color: white; font-weight: 500; padding: 14px; border-radius: 6px; border: none; cursor: pointer; transition: opacity 0.2s; font-size: 15px;"
                            onmouseover="this.style.opacity='0.9';"
                            onmouseout="this.style.opacity='1';">
                        <?=@$dil['txt444'];?>
                    </button>
                </div>
                
                <!-- AŞAMA 2: Kişisel Bilgiler ve Not -->
                <div class="step-content" id="step2" style="display: none;">
                    <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--gray-900); margin-bottom: 1.5rem;"><?=@$dil['txt440'];?></h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem;"><?=@$dil['txt521'];?></label>
                            <input type="text" name="ad" required>
                        </div>
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem;"><?=@$dil['txt522'];?></label>
                            <input type="text" name="soyad" required>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem;">E-posta *</label>
                            <input type="email" name="email" required>
                        </div>
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem;">Telefon *</label>
                            <input type="tel" name="telefon" required>
                        </div>
                    </div>

                    <!-- Intl-Tel-Input for international phone formatting -->
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/css/intlTelInput.css">
                    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/js/intlTelInput.min.js"></script>
                    
                    <style>
                        .iti { width: 100%; display: block; }
                        .iti__flag { background-image: url("https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/img/flags.png"); }
                        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
                          .iti__flag { background-image: url("https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/img/flags@2x.png"); }
                        }
                        /* Input stilini korumak için */
                        .iti__tel-input {
                            padding-top: 12px;
                            padding-bottom: 12px;
                        }
                    </style>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Tüm telefon inputlarını seç (hem normal hem hediye)
                        var phoneInputs = document.querySelectorAll('input[type="tel"]');
                        
                        phoneInputs.forEach(function(phoneInput) {
                            var iti = window.intlTelInput(phoneInput, {
                                initialCountry: 'tr',
                                preferredCountries: ['tr','us','gb','de','sa'],
                                separateDialCode: true,
                                strictMode: true,
                                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/js/utils.js",
                                i18n: {
                                    searchPlaceholder: "Ülke ara"
                                }
                            });
                            
                            var form = phoneInput.closest('form');
                            if (form) {
                                form.addEventListener('submit', function(e){
                                    try {
                                        var fullNumber = iti.getNumber();
                                        if (fullNumber) {
                                            phoneInput.value = fullNumber;
                                        }
                                    } catch(e) { /* noop */ }
                                });
                            }
                        });
                    });
                    </script>
                    
                    <!-- Bağışçı Notu -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 13px; font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem;"><?=@$dil['txt528'];?></label>
                        <textarea name="not_bilgisi" rows="4" placeholder="<?=@$dil['txt529'];?>"></textarea>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 0.75rem; background: var(--gray-50); border-radius: 6px; border: 1px solid var(--gray-200);">
                            <input type="checkbox" name="anonim_bagis" style="width: 18px; height: 18px; margin-right: 10px;">
                            <span style="color: var(--gray-700); font-size: 14px; font-weight: 400;"><?=@$dil['txt530'];?></span>
                        </label>
                        
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 0.75rem; background: var(--gray-50); border-radius: 6px; border: 1px solid var(--gray-200);">
                            <input type="checkbox" name="haber_bulteni" checked style="width: 18px; height: 18px; margin-right: 10px;">
                            <span style="color: var(--gray-700); font-size: 14px; font-weight: 400;"><?=@$dil['txt531'];?></span>
                        </label>
                        
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 0.75rem; background: var(--gray-50); border-radius: 6px; border: 1px solid var(--primary-color);">
                            <input type="checkbox" name="kvkk_onay" required style="width: 18px; height: 18px; margin-right: 10px;">
                            <span style="color: var(--gray-700); font-size: 14px; font-weight: 400;">
                                <a href="icerik/kisisel-verilerin-korunmasi-ve-gizlilik-politikasi" style="color: var(--primary-color); text-decoration: none;"><?=@$dil['txt532'];?></a> metnini okudum ve kabul ediyorum *
                            </span>
                        </label>
                    </div>
                    
                    <div style="display: flex; gap: 0.75rem;">
                        <button type="button" onclick="prevStep(1)" 
                                style="flex: 1; background: var(--gray-100); color: var(--gray-700); font-weight: 500; padding: 14px; border-radius: 6px; border: 1px solid var(--gray-200); cursor: pointer; transition: background 0.2s; font-size: 15px;"
                                onmouseover="this.style.background='var(--gray-200)';"
                                onmouseout="this.style.background='var(--gray-100)';">
                            <?=@$dil['txt20'];?>
                        </button>
                        <button type="button" onclick="nextStep(3)" 
                                style="flex: 1; background: var(--primary-color); color: white; font-weight: 500; padding: 14px; border-radius: 6px; border: none; cursor: pointer; transition: opacity 0.2s; font-size: 15px;"
                                onmouseover="this.style.opacity='0.9';"
                                onmouseout="this.style.opacity='1';">
                            <?=@$dil['txt444'];?>
                        </button>
                    </div>
                </div>
                
                <!-- AŞAMA 3: Ödeme -->
                <div class="step-content" id="step3" style="display: none;">
                    <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--gray-900); margin-bottom: 1.5rem;"><?=@$dil['txt533'];?></h3>
                    
                    <!-- Özet -->
                    <div style="background: var(--gray-50); border-radius: 6px; padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid var(--gray-200);">
                        <h4 style="font-size: 14px; font-weight: 600; color: var(--gray-900); margin-bottom: 1rem;"><?=@$dil['txt534'];?></h4>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid var(--gray-200);">
                                <span style="color: var(--gray-600); font-size: 13px; font-weight: 400;"><?=@$dil['txt535'];?></span>
                                <span style="font-weight: 500; color: var(--gray-900); text-align: right; max-width: 60%; font-size: 13px;"><?php echo $Kampanya['adi'];?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0;">
                                <span style="color: var(--gray-600); font-size: 13px; font-weight: 400;"><?=@$dil['txt536'];?></span>
                                <span style="font-size: 1.5rem; font-weight: 600; color: var(--primary-color);" id="ozet_tutar">0 ₺</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.75rem; border-top: 1px solid var(--gray-200);">
                                <span style="color: var(--gray-600); font-size: 13px; font-weight: 400;"><?=@$dil['txt537'];?></span>
                                <span style="font-weight: 500; color: var(--gray-900); font-size: 13px;" id="ozet_tip"><?=@$dil['txt512'];?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ödeme Yöntemi -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 13px; font-weight: 500; color: var(--gray-700); margin-bottom: 0.75rem;"><?=@$dil['txt538'] ?? 'Ödeme Yöntemi';?></label>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <div class="odeme-yontemi-label" style="display: flex; align-items: center; padding: 1rem; border: 2px solid var(--gray-200); background: white; border-radius: 6px;">
                                <div style="flex: 1;">
                                    <span style="font-weight: 500; color: var(--gray-900); font-size: 14px;">Kredi Kartı ile Ödeme</span>
                                    <p style="font-size: 12px; color: var(--gray-500); margin: 0.25rem 0 0 0;">Güvenli ödeme altyapısı</p>
                                </div>
                            </div>
                            <input type="hidden" name="odeme_yontemi" value="paytr">
                        </div>
                    </div>
                    
                    <script>
                    // Ödeme yöntemi tek ve sabit olduğundan stil yönetimine gerek yok.
                    </script>
                    
                    <div style="display: flex; gap: 0.75rem;">
                        <button type="button" onclick="prevStep(2)" 
                                style="flex: 1; background: var(--gray-100); color: var(--gray-700); font-weight: 500; padding: 14px; border-radius: 6px; border: 1px solid var(--gray-200); cursor: pointer; transition: background 0.2s; font-size: 15px;"
                                onmouseover="this.style.background='var(--gray-200)';"
                                onmouseout="this.style.background='var(--gray-100)';">
                            <?=@$dil['txt20'];?>
                        </button>
                        <button type="submit" 
                                style="flex: 1; background: var(--primary-color); color: white; font-weight: 500; padding: 14px; border-radius: 6px; border: none; cursor: pointer; transition: opacity 0.2s; font-size: 15px;"
                                onmouseover="this.style.opacity='0.9';"
                                onmouseout="this.style.opacity='1';">
                            <?=@$dil['txt540'];?>
                        </button>
                    </div>
                    
                    <div style="margin-top: 1.5rem; text-align: center; padding: 1rem; background: var(--gray-50); border-radius: 6px; border: 1px solid var(--gray-200);">
                        <p style="font-size: 12px; color: var(--gray-600); margin: 0 0 0.75rem 0;">
                            <?=@$dil['txt541'];?>
                        </p>
                        <div style="display: flex; justify-content: center; gap: 0.75rem; align-items: center;">
                            <img src="https://www.paytr.com/wp-content/uploads/logo-white.svg" alt="PayTR" style="height: 20px; opacity: 0.6;">
                            <span style="color: var(--gray-300);">|</span>
                            <i class="fab fa-cc-visa" style="font-size: 24px; color: #1A1F71;"></i>
                            <i class="fab fa-cc-mastercard" style="font-size: 24px; color: #EB001B;"></i>
                            <i class="fab fa-cc-amex" style="font-size: 24px; color: #006FCF;"></i>
                        </div>
                    </div>
                </div>
            </form>
                </div>
            </div>
            
            <!-- Sağ Sidebar - Banner -->
            <?php if($Kampanya['banner_resmi']): ?>
            <aside style="order: 3;">
                <div style="background: white; border-radius: 8px; border: 1px solid var(--gray-200); overflow: hidden; position: sticky; top: 2rem;">
                    <!-- Banner Resmi -->
                    <div style="width: 100%; height: 300px; background-size: cover; background-position: center; background-image: url('<?php echo tema;?>/uploads/bagis_moduller/<?php echo $Kampanya['banner_resmi'];?>');"></div>
                    
                    <!-- Banner İçerik -->
                    <div style="padding: 1.5rem;">
                        <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--gray-900); margin: 0 0 0.75rem 0; line-height: 1.4;">
                            <?php echo $Kampanya['adi'];?>
                        </h2>
                        <p style="font-size: 13px; color: var(--gray-600); margin: 0; line-height: 1.6;">
                            <?php echo mb_substr(strip_tags($Kampanya['aciklama']), 0, 200);?>
                        </p>
                    </div>
                </div>
            </aside>
            <?php endif; ?>
            
        </div>
    </div>
</section>
<!-- BAĞIŞ FORMU BİTİŞ -->
    
    <!-- jQuery Cookie Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    
    <script>
        // İlerleme çubuğu animasyonu
        window.addEventListener('load', function() {
            const bar = document.querySelector('[data-width]');
            if(bar) {
                setTimeout(function() {
                    bar.style.width = bar.getAttribute('data-width');
                }, 200);
            }
        });
        
        let currentStep = 1;
        const COOKIE_NAME = 'bagis_form_data';
        const COOKIE_STEP = 'bagis_form_step';

        // Cookie'den veri yükle
        function loadFromCookie() {
            const savedData = $.cookie(COOKIE_NAME);
            const savedStep = $.cookie(COOKIE_STEP);
            
            if(savedData) {
                try {
                    const formData = JSON.parse(savedData);
                    
                    // Form alanlarını doldur
                    Object.keys(formData).forEach(function(key) {
                        const input = document.querySelector('[name="' + key + '"]');
                        if(input) {
                            if(input.type === 'radio' || input.type === 'checkbox') {
                                if(input.value === formData[key] || formData[key] === true) {
                                    input.checked = true;
                                }
                            } else {
                                input.value = formData[key];
                            }
                        }
                    });
                    
                    // Hediye bağış alanını göster/gizle
            if(formData.hediye_bagis) {
                document.getElementById('hediye_bilgileri').style.display = 'block';
            }
                    
                    console.log('✅ Form verileri cookie\'den yüklendi');
                } catch(e) {
                    console.error('Cookie parse hatası:', e);
                }
            }
            
            // Kaydedilmiş adıma git
            if(savedStep && savedStep != 1) {
                const targetStep = parseInt(savedStep);
                if(targetStep >= 1 && targetStep <= 3) {
                    // Tüm önceki adımları completed yap
            for(let i = 1; i < targetStep; i++) {
                document.getElementById('step' + i).style.display = 'none';
                document.getElementById('step-indicator-' + i).classList.remove('active');
                document.getElementById('step-indicator-' + i).classList.add('completed');
            }
                    
                    // Hedef adımı göster
            currentStep = targetStep;
            document.getElementById('step' + currentStep).style.display = 'block';
            document.getElementById('step-indicator-' + currentStep).classList.add('active');
                    updateSummary();
                    console.log('✅ Adım ' + currentStep + ' yüklendi');
                }
            }
        }

        // Cookie'ye kaydet
        function saveToCookie() {
            const formData = {};
            const form = document.getElementById('bagisForm');
            const inputs = form.querySelectorAll('input, select, textarea');
            
            inputs.forEach(function(input) {
                if(input.name && input.name !== 'modul_id' && input.name !== 'modul_adi' && input.name !== 'kampanya_seo' && input.name !== 'para_birimi') {
                    if(input.type === 'radio' || input.type === 'checkbox') {
                        if(input.checked) {
                            formData[input.name] = input.value || true;
                        }
                    } else if(input.value) {
                        formData[input.name] = input.value;
                    }
                }
            });
            
            // Cookie'ye kaydet (session cookie - tarayıcı kapanana kadar)
            $.cookie(COOKIE_NAME, JSON.stringify(formData), { path: '/' });
            $.cookie(COOKIE_STEP, currentStep, { path: '/' });
            
            console.log('💾 Form verileri kaydedildi. Adım:', currentStep);
        }

        function nextStep(step) {
            if (validateStep(currentStep)) {
                // Önce kaydet
                saveToCookie();
                
                // Mevcut adımı gizle
                document.getElementById('step' + currentStep).style.display = 'none';
                document.getElementById('step-indicator-' + currentStep).classList.remove('active');
                document.getElementById('step-indicator-' + currentStep).classList.add('completed');
                
                // Yeni adımı göster
                currentStep = step;
                document.getElementById('step' + currentStep).style.display = 'block';
                document.getElementById('step' + currentStep).style.animation = 'fadeIn 0.5s ease-in';
                document.getElementById('step-indicator-' + currentStep).classList.add('active');
                
                // Cookie'ye adımı kaydet
                $.cookie(COOKIE_STEP, currentStep, { path: '/' });
                
                // Özet bilgilerini güncelle
                updateSummary();
            }
        }

        function prevStep(step) {
            // Önce kaydet
            saveToCookie();
            
            // Mevcut adımı gizle
            document.getElementById('step' + currentStep).style.display = 'none';
            document.getElementById('step-indicator-' + currentStep).classList.remove('active');
            
            // Yeni adımı göster
            currentStep = step;
            document.getElementById('step' + currentStep).style.display = 'block';
            document.getElementById('step' + currentStep).style.animation = 'fadeIn 0.5s ease-in';
            document.getElementById('step-indicator-' + currentStep).classList.add('active');
            document.getElementById('step-indicator-' + currentStep).classList.remove('completed');
            
            // Cookie'ye adımı kaydet
            $.cookie(COOKIE_STEP, currentStep, { path: '/' });
        }

        function validateStep(step) {
            switch(step) {
                case 1:
                    const tutar = document.querySelector('input[name="tutar"]:checked');
                    const ozelTutar = document.getElementById('ozel_tutar').value;
                    if (!tutar && !ozelTutar) {
                        alert('<?=@$dil['txt542'];?>');
                        return false;
                    }
                    return true;
                    
                case 2:
                    const requiredFields = document.querySelectorAll('#step2 input[required]');
                    for (let field of requiredFields) {
                        if (!field.value) {
                            alert('<?=@$dil['txt543'];?>');
                            field.focus();
                            return false;
                        }
                    }
                    // KVKK kontrolü
                    const kvkk = document.querySelector('input[name="kvkk_onay"]');
                    if (!kvkk.checked) {
                        alert('<?=@$dil['txt544'];?>');
                        return false;
                    }
                    return true;
                    
                default:
                    return true;
            }
        }

        function updateSummary() {
            // Tutar hesapla
            let tutar = 0;
            const selectedTutar = document.querySelector('input[name="tutar"]:checked');
            const ozelTutar = document.getElementById('ozel_tutar').value;
            
            if (selectedTutar) {
                tutar = selectedTutar.value;
            } else if (ozelTutar) {
                tutar = ozelTutar;
            }
            
            document.getElementById('ozet_tutar').textContent = tutar + ' ₺';
            
            // Tip hesapla
            const bagisTipi = document.querySelector('input[name="bagis_tipi"]:checked');
            document.getElementById('ozet_tip').textContent = bagisTipi.value === 'surekli' ? '<?=@$dil['txt513'];?>' : '<?=@$dil['txt512'];?>';
        }

        // Hediye bağışı kontrolü
        document.getElementById('hediye_bagis').addEventListener('change', function() {
            const hediyeBilgileri = document.getElementById('hediye_bilgileri');
            if (this.checked) {
                hediyeBilgileri.style.display = 'block';
            } else {
                hediyeBilgileri.style.display = 'none';
            }
            saveToCookie();
        });

        // Tutar değişikliklerini dinle
        document.querySelectorAll('input[name="tutar"]').forEach(input => {
            input.addEventListener('change', function() {
                document.getElementById('ozel_tutar').value = '';
                updateSummary();
                saveToCookie();
            });
        });

        document.getElementById('ozel_tutar').addEventListener('input', function() {
            document.querySelectorAll('input[name="tutar"]').forEach(input => {
                input.checked = false;
            });
            updateSummary();
            // Bu input event'de zaten saveToCookie çağrılacak (genel listener'dan)
        });

        document.querySelectorAll('input[name="bagis_tipi"]').forEach(input => {
            input.addEventListener('change', function() {
                updateSummary();
                saveToCookie();
            });
        });

        // Tüm input'larda değişiklik olduğunda kaydet
        document.getElementById('bagisForm').addEventListener('input', function(e) {
            if(e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') {
                // Kısa bir gecikme ile kaydet (her tuş vuruşunda değil)
                clearTimeout(window.bagisFormTimeout);
                window.bagisFormTimeout = setTimeout(saveToCookie, 500);
            }
        });

        // Form submit edildiğinde cookie'yi temizle
        document.getElementById('bagisForm').addEventListener('submit', function() {
            $.removeCookie(COOKIE_NAME, { path: '/' });
            $.removeCookie(COOKIE_STEP, { path: '/' });
            console.log('🗑️ Form cookie\'leri temizlendi');
        });

        // Sayfa yüklendiğinde cookie'den yükle
        window.addEventListener('DOMContentLoaded', function() {
            loadFromCookie();
            updateSummary();
        });

        // Sayfa kapanmadan önce kaydet
        window.addEventListener('beforeunload', function() {
            if(currentStep > 1 && currentStep < 3) {
                saveToCookie();
            }
        });
    </script>
