<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php 
$menubul = $db->query("SELECT * FROM menu WHERE menu_url = '".$htc['bagisurl']."' OR link = '".$htc['bagisurl']."' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
$menubas = $db->query("SELECT * FROM menu WHERE id = '{$menubul['menu_ust']}' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);

// Döviz kurları - Dinamik API entegrasyonu
require_once('_class/exchange_rates.php');
$kurlar = ExchangeRates::getRates(['USD', 'EUR']);

// Ülke tespiti için sınıf
require_once('_class/country_detector.php');
$visitor_country = CountryDetector::getCountryCode($_SERVER['REMOTE_ADDR']);
$visitor_country_flag = CountryDetector::getFlagEmoji($visitor_country);
$visitor_country_name = CountryDetector::getCountryName($visitor_country);

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

// Para birimi değiştirme işlemi - localStorage ve session entegrasyonu
if(isset($_GET['para_birimi']) && array_key_exists($_GET['para_birimi'], $kurlar)) {
    $seciliParaBirimi = $_GET['para_birimi'];
    $_SESSION['para_birimi'] = $seciliParaBirimi;
}
?>
 
<script>
document.addEventListener('DOMContentLoaded', function() {
    const storedCurrency = localStorage.getItem('para_birimi');
    const currentCurrency = '<?= $seciliParaBirimi ?>';
    
    // Mevcut aktif dili Google Translate çerezinden tespit et
    const currentLangCookie = document.cookie.match(/googtrans=([^;]+)/);
    let currentLang = 'tr';
    if (currentLangCookie) {
        const parts = currentLangCookie[1].split('/');
        currentLang = parts[parts.length - 1];
    }

    // Dil ve Para Birimi Senkronizasyonu
    let targetCurrency = storedCurrency;
    if ((currentLang === 'en' || currentLang === 'ar') && (!storedCurrency || storedCurrency === 'TRY')) {
        targetCurrency = 'USD';
    } else if (currentLang === 'tr' && storedCurrency !== 'TRY') {
        targetCurrency = 'TRY';
    }

    // Değişiklik gerekiyorsa hem LocalStorage hem Session güncellemesi yap
    if (targetCurrency !== currentCurrency) {
        localStorage.setItem('para_birimi', targetCurrency);
        
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/update_currency.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                if (typeof window.updateCurrencyWithoutReload === 'function') {
                    window.updateCurrencyWithoutReload(targetCurrency);
                } else {
                    location.reload();
                }
            }
        };
        xhr.send('para_birimi=' + targetCurrency);
    }
});
</script>

<?php

// Seçili para birimi sembolü
$paraBirimiSembolu = $paraBirimiSembolleri[$seciliParaBirimi];

// URL'den kategori slug'ını al
$kategoriSlug = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// SEO URL kontrolü - bagis/acil-yardim şeklinde çağrıldığında
$requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$isSeoUrl = false;
$aktifKategoriId = null;

// REQUEST_URI'den slug'ı parse et
if(preg_match('/bagis\/([^\/\?]+)/', $requestUri, $matches)) {
    $kategoriSlug = $matches[1];
    $isSeoUrl = true;
    
    // Tüm kategorileri getir ve slug'ı karşılaştır
    $allCategoriesQuery = $db->prepare("SELECT id, adi FROM bagis_kategori WHERE dil = ? AND durum = ? ORDER BY id ASC");
    $allCategoriesQuery->execute(array($_SESSION['k_dil'], 1));
    $allCategories = $allCategoriesQuery->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($allCategories as $category) {
        // Kategori adından slug oluştur
        $generatedSlug = strtolower(str_replace([' ', 'ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'], 
                                              ['-', 'i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'], 
                                              $category['adi']));
        
        // Slug eşleşiyorsa, aktif kategori ID'sini kaydet
        if($generatedSlug == $kategoriSlug) {
            $aktifKategoriId = $category['id'];
            break;
        }
    }
}
?>

<!-- Tailwind CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<!-- Alpine.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Alpine.js x-cloak CSS - Modal'ın sayfa yüklenirken görünmesini engellemek için -->
<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<script>
// Common country data - available for immediate use
const countryData = [
    { name: 'Türkiye', code: '+90', flag: '🇹🇷', iso: 'TR', format: '(###) ### ## ##', len: 10 },
    { name: 'Germany', code: '+49', flag: '🇩🇪', iso: 'DE', format: '#### #######', len: 11 },
    { name: 'USA', code: '+1', flag: '🇺🇸', iso: 'US', format: '(###) ###-####', len: 10 },
    { name: 'UK', code: '+44', flag: '🇬🇧', iso: 'GB', format: '#### ######', len: 10 },
    { name: 'France', code: '+33', flag: '🇫🇷', iso: 'FR', format: '# ## ## ## ##', len: 9 },
    { name: 'Netherlands', code: '+31', flag: '🇳🇱', iso: 'NL', format: '## ########', len: 10 },
    { name: 'Belgium', code: '+32', flag: '🇧🇪', iso: 'BE', format: '### ## ## ##', len: 9 },
    { name: 'Austria', code: '+43', flag: '🇦🇹', iso: 'AT', format: '#### #######', len: 11 },
    { name: 'Switzerland', code: '+41', flag: '🇨🇭', iso: 'CH', format: '## ### ## ##', len: 9 },
    { name: 'Saudi Arabia', code: '+966', flag: '🇸🇦', iso: 'SA', format: '# #######', len: 9 },
    { name: 'Azerbaijan', code: '+994', flag: '🇦🇿', iso: 'AZ', format: '## ### ## ##', len: 9 },
    { name: 'Russia', code: '+7', flag: '🇷🇺', iso: 'RU', format: '### ###-##-##', len: 10 },
    { name: 'UAE', code: '+971', flag: '🇦🇪', iso: 'AE', format: '# #######', len: 9 },
    { name: 'Norway', code: '+47', flag: '🇳🇴', iso: 'NO', format: '### ## ###', len: 8 },
    { name: 'Sweden', code: '+46', flag: '🇸🇪', iso: 'SE', format: '## ### ## ##', len: 9 },
    { name: 'Denmark', code: '+45', flag: '🇩🇰', iso: 'DK', format: '## ## ## ##', len: 8 },
    { name: 'Finland', code: '+358', flag: '🇫🇮', iso: 'FI', format: '## ### ####', len: 9 },
    { name: 'Canada', code: '+1', flag: '🇨🇦', iso: 'CA', format: '(###) ###-####', len: 10 },
    { name: 'Australia', code: '+61', flag: '🇦🇺', iso: 'AU', format: '### ### ###', len: 9 },
    { name: 'Italy', code: '+39', flag: '🇮🇹', iso: 'IT', format: '### #######', len: 10 },
    { name: 'Spain', code: '+34', flag: '🇪🇸', iso: 'ES', format: '### ### ###', len: 9 },
    { name: 'Qatar', code: '+974', flag: '🇶🇦', iso: 'QA', format: '#### ####', len: 8 },
    { name: 'Kuwait', code: '+965', flag: '🇰🇼', iso: 'KW', format: '#### ####', len: 8 }
];
</script>

 
<!-- PAGE SECTION BAŞLANGIÇ -->
<style>
    .page-section-bg {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f0fdf4 100%);
        min-height: 100vh;
    }
    
    /* Loading Animation Styles */
    .tab-loading-overlay {
        position: relative;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 16px;
        backdrop-filter: blur(8px);
    }
    
    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }
    
    .spinner-circle {
        width: 60px;
        height: 60px;
        border: 4px solid #e2e8f0;
        border-top: 4px solid #14b8a6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .loading-text {
        color: #14b8a6;
        font-size: 16px;
        font-weight: 600;
        animation: pulse 1.5s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .loading-dots {
        display: flex;
        gap: 8px;
    }
    
    .loading-dot {
        width: 10px;
        height: 10px;
        background: #14b8a6;
        border-radius: 50%;
        animation: bounce 1.4s infinite ease-in-out both;
    }
    
    .loading-dot:nth-child(1) { animation-delay: -0.32s; }
    .loading-dot:nth-child(2) { animation-delay: -0.16s; }
    
    @keyframes bounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }
    
    /* Mobile Responsive */
    @media (max-width: 640px) {
        .tab-loading-overlay {
            min-height: 300px;
        }
        
        .spinner-circle {
            width: 50px;
            height: 50px;
            border-width: 3px;
        }
        
        .loading-text {
            font-size: 14px;
        }
        
        .loading-dot {
            width: 8px;
            height: 8px;
        }
    }
</style>
<section class="page-section-bg" x-data="{ 
    showCurrencySelector: false, 
    selectedCurrency: '<?= $seciliParaBirimi ?>', 
    showQuickDonation: false, 
    showYetimModal: false,
    yetimler: [],
    loadingYetimler: false,
    yetimSearch: '',
    selectedYetim: null,
    currentBagisId: null,
    quickDonationYetimId: null,
    quickDonationBagisId: null,
    quickDonationBagisAdi: '',
    quickDonationFiyat: 0,
    currentStep: 1, 
    donationAmount: '', 
    donationType: '',
    customAmountValue: '',
    fullName: localStorage.getItem('quick_fullName') || '', 
    phone: localStorage.getItem('quick_phone') || '', 
    email: localStorage.getItem('quick_email') || '', 
    odemeYontemi: 'paytr',
    agreeTerms: false,
// Yetim sponsorluk için yeni alanlar
    sponsorlukAySayisi: 1,
sponsorlukTipi: 'bireysel', // 'bireysel', 'grup', 'kurumsal'
    odemePeriyodu: 'tek_seferde', // 'tek_seferde' veya 'aylik' - ÖDEME PERİYODU
    aylikOdemeGunu: 1,
    isAutoMatch: false, // Otomatik eşleşme flag'i
    // Sponsorluk süresi
    odemeTipi: 'tek_seferlik', // 'tek_seferlik' veya 'sürekli'
    sponsorlukSuresiAy: null,
    odemeGunu: 1,
    // Ek alanlar
    grupAdi: '',
    kurumUnvani: '',
    // Yetim bağışı kontrolü
    isYetimBagis: false,
    // Ülke bilgisi
    visitorCountry: '<?= $visitor_country ?>',
    visitorCountryFlag: '<?= $visitor_country_flag ?>',
    visitorCountryName: '<?= $visitor_country_name ?>',
    selectedCountry: '<?= $visitor_country ?>',
    // Tel Picker State
    showDialCodes: false,
    phoneSearch: '',
    phoneDialCode: '+90',
    phoneFlag: '🇹🇷',
    phoneFormat: '(###) ### ## ##',
    phoneLen: 10,
    inputNumber: '',
    updateFullPhone() {
        let cleanNumber = this.inputNumber.replace(/\D/g, '');
        if (this.phoneLen && cleanNumber.length > this.phoneLen) {
            cleanNumber = cleanNumber.substring(0, this.phoneLen);
        }
        this.inputNumber = this.applyMask(cleanNumber, this.phoneFormat);
        this.phone = this.phoneDialCode + cleanNumber;
    },
    applyMask(val, mask) {
        if (!mask) return val;
        let masked = '';
        let valIdx = 0;
        for (let i = 0; i < mask.length && valIdx < val.length; i++) {
            if (mask[i] === '#') {
                masked += val[valIdx++];
            } else {
                masked += mask[i];
            }
        }
        return masked;
    },
    selectDialCode(country) {
        this.phoneDialCode = country.code;
        this.phoneFlag = country.flag;
        this.phoneFormat = country.format || '##########';
        this.phoneLen = country.len || 15;
        this.inputNumber = '';
        this.showDialCodes = false;
        this.updateFullPhone();
    },
    getDonationTypeName(typeId) {
        const types = {
            <?php
            $bagisTurleriSorgu = $db->prepare("SELECT * FROM bagis_turleri WHERE durum = 1 ORDER BY sira ASC");
            $bagisTurleriSorgu->execute();
            $bagisTurleri = $bagisTurleriSorgu->fetchAll(PDO::FETCH_ASSOC);
            foreach($bagisTurleri as $tur):
            ?>
            '<?php echo $tur['id']; ?>': '<?php echo addslashes($tur['adi']); ?>',
            <?php endforeach; ?>
        };
        return types[typeId] || '';
    },
    // Direkt sponsorluk formu - yetim seçimi kaldırıldı
    openSponsorshipForm(bagisId) {
        this.currentBagisId = bagisId;
        this.quickDonationYetimId = null; // Yetim seçimi yapılmayacak
        this.quickDonationBagisId = this.currentBagisId;
        this.quickDonationBagisAdi = 'Yetim Sponsorluğu';
        
        const priceInput = document.getElementById('fiyat_' + this.currentBagisId);
        if(priceInput) { this.quickDonationFiyat = priceInput.value; }
        this.showQuickDonation = true;
    }
}
" 
x-init="
    selectedCurrency = localStorage.getItem('para_birimi') || '<?= $seciliParaBirimi ?>';
    
    // Initialize based on visitor country
    if (typeof countryData !== 'undefined') {
        const initialCountry = countryData.find(c => c.iso === this.visitorCountry);
        if (initialCountry) {
            this.phoneDialCode = initialCountry.code;
            this.phoneFlag = initialCountry.flag;
            this.phoneFormat = initialCountry.format || '##########';
            this.phoneLen = initialCountry.len || 15;
            this.updateFullPhone(); // Apply initial empty mask or update state
        }
    }

    // Parse stored phone number
    const storedPhoneValue = localStorage.getItem('quick_phone') || '';
    if (storedPhoneValue) {
        this.phone = storedPhoneValue;
        const matchingCountry = typeof countryData !== 'undefined' ? countryData.find(c => storedPhoneValue.startsWith(c.code)) : null;
        if (matchingCountry) {
            this.phoneDialCode = matchingCountry.code;
            this.phoneFlag = matchingCountry.flag;
            this.phoneFormat = matchingCountry.format || '##########';
            this.phoneLen = matchingCountry.len || 15;
            let rawNum = storedPhoneValue.replace(matchingCountry.code, '').replace(/\D/g, '');
            // applyMask methodu doğrudan çağır
            const maskFn = () => {
                if (!this.phoneFormat) return rawNum;
                let masked = '';
                let valIdx = 0;
                for (let i = 0; i < this.phoneFormat.length && valIdx < rawNum.length; i++) {
                    if (this.phoneFormat[i] === '#') {
                        masked += rawNum[valIdx++];
                    } else {
                        masked += this.phoneFormat[i];
                    }
                }
                return masked;
            };
            this.inputNumber = maskFn.call(this);
        } else {
            this.inputNumber = storedPhoneValue;
        }
    }

    $watch('fullName', val => localStorage.setItem('quick_fullName', val));
    $watch('phone', val => localStorage.setItem('quick_phone', val));
    $watch('email', val => localStorage.setItem('quick_email', val));
"
    <!-- Hero Banner -->
    <div class="relative w-full h-64 md:h-80">
        <img src="<?php echo tema;?>/uploads/arkaplan/arkaplan22/<?php echo $arkaplan['arkaplan22'];?>" alt="<?=@$dil['txt111'];?>" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-white mb-4"><?=@$dil['txt111'];?></h1>
                <nav class="flex justify-center text-white text-sm">
                    <a href="<?php echo $htc['anaurl'];?><?php echo $html;?>" class="hover:text-blue-300 flex items-center">
                        <i class="fas fa-home mr-2"></i> <?=@$dil['txt20'];?>
                    </a>
                    <?php if($menubas['menu_isim'] != ""): ?>
                    <span class="mx-2">/</span>
                    <a href="<?php echo($menubas['menu_url'] == "0" ? $menubas['link'] : $menubas['menu_url']);?>" class="hover:text-blue-300">
                        <?php echo cVCLmHLxbS_ilkbuyuk($menubas['menu_isim']);?>
                    </a>
                    <?php endif; ?>
                    <span class="mx-2">/</span>
                    <span><?=@$dil['txt111'];?></span>
                </nav>
            </div>
        </div>
    </div>
    
    
    <!-- Yetim Listesi Modal -->
    <!-- Yetim Modal Kapatıldı - Direkt Form Kullanılacak -->
    <div id="yetimListModal" x-show="false" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); display: none !important;">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl transform transition-all" @click.away="showYetimModal = false">
                <!-- Header -->
                <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4 rounded-t-2xl flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">Yetim Listesi</h3>
                    <button @click="showYetimModal = false" class="text-white hover:bg-white/20 rounded-full p-2 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Body -->
                <div class="p-6">
                    <!-- Search -->
                    <div class="mb-6">
                        <input type="text" x-model="yetimSearch" placeholder="Yetim Ara (Ad Soyad, Cinsiyet, Eğitim Durumu...)" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>
                    
                    <!-- Loading -->
                    <div x-show="loadingYetimler" class="flex justify-center py-12">
                        <div class="spinner-circle"></div>
                    </div>
                    
                    <!-- List -->
                    <div x-show="!loadingYetimler" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-h-[60vh] overflow-y-auto pr-2">
                        <template x-for="yetim in yetimler.filter(y => {
                            const searchTerm = yetimSearch.toLowerCase();
                            const adSoyad = (y.ad_soyad || '').toLowerCase();
                            const cinsiyet = (y.cinsiyet || '').toLowerCase();
                            const egitimDurumu = (y.egitim_durumu || '').toLowerCase();
                            return adSoyad.includes(searchTerm) || 
                                   cinsiyet.includes(searchTerm) || 
                                   egitimDurumu.includes(searchTerm) ||
                                   searchTerm === '';
                        })" :key="yetim.id">
                            <div class="border rounded-xl p-4 hover:shadow-lg transition flex flex-col items-center text-center bg-gray-50">
                                <div class="w-24 h-24 rounded-full bg-gray-200 mb-4 overflow-hidden">
                                    <img :src="yetim.foto ? '<?php echo tema;?>/uploads/yetimler/' + yetim.foto : 'https://via.placeholder.com/150?text=Yetim'" class="w-full h-full object-cover">
                                </div>
                                <h4 class="font-bold text-lg mb-1" x-text="yetim.ad_soyad"></h4>
                                <p class="text-sm text-gray-500 mb-3" x-text="yetim.cinsiyet"></p>
                                <div class="w-full mb-3 px-2">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1" x-show="yetim.son_bagis_tarihi_format != '-'">
                                        <span>Son Bağış:</span>
                                        <span class="font-semibold" x-text="yetim.son_bagis_tarihi_format"></span>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-600">
                                        <span>Destek Süresi:</span>
                                        <span class="font-semibold" x-text="yetim.sistemde_gecen_sure"></span>
                                    </div>
                                </div>
                                <!-- Yetim seçimi kaldırıldı, sadece gösterim amaçlı -->
                                <div class="w-full bg-gray-400 text-white py-2 rounded-lg cursor-not-allowed">
                                    <i class="fas fa-info-circle mr-2"></i>Bilgi
                                </div>
                            </div>
                        </template>
                        <div x-show="yetimler.length === 0" class="col-span-3 text-center py-8 text-gray-500">
                            Henüz listelenecek yetim bulunmamaktadır.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hızlı Bağış Modal - Modern & Minimal -->
    <div id="quickDonationModal" x-show="showQuickDonation" x-cloak x-init="$el.setAttribute('data-modal', 'quickDonation')" 
         @click.away="showQuickDonation = false" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="background: rgba(0,0,0,0.6); backdrop-filter: blur(8px);">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all" 
                 @click.stop>
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4 rounded-t-2xl flex justify-between items-center">
                    <div>
                        <p style=" font-size: 22px; font-weight: 600; " x-show="quickDonationBagisAdi" class="text-sm text-teal-100 mt-1" x-text="quickDonationBagisAdi"></p>
                    </div>
                    <button style="padding: 4px; border-radius: 28px; height: 35px; width: 35px; background: rgba(255,255,255,0.2);" @click="showQuickDonation = false; currentStep = 1; quickDonationFiyat = 0; donationAmount = ''; donationType = ''; customAmountValue = ''; fullName = ''; phone = ''; email = '';" type="button" class="text-white hover:bg-white hover:bg-opacity-30 focus:outline-none transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                
                <!-- Modal Body -->
                <div class="px-6 py-3">
                    <!-- Step 1: Kişisel Bilgiler -->
                        <div x-show="currentStep === 1" x-transition>
                            <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-user-circle text-teal-600 mr-2"></i> <?=@$dil['txt440'];?>
                        </h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="fullName" class="block text-sm font-semibold text-gray-700 mb-1"><?=@$dil['txt441'];?> <span class="text-xs text-gray-500" x-text="visitorCountryFlag + ' ' + visitorCountryName"></span></label>
                                <input type="text" id="fullName" x-model="fullName" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" placeholder="Ad Soyad">
                            </div>
                            
     
                            
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1"><?=@$dil['txt442'] ?: 'Telefon';?></label>
                                <div class="relative flex items-center">
                                    <!-- Dial Code Selector -->
                                    <div class="relative h-full">
                                        <button style=" height: 58px; " @click="showDialCodes = !showDialCodes" type="button" class="flex items-center gap-1 h-[52px] px-3 bg-gray-50 border-2 border-r-0 border-gray-200 rounded-l-lg hover:bg-gray-100 transition focus:outline-none">
                                            <span class="text-xl" x-text="phoneFlag"></span>
                                            <span style=" color: white; " class="text-sm font-bold text-gray-700" x-text="phoneDialCode"></span>
                                            <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
                                        </button>
                                        
                                        <!-- Searchable Dial Code Dropdown -->
                                        <div x-show="showDialCodes" @click.away="showDialCodes = false" x-cloak 
                                             class="absolute left-0 mt-2 w-72 bg-white border border-gray-100 shadow-2xl rounded-xl z-[100] country-dropdown">
                                            <div class="p-3 sticky top-0 bg-white border-b border-gray-50">
                                                <div class="relative">
                                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                                    <input type="text" x-model="phoneSearch" placeholder="Ülke veya kod ara..." 
                                                           class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                                                </div>
                                            </div>
                                            <div style=" z-index: 99999; position: relative; position: relative; "  class="py-1">
                                                <template x-for="country in countryData.filter(c => c.name.toLowerCase().includes(phoneSearch.toLowerCase()) || c.code.includes(phoneSearch))" :key="country.iso">
                                                    <button @click="selectDialCode(country)" type="button" 
                                                            class="w-full flex items-center justify-between px-4 py-2.5 hover:bg-teal-50 transition group">
                                                        <div class="flex items-center gap-3">
                                                            <span class="text-xl" x-text="country.flag"></span>
                                                            <span class="text-sm font-medium text-gray-700 group-hover:text-teal-700" x-text="country.name"></span>
                                                        </div>
                                                        <span class="text-xs font-bold text-gray-400 group-hover:text-teal-600" x-text="country.code"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Number Input -->
                                     <input type="tel" x-model="inputNumber" @input="updateFullPhone()" 
                                            class="w-full h-[52px] px-4 py-3 border-2 border-gray-200 rounded-r-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" 
                                            :placeholder="phoneFormat.replace(/#/g, 'x')">
                                </div>
                                <!-- Hidden field for form submission if needed, but we use the Alpine 'phone' variable -->
                                <input type="hidden" name="phone_full" :value="phone">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1"><?=@$dil['txt443'];?></label>
                                <input type="email" id="email" x-model="email" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" placeholder="ornek@email.com">
                            </div>
                            
                            <!-- Sponsorluk Türü Seçimi (Yetim Sponsorluğunda) -->
                            <div x-show="quickDonationYetimId || isYetimBagis">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Sponsorluk Türü</label>
                                <div class="space-y-2 mb-4">
                                    <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all" 
                                           :class="sponsorlukTipi === 'bireysel' ? 'border-teal-500 bg-teal-50' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="radio" x-model="sponsorlukTipi" value="bireysel" class="mr-3">
                                        <div>
                                            <span class="font-medium">Bireysel Sponsorluk</span>
                                            <p class="text-xs text-gray-500">Kişisel sponsorluk</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all" 
                                           :class="sponsorlukTipi === 'grup' ? 'border-teal-500 bg-teal-50' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="radio" x-model="sponsorlukTipi" value="grup" class="mr-3">
                                        <div>
                                            <span class="font-medium">Grup Sponsorluğu</span>
                                            <p class="text-xs text-gray-500">Bir grup olarak sponsorluk</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all" 
                                           :class="sponsorlukTipi === 'kurumsal' ? 'border-teal-500 bg-teal-50' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="radio" x-model="sponsorlukTipi" value="kurumsal" class="mr-3">
                                        <div>
                                            <span class="font-medium">Kurumsal Sponsorluk</span>
                                            <p class="text-xs text-gray-500">Kurumsal firma sponsorluğu</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Ek Bilgiler (Grup ve Kurumsal için) -->
                            <div x-show="(quickDonationYetimId || isYetimBagis) && sponsorlukTipi === 'grup'">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Grup Adı</label>
                                        <input type="text" x-model="grupAdi" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" placeholder="Grup adı">
                                    </div>
                                </div>
                            </div>
                            
                            <div x-show="(quickDonationYetimId || isYetimBagis) && sponsorlukTipi === 'kurumsal'">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kurum Ünvanı</label>
                                        <input type="text" x-model="kurumUnvani" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" placeholder="Kurum ünvanı">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ülke Seçimi -->
                            <div style="display:none;">
                                <label for="country" class="block text-sm font-semibold text-gray-700 mb-1">Ülke</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-lg" ></span>
                                    <select style=" z-index: -1; position: relative; " x-model="selectedCountry" class="w-full pl-10 px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition appearance-none">
                                        <option value="TR">🇹🇷 Türkiye</option>
                                        <option value="DE">🇩🇪 Almanya</option>
                                        <option value="US">🇺🇸 Amerika Birleşik Devletleri</option>
                                        <option value="GB">🇬🇧 Birleşik Krallık</option>
                                        <option value="FR">🇫🇷 Fransa</option>
                                        <option value="NL">🇳🇱 Hollanda</option>
                                        <option value="AT">🇦🇹 Avusturya</option>
                                        <option value="BE">🇧🇪 Belçika</option>
                                        <option value="CH">🇨🇭 İsviçre</option>
                                        <option value="DK">🇩🇰 Danimarka</option>
                                        <option value="SE">🇸🇪 İsveç</option>
                                        <option value="NO">🇳🇴 Norveç</option>
                                        <option value="FI">🇫🇮 Finlandiya</option>
                                        <option value="CA">🇨🇦 Kanada</option>
                                        <option value="AU">🇦🇺 Avustralya</option>
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>
                            
<!-- Yetim Sponsorluk Ödeme Periyodu Seçimi -->
                            <div x-show="quickDonationYetimId || isYetimBagis">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Ödeme Periyodu</label>
                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all" 
                                           :class="odemePeriyodu === 'tek_seferde' ? 'border-teal-500 bg-teal-50' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="radio" x-model="odemePeriyodu" value="tek_seferde" class="mr-2">
                                        <div>
                                            <span class="font-medium">Tek Seferde</span>
                                            <p class="text-xs text-gray-500">Tüm aylar için tek ödeme</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all" 
                                           :class="odemePeriyodu === 'aylik' ? 'border-teal-500 bg-teal-50' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="radio" x-model="odemePeriyodu" value="aylik" class="mr-2">
                                        <div>
                                            <span class="font-medium">Aylık Ödeme</span>
                                            <p class="text-xs text-gray-500">Her ay düzenli ödeme</p>
                                        </div>
                                    </label>
                                </div>
                                
                                <div x-show="(quickDonationYetimId || isYetimBagis) && odemePeriyodu === 'aylik'">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kaç Ay?</label>
                                    <select x-model="sponsorlukAySayisi" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <option value="1">1 Ay</option>
                                        <option value="3">3 Ay</option>
                                        <option value="6">6 Ay</option>
                                        <option value="9">9 Ay</option>
                                        <option value="12">12 Ay</option>
                                    </select>
                                </div>
                                
                                <div x-show="(quickDonationYetimId || isYetimBagis) && odemePeriyodu === 'aylik'">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ayın Kaçıncı Günü?</label>
                                    <select x-model="aylikOdemeGunu" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                        <template x-for="gun in Array.from({length: 28}, (_, i) => i + 1)">
                                            <option :value="gun" x-text="gun + '. Gün'"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button @click="submitDonation()" :disabled="!fullName || !phone || !email" :class="!fullName || !phone || !email ? 'bg-gray-300 cursor-not-allowed' : 'bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 shadow-lg'" class="w-full text-white font-bold py-3 px-4 rounded-lg transition-all transform hover:scale-105 disabled:transform-none">
                                <?=@$dil['txt444'];?> <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 2: Onay -->
                    <div x-show="false">
                            <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-check-circle text-teal-600 mr-2"></i> <?=@$dil['txt445'];?>
                        </h4>
                        
                        <div class="bg-gradient-to-r from-teal-50 to-emerald-50 p-5 rounded-lg mb-4 border-2 border-teal-100">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-teal-200">
                                    <span class="font-semibold text-gray-700"><?=@$dil['txt446'];?></span>
                                    <span class="font-medium text-gray-800" x-text="fullName"></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-teal-200">
                                    <span class="font-semibold text-gray-700"><?=@$dil['txt447'];?></span>
                                    <span class="font-medium text-gray-800" x-text="phone"></span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="font-semibold text-gray-700"><?=@$dil['txt448'];?></span>
                                    <span class="font-medium text-gray-800" x-text="email"></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ödeme Yöntemi Seçimi -->
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-3"><?=@$dil['txt538'] ?? 'Ödeme Yöntemi';?></label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all" 
                                       :class="odemeYontemi === 'paytr' ? 'border-teal-500 bg-teal-50' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" x-model="odemeYontemi" value="paytr" class="mr-3 w-4 h-4 text-teal-600 focus:ring-teal-500">
                                    <div class="flex-1">
                                        <span class="font-medium text-gray-900">Kredi Kartı ile Ödeme</span>
                                        <p class="text-xs text-gray-500 mt-0.5">Güvenli ödeme altyapısı</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all" 
                                       :class="odemeYontemi === 'vakifbank' ? 'border-teal-500 bg-teal-50' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" x-model="odemeYontemi" value="vakifbank" class="mr-3 w-4 h-4 text-teal-600 focus:ring-teal-500">
                                    <div class="flex-1">
                                        <span class="font-medium text-gray-900">Kredi Kartı ile Ödeme</span>
                                        <p class="text-xs text-gray-500 mt-0.5">Güvenli ödeme altyapısı</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <div class="bg-teal-50 border-2 border-teal-200 rounded-lg p-4 mb-5">
                            <div class="flex items-start">
                                <i class="fas fa-shield-alt text-teal-600 text-xl mr-3 mt-1"></i>
                                <div>
                                    <p class="text-sm font-semibold text-teal-800 mb-1"><?=@$dil['txt449'];?></p>
                                    <p class="text-xs text-teal-700">
                                        <?=@$dil['txt450'];?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex gap-3">
                            <button @click="currentStep = 1" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-4 rounded-lg transition">
                                <i class="fas fa-arrow-left mr-2"></i> <?=@$dil['txt20'];?>
                            </button>
                            <button  class="flex-1 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-bold py-3 px-4 rounded-lg transition-all transform hover:scale-105 shadow-lg flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i> <?=@$dil['txt451'];?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Main Content -->
            <div class="w-full md:w-3/4">
                <!-- Currency Selector -->
                <style>
                    .currency-selector-btn {
                        background: white;
                        border: 1px solid #e2e8f0;
                        border-radius: 50px;
                        padding: 10px 20px;
                        font-size: 15px;
                        font-weight: 600;
                        color: #2d3748;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                        display: flex;
                        align-items: center;
                        gap: 8px;
                    }
                    .currency-selector-btn:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
                    }
                    .back-link {
                        background: white;
                        border: 1px solid #e2e8f0;
                        border-radius: 50px;
                        padding: 10px 20px;
                        font-size: 15px;
                        font-weight: 600;
                        color: #14b8a6;
                        text-decoration: none;
                        transition: all 0.3s ease;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                        display: inline-flex;
                        align-items: center;
                        gap: 8px;
                    }
                    .back-link:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
                        background: #f0fdfa;
                    }
                </style>
                <div class="mb-8 flex justify-between items-center">
                    <a href="javascript:history.back();" class="back-link">
                        <i class="fas fa-arrow-left"></i>
                        <span><?=@$dil['txt20'];?></span>
                    </a>
                    
                    <div class="relative">
                        <button style="display:none;" @click="showCurrencySelector = !showCurrencySelector" class="currency-selector-btn focus:outline-none">
                            <span><?= $paraBirimiSembolu ?> <?= $seciliParaBirimi ?></span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <div x-show="showCurrencySelector" x-cloak @click.away="showCurrencySelector = false" class="absolute right-0 mt-2 w-56 bg-white shadow-lg rounded-xl py-2 z-10 border border-gray-100">
                            <a href="#" @click.prevent="changeCurrency('TRY')" class="block px-4 py-3 text-sm font-medium text-gray-700 hover:bg-teal-50 hover:text-teal-700 transition"><?=@$dil['txt452'];?></a>
                            <a href="#" @click.prevent="changeCurrency('USD')" class="block px-4 py-3 text-sm font-medium text-gray-700 hover:bg-teal-50 hover:text-teal-700 transition"><?=@$dil['txt453'];?></a>
                            <a href="#" @click.prevent="changeCurrency('EUR')" class="block px-4 py-3 text-sm font-medium text-gray-700 hover:bg-teal-50 hover:text-teal-700 transition"><?=@$dil['txt454'];?></a>
                        </div>
                        
<script>
                        // Global fonksiyonlar - Alpine.js tarafından erişilebilir
                        // Resim hata fonksiyonu
                        function imgError() {
                            console.log('Resim yüklenemedi');
                        }
                        </script>
<script>

                        function submitDonation() {
                            try {
                                // DOM elementlerinden direkt değerleri oku (Alpine.js x-model ile bağlı)
                                const fullNameInput = document.getElementById('fullName');
                                const phoneInput = document.getElementById('phone');
                                const emailInput = document.getElementById('email');
                                
                                // Alpine.js verilerine erişim denemesi
                                const sectionElement = document.querySelector('section[x-data]');
                                let alpineData = null;
                                
                                if (sectionElement && window.Alpine) {
                                    try {
                                        alpineData = Alpine.$data(sectionElement);
                                    } catch(e) {
                                        console.log("Alpine.$data erişilemedi, DOM'dan okunuyor...");
                                    }
                                }
                                
                                // Kişisel bilgi kontrolü - DOM'dan direkt oku
                                const fullName = fullNameInput ? fullNameInput.value.trim() : (alpineData && alpineData.fullName ? alpineData.fullName : '');
                                const phone = (alpineData && alpineData.phone) ? alpineData.phone : '';
                                const email = emailInput ? emailInput.value.trim() : (alpineData && alpineData.email ? alpineData.email : '');

                                if (!fullName || !phone || !email) {
                                    alert('Lütfen tüm zorunlu alanları doldurunuz!');
                                    return;
                                }
                                
                                // İsim ve soyisim ayırma
                                let firstName = fullName;
                                let lastName = '';
                                
                                if (fullName.includes(' ')) {
                                    const nameParts = fullName.split(' ');
                                    firstName = nameParts[0];
                                    lastName = nameParts.slice(1).join(' ');
                                }
                                
                                // Fiyatı Alpine.js data'sından al
                                let tutar = 0;
                                if (alpineData && alpineData.quickDonationFiyat && alpineData.quickDonationFiyat > 0) {
                                    tutar = parseFloat(alpineData.quickDonationFiyat);
                                } else if (alpineData && alpineData.customAmountValue && alpineData.customAmountValue > 0) {
                                    tutar = parseFloat(alpineData.customAmountValue);
                                }
                                
                                // Para birimini Alpine.js state'inden al
                                const paraBirimi = (alpineData && alpineData.selectedCurrency) ? alpineData.selectedCurrency : '<?= $seciliParaBirimi ?>';
                                
                                const donationType = null; // Bağış türü opsiyonel
                                
                                // Ödeme yöntemini Alpine.js data'sından al
                                const odemeYontemi = alpineData && alpineData.odemeYontemi ? alpineData.odemeYontemi : 'paytr';
                                
                                // Veritabanına kayıt ve PayTR'ye yönlendirme
                                const donationData = {
                                    tutar: tutar,
                                    ad: firstName,
                                    soyad: lastName,
                                    telefon: phone,
                                    email: email,
                                    bagis_id: alpineData && alpineData.quickDonationBagisId ? alpineData.quickDonationBagisId : null,
                                    bagis_tur_id: donationType,
                                    bagis_tipi: 'hizli_bagis'
                                };
                                
                                // AJAX isteği
                                const ajaxData = {
                                           islem: 'hizli_bagis_kaydet',
                                           modul_id: '1',
                                           modul_adi: 'Hızlı Bağış',
                                           ad: donationData.ad,
                                           soyad: donationData.soyad,
                                           email: donationData.email,
                                           telefon: donationData.telefon,
                                           bagis_id_param: alpineData.quickDonationBagisId,
                                           kategori_id: alpineData.currentKategoriId,
                                      tutar: tutar,
                                           para_birimi: paraBirimi,
                                      bagis_tipi: 'hizli',
                                      odeme_yontemi: odemeYontemi,
                                      bagis_id_param: donationData.bagis_id,
                                      yetim_id: (alpineData && alpineData.quickDonationYetimId) ? alpineData.quickDonationYetimId : null,
                                      ulke: alpineData && alpineData.selectedCountry ? alpineData.selectedCountry : '<?= $visitor_country ?>'
                                };
                                
                                // Yetim sponsorluk için yeni alanlar - Otomatik eşleşme veya yetim seçimi olduğunda gönder
                                if((alpineData && (alpineData.quickDonationYetimId || alpineData.isAutoMatch)) || 
                                   (donationData.bagis_adi && donationData.bagis_adi.includes('Yetim Sponsorluğu'))) {
                                    
                                    // Otomatik eşleşme flag'ini kontrol et
                                    if(alpineData && alpineData.isAutoMatch) {
                                        ajaxData.otomatik_eslestirme = 1;
                                    } else if(alpineData && alpineData.quickDonationYetimId) {
                                        ajaxData.yetim_id = alpineData.quickDonationYetimId;
                                    }
                                    
                                    // Sponsorluk türü ve ek alanlar
                                    if(alpineData) {
                                        ajaxData.sponsortip = alpineData.sponsorlukTipi ? alpineData.sponsorlukTipi : 'bireysel';
                                        ajaxData.odeme_tipi = alpineData.odemeTipi || 'tek_seferlik';
                                        ajaxData.sponsorluk_suresi_ay = alpineData.sponsorlukAySayisi ? alpineData.sponsorlukAySayisi : null;
                                        ajaxData.odeme_gunu = alpineData.odemeGunu || 1;
                                        
                                        // Grup ve kurumsal için ek alanlar
                                        if(alpineData.sponsorlukTipi === 'grup' && alpineData.grupAdi) {
                                            ajaxData.grup_adi = alpineData.grupAdi;
                                        }
                                        
                                        if(alpineData.sponsorlukTipi === 'kurumsal' && alpineData.kurumUnvani) {
                                            ajaxData.kurum_unvani = alpineData.kurumUnvani;
                                        }
                                        
                                        // Tüm sponsorluk türleri için sorumlu bilgileri
                                        ajaxData.sorumlu_ad_soyad = donationData.ad + ' ' + donationData.soyad;
                                        ajaxData.sorumlu_telefon = donationData.telefon;
                                        ajaxData.sorumlu_email = donationData.email;
                                    }
                                    
                                    ajaxData.sponsorluk_tipi = alpineData.sponsorlukTipi ? alpineData.sponsorlukTipi : 'bireysel';
                                    ajaxData.sponsorluk_suresi_ay = alpineData.sponsorlukAySayisi ? alpineData.sponsorlukAySayisi : null;
                                    ajaxData.odeme_gunu = alpineData.aylikOdemeGunu ? alpineData.aylikOdemeGunu : 1;
                                }
                               
                                // Null olmayan değerleri ekle
                                if(donationData.bagis_id !== null && donationData.bagis_id !== undefined) {
                                    ajaxData.bagis_id = donationData.bagis_id;
                                }
                                if(donationData.bagis_tur_id !== null && donationData.bagis_tur_id !== undefined) {
                                    ajaxData.bagis_tur_id = donationData.bagis_tur_id;
                                }
                                
// Butonu devre dışı bırak - daha güvenli selector
                                const submitButton = document.querySelector('button[onclick="submitDonation()"]') || 
                                                     document.querySelector('button[@click="submitDonation()"]') ||
                                                     document.querySelector('button[type="submit"]') ||
                                                     document.querySelector('button');
                                if (!submitButton) {
                                    console.error('Submit button bulunamadı');
                                    return;
                                }
                                if (submitButton) {
                                    submitButton.disabled = true;
                                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span>İşleniyor...';
                                }
                                
                                $.ajax({
                                     url: '_class/site_islem.php',
                                     type: 'POST',
                                     data: ajaxData,
                                   success: function(response) {
                                        console.log("Sunucu yanıtı:", response);
                                        
                                        // Boş yanıt kontrolü
                                        if (!response || response.trim() === '') {
                                            console.error('Sunucudan boş yanıt geldi');
                                            alert('Bağış işlenirken bir hata oluştu. Lütfen tekrar deneyiniz.');
                                            return;
                                        }
                                        
                                        try {
                                            const result = JSON.parse(response);
                                            console.log("İşlenmiş yanıt:", result);
                                            
                                            if (result.success) {
                                                // Ödeme yöntemine göre yönlendir
                                                if (odemeYontemi === 'paytr') {
                                                    // PayTR URL'i
                                                    const paytrUrl = result.paytr_url || result.redirect_url;
                                                    if (paytrUrl) {
                                                        window.location.href = paytrUrl;
                                                    } else {
                                                        alert('Ödeme yönlendirmesi alınamadı');
if (submitButton) {
                                    submitButton.disabled = false;
                                    submitButton.innerHTML = '<?=@$dil['txt444'];?>';
                                }
                                                    }
                                                } else if (odemeYontemi === 'vakifbank') {
                                                    // Vakıfbank 3DS URL'i
                                                    const vakifbankUrl = result.vakifbank_url || result.redirect_url;
                                                    if (vakifbankUrl) {
                                                        window.location.href = vakifbankUrl;
                                                    } else {
                                                        alert('Ödeme yönlendirmesi alınamadı');
if (submitButton) {
                                    submitButton.disabled = false;
                                    submitButton.innerHTML = '<?=@$dil['txt444'];?>';
                                }
                                                    }
                                                }
                                            } else {
                                                // Hata mesajı göster
                                                const errorMsg = result.message || 'Bağış işlenirken bir hata oluştu';
                                                alert(errorMsg);
                                                submitButton.disabled = false;
                                                submitButton.innerHTML = '<?=@$dil['txt444'];?>';
                                            }
                                        } catch (e) {
                                            console.error('Yanıt işlenirken hata:', e);
                                            alert('Bağış işlenirken bir hata oluştu');
                                            submitButton.disabled = false;
                                            submitButton.innerHTML = '<?=@$dil['txt444'];?>';
                                        }
                                   },
                                   error: function(xhr, status, error) {
                                        console.error('AJAX hatası:', xhr, status, error);
                                        alert('Bağış işlenirken bir bağlantı hatası oluştu');
                                        submitButton.disabled = false;
                                        submitButton.innerHTML = '<?=@$dil['txt444'];?>';
                                   }
                                });
                                
                            } catch (error) {
                                console.error('submitDonation fonksiyonunda hata:', error);
                                alert('Bağış işlenirken bir hata oluştu');
                            }
                        }

                        function changeCurrency(currency) {
                            // Sepet verilerini kaydet
                            if (typeof saveBasketToLocalStorage === 'function') {
                                saveBasketToLocalStorage();
                            } else {
                                var sepetDiv = document.querySelector('[name="sepetdiv"]');
                                if (sepetDiv && sepetDiv.innerHTML.trim() !== '') {
                                    localStorage.setItem('basket_backup', sepetDiv.innerHTML);
                                    localStorage.setItem('basket_backup_time', Date.now().toString());
                                }
                            }
                            
                            // localStorage'a kaydet
                            localStorage.setItem('para_birimi', currency);
                            
                            // AJAX ile session'ı güncelle
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', '/update_currency.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    // Sayfayı yenileme - dinamik güncelleme yap
                                    if (typeof window.updateCurrencyWithoutReload === 'function') {
                                        window.updateCurrencyWithoutReload(currency);
                                    } else {
                                        // Fallback: sayfayı yenile
                                        window.location.reload();
                                    }
                                }
                            };
                            xhr.send('para_birimi=' + currency);
                        }
                        </script>
                    </div>
                </div>
 

                
                <script>
                 document.addEventListener('alpine:init', function() {
                     // Alpine.js global store oluşturma
                     Alpine.store('donation', {
                         donationAmount: '',
                         customAmount: '',
                         customAmountValue: '',
                         fullName: '',
                         phone: '',
                         email: '',
                         agreeTerms: false
                     });
                 });
                 
                 // Bağış türü ismini getir
                 function getDonationTypeName(typeId) {
                     const types = {
                         <?php
                         $bagisTurleriSorgu = $db->prepare("SELECT * FROM bagis_turleri WHERE durum = 1 ORDER BY sira ASC");
                         $bagisTurleriSorgu->execute();
                         $bagisTurleri = $bagisTurleriSorgu->fetchAll(PDO::FETCH_ASSOC);
                         foreach($bagisTurleri as $tur):
                         ?>
                         '<?php echo $tur['id']; ?>': '<?php echo addslashes($tur['adi']); ?>',
                         <?php endforeach; ?>
                     };
                     return types[typeId] || '';
                 }
                 
                 function submitDonation() {
                     try {
                         // DOM elementlerinden direkt değerleri oku (Alpine.js x-model ile bağlı)
                         const fullNameInput = document.getElementById('fullName');
                         const phoneInput = document.getElementById('phone');
                         const emailInput = document.getElementById('email');
                         
                         // Alpine.js verilerine erişim denemesi
                         const sectionElement = document.querySelector('section[x-data]');
                         let alpineData = null;
                         
                         if (sectionElement && window.Alpine) {
                             try {
                                 alpineData = Alpine.$data(sectionElement);
                             } catch(e) {
                                 console.log("Alpine.$data erişilemedi, DOM'dan okunuyor...");
                             }
                         }
                         
                         // Kişisel bilgi kontrolü - DOM'dan direkt oku
                        const fullName = fullNameInput ? fullNameInput.value.trim() : (alpineData && alpineData.fullName ? alpineData.fullName : '');
                        const phone = (alpineData && alpineData.phone) ? alpineData.phone : '';
                        const email = emailInput ? emailInput.value.trim() : (alpineData && alpineData.email ? alpineData.email : '');

                        console.log('Form Değerleri:', { fullName, phone, email }); // Debug için log

                        if (!fullName || !phone || !email) {
                            console.error('Eksik bilgi:', { fullName, phone, email });
                            showNotification('<?=@$dil['txt455'];?>', 'error');
                            return;
                        }
                         
                         // İsim ve soyisim ayırma
                         let firstName = fullName;
                         let lastName = '';
                         
                         if (fullName.includes(' ')) {
                             const nameParts = fullName.split(' ');
                             firstName = nameParts[0];
                             lastName = nameParts.slice(1).join(' ');
                         }
                         
                         // Fiyatı Alpine.js data'sından al (Hemen Bağış Yap butonundan gelen fiyat veya manuel giriş)
                         let tutar = 0;
                         if (alpineData && alpineData.quickDonationFiyat && alpineData.quickDonationFiyat > 0) {
                             tutar = parseFloat(alpineData.quickDonationFiyat);
                         } else if (alpineData && alpineData.customAmountValue && alpineData.customAmountValue > 0) {
                             tutar = parseFloat(alpineData.customAmountValue);
                         }
                         
                         // Para birimini Alpine.js state'inden al (Google Translate uyumu için)
                         const paraBirimi = (alpineData && alpineData.selectedCurrency) ? alpineData.selectedCurrency : '<?= $seciliParaBirimi ?>';
                         
                         const donationType = null; // Bağış türü opsiyonel
                         
                         // Ödeme yöntemini Alpine.js data'sından al
                         const odemeYontemi = alpineData && alpineData.odemeYontemi ? alpineData.odemeYontemi : 'paytr';
                         
                         // Veritabanına kayıt ve PayTR'ye yönlendirme
                         const donationData = {
                             tutar: tutar,
                             ad: firstName,
                             soyad: lastName,
                             telefon: phone,
                             email: email,
                             bagis_id: alpineData && alpineData.quickDonationBagisId ? alpineData.quickDonationBagisId : null,
                             bagis_tur_id: donationType,
                             bagis_tipi: 'hizli_bagis'
                         };
                         
                         console.log("Bağış verileri:", donationData);
                         
                         // AJAX isteği - null değerleri temizle
                         const ajaxData = {
                                   islem: 'hizli_bagis_kaydet',
                                   modul_id: '1',
                                   modul_adi: 'Hızlı Bağış',
                                   ad: donationData.ad,
                                   soyad: donationData.soyad,
                                   email: donationData.email,
                                   telefon: donationData.telefon,
                                   bagis_id_param: alpineData.quickDonationBagisId,
                                   kategori_id: alpineData.currentKategoriId,
                             tutar: tutar,
                                   para_birimi: paraBirimi,
                             bagis_tipi: 'hizli',
                             odeme_yontemi: odemeYontemi,
                             bagis_id_param: donationData.bagis_id,
                             yetim_id: (alpineData && alpineData.quickDonationYetimId) ? alpineData.quickDonationYetimId : null,
                             ulke: alpineData && alpineData.selectedCountry ? alpineData.selectedCountry : '<?= $visitor_country ?>'
                         };
                         
// Yetim sponsorluk için yeni alanlar - Otomatik eşleşme veya yetim seçimi olduğunda gönder
                           if((alpineData && (alpineData.quickDonationYetimId || alpineData.isAutoMatch)) || 
                              (donationData.bagis_adi && donationData.bagis_adi.includes('Yetim Sponsorluğu'))) {
                               
                               // Otomatik eşleşme flag'ini kontrol et
                               if(alpineData && alpineData.isAutoMatch) {
                                   ajaxData.otomatik_eslestirme = 1;
                               } else if(alpineData && alpineData.quickDonationYetimId) {
                                   ajaxData.yetim_id = alpineData.quickDonationYetimId;
                               }
                               
                               // Sponsorluk türü ve ek alanlar
                               if(alpineData) {
                                   ajaxData.sponsortip = alpineData.sponsorlukTipi || 'bireysel';
                                   ajaxData.odeme_tipi = alpineData.odemeTipi || 'tek_seferlik';
                                   ajaxData.sponsorluk_suresi_ay = alpineData.sponsorlukSuresiAy || null;
                                   ajaxData.odeme_gunu = alpineData.odemeGunu || 1;
                                   
                                   // Grup ve kurumsal için ek alanlar
                                   if(alpineData.sponsorlukTipi === 'grup' && alpineData.grupAdi) {
                                       ajaxData.grup_adi = alpineData.grupAdi;
                                   }
                                   
                                   if(alpineData.sponsorlukTipi === 'kurumsal' && alpineData.kurumUnvani) {
                                       ajaxData.kurum_unvani = alpineData.kurumUnvani;
                                   }
                                   
                                   // Tüm sponsorluk türleri için sorumlu bilgileri
                                   ajaxData.sorumlu_ad_soyad = donationData.ad + ' ' + donationData.soyad;
                                   ajaxData.sorumlu_telefon = donationData.telefon;
                                   ajaxData.sorumlu_email = donationData.email;
                               }
                              
                               ajaxData.sponsorluk_tipi = alpineData.sponsorlukTipi ? alpineData.sponsorlukTipi : 'bireysel';
                               ajaxData.sponsorluk_suresi_ay = alpineData.sponsorlukAySayisi ? alpineData.sponsorlukAySayisi : null;
                               ajaxData.odeme_gunu = alpineData.aylikOdemeGunu ? alpineData.aylikOdemeGunu : 1;
                           } else {
                               // Yetim bağışı değilse sponsorluk verilerini gönderme
                               delete ajaxData.sponsorluk_tipi;
                               delete ajaxData.sponsorluk_suresi_ay;
                               delete ajaxData.odeme_gunu;
                               delete ajaxData.grup_adi;
                               delete ajaxData.kurum_unvani;
                               delete ajaxData.sorumlu_ad_soyad;
                               delete ajaxData.sorumlu_telefon;
                               delete ajaxData.sorumlu_email;
                           } else {
                               // Yetim bağışı değilse sponsorluk verilerini gönderme
                               delete ajaxData.sponsorluk_tipi;
                               delete ajaxData.sponsorluk_suresi_ay;
                               delete ajaxData.odeme_gunu;
                               delete ajaxData.grup_adi;
                               delete ajaxData.kurum_unvani;
                               delete ajaxData.sorumlu_ad_soyad;
                               delete ajaxData.sorumlu_telefon;
                               delete ajaxData.sorumlu_email;
                           }
                          
                          // Null olmayan değerleri ekle
                         if(donationData.bagis_id !== null && donationData.bagis_id !== undefined) {
                             ajaxData.bagis_id = donationData.bagis_id;
                         }
                         if(donationData.bagis_tur_id !== null && donationData.bagis_tur_id !== undefined) {
                             ajaxData.bagis_tur_id = donationData.bagis_tur_id;
                         }
                         
                           $.ajax({
                               url: '_class/site_islem.php', // mutlak değil görece path
                               type: 'POST',
                               data: ajaxData,
                             success: function(response) {
                                  console.log("Sunucu yanıtı:", response);
                                  
                                  // Boş yanıt kontrolü
                                  if (!response || response.trim() === '') {
                                      console.error('Sunucudan boş yanıt geldi');
                                      showNotification('<?=@$dil['txt456'];?>', 'error');
                                      return;
                                  }
                                  
                                  try {
                                      const result = JSON.parse(response);
                                      console.log("İşlenmiş yanıt:", result);
                                      
                                      if (result.success) {
                                          // Ödeme yöntemini URL parametresi olarak ekle
                                          const odemeYontemi = alpineData && alpineData.odemeYontemi ? alpineData.odemeYontemi : 'paytr';
                                          // Hızlı yönlendirme: PayTR veya Vakıfbank ödeme sayfası
                                          window.location.href = 'paytr_tekil_bagis.php?bagis_id=' + result.bagis_id + '&odeme_yontemi=' + odemeYontemi;
                                      } else {
                                          console.error("Bağış hatası:", result.message);
                                          showNotification(result.message || '<?=@$dil['txt457'];?>', 'error');
                                      }
                                  } catch (e) {
                                      console.error('JSON parse hatası:', e, response);
                                      // Ödeme yöntemini Alpine.js data'sından al
                                      const odemeYontemi = alpineData && alpineData.odemeYontemi ? alpineData.odemeYontemi : 'paytr';
                                      // Doğrudan PayTR'ye yönlendirme deneyelim
                                      window.location.href = '<?php echo url; ?>/paytr_tekil_bagis.php?tutar=' + donationData.tutar + 
                                          '&ad=' + encodeURIComponent(donationData.ad) + 
                                          '&soyad=' + encodeURIComponent(donationData.soyad) + 
                                          '&email=' + encodeURIComponent(donationData.email) + 
                                          '&telefon=' + encodeURIComponent(phone) +
                                          '&odeme_yontemi=' + encodeURIComponent(odemeYontemi) + 
                                          '&kategori=' + encodeURIComponent(donationData.kategori);
                                  }
                              },
                              error: function(xhr, status, error) {
                                  console.error("AJAX hatası:", status, error);
                                  showNotification('<?=@$dil['txt458'];?>', 'error');
                              }
                         });
                         
                     } catch (error) {
                         console.error('Bağış gönderme hatası:', error);
                         showNotification('<?=@$dil['txt459'];?>' + error.message, 'error');
                     }
                 }
                 
                 function showNotification(message, type) {
                     const notification = document.createElement('div');
                     notification.className = `fixed top-4 right-4 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-4 py-2 rounded-md shadow-lg z-50`;
                     notification.innerHTML = `<div class="flex items-center"><i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle mr-2"></i> ${message}</div>`;
                     document.body.appendChild(notification);
                     
                     setTimeout(() => {
                         notification.remove();
                     }, 3000);
                 }
                 </script>
                


                <!-- Donation Categories Tabs -->
                <?php
                // Tüm kategorileri getir
                $kategoriSorgu = $db->prepare("SELECT * FROM bagis_kategori WHERE dil = ? AND durum = ? ORDER BY sira ASC, id ASC");
                $kategoriSorgu->execute(array($_SESSION['k_dil'], 1));
                $kategoriler = $kategoriSorgu->fetchAll(PDO::FETCH_ASSOC);
                
                // Aktif kategoriyi belirle
                $aktifKategoriId = null;
                if(!empty($kategoriSlug)) {
                    foreach($kategoriler as $kat) {
                        $slug = (isset($kat['slug']) && $kat['slug'] != '' ? $kat['slug'] : (function_exists('cVCLmHLxbS_seo') ? cVCLmHLxbS_seo($kat['adi']) : strtolower(str_replace([' ', 'ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'], ['-', 'i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'], $kat['adi']))));
                        if($slug === $kategoriSlug){
                            $aktifKategoriId = $kat['id'];
                            break;
                        }
                    }
                } else {
                    // İlk kategoriyi aktif yap
                    if(!empty($kategoriler)) {
                        $aktifKategoriId = $kategoriler[0]['id'];
                    }
                }
                ?>
                
                <!-- Modern Kategori Navigasyonu (Sadece genel bağış sayfasında) -->
                <?php if(!$isSeoUrl && !empty($kategoriler)): ?>
                <style>
                    .category-nav {
                        position: relative;
                        background: white;
                        border-radius: 16px;
                        padding: 1rem;
                        margin-bottom: 2rem;
                        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
                    }
                    .category-nav-wrapper {
                        display: flex;
                        gap: 10px;
                        overflow-x: auto;
                        overflow-y: visible;
                        scrollbar-width: thin;
                        scrollbar-color: #14b8a6 #f0f0f0;
                        padding-bottom: 5px;
                        scroll-behavior: smooth;
                    }
.category-scroll-btn {
    position: absolute;
    border-color: unset !important;
    top: 39%;
    transform: translateY(-50%);
    z-index: 10;
    background: #ac2525c2 !important;
    border: 2px solid white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
    color: white;
    font-size: 18px;
}
                    .category-scroll-btn:hover {
                        background: rgba(13, 148, 136, 0.95);
                        transform: translateY(-50%) scale(1.1);
                        box-shadow: 0 6px 16px rgba(20, 184, 166, 0.4);
                    }
                    .category-scroll-btn:active {
                        transform: translateY(-50%) scale(0.95);
                    }
                    .category-scroll-btn.left {
                        left: 8px;
                    }
                    .category-scroll-btn.right {
                        right: 8px;
                    }
                    .category-scroll-btn.hidden {
                        opacity: 0;
                        pointer-events: none;
                    }
                    .category-nav-wrapper::-webkit-scrollbar {
                        height: 6px;
                    }
                    .category-nav-wrapper::-webkit-scrollbar-track {
                        background: #f0f0f0;
                        border-radius: 10px;
                    }
                    .category-nav-wrapper::-webkit-scrollbar-thumb {
                        background: #14b8a6;
                        border-radius: 10px;
                    }
                    .category-item {
                        position: relative;
                        flex-shrink: 0;
                        padding: 10px 20px;
                        background: #f8f9fa;
                        border: 2px solid transparent;
                        border-radius: 12px;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        white-space: nowrap;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                        text-decoration: none;
                        color: #4a5568;
                        font-weight: 600;
                        font-size: 14px;
                    }
                    .category-item:hover {
                        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
                        color: white;
                        transform: translateY(-2px);
                        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
                        border-color: #14b8a6;
                    }
                    .category-item.active {
                        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
                        color: white;
                        border-color: #0d9488;
                        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.4);
                    }
                    .category-item .category-icon {
                        font-size: 16px;
                    }
                    .category-item .category-tooltip {
                        position: absolute;
                        bottom: 100%;
                        left: 50%;
                        transform: translateX(-50%) translateY(-8px);
                        background: #1f2937;
                        color: white;
                        padding: 10px 14px;
                        border-radius: 8px;
                        font-size: 12px;
                        line-height: 1.5;
                        white-space: normal;
                        min-width: 200px;
                        max-width: 280px;
                        opacity: 0;
                        pointer-events: none;
                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        z-index: 1000;
                        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
                        margin-bottom: 8px;
                    }
                    .category-item .category-tooltip::after {
                        content: '';
                        position: absolute;
                        top: 100%;
                        left: 50%;
                        transform: translateX(-50%);
                        border: 6px solid transparent;
                        border-top-color: #1f2937;
                    }
                    .category-item:hover .category-tooltip {
                        opacity: 1;
                        transform: translateX(-50%) translateY(0);
                        pointer-events: auto;
                    }
                    @media (max-width: 640px) {
                        .category-item {
                            padding: 8px 16px;
                            font-size: 13px;
                        }
                        .category-item .category-tooltip {
                            display: none;
                        }
                        .category-scroll-btn {
                            width: 35px;
                            height: 35px;
                            font-size: 14px;
                        }
                        .category-scroll-btn.left {
                            left: 4px;
                        }
                        .category-scroll-btn.right {
                            right: 4px;
                        }
                    }
                </style>
                <div class="category-nav">
                    <!-- Sol Kaydırma Butonu -->
                    <button class="category-scroll-btn left hidden" id="categoryScrollLeft" onclick="scrollCategories('left')" aria-label="Sol kaydır">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <!-- Sağ Kaydırma Butonu -->
                    <button class="category-scroll-btn right hidden" id="categoryScrollRight" onclick="scrollCategories('right')" aria-label="Sağ kaydır">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <div class="category-nav-wrapper" id="categoryNavWrapper">
                        <?php foreach($kategoriler as $kat): 
                            $katSlug = (isset($kat['slug']) && $kat['slug'] != '' ? $kat['slug'] : (function_exists('cVCLmHLxbS_seo') ? cVCLmHLxbS_seo($kat['adi']) : strtolower(str_replace([' ', 'ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'], ['-', 'i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'], $kat['adi']))));
                            $katUrl = $htc['bagisurl'] . '/' . $katSlug . $html;
                            $isActive = ($kat['id'] == $aktifKategoriId);
                        ?>
                        <a href="<?php echo $katUrl; ?>" class="category-item <?php echo $isActive ? 'active' : ''; ?>">
                            <?php if(!empty($kat['icon_class'])): ?>
                                <i class="category-icon <?php echo htmlspecialchars($kat['icon_class']); ?>"></i>
                            <?php elseif(!empty($kat['ikon'])): ?>
                                <img src="<?php echo tema; ?>/uploads/bagis_kategoriler/<?php echo $kat['ikon']; ?>" alt="<?php echo $kat['adi']; ?>" class="category-icon" style="width: 16px; height: 16px; object-fit: contain;">
                            <?php endif; ?>
                            <span><?php echo $kat['adi']; ?></span>
                            <?php if($kat['aciklama'] != ""): ?>
                            <div class="category-tooltip">
                                <?php echo htmlspecialchars($kat['aciklama']); ?>
                            </div>
                            <?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <script>
                // Kategori kaydırma fonksiyonu
                function scrollCategories(direction) {
                    const wrapper = document.getElementById('categoryNavWrapper');
                    if (!wrapper) return;
                    
                    const scrollAmount = 300;
                    const currentScroll = wrapper.scrollLeft;
                    const newScroll = direction === 'left' 
                        ? currentScroll - scrollAmount 
                        : currentScroll + scrollAmount;
                    
                    wrapper.scrollTo({
                        left: newScroll,
                        behavior: 'smooth'
                    });
                    
                    // Buton görünürlüğünü kontrol et
                    setTimeout(checkCategoryScrollButtons, 100);
                }
                
                // Kaydırma butonlarının görünürlüğünü kontrol et
                function checkCategoryScrollButtons() {
                    const wrapper = document.getElementById('categoryNavWrapper');
                    const leftBtn = document.getElementById('categoryScrollLeft');
                    const rightBtn = document.getElementById('categoryScrollRight');
                    
                    if (!wrapper || !leftBtn || !rightBtn) return;
                    
                    const scrollLeft = wrapper.scrollLeft;
                    const scrollWidth = wrapper.scrollWidth;
                    const clientWidth = wrapper.clientWidth;
                    
                    // Sol buton görünürlüğü
                    if (scrollLeft > 10) {
                        leftBtn.classList.remove('hidden');
                    } else {
                        leftBtn.classList.add('hidden');
                    }
                    
                    // Sağ buton görünürlüğü
                    if (scrollLeft < scrollWidth - clientWidth - 10) {
                        rightBtn.classList.remove('hidden');
                    } else {
                        rightBtn.classList.add('hidden');
                    }
                }
                
                // Sayfa yüklendiğinde ve scroll olduğunda butonları kontrol et
                document.addEventListener('DOMContentLoaded', function() {
                    const wrapper = document.getElementById('categoryNavWrapper');
                    if (wrapper) {
                        wrapper.addEventListener('scroll', checkCategoryScrollButtons);
                        checkCategoryScrollButtons();
                        
                        // Resize event'inde de kontrol et
                        window.addEventListener('resize', function() {
                            setTimeout(checkCategoryScrollButtons, 100);
                        });
                    }
                });
                </script>
                <?php endif; ?>
                
<?php if($isSeoUrl): ?>
<div class="mb-10 text-center">
    <a href="<?php echo $htc['bagisurl'].$html; ?>" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-slate-200 hover:border-teal-500 hover:bg-teal-50 text-slate-600 hover:text-teal-700 font-bold rounded-xl transition-all duration-200 group no-underline">
        <i class="fas fa-th-large text-lg opacity-70 group-hover:opacity-100"></i>
        <span class="text-base tracking-wide"><?=@$dil['txt460'] ?: 'Tüm Kategorileri Göster';?></span>
        <i class="fas fa-chevron-right text-sm group-hover:translate-x-1 transition-transform"></i>
    </a>
</div>
<?php endif; ?>
                
                
                <!-- Bağış Cards Content -->
                <?php 
                // SEO URL ile çağrıldıysa sadece ilgili kategoriyi göster
                $gosterilecekKategoriler = $kategoriler;
                if($isSeoUrl && !empty($aktifKategoriId)) {
                    // Sadece aktif kategoriyi göster
                    $gosterilecekKategoriler = array_filter($kategoriler, function($kat) use ($aktifKategoriId) {
                        return $kat['id'] == $aktifKategoriId;
                    });
                }
                
                // Tüm bağışları topla
                $tumBagislar = [];
                foreach($gosterilecekKategoriler as $kategori) {
                    $bagisSorgu = $db->prepare("SELECT * FROM bagislar WHERE dil = ? and durum = ? and kategori = ? order by id asc");
                    $bagisSorgu->execute(array($_SESSION['k_dil'], 1, $kategori['id']));
                    $kategoriBagislari = $bagisSorgu->fetchAll(PDO::FETCH_ASSOC);
                    foreach($kategoriBagislari as $bagis) {
                        $bagis['kategori_adi'] = $kategori['adi'];
                        $tumBagislar[] = $bagis;
                    }
                }
                ?>
                    
                    <!-- Resimdeki Tasarıma Uygun Bağış Cards -->
                    <style>
                        .donation-card-link {
                            text-decoration: none;
                            color: inherit;
                            display: flex;
                            height: 100%;
                        }
                        .donation-card-link:hover {
                            text-decoration: none;
                            color: inherit;
                        }
                        .donation-card {
                            transition: all 0.3s ease;
                            background: white;
                            border-radius: 12px;
                            overflow: hidden;
                            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                            cursor: pointer;
                            display: flex;
                            flex-direction: column;
                            width: 100%;
                            height: 100%;
                        }
                        .donation-card:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
                        }
                        .card-image-container {
                            position: relative;
                            width: 100%;
                            height: 160px;
                            overflow: hidden;
                        }
                        .donation-card .card-image {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }
                        .donation-card .card-content {
                            padding: 0.875rem;
                            text-align: center;
                            display: flex;
                            flex-direction: column;
                            flex-grow: 1;
                        }
                        .donation-card .card-content > div:last-child {
                            margin-top: auto;
                        }
                        .donation-card .card-title {
                            font-size: 0.95rem;
                            font-weight: 700;
                            color: #1a202c;
                            margin-bottom: 0.75rem;
                            line-height: 1.3;
                        }
                        .donation-card .price-input-wrapper {
                            position: relative;
                            margin-bottom: 0.625rem;
                        }
                        .donation-card .price-input-wrapper input {
                            width: 100%;
                            padding: 8px 35px 8px 10px;
                            border: 1px solid #e2e8f0;
                            border-radius: 6px;
                            font-size: 14px;
                            font-weight: 600;
                            color: #2d3748;
                            text-align: left;
                            background: white;
                            transition: all 0.2s ease;
                        }
                        .donation-card .price-input-wrapper input:focus {
                            outline: none;
                            border-color: #14b8a6;
                            background: white;
                            box-shadow: 0 0 0 2px rgba(20, 184, 166, 0.1);
                        }
                        .donation-card .price-input-wrapper .currency-icon {
                            position: absolute;
                            right: 10px;
                            top: 50%;
                            transform: translateY(-50%);
                            color: #14b8a6;
                            font-size: 13px;
                            pointer-events: none;
                            font-weight: 700;
                        }
                        .donation-card .quick-amount-buttons {
                            display: flex;
                            gap: 6px;
                            margin-bottom: 0.625rem;
                            flex-wrap: wrap;
                            justify-content: center;
                        }
                        .donation-card .quick-amount-btn {
                            flex: 1;
                            min-width: 0;
                            padding: 8px 10px;
                            border: 1.5px solid #14b8a6;
                            border-radius: 6px;
                            background: white;
                            color: #14b8a6;
                            font-size: 12px;
                            font-weight: 700;
                            cursor: pointer;
                            transition: all 0.2s ease;
                            text-align: center;
                            white-space: nowrap;
                        }
                        .donation-card .quick-amount-btn:hover {
                            background: #14b8a6;
                            color: white;
                            transform: translateY(-1px);
                            box-shadow: 0 2px 6px rgba(20, 184, 166, 0.3);
                        }
                        .donation-card .quick-amount-btn:active {
                            transform: translateY(0);
                        }
                        .donation-card .donate-button {
                            width: 100%;
                            padding: 10px 14px;
                            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
                            color: white;
                            border: none;
                            border-radius: 6px;
                            font-size: 13px;
                            font-weight: 700;
                            cursor: pointer;
                            transition: all 0.3s ease;
                            box-shadow: 0 2px 8px rgba(20, 184, 166, 0.3);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 5px;
                        }
                        .donation-card .donate-button:hover {
                            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
                            transform: translateY(-1px);
                            box-shadow: 0 4px 12px rgba(20, 184, 166, 0.4);
                        }
                        .donation-card .donate-button:active {
                            transform: translateY(0);
                        }
                        .donation-card .donate-button i {
                            font-size: 13px;
                        }
                        .donation-card .quick-button {
                            width: 38px;
                            padding: 10px;
                            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                            color: white;
                            border: none;
                            border-radius: 6px;
                            font-size: 13px;
                            font-weight: 700;
                            cursor: pointer;
                            transition: all 0.3s ease;
                            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-shrink: 0;
                        }
                        .donation-card .quick-button:hover {
                            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
                            transform: translateY(-1px);
                            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
                        }
                        .donation-card .quick-button:active {
                            transform: translateY(0);
                        }
                        .donation-card .quick-button i {
                            font-size: 13px;
                        }
                        @media (max-width: 640px) {
                            .card-image-container {
                                height: 140px;
                            }
                            .donation-card .card-content {
                                padding: 0.75rem;
                                background: #f4921e !important;
                            }
                            .donation-card {
                                background: #f4921e !important;
                            }
                            .donation-card .card-title {
                                font-size: 1rem;
                                font-weight: 800;
                                margin-bottom: 0.625rem;
                                color: #fff !important;
                            }
                            .donation-card .price-input-wrapper input {
                                padding: 10px 35px 10px 12px;
                                font-size: 18px;
                                font-weight: 800;
                                background: #fff;
                                color: #1a202c;
                            }
                            .donation-card .price-input-wrapper .currency-icon {
                                font-size: 18px;
                                font-weight: 800;
                            }
                            .donation-card .quick-amount-btn {
                                padding: 8px 10px;
                                font-size: 14px;
                                font-weight: 800;
                            }
                            .donation-card .donate-button {
                                padding: 12px 16px;
                                font-size: 15px;
                                font-weight: 800;
                            }
                            .donation-card .quick-button {
                                width: 36px;
                                padding: 9px;
                            }
                        }
                        @media (min-width: 641px) and (max-width: 1024px) {
                            .card-image-container {
                                height: 150px;
                            }
                        }
                    </style>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-4 md:gap-5 max-w-7xl mx-auto" style="grid-auto-rows: minmax(auto, 1fr);">
<?php 
foreach ($tumBagislar as $Sonuc):
    // Veritabanındaki miktar TRY cinsinden, seçili para birimine çevir
    $miktar = $Sonuc['miktar'];
    if ($seciliParaBirimi != 'TRY') {
        $miktar = ExchangeRates::convert($miktar, 'TRY', $seciliParaBirimi, $kurlar);
    }

    // Bağış slug'ı
    if (!empty($Sonuc['seo'])) {
        $bagisSlug = $Sonuc['seo'];
    } else {
        $bagisSlug = function_exists('cVCLmHLxbS_seo')
            ? cVCLmHLxbS_seo($Sonuc['adi'])
            : strtolower(str_replace(
                [' ', 'ı','ğ','ü','ş','ö','ç','İ','Ğ','Ü','Ş','Ö','Ç'],
                ['-','i','g','u','s','o','c','i','g','u','s','o','c'],
                $Sonuc['adi']
            ));
    }

    // URL kontrolü
    $bagisUrl = (isset($Sonuc['detay_linki']) && $Sonuc['detay_linki'] == 1) ? 'bagis-detay/' . $bagisSlug . $html : 'javascript:;';

    // --- YENİ: SABİT FİYAT KONTROLÜ ---
$isFixedPrice = (isset($Sonuc['degismeyen_fiyat']) && ($Sonuc['degismeyen_fiyat'] == 1 || $Sonuc['degismeyen_fiyat'] == '1'));
?>
    <div class="donation-card" onclick="window.location.href='<?php echo $bagisUrl; ?>'">
        <div class="card-image-container">
            <img src="<?php echo tema;?>/uploads/bagislar/<?php echo $Sonuc['kapak']; ?>" 
                 class="card-image" 
                 alt="<?php echo $Sonuc['adi']; ?>">
        </div>
        
        <div class="card-content">
            <h3 class="card-title"><?php echo $Sonuc['adi']; ?></h3>
            
            <?php
            // Para birimine göre minimum değer
            $inputMinDeger = ($seciliParaBirimi == 'USD') ? 10 : (($seciliParaBirimi == 'EUR') ? 5 : 1);
            ?>
            <div class="price-input-wrapper" onclick="event.stopPropagation();">
<input 
    type="number" 
    id="fiyat_<?php echo $Sonuc['id']; ?>" 
    data-id="<?php echo $Sonuc['id']; ?>"
    data-birim-fiyat="<?php echo $miktar; ?>"
    data-para-birimi="<?php echo $seciliParaBirimi; ?>"
    name="fiyat" 
    value="<?php echo $miktar; ?>"
    min="<?php echo $inputMinDeger; ?>"
    step="0.01"
    placeholder="Bağış tutarı"
    <?php if($isFixedPrice === true): ?>
        readonly 
        style="background-color: #f3f4f6; cursor: not-allowed; color: #6b7280; font-weight: bold;"
    <?php else: ?>
        onchange="updatePriceManually('<?php echo $Sonuc['id']; ?>'); validateMinAmount('<?php echo $Sonuc['id']; ?>', '<?php echo $seciliParaBirimi; ?>');"
    <?php endif; ?>
>
                <span class="currency-icon" data-para-birimi="<?php echo $seciliParaBirimi; ?>"><?= $paraBirimiSembolu ?></span>
            </div>
            
            <div class="quick-amount-buttons" onclick="event.stopPropagation();">
                <?php if(!$isFixedPrice): ?>
                    <?php
                    $hazirSecenekler = ($seciliParaBirimi == 'TRY') ? [500, 1000, 2000] : [10, 20, 50];
                    foreach($hazirSecenekler as $secenek): 
                    ?>
                        <button 
                            type="button" 
                            class="quick-amount-btn"
                            onclick="setQuickAmount('<?php echo $Sonuc['id']; ?>', <?php echo $secenek; ?>)"
                        >
                            <?php echo number_format($secenek, 0, ',', '.'); ?> <?= $paraBirimiSembolu ?>
                        </button>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="flex flex-col gap-4 w-full mt-4" style="margin-top: auto;" onclick="event.stopPropagation();">
                <?php if(isset($Sonuc['yetim_bagisi']) && $Sonuc['yetim_bagisi'] == 1): ?>
                    <button 
                        type="button" 
                        onclick="openQuickDonationWithPrice('<?php echo $Sonuc['id']; ?>', 'Yetim Sponsorluğu', document.getElementById('fiyat_<?php echo $Sonuc['id']; ?>').value, true)"
                        class="group relative w-full flex items-center justify-between bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white rounded-2xl p-1 shadow-lg shadow-teal-200 transition-all duration-300 hover:shadow-teal-300 hover:-translate-y-1"
                    >
                        <div class="bg-white/20 rounded-xl p-3 backdrop-blur-sm group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-child text-2xl animate-pulse"></i> 
                        </div>
                        <div class="flex-1 text-center pr-12"> 
                            <div class="text-lg font-bold tracking-wide">
                                <?php echo (isset($_SESSION['k_dil']) && $_SESSION['k_dil'] == 3) ? 'كن كفيلاً' : 'Sponsor Ol'; ?>
                            </div>
                            <div class="text-xs text-teal-100 font-medium">
                                <?php echo (isset($_SESSION['k_dil']) && $_SESSION['k_dil'] == 3) ? 'رعاية مباشرة' : 'Direk Sponsorluk'; ?>
                            </div>
                        </div>
                    </button>
                <?php else: ?>
                    <button 
                        type="button" 
                        onclick="openQuickDonationWithPrice('<?php echo $Sonuc['id']; ?>', '<?php echo addslashes($Sonuc['adi']); ?>', document.getElementById('fiyat_<?php echo $Sonuc['id']; ?>').value)"
                        class="group relative w-full flex items-center justify-between bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white rounded-2xl p-1 shadow-lg shadow-red-200 transition-all duration-300 hover:shadow-red-300 hover:-translate-y-1"
                    >
                        <div class="bg-white/20 rounded-xl p-3 backdrop-blur-sm group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-heart text-2xl animate-pulse"></i> 
                        </div>
                        <div class="flex-1 text-center pr-12"> 
                            <div class="text-lg font-bold tracking-wide">
                                <?php echo (isset($_SESSION['k_dil']) && $_SESSION['k_dil'] == 3) ? 'تبرع الآن' : @$dil['txt461']; ?>
                            </div>
                            <div class="text-xs text-red-100 font-medium"><?=@$dil['txt462'];?></div>
                        </div>
                    </button>

                    <button 
                        type="button" 
                        onclick="addBasket('<?php echo $Sonuc['id']; ?>', (window.Alpine ? Alpine.$data(document.querySelector('section[x-data]')).selectedCurrency : '<?= $seciliParaBirimi ?>'), document.getElementById('fiyat_<?php echo $Sonuc['id']; ?>').value, 1);" 
                        class="w-full flex items-center justify-center gap-3 bg-gray-50 text-gray-600 hover:bg-white border border-gray-200 hover:border-gray-300 hover:text-gray-800 font-semibold py-3.5 rounded-xl transition-all duration-200 active:scale-95"
                    >
                        <i class="fas fa-plus text-sm"></i>
                        <span><?=@$dil['txt463'];?></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
                    </div>
            </div>

            <!-- Sidebar -->
            <div class="w-full md:w-1/4">
                <!-- Donation Cart -->
                <style>
                    .sidebar-card {
                        background: white;
                        border-radius: 16px;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                        padding: 1.5rem;
                        margin-bottom: 1.5rem;
                        position: sticky;
                        top: 1rem;
                    }
                    .quick-donation-btn {
                        width: 100%;
                        padding: 16px 24px;
                        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
                        color: white;
                        border: none;
                        border-radius: 10px;
                        font-size: 16px;
                        font-weight: 700;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 8px;
                    }
                    .quick-donation-btn:hover {
                        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
                        transform: translateY(-2px);
                        box-shadow: 0 6px 16px rgba(20, 184, 166, 0.4);
                    }
                    .sidebar-title {
                        font-size: 1.25rem;
                        font-weight: 700;
                        color: #1a202c;
                        margin-bottom: 1.25rem;
                        padding-bottom: 0.75rem;
                        border-bottom: 2px solid #e2e8f0;
                    }
                </style>
                <div style=" z-index: 3; " class="sidebar-card">
                    <h3 class="sidebar-title"><?=@$dil['txt113'];?></h3>
                    <div class="basket-card" name="sepetdiv"></div>
                    

                </div>
                
                <!-- Security Badge -->
                <div style=" position: relative; " class="sidebar-card">
                    <img class="w-full rounded-lg" src="https://www.paytr.com/img/odeme_sayfasi/os_kartlar.png" alt="Kart Güvenliği">
                    <p class="text-xs text-gray-600 mt-3 text-center leading-relaxed"><?=@$dil['txt465'];?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom JavaScript for Currency Handling -->
<script>
// Mevcut sepete ekleme fonksiyonunu güncelleme
function addBasket(id, currency, amount, adet) {
    // Adet parametresi yoksa varsayılan olarak 1 kullan
    adet = adet || 1;
    
    $.ajax({
        type: "POST",
        url: "_class/site_islem.php",
        data: { 
            'sepet_ekle': '1',
            'bagis_id': id, 
            'bagis_adi': $('#fiyat_' + id).closest('.bg-white').find('h3').text() || $('#fiyat_' + id).closest('.donation-card').find('h3').text(),
            'bagis_tutar': amount,
            'bagis_adet': adet,
            'para_birimi': currency
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Sepeti güncelle
                $.ajax({
                    type: "POST",
                    url: "<?php echo url; ?>/sepete-ekle.php",
                    data: { 'sepet_goster': '1' },
                    success: function(data) {
                        $('[name="sepetdiv"]').html(data);
                    }
                });
                
                // Başarılı bildirim göster
                showNotification(response.message, 'success');
            } else {
                // Hata bildirimi göster
                showNotification(response.message, 'error');
            }
        },
        error: function() {
            // Hata bildirimi göster
            showNotification('<?=@$dil['txt466'];?>', 'error');
        }
    });
}

// Kart numarası formatı
function formatCardNumber(input) {
    // Sadece rakamları al
    let value = input.value.replace(/\D/g, '');
    
    // 16 karakterden fazlasını kes
    if (value.length > 16) {
        value = value.slice(0, 16);
    }
    
    // Her 4 rakamdan sonra boşluk ekle
    let formattedValue = '';
    for (let i = 0; i < value.length; i++) {
        if (i > 0 && i % 4 === 0) {
            formattedValue += ' ';
        }
        formattedValue += value[i];
    }
    
    // Formatlanmış değeri input'a ata
    input.value = formattedValue;
}

// Son kullanma tarihi formatı
function formatExpiryDate(input) {
    // Sadece rakamları al
    let value = input.value.replace(/\D/g, '');
    
    // 4 karakterden fazlasını kes
    if (value.length > 4) {
        value = value.slice(0, 4);
    }
    
    // Ay/Yıl formatı
    if (value.length > 2) {
        value = value.slice(0, 2) + '/' + value.slice(2);
    }
    
    // Formatlanmış değeri input'a ata
    input.value = value;
}

// CVC formatı
function formatCVC(input) {
    // Sadece rakamları al
    let value = input.value.replace(/\D/g, '');
    
    // 3 veya 4 karakterden fazlasını kes
    if (value.length > 4) {
        value = value.slice(0, 4);
    }
    
    // Formatlanmış değeri input'a ata
    input.value = value;
}

// Telefon numarası formatı
function formatPhoneNumber(input) {
    // Sadece rakamları al
    let value = input.value.replace(/\D/g, '');
    
    // 11 karakterden fazlasını kes
    if (value.length > 11) {
        value = value.slice(0, 11);
    }
    
    // Türk telefon numarası formatı: 05XX XXX XX XX
    let formattedValue = '';
    for (let i = 0; i < value.length; i++) {
        if (i === 3 || i === 6 || i === 8) {
            formattedValue += ' ';
        }
        formattedValue += value[i];
    }
    
    // Formatlanmış değeri input'a ata
    input.value = formattedValue;
}

// Bildirim gösterme fonksiyonu
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-4 py-2 rounded-md shadow-lg z-50`;
    notification.innerHTML = `<div class="flex items-center"><i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle mr-2"></i> ${message}</div>`;
    document.body.appendChild(notification);
    
    // 3 saniye sonra bildirimi kaldır
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Adet artırma fonksiyonu
function increaseQuantity(bagisId) {
    const adetInput = document.getElementById('adet_' + bagisId);
    const currentValue = parseInt(adetInput.value) || 1;
    adetInput.value = currentValue + 1;
    updateTotalPrice(bagisId);
}

// Adet azaltma fonksiyonu
function decreaseQuantity(bagisId) {
    const adetInput = document.getElementById('adet_' + bagisId);
    const currentValue = parseInt(adetInput.value) || 1;
    if (currentValue > 1) {
        adetInput.value = currentValue - 1;
        updateTotalPrice(bagisId);
    }
}

// Toplam fiyatı güncelleme fonksiyonu (Birim Fiyat x Adet)
function updateTotalPrice(bagisId) {
    const fiyatInput = document.getElementById('fiyat_' + bagisId);
    const adetInput = document.getElementById('adet_' + bagisId);
    
    if (fiyatInput && adetInput) {
        const birimFiyat = parseFloat(fiyatInput.getAttribute('data-birim-fiyat')) || 0;
        const adet = parseInt(adetInput.value) || 1;
        const toplamFiyat = birimFiyat * adet;
        fiyatInput.value = toplamFiyat.toFixed(2);
    }
}

// Fiyat manuel değiştirildiğinde birim fiyatı güncelle
function updatePriceManually(bagisId) {
    const fiyatInput = document.getElementById('fiyat_' + bagisId);
    const adetInput = document.getElementById('adet_' + bagisId);
    
    if (fiyatInput && adetInput) {
        const yeniFiyat = parseFloat(fiyatInput.value) || 0;
        const adet = parseInt(adetInput.value) || 1;
        const yeniBirimFiyat = yeniFiyat / adet;
        fiyatInput.setAttribute('data-birim-fiyat', yeniBirimFiyat.toFixed(2));
    }
}

// Global fonksiyonlar - Alpine.js dışında tanımlanmalı
function updatePriceManually(bagisId) {
    const fiyatInput = document.getElementById('fiyat_' + bagisId);
    const adetInput = document.getElementById('adet_' + bagisId);
    
    if (fiyatInput && adetInput) {
        const yeniFiyat = parseFloat(fiyatInput.value) ||0;
        const adet = parseInt(adetInput.value) || 1;
        const yeniBirimFiyat = yeniFiyat / adet;
        fiyatInput.setAttribute('data-birim-fiyat', yeniBirimFiyat.toFixed(2));
    }
}

// Hızlı miktar seçimi fonksiyonu
function setQuickAmount(bagisId, amount) {
    const fiyatInput = document.getElementById('fiyat_' + bagisId);
    if (fiyatInput) {
        fiyatInput.value = amount;
        // Birim fiyatı güncelle
        fiyatInput.setAttribute('data-birim-fiyat', amount);
        // Input değişikliğini tetikle
        if (fiyatInput.onchange) {
            fiyatInput.onchange();
        }
        // Event dispatch
        const event = new Event('change', { bubbles: true });
        fiyatInput.dispatchEvent(event);
    }
}

// Hızlı bağış modal'ını aç
function openQuickDonationWithPrice(bagisId, bagisAdi, fiyat, isAutoMatch = false) {
    // Alpine.js store'a erişim
    const sectionElement = document.querySelector('section[x-data]');
    if (sectionElement && window.Alpine) {
        const alpineData = Alpine.$data(sectionElement);
        
        // Fiyatı parse et (string'den number'a çevir)
        const bagisFiyati = parseFloat(fiyat) || 0;
        
        // Modal'ı aç
        alpineData.showQuickDonation = true;
        alpineData.quickDonationBagisId = bagisId;
        alpineData.quickDonationBagisAdi = bagisAdi;
        alpineData.currentStep = 1;
        
        // Fiyatı kaydet (submitDonation'da kullanılacak)
        alpineData.quickDonationFiyat = bagisFiyati;
        
        // Otomatik eşleşme flag'ini set et
        alpineData.isAutoMatch = isAutoMatch;
        
        // Yetim bağışı kontrolü
        alpineData.isYetimBagis = bagisAdi.includes('Yetim Sponsorluğu') || isAutoMatch;
        
        // Yetim bağışı değilse ve otomatik eşleşme değilse yetim ID'sini sıfırla
        if (!isAutoMatch) {
            alpineData.quickDonationYetimId = null;
        }
        alpineData.selectedYetim = null;
        
        // Alanları temizle
        alpineData.donationAmount = '';
        alpineData.customAmountValue = '';
        alpineData.donationType = '';
        alpineData.fullName = '';
        alpineData.phone = '';
        alpineData.email = '';
    }
}

// Adet değiştiğinde fiyatı otomatik güncelle
function initAdetFiyatHesaplama() {
    document.querySelectorAll('[id^="adet_"]').forEach(function(adetInput) {
        const bagisId = adetInput.id.replace('adet_', '');
        const fiyatInput = document.getElementById('fiyat_' + bagisId);
        
        if(fiyatInput && adetInput) {
            // Eğer zaten event listener eklenmişse, tekrar ekleme
            if(adetInput.hasAttribute('data-listener-added')) {
                return;
            }
            
            const birimFiyat = parseFloat(fiyatInput.getAttribute('data-birim-fiyat')) || parseFloat(fiyatInput.value) || 0;
            
            // Birim fiyatı data attribute'a kaydet
            if(!fiyatInput.getAttribute('data-birim-fiyat') && birimFiyat > 0) {
                fiyatInput.setAttribute('data-birim-fiyat', birimFiyat);
            }
            
            function updateFiyat() {
                const adet = parseFloat(adetInput.value) || 1;
                const birimFiyatValue = parseFloat(fiyatInput.getAttribute('data-birim-fiyat')) || birimFiyat;
                const toplamFiyat = birimFiyatValue * adet;
                fiyatInput.value = toplamFiyat.toFixed(2);
            }
            
            adetInput.addEventListener('input', updateFiyat);
            adetInput.addEventListener('change', updateFiyat);
            adetInput.setAttribute('data-listener-added', 'true');
        }
    });
}

// Para birimi güncelleme (sayfa yenilemeden)
window.updateCurrencyWithoutReload = function(newCurrency) {
    try {
        // Para birimi sembolleri
        const currencySymbols = {
            'TRY': '₺',
            'USD': '$',
            'EUR': '€'
        };
        
        const newSymbol = currencySymbols[newCurrency] || '₺';
        
        // Currency selector butonunu güncelle
        const currencyBtn = document.querySelector('.currency-selector-btn span');
        if (currencyBtn) {
            currencyBtn.textContent = newSymbol + ' ' + newCurrency;
        }

        // Alpine.js state'ini güncelle
        const sectionElement = document.querySelector('section[x-data]');
        if (sectionElement && window.Alpine) {
            try {
                const alpineData = Alpine.$data(sectionElement);
                if (alpineData) {
                    alpineData.selectedCurrency = newCurrency;
                }
            } catch(e) {
                console.log('Alpine data update error:', e);
            }
        }
        
        // Döviz kurlarını al ve fiyatları güncelle
        updateDonationPricesWithCurrency(newCurrency);
        
        // Sepeti yeniden yükle (sidebar card'ı koruyarak)
        var sepetDiv = document.querySelector('[name="sepetdiv"]');
        if (sepetDiv) {
            // Sepet div'inin parent'ını kontrol et (sidebar card)
            var sidebarCard = sepetDiv.closest('.sidebar-card');
            if (!sidebarCard) {
                // Sidebar card yoksa, normal yükleme yap
                if (typeof basketReload === 'function') {
                    setTimeout(function() {
                        basketReload();
                    }, 300);
                } else {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo url; ?>/sepete-ekle.php",
                        data: { 'sepet_goster': '1' },
                        success: function(data) {
                            if (data && data.trim() !== '') {
                                sepetDiv.innerHTML = data;
                            }
                        }
                    });
                }
            } else {
                // Sidebar card var, sadece sepet içeriğini güncelle
                if (typeof basketReload === 'function') {
                    setTimeout(function() {
                        basketReload();
                    }, 300);
                } else {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo url; ?>/sepete-ekle.php",
                        data: { 'sepet_goster': '1' },
                        success: function(data) {
                            if (data && data.trim() !== '' && sepetDiv) {
                                sepetDiv.innerHTML = data;
                            }
                        },
                        error: function() {
                            console.log('Sepet yükleme hatası');
                        }
                    });
                }
            }
        }
    } catch(e) {
        console.log('Para birimi güncelleme hatası:', e);
    }
};

// Bağış sayfasındaki fiyatları para birimine göre güncelle
window.updateDonationPagePrices = function(newCurrency) {
    updateDonationPricesWithCurrency(newCurrency);
};

// Döviz kurları (PHP'den gelen)
const EXCHANGE_RATES = {
    'USD': <?php echo isset($kurlar['USD']) && $kurlar['USD'] > 0 ? $kurlar['USD'] : 30; ?>,
    'EUR': <?php echo isset($kurlar['EUR']) && $kurlar['EUR'] > 0 ? $kurlar['EUR'] : 32; ?>
};

// Minimum değer kontrolü
function validateMinAmount(bagisId, currency) {
    const input = document.getElementById('fiyat_' + bagisId);
    if (!input) return;
    
    let minValue = 1;
    if (currency === 'USD') {
        minValue = 10;
    } else if (currency === 'EUR') {
        minValue = 5;
    }
    
    const currentValue = parseFloat(input.value) || 0;
    if (currentValue < minValue) {
        input.value = minValue;
        input.setAttribute('data-birim-fiyat', minValue);
    }
    
    // Min attribute'u güncelle
    input.setAttribute('min', minValue);
    input.setAttribute('data-para-birimi', currency);
}

// Döviz kurları ile fiyatları güncelle
function updateDonationPricesWithCurrency(newCurrency) {
    // Mevcut para birimini al
    const currentCurrency = '<?= $seciliParaBirimi ?>';
    
    if (currentCurrency === newCurrency) {
        return; // Aynı para birimiyse güncelleme yapma
    }
    
    // Döviz kurlarını kullan
    const exchangeRates = EXCHANGE_RATES;
    
    // Para birimi sembolleri
    const currencySymbols = {
        'TRY': '₺',
        'USD': '$',
        'EUR': '€'
    };
    
    // Minimum değerler
    const minValues = {
        'TRY': 1,
        'USD': 10,
        'EUR': 5
    };
    
    // Hazır seçenekler
    const quickAmounts = {
        'TRY': [500, 1000, 2000],
        'USD': [10, 20, 50],
        'EUR': [10, 20, 50]
    };
    
    const newSymbol = currencySymbols[newCurrency] || '₺';
    const newMinValue = minValues[newCurrency] || 1;
    
document.querySelectorAll('input[data-id][data-birim-fiyat]').forEach(function(input) {
    const birimFiyat = parseFloat(input.getAttribute('data-birim-fiyat')) || 0;
    const oldCurrency = input.getAttribute('data-para-birimi') || currentCurrency;
    
    if (birimFiyat > 0) {
        let yeniFiyat = birimFiyat;
        
        if (oldCurrency === 'TRY' && newCurrency !== 'TRY') {
            const rate = exchangeRates[newCurrency] || 1;
            yeniFiyat = birimFiyat / rate;
        }
        else if (oldCurrency !== 'TRY' && newCurrency === 'TRY') {
            const rate = exchangeRates[oldCurrency] || 1;
            yeniFiyat = birimFiyat * rate;
        }
        else if (oldCurrency !== 'TRY' && newCurrency !== 'TRY') {
            const currentRate = exchangeRates[oldCurrency] || 1;
            const newRate = exchangeRates[newCurrency] || 1;
            yeniFiyat = (birimFiyat * currentRate) / newRate;
        }
        
        if (!input.hasAttribute('readonly')) {
            if (yeniFiyat < newMinValue) {
                yeniFiyat = newMinValue;
            }
        }
        
        input.value = yeniFiyat.toFixed(2);
        input.setAttribute('data-birim-fiyat', yeniFiyat.toFixed(2));
        input.setAttribute('data-para-birimi', newCurrency);
        input.setAttribute('min', newMinValue);
        
        // Sabit fiyatlı (readonly) alanların görsel stilini koru
        if (input.hasAttribute('readonly')) {
            input.style.backgroundColor = '#f3f4f6';
            input.style.cursor = 'not-allowed';
            input.style.color = '#6b7280';
        }
        
        const currencyIcon = input.parentElement.querySelector('.currency-icon');
        if (currencyIcon) {
            currencyIcon.textContent = newSymbol;
            currencyIcon.setAttribute('data-para-birimi', newCurrency);
        }
    }
});
    
    // Hazır seçenek butonlarını güncelle
    const quickAmountBtns = document.querySelectorAll('.quick-amount-btn');
    const newAmounts = quickAmounts[newCurrency] || [500, 1000, 2000];
    
    // Her bağış kartı için butonları güncelle
    document.querySelectorAll('.donation-card').forEach(function(card) {
        const fiyatInput = card.querySelector('input[data-id]');
        if (!fiyatInput) return;
        
        const bagisId = fiyatInput.getAttribute('data-id');
        const cardButtons = card.querySelectorAll('.quick-amount-btn');
        
        cardButtons.forEach(function(btn, index) {
            if (index < newAmounts.length) {
                const amount = newAmounts[index];
                btn.textContent = amount + ' ' + newSymbol;
                btn.setAttribute('onclick', "event.stopPropagation(); setQuickAmount('" + bagisId + "', " + amount + ")");
            }
        });
    });
}

// Sepet geri yükleme fonksiyonu
function restoreBasketOnPageLoad() {
    try {
        var sepetDiv = document.querySelector('[name="sepetdiv"]');
        if (!sepetDiv) {
            // Sepet div'i yoksa, normal yükleme yap
            setTimeout(function() {
                if (typeof basketReload === 'function') {
                    basketReload();
                }
            }, 500);
            return;
        }
        
        // Önce normal yükleme yap (güncel veriyi al)
        var loadBasket = function() {
            if (typeof basketReload === 'function') {
                basketReload();
            } else {
                $.ajax({
                    type: "POST",
                    url: "<?php echo url; ?>/sepete-ekle.php",
                    data: { 'sepet_goster': '1' },
                    success: function(data) {
                        if (data && data.trim() !== '') {
                            sepetDiv.innerHTML = data;
                        }
                    },
                    error: function() {
                        // Hata durumunda localStorage'dan yedek yükle
                        var backup = localStorage.getItem('basket_backup');
                        if (backup && backup.trim() !== '') {
                            sepetDiv.innerHTML = backup;
                        }
                    }
                });
            }
        };
        
        // Hemen yükle
        loadBasket();
        
        // localStorage'dan yedek varsa ve sayfa ilk yükleniyorsa, kullan
        var backup = localStorage.getItem('basket_backup');
        var backupTime = localStorage.getItem('basket_backup_time');
        
        if (backup && backupTime && backup.trim() !== '') {
            var timeDiff = Date.now() - parseInt(backupTime);
            if (timeDiff < 300000) { // 5 dakika = 300000 ms
                // Sadece sepet boşsa yedeği kullan
                if (!sepetDiv.innerHTML || sepetDiv.innerHTML.trim() === '' || 
                    sepetDiv.innerHTML.indexOf('txt117') !== -1 || 
                    sepetDiv.innerHTML.indexOf('txt115') !== -1) {
                    // Sepet boş, yedeği göster
                    sepetDiv.innerHTML = backup;
                }
            } else {
                // Eski yedek, temizle
                localStorage.removeItem('basket_backup');
                localStorage.removeItem('basket_backup_time');
            }
        }
    } catch(e) {
        console.log('Sepet geri yükleme hatası:', e);
        // Hata durumunda normal yükleme yap
        if (typeof basketReload === 'function') {
            setTimeout(function() {
                basketReload();
            }, 500);
        }
    }
}

// Sepet değişikliklerini izle ve localStorage'a kaydet
function watchBasketChanges() {
    // MutationObserver ile sepet değişikliklerini izle
    var sepetDiv = document.querySelector('[name="sepetdiv"]');
    if (!sepetDiv) {
        return;
    }
    
    // Sepet içeriğini kontrol et
    var lastContent = sepetDiv.innerHTML;
    
    var observer = new MutationObserver(function(mutations) {
        // Sepet div'inin hala var olduğunu kontrol et
        var currentSepetDiv = document.querySelector('[name="sepetdiv"]');
        if (!currentSepetDiv) {
            return;
        }
        
        var currentContent = currentSepetDiv.innerHTML;
        
        // İçerik gerçekten değişti mi kontrol et
        if (currentContent !== lastContent && currentContent.trim() !== '') {
            lastContent = currentContent;
            
            // Sepet değişti, localStorage'a kaydet
            try {
                // Sadece geçerli sepet içeriğini kaydet
                if (currentContent && 
                    !currentContent.includes('undefined') && 
                    currentContent.length > 10) {
                    localStorage.setItem('basket_backup', currentContent);
                    localStorage.setItem('basket_backup_time', Date.now().toString());
                }
            } catch(e) {
                console.log('Sepet kaydetme hatası:', e);
            }
        }
    });
    
    observer.observe(sepetDiv, {
        childList: true,
        subtree: true,
        characterData: true
    });
}

// Sayfa yüklendiğinde input formatlarını ayarla
document.addEventListener('DOMContentLoaded', function() {
    // Önce sayfanın tamamen yüklendiğinden emin ol
    setTimeout(function() {
        // Sidebar card'ın varlığını kontrol et
        var sidebarCard = document.querySelector('.sidebar-card');
        var sepetDiv = document.querySelector('[name="sepetdiv"]');
        
        if (sidebarCard && sepetDiv) {
            // Sepeti geri yükle
            restoreBasketOnPageLoad();
            
            // Sepet değişikliklerini izle
            watchBasketChanges();
        } else {
            // Sidebar card henüz yüklenmemiş, biraz bekle
            setTimeout(function() {
                restoreBasketOnPageLoad();
                watchBasketChanges();
            }, 500);
        }
    }, 300);
    
    // Adet-fiyat hesaplama fonksiyonunu başlat
    initAdetFiyatHesaplama();
    
    // Alpine.js tab değiştiğinde de çalıştır (biraz gecikme ile)
    setTimeout(function() {
        initAdetFiyatHesaplama();
    }, 500);
    
    // Kart numarası formatı
    const cardNumberInput = document.getElementById('cardNumber');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function() {
            formatCardNumber(this);
        });
    }
    
    // Son kullanma tarihi formatı
    const cardExpiryInput = document.getElementById('cardExpiry');
    if (cardExpiryInput) {
        cardExpiryInput.addEventListener('input', function() {
            formatExpiryDate(this);
        });
    }
    
    // CVC formatı
    const cardCVCInput = document.getElementById('cardCVC');
    if (cardCVCInput) {
        cardCVCInput.addEventListener('input', function() {
            formatCVC(this);
        });
    }
    
    // Telefon numarası formatı
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            formatPhoneNumber(this);
        });
    }
});

// Alpine.js tab değiştiğinde adet-fiyat hesaplamayı yeniden başlat
document.addEventListener('DOMContentLoaded', function() {
    // MutationObserver ile tab içeriği değiştiğinde tetikle
    setTimeout(function() {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                    const target = mutation.target;
                    if (target.classList.contains('tab-content')) {
                        const isVisible = target.style.display !== 'none' && 
                                         !target.hasAttribute('x-cloak') &&
                                         window.getComputedStyle(target).display !== 'none';
                        if (isVisible) {
                            setTimeout(function() {
                                initAdetFiyatHesaplama();
                            }, 200);
                        }
                    }
                }
            });
        });
        
        // Tüm tab-content elementlerini gözle
        document.querySelectorAll('.tab-content').forEach(function(tab) {
            observer.observe(tab, {
                attributes: true,
                attributeFilter: ['style', 'x-show']
            });
        });
    }, 500);
});
</script>

<style>
                        .donation-card:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
                        }
                        .card-image-container {
                            position: relative;
                            width: 100%;
                            height: 160px;
                            overflow: hidden;
                        }
                        .donation-card .card-image {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }
                        .donation-card .card-content {
                            padding: 0.875rem;
                            text-align: center;
                            display: flex;
                            flex-direction: column;
                            flex-grow: 1;
                        }
                        .donation-card .card-content > div:last-child {
                            margin-top: auto;
                        }
                        .donation-card .card-title {
                            font-size: 0.95rem;
                            font-weight: 700;
                            color: #1a202c;
                            margin-bottom: 0.75rem;
                            line-height: 1.3;
                        }
                        .donation-card .price-input-wrapper {
                            position: relative;
                            margin-bottom: 0.625rem;
                        }
                        .donation-card .price-input-wrapper input {
                            width: 100%;
                            padding: 8px 35px 8px 10px;
                            border: 1px solid #e2e8f0;
                            border-radius: 6px;
                            font-size: 14px;
                            font-weight: 600;
                            color: #2d3748;
                            text-align: left;
                            background: white;
                            transition: all 0.2s ease;
                        }
                        .donation-card .price-input-wrapper input:focus {
                            outline: none;
                            border-color: #14b8a6;
                            background: white;
                            box-shadow: 0 0 0 2px rgba(20, 184, 166, 0.1);
                        }
                        .donation-card .price-input-wrapper .currency-icon {
                            position: absolute;
                            right: 10px;
                            top: 50%;
                            transform: translateY(-50%);
                            color: #14b8a6;
                            font-size: 13px;
                            pointer-events: none;
                            font-weight: 700;
                        }
                        .donation-card .quick-amount-buttons {
                            display: flex;
                            gap: 6px;
                            margin-bottom: 0.625rem;
                            flex-wrap: wrap;
                            justify-content: center;
                        }
                        .donation-card .quick-amount-btn {
                            flex: 1;
                            min-width: 0;
                            padding: 8px 10px;
                            border: 1.5px solid #14b8a6;
                            border-radius: 6px;
                            background: white;
                            color: #14b8a6;
                            font-size: 12px;
                            font-weight: 700;
                            cursor: pointer;
                            transition: all 0.2s ease;
                            text-align: center;
                            white-space: nowrap;
                        }
                        .donation-card .quick-amount-btn:hover {
                            background: #14b8a6;
                            color: white;
                            transform: translateY(-1px);
                            box-shadow: 0 2px 6px rgba(20, 184, 166, 0.3);
                        }
                        .donation-card .quick-amount-btn:active {
                            transform: translateY(0);
                        }
                        .donation-card .donate-button {
                            width: 100%;
                            padding: 10px 14px;
                            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
                            color: white;
                            border: none;
                            border-radius: 6px;
                            font-size: 13px;
                            font-weight: 700;
                            cursor: pointer;
                            transition: all 0.3s ease;
                            box-shadow: 0 2px 8px rgba(20, 184, 166, 0.3);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 5px;
                        }
                        .donation-card .donate-button:hover {
                            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
                            transform: translateY(-1px);
                            box-shadow: 0 4px 12px rgba(20, 184, 166, 0.4);
                        }
                        .donation-card .donate-button:active {
                            transform: translateY(0);
                        }
                        .donation-card .donate-button i {
                            font-size: 13px;
                        }
                        .donation-card .quick-button {
                            width: 38px;
                            padding: 10px;
                            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                            color: white;
                            border: none;
                            border-radius: 6px;
                            font-size: 13px;
                            font-weight: 700;
                            cursor: pointer;
                            transition: all 0.3s ease;
                            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-shrink: 0;
                        }
                        .donation-card .quick-button:hover {
                            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
                            transform: translateY(-1px);
                            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
                        }
                        .donation-card .quick-button:active {
                            transform: translateY(0);
                        }
                        .donation-card .quick-button i {
                            font-size: 13px;
                        }
                        @media (max-width: 640px) {
                            .card-image-container {
                                height: 140px;
                            }
.donation-card .card-content {
    padding: 0.75rem;
    background: #ffffff !important;
}
                            .donation-card {
                                background: #f4921e !important;
                            }
                            .donation-card .card-title {
                                font-size: 1rem;
                                font-weight: 800;
                                margin-bottom: 0.625rem;
                                color: #fff !important;
                            }
                            .donation-card .price-input-wrapper input {
                                padding: 10px 35px 10px 12px;
                                font-size: 18px;
                                font-weight: 800;
                                background: #fff;
                                color: #1a202c;
                            }
                            .donation-card .price-input-wrapper .currency-icon {
                                font-size: 18px;
                                font-weight: 800;
                            }
                            .donation-card .quick-amount-btn {
                                padding: 8px 10px;
                                font-size: 14px;
                                font-weight: 800;
                            }
                            .donation-card .donate-button {
                                padding: 12px 16px;
                                font-size: 15px;
                                font-weight: 800;
                            }
                            .donation-card .quick-button {
                                width: 36px;
                                padding: 9px;
                            }
                        }
                        @media (min-width: 641px) and (max-width: 1024px) {
                            .card-image-container {
                                height: 150px;
                            }
                        }

                    </style>



                            <!-- Custom Mobile Phone Picker with Alpine.js (No heavy libraries) -->
                            <style>
                                .country-dropdown {
                                    max-height: 250px;
                                    overflow-y: auto;
                                    scrollbar-width: thin;
                                    scrollbar-color: #14b8a6 #f1f5f9;
                                }
                                .country-dropdown::-webkit-scrollbar { width: 6px; }
                                .country-dropdown::-webkit-scrollbar-track { background: #f1f5f9; }
                                .country-dropdown::-webkit-scrollbar-thumb { background: #14b8a6; border-radius: 10px; }
                            </style>

<!-- Country mapping for phone labels (optional) -->

<!-- PAGE SECTION BİTİŞ -->
<?php include('slider_menu.php');?>

<script>
// Global imgError fonksiyonu
window.imgError = function() {
};

// Global submitDonation fonksiyonu
window.submitDonation = function() {
    try {
        console.log('=== submitDonation BAŞLATILDI ===');
        
        // 1. Butonu bul ve kontrol et
        const buttons = document.querySelectorAll('button');
        let submitButton = null;
        
        for (let i = 0; i < buttons.length; i++) {
            const btn = buttons[i];
            if (btn.onclick && btn.onclick.toString().includes('submitDonation')) {
                submitButton = btn;
                break;
            }
            if (btn.getAttribute('@click') && btn.getAttribute('@click').includes('submitDonation')) {
                submitButton = btn;
                break;
            }
        }
        
        if (!submitButton) {
            console.error('Submit button bulunamadı');
            alert('Sistem hatası: Buton bulunamadı');
            return;
        }
        
        // 2. Alpine.js verilerine eriş
        const sectionElement = document.querySelector('section[x-data]');
        if (!sectionElement || !window.Alpine) {
            console.error('Alpine.js bulunamadı');
            alert('Sistem hazır değil, lütfen bekleyin');
            return;
        }
        
        const alpineData = window.Alpine.$data(sectionElement);
        if (!alpineData) {
            console.error('Alpine data alınamadı');
            alert('Form verileri okunamadı');
            return;
        }
        
        console.log('Alpine data:', alpineData);
        
        // 3. isYetim Değişkenini Tanımla (Hata Veren Kısım Düzeltildi)
        const isYetim = !!(alpineData.quickDonationYetimId || alpineData.isYetimBagis);
        
        // 4. Form Validasyonu
        const fullName = (alpineData.fullName || '').trim();
        const phone = (alpineData.phone || '').trim();
        const email = (alpineData.email || '').trim();
        
        if (!fullName || !phone || !email) {
            console.error('Form eksik:', { fullName, phone, email });
            alert('Lütfen tüm zorunlu alanları doldurun');
            return;
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Lütfen geçerli bir e-posta adresi girin');
            return;
        }
// 5. Tutar Belirleme
let tutar = 0;

// Sadece hızlı bağış modalı içindeki aktif fiyat inputunu hedefle
const modalPriceInput = document.querySelector('#quickDonationModal input[name="fiyat"]');

if (modalPriceInput) {
    // Kullanıcının manuel girdiği güncel değeri al
    const inputValue = parseFloat(modalPriceInput.value);
    
    if (inputValue > 0) {
        tutar = inputValue;
        // Alpine.js tarafını da manuel girişle senkronize et (önlem olarak)
        if (alpineData) alpineData.quickDonationFiyat = tutar;
    } 
}

// Eğer inputtan değer alınamadıysa yedek olarak Alpine verilerine bak
if (!(tutar > 0)) {
    tutar = parseFloat(alpineData.quickDonationFiyat) || 
            parseFloat(alpineData.customAmountValue) || 
            parseFloat(alpineData.donationAmount) || 0;
}

// Hata Kontrolü
if (tutar <= 0) {
    alert('Bağış tutarı belirlenemedi. Lütfen geçerli bir tutar girin.');
    return;
}
        
        // 6. Veri Hazırlama (AJAX Paketi)
        const ajaxData = {
            islem: isYetim ? 'yetim_sponsorluk_kaydet' : 'hizli_bagis_kaydet',
            fullName: fullName,
            telefon: phone,
            email: email,
            bagis_id_param: alpineData.quickDonationBagisId || '',
            kategori_id: alpineData.currentKategoriId || '',
            tutar: tutar.toString().replace(/[^0-9.]/g, ''),
            para_birimi: alpineData.selectedCurrency || 'TRY',
            ulke: alpineData.selectedCountry || 'TR',
            odeme_yontemi: alpineData.odemeYontemi || 'paytr',
            web: 1
        };

        // 7. Yetim Sponsorluk Verileri (Koşullu Ekleme)
        if (isYetim) {
            ajaxData.yetim_id = alpineData.quickDonationYetimId || null;
            ajaxData.sponsortip = alpineData.sponsorlukTipi || 'bireysel';
            ajaxData.odeme_tipi = (alpineData.odemePeriyodu === 'aylik') ? 'sürekli' : 'tek_seferlik';
            ajaxData.odeme_gunu = alpineData.aylikOdemeGunu || 1;
            ajaxData.sponsorluk_suresi_ay = alpineData.sponsorlukAySayisi || 1;
            
            // Kurumsal / Grup Detayları
            if(alpineData.sponsorlukTipi === 'grup') {
                ajaxData.grup_adi = alpineData.grupAdi || '';
            } else if(alpineData.sponsorlukTipi === 'kurumsal') {
                ajaxData.kurum_unvani = alpineData.kurumUnvani || '';
            }
            
            // Sorumlu Bilgileri
            ajaxData.sorumlu_ad_soyad = fullName;
            ajaxData.sorumlu_telefon = phone;
            ajaxData.sorumlu_email = email;
        }
        
        console.log('=== GÖNDERİLEN VERİLER ===', ajaxData);
        
        // 8. Buton Kilitleme
        submitButton.disabled = true;
        const originalBtnText = submitButton.innerHTML;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span>İşleniyor...';
        
        // 9. AJAX Gönderimi
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '_class/site_islem.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                console.log('=== SUNUCU YANITI ===', xhr.responseText);
                
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Yönlendirme
                            const redirectUrl = response.redirect || response.paytr_url || ('paytr_tekil_bagis.php?bagis_id=' + response.bagis_id);
                            window.location.href = redirectUrl;
                        } else {
                            alert(response.message || 'Bir hata oluştu');
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalBtnText;
                        }
                    } catch (e) {
                        console.error('JSON parse hatası:', e);
                        alert('Sunucudan geçersiz yanıt geldi.');
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalBtnText;
                    }
                } else {
                    alert('Sunucu hatası: ' + xhr.status);
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalBtnText;
                }
            }
        };
        
        xhr.onerror = function() {
            alert('Bağlantı hatası oluştu');
            submitButton.disabled = false;
            submitButton.innerHTML = originalBtnText;
        };
        
        xhr.send(new URLSearchParams(ajaxData));
        
    } catch (error) {
        console.error('submitDonation genel hata:', error);
        alert('Sistem hatası: ' + error.message);
    }
};
</script>