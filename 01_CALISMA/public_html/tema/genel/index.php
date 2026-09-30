<?php 
define("GUVENLIK",true);
$uri = $_SERVER['REQUEST_URI'];
$uri = str_replace('/', '', $uri);
?>
<html lang="tr">
<head>
	<?php 
	if($moduller['alan21'] == "1"){
		if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off")
		{
			$redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: ' . $redirect);
			exit();
		}
	}
	?>
	<?php $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
	$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
	$baseDir = str_replace('\\', '/', dirname($_SERVER['PHP_SELF']));
	if ($baseDir === '/' || $baseDir === '.') {
		$baseDir = '/';
	}
	$url=$protocol.$_SERVER["HTTP_HOST"].$baseDir; 
	$sayfalink = $protocol.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$dilsay		= $db->query("SELECT * FROM  diller")->rowCount();
	$dilyaz  	= $db->query("SELECT * FROM diller WHERE id = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
	?>
    <?php
    // Google Translate Auto-Switch Logic via URL
    if(isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
        $targetLang = $_GET['lang'];
        $cookieName = 'googtrans';
        $cookieValue = '/tr/' . $targetLang;
        $cookieDomain = '.' . str_replace('www.', '', $_SERVER['HTTP_HOST']); // Subdomain support
        
        // Check if cookie differs
        if(!isset($_COOKIE[$cookieName]) || $_COOKIE[$cookieName] != $cookieValue) {
            // Set for root path and potential subdomains
            setcookie($cookieName, $cookieValue, time() + (86400 * 30), '/', $cookieDomain); 
            setcookie($cookieName, $cookieValue, time() + (86400 * 30), '/'); 
            
            $_COOKIE[$cookieName] = $cookieValue;
        }
    }
    ?>

<?php 
require_once('_class/contact_section_functions.php');

$sayfalink = $_SERVER['REQUEST_URI'];

// ðŸ”¹ URL veya GET parametresine gÃ¶re dinamik sayfa adÄ± bul
if (isset($_GET['sayfa']) && $_GET['sayfa'] != '') {
    $page_name = trim(strtolower($_GET['sayfa']));
} else {
    // SEO dostu URL'yi yakala
    $page_name = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $page_name = str_replace(['-', ' '], '_', $page_name);

    // BoÅŸ veya kÃ¶k URL'de "anasayfa" olarak ata
    if ($page_name == '' || $page_name == 'index' || $page_name == 'index.php') {
        $page_name = 'anasayfa';
    }
}
?>
	<?php require_once('pages/sayac.php');?>
	<base href="<?php echo $url;?><?php echo(altklasor == "1" ? '/' : '');?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	
	<title><?php echo $title;?></title>
	<meta name="description" content="<?php echo $description;?>" />
	<meta name="keywords" content="<?php echo $keywords;?>" />
	
	<!-- Facebook Metadata Start -->
	<meta property="og:image:height" content="300" />
	<meta property="og:image:width" content="573" />
	<meta property="og:title" content="<?php echo $title;?>" />
	<meta property="og:description" content="<?php echo $description;?>" />
	<meta property="og:url" content="<?php echo $sayfalink;?>" />
	<meta property="og:image" content="<?php echo $url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $paylasim;?>" />
	<?php echo dogrulama;?>
	<link rel="shortcut icon" href="<?php echo tema;?>/uploads/favicon/<?php echo fav;?>">

    <!-- Font Preload -->
    <link rel="preload" href="<?php echo tema;?>/assets/fonts/Helvetica-Bold.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?php echo tema;?>/assets/fonts/Gilroy-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?php echo tema;?>/assets/fonts/Gilroy-Italic.woff2" as="font" type="font/woff2" crossorigin>

    <link rel="stylesheet" type="text/css" href="<?php echo tema;?>/assets/css/normalize.css">
    <link rel="stylesheet" href="<?php echo tema;?>/assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo tema;?>/assets/bower_components/owl.carousel/dist/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo tema;?>/assets/bower_components/components-font-awesome/css/all.css">

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700,800,900|Roboto:400,500,700,900&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&subset=latin-ext" rel="stylesheet">
	
    <link rel="stylesheet" href="<?php echo tema;?>/assets/css/style.php">
	<link rel="stylesheet" href="<?php echo tema;?>/assets/css/iziModal.min.css" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.css"/>
	<link rel="stylesheet" href="<?php echo tema;?>/assets/css/search.css">
    <link rel="stylesheet" href="<?php echo tema;?>/assets/bower_components/plyr-master/dist/plyr.css">
    <link rel="stylesheet" href="<?php echo tema;?>/assets/css/loader.css">
	<link rel="stylesheet" href="<?php echo tema;?>/assets/css/fancybox.css" />
	
	<!-- Font Awesome CSS -->
    <link rel="stylesheet" href="<?php echo tema;?>/assets/css/font-awesome5.min.css" />
    <link rel="stylesheet" href="<?php echo tema;?>/assets/css/all.css" />

	<!-- Google Translate Styles -->
	<style>
	.page-section-bg {
    min-height: auto !important;
}
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
		/* Google Translate Çevrilen Metinler İçin Font Boyutu Düzenlemesi */
		body[dir="rtl"] {
			font-size: 1.15em !important;
		}
		
		body[dir="rtl"] h1,
		body[dir="rtl"] h2,
		body[dir="rtl"] h3,
		body[dir="rtl"] h4,
		body[dir="rtl"] h5,
		body[dir="rtl"] h6,
		body[dir="rtl"] .g-title,
		body[dir="rtl"] .section-title,
		body[dir="rtl"] .page-title,
		body[dir="rtl"] .slide-title {
			font-size: 1.25em !important;
		}
		
		/* Google Translate'in eklediği küçük font-size'ları override et */
		body[dir="rtl"] * {
			font-size: inherit !important;
		}
		
		body[dir="rtl"] p,
		body[dir="rtl"] span,
		body[dir="rtl"] div,
		body[dir="rtl"] li,
		body[dir="rtl"] a {
			font-size: 1.15em !important;
		}
		
		body[dir="rtl"] .donation-card .card-title,
		body[dir="rtl"] .donation-card .price-input-wrapper input,
		body[dir="rtl"] .donation-card .quick-amount-btn,
		body[dir="rtl"] .donation-card .donate-button {
			font-size: 1.15em !important;
		}
		
		/* Google Translate banner ve menü için */
		.goog-te-banner-frame,
		.goog-te-menu-frame {
			font-size: 13px !important;
		}
.header-bottom {
    background: linear-gradient(rgb(184 179 179 / 0%), rgb(255 255 255 / 0%), #00000000) !important;
    box-shadow: 0 2px 10px rgb(255 255 255 / 0%);
    position: relative;
    margin-top: -60px;
    top: 43px;
}
.header-top ul li a, .header-bottom .navs li a {
    color: red !important;
    font-weight: 600;
}
/* PC header-top: dil dropdown butonu görünsün ve doğru yerde dursun */
.header-top .container > div {
    position: relative;
    flex-wrap: wrap;
}
@media (min-width: 768px) {
    .header-top .desktop-lang-dropdown {
        display: inline-block !important;
        position: relative !important;
        left: auto !important;
        right: auto !important;
        vertical-align: middle;
    }
    .header-top .desktop-lang-dropdown .lang-menu {
        right: 0;
        left: auto;
        top: calc(100% + 8px);
    }
}
.desktop-lang-dropdown .lang-btn {
    color: #fff !important;
    border-color: rgba(255,255,255,0.5);
    background: rgba(255,255,255,0.1);
}
.desktop-lang-dropdown .lang-btn:hover {
    background: rgba(255,255,255,0.2);
    color: #fff !important;
}
.desktop-lang-dropdown .arrow-down {
    border-top-color: #fff;
}
.slider-overlay {
    background: transparent;
    opacity: 1;
}
	</style>

	<?php include('pages/ozelcss.php');?>
	<?php 
	if($moduller['alan22'] == "1")
	{
		$Filename = 'havadurumu.txt';
		$dosya = fopen($Filename, 'r');
		$tarih = strtotime(fread($dosya, filesize($Filename)));
		$Bugun = strtotime(date('Y-m-d H:i:s'));
		$BTarih = date('Y-m-d H:i:s');
		$fark  = abs($tarih - $Bugun);
		$Dakika = $fark/60;
	
		if($Dakika > 300)
		{
			set_time_limit(0);
			$date = date('Y-m-d');
			$db->query("DELETE FROM havadurumu WHERE tarih LIKE '%{$date}%' ");



            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.ntvhava.com/".cVCLmHLxbS_seo($ayar['havadurumu'])."-hava-durumu",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Connection: keep-alive',
                    'Cache-Control: max-age=0',
                    'sec-ch-ua: "Google Chrome";v="89", "Chromium";v="89", ";Not A Brand";v="99"',
                    'sec-ch-ua-mobile: ?0',
                    'Upgrade-Insecure-Requests: 1',
                    'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                    'Sec-Fetch-Site: none',
                    'Sec-Fetch-Mode: navigate',
                    'Sec-Fetch-User: ?1',
                    'Sec-Fetch-Dest: document',
                    'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Cookie: ARRAffinity=243f00316f8b3d2dc20f56e21bf2af02b70961694164415ba4fd25af196238b4; ai_user=aVQhs|2021-04-15T06:15:00.988Z; ai_session=TmgSk|1618467306998.605|1618467306998.605; __utma=199725425.418725167.1618467307.1618467307.1618467307.1; __utmc=199725425; __utmz=199725425.1618467307.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __utmt=1; __utmb=199725425.1.10.1618467307'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

			$havadurumu = str_get_html($response);
		
			// Find all article blocks
			if($havadurumu !== false) {
				foreach($havadurumu->find('ul.daily-report-tab-content-pane-items li') as $article) {
					$item['tahmin']     = $article->find('.daily-report-tab-content-pane-item-text', 0)->plaintext;
					$item['derece']    = $article->find('.daily-report-tab-content-pane-item-box-bottom-degree-big', 0)->plaintext;
					$item['img'] = 'https://www.ntvhava.com/'.$article->find('img', 0)->src;
					$hava[] = $item;
					$query = $db->prepare("INSERT INTO havadurumu SET tarih = ? , tahmin = ?, derece = ?, img = ?");
					$query->execute(array(
						$BTarih,$item['tahmin'],$item['derece'],$item['img']
					));
				}
			}
			$dosya = fopen($Filename, 'w');
			fwrite($dosya, date('Y-m-d H:i:s'));
			fclose($dosya);
		}
	}
	?>
	<script src="<?php echo tema;?>/assets/bower_components/jquery/dist/jquery.min.js"></script>
	<script src="<?php echo tema;?>/assets/js/sweetalert2.all.min.js"></script>
	<script src="<?php echo tema;?>/assets/js/sweetalert2.min.js"></script>
	<?php echo analytics;?>
	<?php echo canli_destek;?>
	<?php
	if($moduller['alan20'] == "1"){
		$html = ".html";
	}
	else
	{
		$html = "";
	}	
	?>
		<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-58b57282384b6d76"></script>
	
	<!-- Google Translate Script -->
	<script type="text/javascript">
		function googleTranslateElementInit2() {
			new google.translate.TranslateElement({
				pageLanguage: 'tr',
				autoDisplay: false
			}, 'google_translate_element2');
		}
		
		function GTranslateFireEvent(a, b) {
			try {
				if (document.createEvent) {
					var c = document.createEvent("HTMLEvents");
					c.initEvent(b, true, true);
					a.dispatchEvent(c);
				} else {
					var c = document.createEventObject();
					a.fireEvent('on' + b, c);
				}
			} catch (e) {
			}
		}
		
function doGTranslate(a, element) {
    if (a.value) a = a.value;
    if (a == '') return;
    var b = a.split('|')[1]; // Hedef dil kodu: tr, en, ar
    var c;
    var d = document.getElementsByTagName('select');
    for (var i = 0; i < d.length; i++) {
        if (d[i].className == 'goog-te-combo') c = d[i];
    }
    if (document.getElementById('google_translate_element2') == null || 
        document.getElementById('google_translate_element2').innerHTML.length == 0 || 
        !c || c.length == 0 || c.innerHTML.length == 0) {
        setTimeout(function() {
            doGTranslate(a, element);
        }, 500);
    } else {
        // Sepet verilerini yedekle
        saveBasketToLocalStorage();
        
        c.value = b;
        GTranslateFireEvent(c, 'change');
        GTranslateFireEvent(c, 'change');
        
        // Aktif dil butonunu görsel olarak güncelle
        updateActiveLang(b);
        
        // Dil bazlı para birimi belirleme
        var currency = (b === 'ar' || b === 'en') ? 'USD' : 'TRY';
        
        // 1. LocalStorage güncelle
        localStorage.setItem('para_birimi', currency);
        
        // 2. Sunucu tarafını (Session) güncelle
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/update_currency.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                // 3. Bağış sayfasındaysak fiyatları ve simgeleri dinamik güncelle
                if (typeof window.updateCurrencyWithoutReload === 'function') {
                    window.updateCurrencyWithoutReload(currency);
                }
            }
        };
        xhr.send('para_birimi=' + currency);
    }
}
		
		// Sepet verilerini localStorage'a kaydet
		function saveBasketToLocalStorage() {
			// bagis/genel sayfasında sepet yedeği alma - Google Translate ile HTML bozulmasını önlemek için kapalı
			try {
				var path = window.location.pathname || '';
				if (path.indexOf('/bagis/genel') !== -1) {
					return;
				}
			} catch(e) {}
			try {
				var sepetDiv = document.querySelector('[name="sepetdiv"]');
				if (sepetDiv && sepetDiv.innerHTML.trim() !== '') {
					localStorage.setItem('basket_backup', sepetDiv.innerHTML);
					localStorage.setItem('basket_backup_time', Date.now().toString());
				}
			} catch(e) {
				console.log('Sepet kaydetme hatası:', e);
			}
		}
		
		// Sepet verilerini localStorage'dan geri yükle
		function restoreBasketFromLocalStorage() {
			// bagis/genel sayfasında sepet yedeğinden geri yükleme - Google Translate ile HTML bozulmasını önlemek için kapalı
			try {
				var path = window.location.pathname || '';
				if (path.indexOf('/bagis/genel') !== -1) {
					return;
				}
			} catch(e) {}
			try {
				var sepetDiv = document.querySelector('[name="sepetdiv"]');
				if (!sepetDiv) {
					return;
				}
				
				// Sidebar card'ın varlığını kontrol et
				var sidebarCard = sepetDiv.closest('.sidebar-card');
				if (!sidebarCard) {
					// Sidebar card yoksa, normal yükleme yap
					if (typeof basketReload === 'function') {
						setTimeout(function() {
							basketReload();
						}, 500);
					}
					return;
				}
				
				var backup = localStorage.getItem('basket_backup');
				var backupTime = localStorage.getItem('basket_backup_time');
				
				// 5 dakikadan eski yedekleri kullanma
				if (backup && backupTime && backup.trim() !== '') {
					var timeDiff = Date.now() - parseInt(backupTime);
					if (timeDiff < 300000) { // 5 dakika = 300000 ms
						// Sadece sepet boşsa veya geçersizse yedeği kullan
						var currentContent = sepetDiv.innerHTML || '';
						if (!currentContent || currentContent.trim() === '' || 
							currentContent.indexOf('txt117') !== -1 || 
							currentContent.indexOf('txt115') !== -1) {
							sepetDiv.innerHTML = backup;
						}
						
						// Sepeti yeniden yükle (AJAX ile güncel veriyi al)
						if (typeof basketReload === 'function') {
							setTimeout(function() {
								basketReload();
							}, 500);
						} else {
							$.ajax({
								type: "POST",
								url: "<?php echo url; ?>/sepete-ekle.php",
								data: { 'sepet_goster': '1' },
								success: function(data) {
									if (data && data.trim() !== '' && sepetDiv) {
										sepetDiv.innerHTML = data;
									}
								}
							});
						}
					} else {
						// Eski yedek, temizle
						localStorage.removeItem('basket_backup');
						localStorage.removeItem('basket_backup_time');
					}
				} else {
					// Yedek yok, normal yükleme yap
					if (typeof basketReload === 'function') {
						setTimeout(function() {
							basketReload();
						}, 500);
					}
				}
			} catch(e) {
				console.log('Sepet geri yükleme hatası:', e);
			}
		}
		
		// Sayfa yenilemeden güncelleme
		function updatePageWithoutReload(currency) {
			// Para birimi değişikliğini bildir
			if (typeof window.updateCurrencyWithoutReload === 'function') {
				window.updateCurrencyWithoutReload(currency);
			} else {
				// Bağış sayfasındaki fiyatları güncelle
				updateDonationPrices(currency);
				
				// Sepeti geri yükle (sadece bağış sayfası değilse)
				if (window.location.href.indexOf('bagis') === -1) {
					setTimeout(function() {
						restoreBasketFromLocalStorage();
					}, 500);
				}
			}
		}
		
		// Bağış sayfasındaki fiyatları güncelle
		function updateDonationPrices(currency) {
			// Bu fonksiyon bagis.php'de override edilebilir
			if (typeof window.updateDonationPagePrices === 'function') {
				window.updateDonationPagePrices(currency);
			}
		}
		
		// Dil kodu -> bayrak ikon sınıfı eşlemesi
		var langToFlag = { 'tr': 'flag-icon-tr', 'en': 'flag-icon-gb', 'ar': 'flag-icon-sa' };
		
		// Aktif dil durumunu güncelle (hem desktop hem mobil) - linkler + buton ikonları
		function updateActiveLang(langCode) {
			// Tüm dil linklerini bul (desktop ve mobil - .lang-menu içindeki linkler)
			var allLangLinks = document.querySelectorAll('.lang-menu a');
			
			// Önce hepsini pasif yap
			allLangLinks.forEach(function(link) {
				link.classList.remove('active');
			});
			
			// Seçilen dile ait tüm linkleri aktif yap (hem desktop hem mobil)
			allLangLinks.forEach(function(link) {
				var onclickAttr = link.getAttribute('onclick');
				if (onclickAttr) {
					var match = onclickAttr.match(/\|([^'"]+)/);
					if (match && match[1] === langCode) {
						link.classList.add('active');
					}
				}
			});
			
			// Dil butonlarındaki bayrak ikonunu güncelle (PC + mobil)
			var flagClass = langToFlag[langCode] || 'flag-icon-tr';
			var flagIcons = document.querySelectorAll('.lang-btn .current-flag, .lang-btn-desktop .current-flag');
			flagIcons.forEach(function(icon) {
				icon.className = 'flag-icon current-flag ' + flagClass;
			});
		}
		
		// Sayfa yÃ¼klendiÄŸinde aktif dili kontrol et
		function initGoogleTranslate() {
			var currentLang = document.cookie.match(/googtrans=([^;]+)/);
			var langCode = 'tr'; // VarsayÄ±lan TÃ¼rkÃ§e
			
			if (currentLang) {
				var langPath = currentLang[1];
				// googtrans cookie formatÄ±: /tr/ar veya /auto/tr gibi
				// BoÅŸ deÄŸilse ve "/" iÃ§eriyorsa parse et
				if (langPath && langPath.indexOf('/') !== -1) {
					var parts = langPath.split('/').filter(function(part) {
						return part && part !== 'auto';
					});
					if (parts.length > 0) {
						langCode = parts[parts.length - 1];
					}
				} else if (langPath) {
					langCode = langPath;
				}
			}
			
			// Aktif dil durumunu gÃ¼ncelle
			setTimeout(function() {
				updateActiveLang(langCode);
			}, 500);
		}
		
		// DOM yÃ¼klendiÄŸinde ve Google Translate hazÄ±r olduÄŸunda Ã§alÄ±ÅŸtÄ±r
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function() {
				setTimeout(initGoogleTranslate, 1000);
			});
		} else {
			setTimeout(initGoogleTranslate, 1000);
		}
		
		// Google Translate yÃ¼klendiÄŸinde de kontrol et
		window.addEventListener('load', function() {
			setTimeout(initGoogleTranslate, 1500);
		});
		
		// Cookie deÄŸiÅŸikliklerini dinle (dil deÄŸiÅŸtiÄŸinde)
		var lastCookie = document.cookie;
		setInterval(function() {
			var currentCookie = document.cookie;
			if (currentCookie !== lastCookie) {
				lastCookie = currentCookie;
				var langMatch = currentCookie.match(/googtrans=([^;]+)/);
				if (langMatch) {
					var langPath = langMatch[1];
					var parts = langPath.split('/').filter(function(part) {
						return part && part !== 'auto';
					});
					if (parts.length > 0) {
						updateActiveLang(parts[parts.length - 1]);
					}
				}
			}
		}, 500);
		
		// Google Translate select deÄŸiÅŸikliklerini dinle
		document.addEventListener('DOMContentLoaded', function() {
			setTimeout(function() {
				var translateSelect = document.querySelector('.goog-te-combo');
				if (translateSelect) {
					translateSelect.addEventListener('change', function() {
						var selectedLang = this.value;
						if (selectedLang) {
							updateActiveLang(selectedLang);
						}
					});
				}
			}, 2000);
			
			// Mobil dil seÃ§ici butonlarÄ± iÃ§in event listener ekle
			var mobileLangLinks = document.querySelectorAll('.google-translate-lang.mobile-lang a');
			mobileLangLinks.forEach(function(link) {
				// Click event
				link.addEventListener('click', function(e) {
					e.preventDefault();
					e.stopPropagation();
					var onclickAttr = this.getAttribute('onclick');
					if (onclickAttr) {
						var match = onclickAttr.match(/doGTranslate\(['"]([^'"]+)['"]/);
						if (match) {
							doGTranslate(match[1], this);
						}
					}
					return false;
				});
				
				// Touch event (mobil iÃ§in)
				link.addEventListener('touchend', function(e) {
					e.preventDefault();
					e.stopPropagation();
					var onclickAttr = this.getAttribute('onclick');
					if (onclickAttr) {
						var match = onclickAttr.match(/doGTranslate\(['"]([^'"]+)['"]/);
						if (match) {
							doGTranslate(match[1], this);
						}
					}
					return false;
				}, {passive: false});
			});
		});
	</script>
	<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2"></script>
	<div id="google_translate_element2" style="display:none;visibility: hidden;"></div>
	<style>
	    .iti__selected-country {
    z-index: 1;
    position: relative;
    display: flex;
    align-items: center;
    height: 100%;
    background: none;
    border: 0;
    margin: 0;
    padding: 0;
    font-family: inherit;
    font-size: inherit;
    color: inherit;
    border-radius: 0;
    font-weight: inherit;
    line-height: inherit;
    text-decoration: none;
    font-family: var(--font-heading);
    color: rgb(0 0 0) !important;
    transition: 0.3s;
    border-color: unset !important;
    background: #f0f0f0 !important;
}

	</style>
</head>
<body>
	<?php echo whatsapp;?>
	<div class="mizac-testi-btn">
		<a href="<?php echo $url;?>/mizac-testi<?php echo $html;?>">
			Mizaç Testi
		</a>
	</div>
    <!-- MAÄ°N BAÅžLANGIÃ‡-->
    <main class="main-wrap">

        <!-- HEADER BAÅžLANGIÃ‡ -->
        <section class="header">

            <!-- HEADER-TOP BAÅžLANGIÃ‡ -->
            <div class="header-top">
                <div class="container">

                    <div>
                    
						
						<!-- Google Translate Dil Seçici - PC dropdown -->
						<div class="custom-lang-dropdown desktop-lang-dropdown d-none d-md-block">
							<button type="button" class="lang-btn lang-btn-desktop" onclick="toggleDesktopLangMenu(event)">
								<i class="flag-icon flag-icon-tr current-flag"></i>
								<span class="arrow-down"></span>
							</button>
							<div class="lang-menu" id="desktopLangMenu">
								<a href="#" onclick="doGTranslateUrl('tr|tr'); return false;"><i class="flag-icon flag-icon-tr"></i> <span>Türkçe</span></a>
								<a href="#" onclick="doGTranslateUrl('tr|ar'); return false;"><i class="flag-icon flag-icon-sa"></i> <span>Arapça</span></a>
								<a href="#" onclick="doGTranslateUrl('tr|en'); return false;"><i class="flag-icon flag-icon-gb"></i> <span>İngilizce</span></a>
							</div>
						</div>
						<script>
						function doGTranslateUrl(langPair) {
							// Example: tr|en
							var targetLang = langPair.split('|')[1];
							
							// Check current cookie
							var currentCookie = document.cookie.match(/(^|;)\s*googtrans=([^;]+)/);
							var currentLang = 'tr';
							if(currentCookie) {
								var parts = currentCookie[2].split('/');
								currentLang = parts[parts.length-1];
							}

							// If unnecessary click
							if(currentLang === targetLang) return;

							// 1. Manually set cookies
							var domain = window.location.hostname;
							// subdomain fix
							if(domain.indexOf('www.') === 0) domain = domain.substring(4);
							
							document.cookie = "googtrans=/tr/" + targetLang + "; domain=." + domain + "; path=/; expires=Thu, 01 Jan 2030 00:00:00 UTC";
							document.cookie = "googtrans=/tr/" + targetLang + "; path=/; expires=Thu, 01 Jan 2030 00:00:00 UTC";

							// 2. Redirect to new URL
							var path = window.location.pathname;
							var search = window.location.search;
							var pfx = ['en', 'ar']; // foreign langs
							
							var newPath = path;
							
							// Strip existing prefixes
							for(var i=0; i<pfx.length; i++) {
								if(newPath.indexOf('/'+pfx[i]) === 0) {
									newPath = newPath.replace('/'+pfx[i], '');
									break;
								}
							}
							if(newPath.charAt(0) !== '/') newPath = '/' + newPath;

							if(targetLang === 'tr') {
								// Go to root
								window.location.href = newPath + search;
							} else {
								// Go to /lang/path
								window.location.href = '/' + targetLang + newPath + search;
							}
						}
						</script>
						<span class="h-border"></span>
						
						<?php if($dilsay > 1){?>
						<a href="javascript:;" class="trigger-link"><i class="fad fa-globe"></i> <?=@$dil['txt1'];?></a></li>
						<div id="modal-demo" class="iziModal text-center">
							<div class="p-4">
								<div class="lang">
									<h4><?=@$dil['txt2'];?></h4>
									<?php 
									$DILSorgu = $db->prepare("SELECT * FROM diller ORDER BY sira ASC");
									$DILSorgu->execute();
									$DILislem 	= $DILSorgu->fetchALL(PDO::FETCH_ASSOC);?>						
									<?php foreach ( $DILislem as $DILSonuc ){?> 
										<a data-id="<?=@$DILSonuc['id'];?>" href="javascript:;" class="<?php echo($dilyaz['id'] == $DILSonuc['id'] ? 'activelang' : '');?> dildegis"><i class="flag-icon <?php echo $DILSonuc['bayrak'];?>"></i> <?php echo $DILSonuc['adi'];?></a>				
									<?php }?>								
									<div class="clear"></div>
								</div>
								<div class="clear"></div>
							</div>						
						</div>
						<?php }?>
                        <ul class="social">
							<?php if(facebook){?><li><a title="Facebook" target="_blank" rel="noopener" href="<?php echo facebook;?>"><i class="fab fa-facebook-f"></i></a></li><?php }?>
							<?php if(twitter){?><li><a title="Twitter" target="_blank" rel="noopener" href="<?php echo twitter;?>"><i class="fab fa-twitter"></i></a></li><?php }?>
							<?php if(instagram){?><li><a title="Instagram" target="_blank" rel="noopener" href="<?php echo instagram;?>"><i class="fab fa-instagram"></i></a></li><?php }?>
							<?php if(linkedin){?><li><a title="LinkedIn" target="_blank" rel="noopener" href="<?php echo linkedin;?>"><i class="fab fa-linkedin-in"></i></a></li><?php }?>
							<?php if(youtube){?><li><a title="YouTube" target="_blank" rel="noopener" href="<?php echo youtube;?>"><i class="fab fa-youtube"></i></a></li><?php }?>
                        </ul>
						
                        <span class="h-border"></span>

						<ul class="navs" style="padding-left: 0 !important;">
						<?php $Sorgu = $db->prepare("SELECT * FROM topmenu WHERE menu_durum = ? AND dil = ? ORDER BY menu_sira ASC");
						$Sorgu->execute(array("1",$_SESSION['k_dil']));
						$islem = $Sorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $islem as $Sonuc ){?>
                            <li  class="hover-bar"> <a style=" color: white !important; position: relative; top: 8px; opacity: 1; " <?php echo($Sonuc['sekme'] == 1 ? 'target="_blank"' : '');?> href="<?php echo($Sonuc['menu_url'] == "0" ? $Sonuc['link'] : $Sonuc['menu_url']); ?>"> <?php echo $Sonuc['menu_isim']?> </a></li>
							<?php }?>
                        </ul>
                        
                    </div>


                </div>
            </div>
            <!-- HEADER-TOP BÄ°TÄ°Åž -->

            <!-- HEADER-BOTTOM BAÅžLANGIÃ‡ -->
            <div class="header-bottom">
                <div class="container">

                    <div class="row">


                        <div class="col-xl-9 col-lg-7 col-md-4 p-0 position-static">
                            <ul class="navs">	
                             <a href="<?php echo (!in_array($uri,['anasayfa',''])) ? $htc['anaurl'] : '#';?><?php echo $html;?>"><img class="logo z-index-9" src="<?php echo tema;?>/uploads/logo/<?php echo logo;?>" alt="<?php echo firma_adi;?>"></a>
							<?php $MENUSorgu = $db->prepare("SELECT * FROM menu WHERE menu_durum = ? AND menu_ust = ? AND dil = ? ORDER BY menu_sira ASC");
							$MENUSorgu->execute(array("1","0",$_SESSION['k_dil']));
							$MENUislem = $MENUSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $MENUislem as $MENUSonuc ){?>
							<?php $altvarmi	= $db->query("SELECT * FROM menu WHERE menu_durum = '1' AND menu_ust = '{$MENUSonuc['id']}' ORDER BY id DESC")->rowCount();?>
							<?php if($MENUSonuc['tip']==0){?>
							<!-- alt menÃ¼ yok BAÅžLANGIÃ‡ -->
                                <li class="header-item">
                                    <a class="hover-bar" href="<?php echo($MENUSonuc['menu_url'] == "0" ? $MENUSonuc['link'] : $MENUSonuc['menu_url'].$html); ?>">
                                        <?php if(!empty($MENUSonuc['menu_icon'])): ?><i class="<?php echo htmlspecialchars($MENUSonuc['menu_icon'], ENT_QUOTES, 'UTF-8'); ?>"></i><?php endif; ?>
                                        <?php echo $MENUSonuc['menu_isim']; ?>
                                    </a>
                                </li>
								<!-- alt menÃ¼ yok BÄ°TÄ°Åž -->
								<?php } else { ?>
								<?php if($MENUSonuc['tip']==1){?>
								<!-- MENÃœ TÄ°P 1  BAÅžLANGIÃ‡ -->
                                <li class="header-item">
                                    <a <?php echo($MENUSonuc['sekme'] == 1 ? 'target="_blank"' : '');?> class="hover-bar" href="<?php echo($MENUSonuc['menu_url'] == "0" ? $MENUSonuc['link'] : $MENUSonuc['menu_url']); ?>">
                                        <?php if(!empty($MENUSonuc['menu_icon'])): ?><i class="<?php echo htmlspecialchars($MENUSonuc['menu_icon'], ENT_QUOTES, 'UTF-8'); ?>"></i><?php endif; ?>
                                        <?php echo $MENUSonuc['menu_isim']; ?>
                                    </a>
                                    <div class="header-dropdown bg-red">
                                        <img src="<?php echo tema;?>/uploads/arkaplan/arkaplan21/<?php echo $arkaplan['arkaplan21'];?>" class="bg-image">
										<?php $ALTMENUSorgu = $db->prepare("SELECT * FROM menu WHERE menu_durum = ? AND menu_ust = ? AND dil = ? ORDER BY menu_sira ASC");
										$ALTMENUSorgu->execute(array("1",$MENUSonuc['id'],$_SESSION['k_dil']));
										$ALTMENUislem = $ALTMENUSorgu->fetchALL(PDO::FETCH_ASSOC);?>
										<?php if($ALTMENUSorgu->rowCount()){?>
                                        <div class="container">
                                            <div class="row py-5">
											<?php foreach ( $ALTMENUislem as $ALTMENUSonuc ){?>
											<div class="col-3 py-3">
                                                    <div class="link-box">
                                                        <a <?php echo($ALTMENUSonuc['sekme'] == 1 ? 'target="_blank"' : '');?> href="<?php echo($ALTMENUSonuc['menu_url'] == "0" ? $ALTMENUSonuc['link'] : $ALTMENUSonuc['menu_url'].$html); ?>">
                                                            <?php if(!empty($ALTMENUSonuc['menu_icon'])): ?><i class="<?php echo htmlspecialchars($ALTMENUSonuc['menu_icon'], ENT_QUOTES, 'UTF-8'); ?>"></i> <?php endif; ?>
                                                            <?php echo $ALTMENUSonuc['menu_isim']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                             <?php }?>
											 <?php if($MENUSonuc['tbuton']==""){?>
											 <?php }else{?>
												<div class="col-3 py-3">
                                                    <div class="link-box tumunu-gor">
                                                        <a href="<?php echo $MENUSonuc['tbuton']; ?>">
                                                            <?=@$dil['txt3'];?> <i class="far fa-arrow-right ml-2"></i>
                                                        </a>
                                                    </div>
                                                </div>
											 <?php } ?>
											</div>
                                        </div>
										<?php }?>
                                    </div>

                                </li>
								<!-- MENÃœ TÄ°P 1 BÄ°TÄ°Åž -->
								<?php } else { ?>
								
							   <?php if($MENUSonuc['tip']==2){?>
								<!-- MENÃœ TÄ°P 2 BAÅžLANGIÃ‡ -->
                                <li class="header-item">
                                    <a <?php echo($MENUSonuc['sekme'] == 1 ? 'target="_blank"' : '');?> class="hover-bar" href="<?php echo($MENUSonuc['menu_url'] == "0" ? $MENUSonuc['link'] : $MENUSonuc['menu_url']); ?>">
                                        <?php if(!empty($MENUSonuc['menu_icon'])): ?><i class="<?php echo htmlspecialchars($MENUSonuc['menu_icon'], ENT_QUOTES, 'UTF-8'); ?>"></i><?php endif; ?>
                                        <?php echo $MENUSonuc['menu_isim']; ?>
                                    </a>
                                    <?php 
                                    // Tip 2 için güvenli limit değerleri
                                    $klimit = !empty($MENUSonuc['klimit']) && is_numeric($MENUSonuc['klimit']) ? (int)$MENUSonuc['klimit'] : 5;
                                    $ilimit = !empty($MENUSonuc['ilimit']) && is_numeric($MENUSonuc['ilimit']) ? (int)$MENUSonuc['ilimit'] : 6;
                                    $tipkat = isset($MENUSonuc['tipkat']) ? (int)$MENUSonuc['tipkat'] : 0;
                                    $kategori = isset($MENUSonuc['kategori']) ? (int)$MENUSonuc['kategori'] : 0;
                                    ?>
                                    <!-- MENÃœ TÄ°P 2 haber ve haber kategorisi aÃ§Ä±k isteniyorsa BAÅžLANGIÃ‡-->
								    <?php if($tipkat==0){?>
									<?php if($kategori==0){?>
									 <div class="header-dropdown bg-white">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-4 py-5 pr-0 right-shadow">
                                                    <ul class="header-tabs">
														<?php $HKSorgu = $db->prepare("SELECT * FROM haber_kategori WHERE durum = ? AND dil = ? ORDER BY sira ASC LIMIT ".$klimit."");
														$HKSorgu->execute(array("1",$_SESSION['k_dil']));
														$HKislem = $HKSorgu->fetchALL(PDO::FETCH_ASSOC);
														$HKmansetsay = 1;?>
														<?php foreach ( $HKislem as $HKSonuc ){?>
                                                        <li class="<?php echo($HKmansetsay++ == 1 ? 'active' : '');?> mb-2 tab-link" datatarget="#<?php echo $HKSonuc['seo'];?>">
                                                            <i class="fal fa-chevron-right mr-2"></i> <?php echo $HKSonuc['adi'];?>
                                                        </li>
														<?php }?>
														<?php
														// Alt menü öğeleri (custom URL'ler)
														$AltMenuSorgu = $db->prepare("SELECT * FROM menu WHERE menu_ust = ? AND menu_durum = ? AND dil = ? ORDER BY menu_sira ASC");
														$AltMenuSorgu->execute(array($MENUSonuc['id'], "1", $_SESSION['k_dil']));
														$AltMenuIslem = $AltMenuSorgu->fetchALL(PDO::FETCH_ASSOC);
														foreach ($AltMenuIslem as $AltMenuSonuc) {
															$altMenuUrl = $AltMenuSonuc['menu_url'] == "0" ? $AltMenuSonuc['link'] : $AltMenuSonuc['menu_url'];
														?>
                                                        <li class="mb-2" onclick="window.location.href='<?php echo $altMenuUrl; ?>'" style="cursor:pointer;">
                                                            <i class="fal fa-chevron-right mr-2"></i> <?php echo $AltMenuSonuc['menu_isim']; ?>
                                                        </li>
														<?php } ?>
                                                      </ul>
                                                </div>
                                                <div class="col-8 py-4">
                                                    <img src="<?php echo tema;?>/uploads/arkaplan/arkaplan21/<?php echo $arkaplan['arkaplan21'];?>" class="bg-image">
													<?php $HKASorgu = $db->prepare("SELECT * FROM haber_kategori WHERE durum = ? AND dil = ? ORDER BY sira ASC LIMIT ".$klimit."");
													$HKASorgu->execute(array("1",$_SESSION['k_dil']));
													$HKAislem = $HKASorgu->fetchALL(PDO::FETCH_ASSOC);
													$HKAmansetsay = 1;?>
													<?php foreach ( $HKAislem as $HKASonuc ){?>
                                                    <div class="tab-panel <?php echo($HKAmansetsay++ == 1 ? 'active' : '');?>" id="<?php echo $HKASonuc['seo'];?>">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h3 class="g-title">
                                                                   <?php echo $HKASonuc['adi'];?>
                                                                    <a class="drp-tumunu-gor hover-bar" href="<?php echo $htc['haberkategoriurl']; ?>/<?php echo $HKASonuc['seo']; ?><?php echo $html;?>">
                                                                        <?=@$dil['txt3'];?>
                                                                    </a>
                                                                </h3>
                                                            </div>
															<?php $HSorgu = $db->prepare("SELECT * FROM haberler WHERE durum = ? AND dil = ? and kategori = ? ORDER BY sira ASC LIMIT ".$ilimit."");
															$HSorgu->execute(array("1",$_SESSION['k_dil'],$HKASonuc['id']));
															$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
															<?php foreach ( $Hislem as $HSonuc ){?>
															   <div class="col-4 my-3">
                                                                <div class="drp-haber-box">
                                                                    <a href="<?php echo $htc['haberdetayurl']; ?>/<?php echo $HSonuc['seo']; ?><?php echo $html;?>">
                                                                        <div class="row m-0">
                                                                            <div class="col-4 p-0">
                                                                                <img src="<?php echo tema;?>/uploads/haberler/<?php echo $HSonuc['resim'];?>" onerror="imgError(this);">
                                                                            </div>
                                                                            <div class="col-8 p-0 content">
                                                                                <div>
                                                                                    <h4>
                                                                                       <?php echo $HSonuc['adi'];?>
                                                                                    </h4>
                                                                                    <p><?php echo cVCLmHLxbS_tarih2($HSonuc['tarih']);?></p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            </div>
															<?php }?>
															</div>
                                                    </div>
													<?php }?>
                                                  </div>
                                            </div>
                                        </div>
                                    </div>
									<!-- MENÃœ TÄ°P 2 haber ve haber kategorisi aÃ§Ä±k isteniyorsa BÄ°TÄ°Åž-->
									<?php } else if($kategori==1) { ?>
									<!-- MENÃœ TÄ°P 2 PROJELER ve PROJE kategorisi aÃ§Ä±k isteniyorsa BAÅžLANGIÃ‡-->
									<div class="header-dropdown bg-white">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-4 py-5 pr-0 right-shadow">
                                                    <ul class="header-tabs">
														<?php $PKSorgu = $db->prepare("SELECT * FROM proje_kategori WHERE durum = ? AND dil = ? ORDER BY sira ASC LIMIT ".$klimit."");
														$PKSorgu->execute(array("1",$_SESSION['k_dil']));
														$PKislem = $PKSorgu->fetchALL(PDO::FETCH_ASSOC);
														$PKmansetsay = 1;?>
														<?php foreach ( $PKislem as $PKSonuc ){?>
                                                        <li class="<?php echo($PKmansetsay++ == 1 ? 'active' : '');?> mb-2 tab-link" datatarget="#<?php echo $PKSonuc['seo'];?>">
                                                            <i class="fal fa-chevron-right mr-2"></i> <?php echo $PKSonuc['adi'];?>
                                                        </li>
														<?php }?>
														<?php
														$AltMenuSorgu2 = $db->prepare("SELECT * FROM menu WHERE menu_ust = ? AND menu_durum = ? AND dil = ? ORDER BY menu_sira ASC");
														$AltMenuSorgu2->execute(array($MENUSonuc['id'], "1", $_SESSION['k_dil']));
														$AltMenuIslem2 = $AltMenuSorgu2->fetchALL(PDO::FETCH_ASSOC);
														foreach ($AltMenuIslem2 as $AltMenuSonuc2) {
															$altMenuUrl2 = $AltMenuSonuc2['menu_url'] == "0" ? $AltMenuSonuc2['link'] : $AltMenuSonuc2['menu_url'];
														?>
                                                        <li class="mb-2" onclick="window.location.href='<?php echo $altMenuUrl2; ?>'" style="cursor:pointer;">
                                                            <i class="fal fa-chevron-right mr-2"></i> <?php echo $AltMenuSonuc2['menu_isim']; ?>
                                                        </li>
														<?php } ?>
                                                      </ul>
                                                </div>
                                                <div class="col-8 py-4">
                                                    <img src="<?php echo tema;?>/uploads/arkaplan/arkaplan21/<?php echo $arkaplan['arkaplan21'];?>" class="bg-image">
													<?php $PKASorgu = $db->prepare("SELECT * FROM proje_kategori WHERE durum = ? AND dil = ? ORDER BY sira ASC LIMIT ".$klimit."");
													$PKASorgu->execute(array("1",$_SESSION['k_dil']));
													$PKAislem = $PKASorgu->fetchALL(PDO::FETCH_ASSOC);
													$PKAmansetsay = 1;?>
													<?php foreach ( $PKAislem as $PKASonuc ){?>
                                                    <div class="tab-panel <?php echo($PKAmansetsay++ == 1 ? 'active' : '');?>" id="<?php echo $PKASonuc['seo'];?>">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h3 class="g-title">
                                                                   <?php echo $PKASonuc['adi'];?>
                                                                    <a class="drp-tumunu-gor hover-bar" href="<?php echo $htc['projekategoriurl']; ?>/<?php echo $PKASonuc['seo']; ?><?php echo $html;?>">
                                                                        <?=@$dil['txt3'];?>
                                                                    </a>
                                                                </h3>
                                                            </div>
															<?php $PSorgu = $db->prepare("SELECT * FROM projeler WHERE durum = ? AND dil = ? and kategori = ? ORDER BY sira ASC LIMIT ".$ilimit."");
															$PSorgu->execute(array("1",$_SESSION['k_dil'],$PKASonuc['id']));
															$Pislem = $PSorgu->fetchALL(PDO::FETCH_ASSOC);?>
															<?php foreach ( $Pislem as $PSonuc ){?>
															<div class="col-4 my-3">
                                                                <div class="drp-haber-box">
                                                                    <a href="<?php echo $htc['projedetayurl']; ?>/<?php echo $PSonuc['seo']; ?><?php echo $html;?>">
                                                                        <div class="row m-0">
                                                                            <div class="col-4 p-0">
                                                                                <img src="<?php echo tema;?>/uploads/projeler/<?php echo $PSonuc['kapak'];?>" onerror="imgError(this);">
                                                                            </div>
                                                                            <div class="col-8 p-0 content">
                                                                                <div>
                                                                                    <h4>
                                                                                       <?php echo $PSonuc['adi'];?>
                                                                                    </h4>
                                                                                    <p><?php echo cVCLmHLxbS_tarih2($PSonuc['tarih']);?></p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            </div>
															<?php }?>
														 </div>
                                                       </div>
													<?php }?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
									<!-- MENÃœ TÄ°P 2 PROJELER ve PROJE kategorisi aÃ§Ä±k isteniyorsa BÄ°TÄ°Åž-->	
									<?php }else{?>
									<!-- MENÃœ TÄ°P 2 PROFÄ°LLER ve PROFÄ°L kategorisi aÃ§Ä±k isteniyorsa BAÅžLANGIÃ‡-->
									<div class="header-dropdown bg-white">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-4 py-5 pr-0 right-shadow">
                                                    <ul class="header-tabs">
														<?php $PKSorgu = $db->prepare("SELECT * FROM profil_kategori WHERE durum = ? AND dil = ? ORDER BY id ASC LIMIT ".$klimit."");
														$PKSorgu->execute(array("1",$_SESSION['k_dil']));
														$PKislem = $PKSorgu->fetchALL(PDO::FETCH_ASSOC);
														$PRFKmansetsay = 1;?>
														<?php foreach ( $PKislem as $PKSonuc ){?>
                                                        <li class="<?php echo($PRFKmansetsay++ == 1 ? 'active' : '');?> mb-2 tab-link" datatarget="#<?php echo $PKSonuc['seo'];?>">
                                                            <i class="fal fa-chevron-right mr-2"></i> <?php echo $PKSonuc['adi'];?>
                                                        </li>
														<?php }?>
														<?php
														$AltMenuSorgu3 = $db->prepare("SELECT * FROM menu WHERE menu_ust = ? AND menu_durum = ? AND dil = ? ORDER BY menu_sira ASC");
														$AltMenuSorgu3->execute(array($MENUSonuc['id'], "1", $_SESSION['k_dil']));
														$AltMenuIslem3 = $AltMenuSorgu3->fetchALL(PDO::FETCH_ASSOC);
														foreach ($AltMenuIslem3 as $AltMenuSonuc3) {
															$altMenuUrl3 = $AltMenuSonuc3['menu_url'] == "0" ? $AltMenuSonuc3['link'] : $AltMenuSonuc3['menu_url'];
														?>
                                                        <li class="mb-2" onclick="window.location.href='<?php echo $altMenuUrl3; ?>'" style="cursor:pointer;">
                                                            <i class="fal fa-chevron-right mr-2"></i> <?php echo $AltMenuSonuc3['menu_isim']; ?>
                                                        </li>
														<?php } ?>
                                                      </ul>
                                                </div>
                                                <div class="col-8 py-4">
                                                    <img src="<?php echo tema;?>/uploads/arkaplan/arkaplan21/<?php echo $arkaplan['arkaplan21'];?>" class="bg-image">
													<?php $PKASorgu = $db->prepare("SELECT * FROM profil_kategori WHERE durum = ? AND dil = ? ORDER BY id ASC LIMIT ".$klimit."");
													$PKASorgu->execute(array("1",$_SESSION['k_dil']));
													$PKAislem = $PKASorgu->fetchALL(PDO::FETCH_ASSOC);
													$PRFKAmansetsay = 1;?>
													<?php foreach ( $PKAislem as $PKASonuc ){?>
                                                    <div class="tab-panel <?php echo($PRFKAmansetsay++ == 1 ? 'active' : '');?>" id="<?php echo $PKASonuc['seo'];?>">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h3 class="g-title">
                                                                   <?php echo $PKASonuc['adi'];?>
                                                                    <a class="drp-tumunu-gor hover-bar" href="<?php echo $htc['profilkategoriurl']; ?>/<?php echo $PKASonuc['seo']; ?><?php echo $html;?>">
                                                                        <?=@$dil['txt3'];?>
                                                                    </a>
                                                                </h3>
                                                            </div>
															<?php $PSorgu = $db->prepare("SELECT * FROM profiller WHERE durum = ? AND dil = ? and kategori = ? ORDER BY sira ASC LIMIT ".$ilimit."");
															$PSorgu->execute(array("1",$_SESSION['k_dil'],$PKASonuc['id']));
															$Pislem = $PSorgu->fetchALL(PDO::FETCH_ASSOC);?>
															<?php foreach ( $Pislem as $PSonuc ){?>
															<div class="col-4 my-3">
                                                                <div class="drp-haber-box">
                                                                    <a href="<?php echo $htc['profildetayurl']; ?>/<?php echo $PSonuc['seo']; ?><?php echo $html;?>">
                                                                        <div class="row m-0">
                                                                            <div class="col-4 p-0">
                                                                                <img src="<?php echo tema;?>/uploads/profiller/<?php echo $PSonuc['kapak'];?>" onerror="imgError(this);">
                                                                            </div>
                                                                            <div class="col-8 p-0 content">
                                                                                <div>
                                                                                    <h4>
                                                                                       <?php echo $PSonuc['adi'];?>
                                                                                    </h4>
                                                                                    <p><?php echo cVCLmHLxbS_tarih2($PSonuc['tarih']);?></p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            </div>
															<?php }?>
														 </div>
                                                       </div>
													<?php }?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
									<?php }?>
									<!-- MENÃœ TÄ°P 2 PROFÄ°LLER ve PROFÄ°L kategorisi aÃ§Ä±k isteniyorsa BÄ°TÄ°Åž-->		
									<?php } else { ?>									
									<!-- MENÃœ TÄ°P 2 HABERLER ve HABER kategorisi KAPALI isteniyorsa BAÅžLANGIÃ‡-->
									<?php if($kategori==0){?>
									 <div class="header-dropdown bg-white">
                                        <div class="container">
                                            <div class="row">											
												<img src="<?php echo tema;?>/uploads/arkaplan/arkaplan21/<?php echo $arkaplan['arkaplan21'];?>" class="bg-image">													
												<div class="tab-panel active full py-5" id="haberler">
													<div class="row">
														<div class="col-12">
															<h3 class="g-title">
															   <?php echo $MENUSonuc['menu_isim']; ?>
																<a class="drp-tumunu-gor hover-bar" href="<?php echo $htc['haberlerurl']; ?><?php echo $html;?>">
																	<?=@$dil['txt3'];?>
																</a>
															</h3>
														</div>
														<?php $HSorgu = $db->prepare("SELECT * FROM haberler WHERE durum = ? AND dil = ? ORDER BY sira ASC LIMIT ".$ilimit."");
														$HSorgu->execute(array("1",$_SESSION['k_dil']));
														$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
														<?php foreach ( $Hislem as $HSonuc ){?>
														   <div class="col-4 my-3">
															<div class="drp-haber-box">
																<a href="<?php echo $htc['haberdetayurl']; ?>/<?php echo $HSonuc['seo']; ?><?php echo $html;?>">
																	<div class="row m-0">
																		<div class="col-4 p-0">
																			<img src="<?php echo tema;?>/uploads/haberler/<?php echo $HSonuc['resim'];?>" onerror="imgError(this);">
																		</div>
																		<div class="col-8 p-0 content">
																			<div>
																				<h4>
																				   <?php echo $HSonuc['adi'];?>
																				</h4>
																				<p><?php echo cVCLmHLxbS_tarih2($HSonuc['tarih']);?></p>
																			</div>
																		</div>
																	</div>
																</a>
															</div>
														</div>
														<?php }?>
														</div>
													</div>												
                                                 
                                            </div>
                                        </div>
                                    </div>
									<!-- MENÃœ TÄ°P 2 HABERLER ve HABER kategorisi KAPALI isteniyorsa BÄ°TÄ°Åž-->
									<?php } else if($kategori==1) { ?>
									<!-- MENÃœ TÄ°P 2 PROJELER ve PROJELER kategorisi KAPALI isteniyorsa BAÅžLANGIÃ‡-->
									<div class="header-dropdown bg-white">
                                        <div class="container">
										<div class="row">
                                              <img src="<?php echo tema;?>/uploads/arkaplan/arkaplan21/<?php echo $arkaplan['arkaplan21'];?>" class="bg-image">
													<div class="tab-panel active full py-5" id="projeler">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h3 class="g-title">
                                                                <?php echo $MENUSonuc['menu_isim']; ?>
                                                                    <a class="drp-tumunu-gor hover-bar" href="<?php echo $htc['projelerurl']; ?><?php echo $html;?>">
                                                                        <?=@$dil['txt3'];?>
                                                                    </a>
                                                                </h3>
                                                            </div>
															<?php $PSorgu = $db->prepare("SELECT * FROM projeler WHERE durum = ? AND dil = ? ORDER BY sira ASC LIMIT ".$ilimit."");
															$PSorgu->execute(array("1",$_SESSION['k_dil']));
															$Pislem = $PSorgu->fetchALL(PDO::FETCH_ASSOC);?>
															<?php foreach ( $Pislem as $PSonuc ){?>
															   <div class="col-3 my-3">
                                                                <div class="drp-haber-box">
                                                                    <a href="<?php echo $htc['projedetayurl']; ?>/<?php echo $PSonuc['seo']; ?><?php echo $html;?>">
                                                                        <div class="row m-0">
                                                                            <div class="col-4 p-0">
                                                                                <img src="<?php echo tema;?>/uploads/projeler/<?php echo $PSonuc['kapak'];?>" onerror="imgError(this);">
                                                                            </div>
                                                                            <div class="col-8 p-0 content">
                                                                                <div>
                                                                                    <h4>
                                                                                       <?php echo $PSonuc['adi'];?>
                                                                                    </h4>
                                                                                    <p><?php echo cVCLmHLxbS_tarih2($PSonuc['tarih']);?></p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            </div>
															<?php }?>
														 </div>
                                                   </div>
                                            </div>
                                        </div>
                                     </div>
									 <!-- MENÃœ TÄ°P 2 PROJELER ve PROJELER kategorisi KAPALI isteniyorsa BÄ°TÄ°Åž-->
									<?php }else{?>
									<!-- MENÃœ TÄ°P 2 PROFÄ°LLER ve PROFÄ°L kategorisi KAPALI isteniyorsa BAÅžLANGIÃ‡-->
									<div class="header-dropdown bg-white">
                                        <div class="container">
										<div class="row">
											<img src="<?php echo tema;?>/uploads/arkaplan/arkaplan21/<?php echo $arkaplan['arkaplan21'];?>" class="bg-image">
											<div class="tab-panel active full py-5" id="profiller">
												<div class="row">
													<div class="col-12">
														<h3 class="g-title">
														<?php echo $MENUSonuc['menu_isim']; ?>
															<a class="drp-tumunu-gor hover-bar" href="<?php echo $htc['profillerurl']; ?><?php echo $html;?>">
																<?=@$dil['txt3'];?>
															</a>
														</h3>
													</div>
													<?php $PSorgu = $db->prepare("SELECT * FROM profiller WHERE durum = ? AND dil = ? ORDER BY sira ASC LIMIT ".$ilimit."");
													$PSorgu->execute(array("1",$_SESSION['k_dil']));
													$Pislem = $PSorgu->fetchALL(PDO::FETCH_ASSOC);?>
													<?php foreach ( $Pislem as $PSonuc ){?>
													   <div class="col-3 my-3">
														<div class="drp-haber-box">
															<a href="<?php echo $htc['profildetayurl']; ?>/<?php echo $PSonuc['seo']; ?><?php echo $html;?>">
																<div class="row m-0">
																	<div class="col-4 p-0">
																		<img src="<?php echo tema;?>/uploads/profiller/<?php echo $PSonuc['kapak'];?>" onerror="imgError(this);">
																	</div>
																	<div class="col-8 p-0 content">
																		<div>
																			<h4>
																			   <?php echo $PSonuc['adi'];?>
																			</h4>
																			<p><?php echo $PSonuc['tarih'];?></p>
																		</div>
																	</div>
																</div>
															</a>
														</div>
													</div>
													<?php }?>
												 </div>
											</div>
                                           </div>
                                        </div>
                                     </div>
									 <!-- MENÃœ TÄ°P 2 PROFÄ°LLER ve PROFÄ°L kategorisi KAPALI isteniyorsa BÄ°TÄ°Åž-->
									<?php }?>
								<?php }?>								
								</li>
								<?php } else { ?>
								<!-- MENÃœ TÄ°P 2 BÄ°TÄ°Åž -->
								
								<!-- MENÃœ TÄ°P 3 BAÅžLANGIÃ‡ -->
                                <li class="header-item2 position-relative">
                                    <a <?php echo($MENUSonuc['sekme'] == 1 ? 'target="_blank"' : '');?> class="hover-bar" href="<?php echo($MENUSonuc['menu_url'] == "0" ? $MENUSonuc['link'] : $MENUSonuc['menu_url']); ?>">
                                        <?php if(!empty($MENUSonuc['menu_icon'])): ?><i class="<?php echo htmlspecialchars($MENUSonuc['menu_icon'], ENT_QUOTES, 'UTF-8'); ?>"></i><?php endif; ?>
                                        <?php echo $MENUSonuc['menu_isim']; ?>
                                    </a>
									<?php $ALTMENUSorgu = $db->prepare("SELECT * FROM menu WHERE menu_durum = ? AND menu_ust = ? AND dil = ? ORDER BY menu_sira ASC");
									$ALTMENUSorgu->execute(array("1",$MENUSonuc['id'],$_SESSION['k_dil']));
									$ALTMENUislem = $ALTMENUSorgu->fetchALL(PDO::FETCH_ASSOC);?>
									<?php if($ALTMENUSorgu->rowCount()){?>	
								   <ul class="header-dropdown-small">
									<?php foreach ( $ALTMENUislem as $ALTMENUSonuc ){?>
                                        <li><a <?php echo($ALTMENUSonuc['sekme'] == 1 ? 'target="_blank"' : '');?> href="<?php echo($ALTMENUSonuc['menu_url'] == "0" ? $ALTMENUSonuc['link'] : $ALTMENUSonuc['menu_url'].$html); ?>">
											<?php if(!empty($ALTMENUSonuc['menu_icon'])): ?><i class="<?php echo htmlspecialchars($ALTMENUSonuc['menu_icon'], ENT_QUOTES, 'UTF-8'); ?>"></i> <?php endif; ?>
											<?php echo $ALTMENUSonuc['menu_isim']; ?></a></li>
                                      <?php }?>  
                                    </ul>
									<?php }?>
                                </li>
								    <!-- MENÃœ TÄ°P 3 BÄ°TÅž -->
								<?php } } }?>
									
                                <?php }?>
                            </ul>

                        </div>
                        <div class="col-md-1 d-flex align-items-center">
                            <?php if($moduller['alan31'] == "1"){ ?>
                            <div class="header-bagis-button mr-2">
                                <a href="<?php echo $htc['bagisurl'];?><?php echo $html;?>" class="btn-bagis-yap" title="<?=@$dil['txt111'];?>">
                                    <i class="fas fa-heart"></i> <?=@$dil['txt111'];?>
                                </a>
                            </div>
                            <?php } ?>
                            <?php if($moduller['alan32'] == "1"){ ?>
                            <div class="search-box">
                                <a href="javascript:void(0)" id="btn-search">
                                    <i class="fas fa-search"></i>
                                </a>
                            </div>
                            <?php } ?>
                        </div>

                    </div>


                </div>
            </div>
            <!-- HEADER-BOTTOM BÄ°TÄ°Åž -->

            <!-- HEADER-MOBÄ°LE BAÅžLANGIÃ‡ -->
            <div class="header-mobile">
                <div class="container">

                    <div class="row">

                        <div class="col-4 hamburger-box">
                            <div id="sidebarCollapse" class="icon">
                                <div class="hamburger">
                                </div>
                            </div>
                        </div>
<div class="col-4 logo-box">
    <a href="<?php echo $htc['anaurl'];?><?php echo $html;?>">
        <img class="logo" src="<?php echo tema;?>/uploads/logo/<?php echo logo;?>" alt="<?php echo firma_adi;?>">
    </a>

<div class="custom-lang-dropdown">
    <button class="lang-btn" onclick="toggleLangMenu(event)">
        <i class="flag-icon flag-icon-tr current-flag"></i>
        <span class="arrow-down"></span>
    </button>
    
    <div class="lang-menu" id="langMenu">
        <a href="#" onclick="changeLang('tr', 'flag-icon-tr'); doGTranslate('tr|tr', this); return false;">
            <i class="flag-icon flag-icon-tr"></i> 
            <span>Türkçe</span>
        </a>
        <a href="#" onclick="changeLang('sa', 'flag-icon-sa'); doGTranslate('tr|ar', this); return false;">
            <i class="flag-icon flag-icon-sa"></i> 
            <span>Arabic</span>
        </a>
        <a href="#" onclick="changeLang('gb', 'flag-icon-gb'); doGTranslate('tr|en', this); return false;">
            <i class="flag-icon flag-icon-gb"></i> 
            <span>English</span>
        </a>
    </div>
</div>
</div>
 <?php if($moduller['alan32'] == "1"){ ?>
                        <div class="col-4">
                            <div class="search-box">
                                <a href="javascript:void(0)" id="btn-search2">
                                    <i class="fas fa-search"></i>
                                </a>
                            </div>
                            
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <!-- HEADER-MOBÄ°LE BÄ°TÄ°Åž -->

        </section>
        <!-- HEADER BÄ°TÄ°Åž -->

        <?php if($moduller['alan31'] == "1"){ ?>
        <style>
        @media (max-width: 768px){
.mobile-donate-fixed {
    position: relative;
    left: 0;
    right: 0;
    top: -6px;
    z-index: 3;
    display: flex;
    justify-content: center;
    height: 0pc;
}
.mobile-donate-fixed .btn {
    background: rgb(236 38 38) !important;
    border-color: rgb(207 44 61) !important;
    color: #fff;
    padding: 5px 8px;
    border-radius: 0px;
    box-shadow: 0 6px 16px rgba(0,0,0,.15);
    font-weight: 600;
    height: 34px;
    font-size: 13px;
    width: 100%;
}
        }
        </style>
        <div class="mobile-donate-fixed d-lg-none d-md-none">
            <a href="<?php echo $htc['bagisurl'];?><?php echo $html;?>" class="btn">
                <i class="fas fa-heart mr-1"></i> <?=@$dil['txt111'];?>
            </a>
        </div>
        <?php } ?>

        <!-- MOBÄ°LE MENU BAÅžLANGIÃ‡ -->
        <nav id="mobile-menu">
            <div id="dismiss">
                <i class="fas fa-arrow-left"></i>
            </div>

            <div class="sidebar-header">
	<a href="<?php echo $htc['anaurl'];?><?php echo $html;?>"><img style="max-width: 150px; height: auto; object-fit: contain;" class="logo z-index-9" src="<?php echo tema;?>/uploads/logo/<?php echo logo;?>" alt="<?php echo firma_adi;?>"></a>

            </div>

            <ul class="list-unstyled components">			
			<?php $MENUSorgu = $db->prepare("SELECT * FROM menu WHERE menu_durum = ? AND menu_ust = ? AND dil = ? ORDER BY menu_sira ASC");
			$MENUSorgu->execute(array("1","0",$_SESSION['k_dil']));
			$MENUislem = $MENUSorgu->fetchALL(PDO::FETCH_ASSOC);?>
			<?php foreach ( $MENUislem as $MENUSonuc ){?>
			<?php $altvarmi	= $db->query("SELECT * FROM menu WHERE menu_durum = '1' AND menu_ust = '{$MENUSonuc['id']}' ORDER BY id DESC LIMIT 1")->rowCount();?>
				<li>
				<a <?php echo($MENUSonuc['sekme'] == 1 ? 'target="_blank"' : '');?> <?php echo($altvarmi > 0 ? 'href="javascript:;" class="drp-mobile-link"' : 'href="'.($MENUSonuc['menu_url'] == "0" ? $MENUSonuc['link'] : $MENUSonuc['menu_url'].$html).'"');?>>
					<?php if(!empty($MENUSonuc['menu_icon'])): ?><i class="<?php echo htmlspecialchars($MENUSonuc['menu_icon'], ENT_QUOTES, 'UTF-8'); ?>"></i> <?php endif; ?>
					<?php echo $MENUSonuc['menu_isim']; ?> <?php echo($altvarmi > 0 ? ' <i class="fa fa-chevron-right float-right"></i>' : '');?></a>
				<?php $ALTMENUSorgu = $db->prepare("SELECT * FROM menu WHERE menu_durum = ? AND menu_ust = ? AND dil = ? ORDER BY menu_sira ASC");
				$ALTMENUSorgu->execute(array("1",$MENUSonuc['id'],$_SESSION['k_dil']));
				$ALTMENUislem = $ALTMENUSorgu->fetchALL(PDO::FETCH_ASSOC);?>
				<?php if($ALTMENUSorgu->rowCount()){?>
					 <ul class="drp-mobile list-unstyled" id="<?php echo cVCLmHLxbS_seo($MENUSonuc['menu_isim']); ?>">
					  <?php foreach ( $ALTMENUislem as $ALTMENUSonuc ){?>
					   <li><a <?php echo($ALTMENUSonuc['sekme'] == 1 ? 'target="_blank"' : '');?> href="<?php echo($ALTMENUSonuc['menu_url'] == "0" ? $ALTMENUSonuc['link'] : $ALTMENUSonuc['menu_url'].$html); ?>">
						   <?php if(!empty($ALTMENUSonuc['menu_icon'])): ?><i class="<?php echo htmlspecialchars($ALTMENUSonuc['menu_icon'], ENT_QUOTES, 'UTF-8'); ?>"></i> <?php endif; ?>
						   <?php echo $ALTMENUSonuc['menu_isim']; ?></a></li>
					   <?php }?>
					</ul>
					<?php }?>
				</li>
				<?php }?> 
            </ul>
        </nav>
        <!-- MOBÄ°LE MENU BÄ°TÄ°Åž -->
		<?php 
		if(isset($_GET['sayfa'])){
			$s = $_GET['sayfa'];
			switch($s){
				
			case ''.$htc['anaurl'].'';
			require_once("pages/anasayfa.php");
			break;
			
			case ''.$htc['sayfaurl'].'';
			require_once("pages/sayfalar.php");
			break;
			
			case ''.$htc['projekategoriurl'].'';
			require_once("pages/proje_kategori.php");
			break;
			
			case ''.$htc['projelerurl'].'';
			require_once("pages/projeler.php");
			break;

			case ''.$htc['etkierisimurl'].'';
			require_once("pages/etki_erisim.php");
			break;
			
			case ''.$htc['projedetayurl'].'';
			require_once("pages/proje_detay.php");
			break;
			
			case ''.$htc['haberurl'].'';
			require_once("pages/haberler.php");
			break;
			
			case ''.$htc['haberkategoriurl'].'';
			require_once("pages/haber_kategori.php");
			break;
			
			case ''.$htc['haberdetayurl'].'';
			require_once("pages/haber_detay.php");
			break;				
			
			case ''.$htc['hizmeturl'].'';
			require_once("pages/hizmetler.php");
			break;
			
			case ''.$htc['hizmetdetayurl'].'';
			require_once("pages/hizmet_detay.php");
			break;

			case ''.$htc['birimurl'].'';
			require_once("pages/birimler.php");
			break;
			
			case ''.$htc['birimdetayurl'].'';
			require_once("pages/birim_detay.php");
			break;	
						
			case ''.$htc['fotourl'].'';
			require_once("pages/foto_galeri.php");
			break;
			
			case ''.$htc['fotodetayurl'].'';
			require_once("pages/foto.php");
			break;
			
			case ''.$htc['videourl'].'';
			require_once("pages/video_galeri.php");
			break;

			case ''.$htc['dosyalarurl'].'';
			require_once("pages/dosyalar.php");
			break;
			
			case ''.$htc['videodetayurl'].'';
			require_once("pages/video.php");
			break;
			
			case ''.$htc['etkinlikurl'].'';
			require_once("pages/etkinlikler.php");
			break;
			
			case ''.$htc['etkinlikdetayurl'].'';
			require_once("pages/etkinlik_detay.php");
			break;
			
			case ''.$htc['duyuruurl'].'';
			require_once("pages/duyurular.php");
			break;
			
			case ''.$htc['duyurudetayurl'].'';
			require_once("pages/duyuru_detay.php");
			break;
			
			case ''.$htc['ihaleurl'].'';
			require_once("pages/ihaleler.php");
			break;
			
			case ''.$htc['ihaledetayurl'].'';
			require_once("pages/ihale_detay.php");
			break;
			
			case ''.$htc['ilanurl'].'';
			require_once("pages/ilanlar.php");
			break;
			
			case ''.$htc['ilandetayurl'].'';
			require_once("pages/ilan_detay.php");
			break;
			
			case ''.$htc['kararurl'].'';
			require_once("pages/meclis_kararlari.php");
			break;
			
			case ''.$htc['karardetayurl'].'';
			require_once("pages/meclis_kararlari_detay.php");
			break;
			
			case ''.$htc['faaliyeturl'].'';
			require_once("pages/faaliyet_raporlari.php");
			break;
			
			case ''.$htc['faaliyetdetayurl'].'';
			require_once("pages/faaliyet_raporlari_detay.php");
			break;
			
			case ''.$htc['profillerurl'].'';
			require_once("pages/profiller.php");
			break;
			
			case ''.$htc['profilkategoriurl'].'';
			require_once("pages/profil_kategori.php");
			break;
			
			case ''.$htc['profildetayurl'].'';
			require_once("pages/profil_detay.php");
			break;
			
			case ''.$htc['bagisurl'].'';
			require_once("pages/bagis.php");
			break;

			case 'bagis-detay':
			require_once("pages/bagis_detay.php");
			break;
			
			case ''.$htc['bagissepeturl'].'';
			require_once("pages/bagis_sepet.php");
			break;
			
			case ''.$htc['bagisodemeurl'].'';
			require_once("pages/bagis_odeme.php");
			break;
			
			case ''.$htc['bagissonucurl'].'';
			require_once("pages/bagis_sonuc.php");
			break;
			
			case ''.$htc['aidaturl'].'';
			require_once("pages/aidat.php");
			break;
			
			case ''.$htc['aidatlisteurl'].'';
			require_once("pages/aidat_listesi.php");
			break;
			
		case ''.$htc['aidatodemeurl'].'';
		require_once("pages/aidat_odeme.php");
		break;
		
		case ''.$htc['aidatsonucurl'].'';
		require_once("pages/aidat_sonuc.php");
		break;
		
		case ''.$htc['programlarurl'].'';
		require_once("pages/programlar.php");
		break;

		case ''.$htc['programdetayurl'].'';
		require_once("pages/program_detay.php");
		break;

		case ''.$htc['karakterprogramlariurl'].'';
		require_once("pages/karakter_programlari.php");
		break;

		case ''.$htc['karakterprogramdetayurl'].'';
		require_once("pages/karakter_program_detay.php");
		break;
		
		case ''.$htc['bagismodulurl'].'';
		require_once("pages/bagis_moduller.php");
		break;
		
		case ''.$htc['bagismoduldetayurl'].'';
		case 'bagis-kampanya';
		// Tekil sayfa kontrolÃ¼
		$Sorgu = $db->prepare("SELECT tekil_sayfa FROM bagis_moduller WHERE seo = ? AND durum = 1");
		$Sorgu->execute(array($_GET['id']));
		if($Sorgu->rowCount() > 0)
		{
			$Modul = $Sorgu->fetch(PDO::FETCH_ASSOC);
			if($Modul['tekil_sayfa'] == 1)
			{
				require_once("pages/bagis_tekil_sayfa.php");
			}
			else
			{
				require_once("pages/bagis_modul_detay.php");
			}
		}
		else
		{
			require_once("pages/bagis_modul_detay.php");
		}
		break;
		

		
		case ''.$htc['randevuurl'].'';
		require_once("pages/randevu.php");
		break;
			
			case '404';
			require_once("pages/404.php");
			break;
			
			case 'ara';
			require_once("pages/ara.php");
			break;
			
			case ''.$htc['iletisimurl'].'';
			require_once("pages/iletisim.php");
			break;

			case ''.$htc['derslerurl'].'';
			require_once("pages/dersler.php");
			break;
			
			
		case ''.$htc['hesapnumaralarimizurl'].'';
		require_once("pages/hesap_numaralarimiz.php");
		break;
		
		case ''.$htc['okullarurl'].'';
		require_once("pages/okullar.php");
		break;
		
	case ''.$htc['ogrenme_deneyimiurl'].'';
		require_once("pages/ogrenme_deneyimi.php");
		break;
		
	case ''.$htc['ogrenme_deneyimi_detayurl'].'';
		require_once("pages/ogrenme_deneyimi_detay.php");
		break;
		
	case ''.$htc['destekleme_yollariurl'].'';
		require_once("pages/destekleme_yollari.php");
		break;
		
	case ''.$htc['destekleme_yollari_detayurl'].'';
		require_once("pages/destekleme_yollari_detay.php");
		break;
						
	case 'mizac-testi':
		require_once("pages/mizac_testi.php");
		break;

	default:
			require_once("pages/anasayfa.php");
			}
		}else{
		require_once("pages/anasayfa.php");
		}
		?>
        <!-- FOOTER SECTÄ°ON BAÅžLANGIÃ‡ -->
        <footer class="footer">
			<div class="footer-ust">
				<div class="container">
					<div class="footer-row row">
						<div class="footer-col col-lg-9">
							<div class="row">
							<?php $FMENUSorgu = $db->prepare("SELECT * FROM footermenu WHERE menu_durum = ? AND menu_ust = ? AND dil = ? ORDER BY menu_sira ASC");
							$FMENUSorgu->execute(array("1","0",$_SESSION['k_dil']));
							$FMENUislem = $FMENUSorgu->fetchALL(PDO::FETCH_ASSOC);?>
								<?php foreach ( $FMENUislem as $FMENUSonuc ){?>
								<div class="footer-dbv7">
									<div class="footer-baslik"><?php echo $FMENUSonuc['menu_isim']; ?></div>
									<ul>
									<?php $FALTMENUSorgu = $db->prepare("SELECT * FROM footermenu WHERE menu_durum = ? AND menu_ust = ? AND dil = ? ORDER BY menu_sira ASC");
									$FALTMENUSorgu->execute(array("1",$FMENUSonuc['id'],$_SESSION['k_dil']));
									$FALTMENUislem = $FALTMENUSorgu->fetchALL(PDO::FETCH_ASSOC);?>
										<?php foreach ( $FALTMENUislem as $FALTMENUSonuc ){?>
										<li>
											<a <?php echo($FALTMENUSonuc['sekme'] == 1 ? 'target="_blank"' : '');?> href="<?php echo($FALTMENUSonuc['menu_url'] == "0" ? $FALTMENUSonuc['link'] : $FALTMENUSonuc['menu_url'].$html); ?>"><?php echo $FALTMENUSonuc['menu_isim']; ?></a>
										</li>
										<?php }?>
									</ul>
								</div>
								<?php }?>
							</div>
						</div>
						<div class="footer-col footer-son col-lg-3">
							<div style=" margin-top: 30px; " class="footer-logo"><a href="<?php echo $htc['anaurl'];?><?php echo $html;?>"><img src="<?php echo tema;?>/uploads/logo/footer/<?php echo footerlogo;?>" alt="Logo"></a></div>
							<div class="footer-alan">
								<div class="footerkutu align-center">									
									<div class="description">										
										<div class="text"><?=@$dil['txt4'];?></div>
										<div class="title"><?php echo telefon;?></div>
									</div>
								</div>
							</div>
							<?php if($moduller['alan22'] == "1"){?>
							<div class="mt-3 hava-kutular">								
							<?php 
							$Tarih = date('Y-m-d');
							$query = $db->query("SELECT * FROM havadurumu WHERE tarih LIKE '%{$Tarih}%' ORDER BY tarih DESC LIMIT 3", PDO::FETCH_ASSOC);
							if ( $query->rowCount() ){
							echo '<div class="mt-3 hava-kutular">';
							foreach ($query as $hava){ ?>
								<div class="hava-kutu">
									<h1><?php echo $hava['derece'];?> <img src="<?php echo $hava['img'];?>"></h1>
									<p class="gun"><?php setlocale(LC_TIME, 'tr_TR.UTF-8'); echo  strftime('%A');?></p>
									<p><?php echo $hava['tahmin'];?></p>
								</div>
							<?php  }
							echo '</div>';
							}
							?>
							</div>
							<?php }?>
							<div class="footer-social">
								<?php if(facebook){?><a title="facebook" href="<?php echo facebook;?>"><i class="fab fa-facebook-f"></i></a><?php }?>
								<?php if(twitter){?><a title="telegram" href="<?php echo twitter;?>"><i class="fab fa-twitter"></i></a><?php }?>
								<?php if(instagram){?><a title="instagram" href="<?php echo instagram;?>"><i class="fab fa-instagram"></i></a><?php }?>
								<?php if(linkedin){?><a title="linkedin" href="<?php echo linkedin;?>"><i class="fab fa-linkedin-in"></i></a><?php }?>
								<?php if(youtube){?><a title="youtube" href="<?php echo youtube;?>"><i class="fab fa-youtube"></i></a><?php }?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="footer-bottom">
				<div class="container footerkutu align-center justify-between">
					<div class="text copyright"><?php echo copyright;?></div>
					<a class="text design" href="" target="_blank"></a>
				</div>
			</div>
		</footer>
        <!-- FOOTER SECTÄ°ON BÄ°TÄ°Åž -->

    </main>
    <!-- MAÄ°N BÄ°TÄ°Åž -->

    <!-- SEARCH MODAL BAÅžLANGIÃ‡ -->
    <div class="search">
        <button id="btn-search-close" class="btn btn--search-close" aria-label="Close search form"><svg class="icon icon--cross">
                <use xlink:href="#icon-cross"></use>
            </svg>
		</button>
        <form class="search__form" action="ara<?php echo $html;?>">
            <input class="search__input" name="kelime" type="search" placeholder="" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />
            <span class="search__info"><?=@$dil['txt5'];?></span>
        </form>
    </div>
    <!-- SEARCH MODAL BÄ°TÄ°Åž -->

    <!-- UP BUTON BAÅžLANGIÃ‡ -->

    <button id="page-up"> <i class="fa fa-chevron-up"></i> </button>

    <!-- UP BUTON BÄ°TÄ°Åž -->

    <!-- PAGE LOADER BAÅžLANGIÃ‡ -->
	<?php if($moduller['alan19'] == "1"){?>
    <div class="loader">
        <div class="la-square-loader">
            <div></div>
        </div>
    </div>
	<?php }?>
    <!-- PAGE LOADER BÄ°TÄ°Åž -->


    <!-- SEARCH MODAL SVG BAÅžLANGIÃ‡ -->
    <!-- bu svg kodlarÄ± arama kÄ±smÄ±ndaki icon vs iÃ§in kullanÄ±ldÄ± silmeyin -->
    <svg class="hidden">
        <defs>
            <symbol id="icon-arrow" viewBox="0 0 24 24">
                <title>arrow</title>
                <polygon points="6.3,12.8 20.9,12.8 20.9,11.2 6.3,11.2 10.2,7.2 9,6 3.1,12 9,18 10.2,16.8 " />
            </symbol>
            <symbol id="icon-drop" viewBox="0 0 24 24">
                <title>drop</title>
                <path d="M12,21c-3.6,0-6.6-3-6.6-6.6C5.4,11,10.8,4,11.4,3.2C11.6,3.1,11.8,3,12,3s0.4,0.1,0.6,0.3c0.6,0.8,6.1,7.8,6.1,11.2C18.6,18.1,15.6,21,12,21zM12,4.8c-1.8,2.4-5.2,7.4-5.2,9.6c0,2.9,2.3,5.2,5.2,5.2s5.2-2.3,5.2-5.2C17.2,12.2,13.8,7.3,12,4.8z" />
                <path d="M12,18.2c-0.4,0-0.7-0.3-0.7-0.7s0.3-0.7,0.7-0.7c1.3,0,2.4-1.1,2.4-2.4c0-0.4,0.3-0.7,0.7-0.7c0.4,0,0.7,0.3,0.7,0.7C15.8,16.5,14.1,18.2,12,18.2z" />
            </symbol>
            <symbol id="icon-search" viewBox="0 0 24 24">
                <title>search</title>
                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
            </symbol>
            <symbol id="icon-cross" viewBox="0 0 24 24">
                <title>cross</title>
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
            </symbol>
        </defs>
    </svg>
    <!-- SEARCH MODAL SVG BÄ°TÄ°Åž -->
    <div class="overlay"></div>    
    <script src="<?php echo tema;?>/assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?php echo tema;?>/assets/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>
    <script src="<?php echo tema;?>/assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="<?php echo tema;?>/assets/bower_components/jquery.nicescroll/dist/jquery.nicescroll.min.js"></script>
    <script src="<?php echo tema;?>/assets/bower_components/plyr-master/dist/plyr.js"></script>
	<script src="<?php echo tema;?>/assets/js/fancybox.js"></script>
	<script src="<?php echo tema;?>/assets/js/iziModal.min.js"></script>
    <script src="<?php echo tema;?>/assets/js/main.js"></script>
	<script>
	$(document).on('click', '.dildegis', function () {
		var dilID = $(this).data("id");
		$.ajax({
			url: 'dildegis.php',
			dataType: 'JSON',
			data: {id: dilID},
		})
		.done(function(msg) {
			if(msg.hata){
				alert("<?=@$dil['txt575'];?>");
			}else{
				window.location = "index.html";
			}
		})
		.fail(function(err) {
			console.log(err);
		});
	});
	</script>
	
	<script type="text/javascript">
	if($("#modal-demo").length){
		$("#modal-demo").iziModal({
			title: "",
			subtitle: "",
			iconClass: '',
			background:null,
			theme:'light',
			closeButton:true,
			overlay:true,
			overlayClose:true,
			transitionInOverlay:'fadeIn',
			transitionOutOverlay:'fadeOut',
			overlayColor: 'rgba(0, 0, 0, 0.85)',
			width: 500,
			padding: 20
		});
		$(document).on('click', '.trigger-link', function (event) {
			event.preventDefault();
			$('#modal-demo').iziModal('open');
		});
	}
	</script>
	<script>
	$(document).ready(function(){
	  $(".col-kolaymenu").click(function(){
		$(".kolay-menu > ul").toggle();
	  });
	});
	function addBasket(itemID = 0){
		if(isNaN(itemID) || itemID <= 0){
			swal({
				type: 'warning',
				title: '<?=@$dil['txt6'];?>',
				text: '<?=@$dil['txt7'];?>',
				confirmButtonText: '<?=@$dil['txt8'];?>',
				timer: 5000
			})
			return
		}
		var priceDom = $("input[data-id='"+itemID+"']");
		if($(priceDom).length == 0){
			swal({
				type: 'warning',
				title: '<?=@$dil['txt6'];?>',
				text: '<?=@$dil['txt7'];?>',
				confirmButtonText: '<?=@$dil['txt8'];?>',
				timer: 5000
			})
			return
		}
		var price = parseFloat(($(priceDom).val()).replace(",","."));
		if(isNaN(price)){
			swal({
				type: 'warning',
				title: '<?=@$dil['txt6'];?>',
				text: '<?=@$dil['txt9'];?>',
				confirmButtonText: '<?=@$dil['txt8'];?>',
				timer: 5000
			})
			return
		}
		if(price <= 0.99){
			swal({
				type: 'warning',
				title: '<?=@$dil['txt6'];?>',
				text: '<?=@$dil['txt10'];?>',
				confirmButtonText: '<?=@$dil['txt8'];?>',
				timer: 5000
			})
			return
		}
		$.ajax({
			url: 'sepete-ekle.php',
			type: 'POST',
			dataType: 'json',
			data: {id: itemID,price: price},
		})
		.done(function(msg) {
			basketReload();			
		})
		.fail(function(err) {
			basketReload();
			swal({
				type: 'success',
				title: '<?=@$dil['txt11'];?>',
				text: '<?=@$dil['txt12'];?>',
				confirmButtonText: '<?=@$dil['txt8'];?>',
				timer: 5000
			})
		});
	}

	function basketReload(){
		var domCheck = $("div[name='sepetdiv']");
		if($(domCheck).length == 0) return
		$.ajax({
			url: 'sepete-ekle.php',
			dataType: 'html',
		})
		.done(function(html) {
			$(domCheck).html(html);
			// Sepeti localStorage'a kaydet
			try {
				localStorage.setItem('basket_backup', html);
				localStorage.setItem('basket_backup_time', Date.now().toString());
			} catch(e) {
				console.log('Sepet kaydetme hatası:', e);
			}
		})
		.fail(function() {
			console.log("error");
		});
	}
	
	// Sayfa yüklendiğinde sepeti geri yükle
	$(document).ready(function() {
		// Bağış sayfasındaysa sepeti geri yükle
		if (window.location.href.indexOf('bagis') !== -1) {
			setTimeout(function() {
				if (typeof restoreBasketFromLocalStorage === 'function') {
					restoreBasketFromLocalStorage();
				}
			}, 500);
		}
	});

	$(document).on('click', '[data-sil]', function(event) {
		event.preventDefault();
		var sesID = $(this).data('sil');
		$.ajax({
			url: 'sepete-ekle.php',
			type: 'POST',
			dataType: 'json',
			data: {sil: sesID},
		})
		.always(function() {
			var domCheck = $("div[name='sepetdiv']");
			if($(domCheck).length == 0){
				window.location.reload()
			}else{
				basketReload();
			}
		});
		
	});

	$(document).ready(function() {
		basketReload();
	});
	
	// imgError global fonksiyon - Google Translate koruması ile
	if (typeof window.imgError !== 'function') {
		window.imgError = function(image) {
			if (image && image.nodeName === 'IMG') {
				image.onerror = null;
				image.src = "<?php echo tema;?>/assets/images/no-image.png";
				return true;
			}
		};
	}
	
	function imgError(image) {
		return window.imgError(image);
	}
	$(document).ready(function() {
		$(window).scroll(function() {
			$('.lazy').each(function() {
				if ($(this).offset().top < ($(window).scrollTop() + $(window).height() + 100)) {
					$(this).attr('src', $(this).attr('data-src'));
				}
			});
		});
	});
	</script>
	<script>
    // Language Link Fixer
    document.addEventListener("DOMContentLoaded", function() {
        var path = window.location.pathname;
        var langPrefix = null;
        
        // Detect language prefix
        if (path.match(/^\/en(\/|$)/)) {
            langPrefix = '/en';
        } else if (path.match(/^\/ar(\/|$)/)) {
            langPrefix = '/ar';
        }

        if (langPrefix) {
            var links = document.querySelectorAll('a[href]');
            var origin = window.location.origin;

            links.forEach(function(link) {
                var href = link.getAttribute('href');
                
                // Skip if empty, javascript:, anchor #, external, or already prefixed
                if (!href || href.startsWith('javascript:') || href.startsWith('#') || href.startsWith('tel:') || href.startsWith('mailto:')) return;
                
                // Handle absolute URLs with same origin
                if (href.startsWith(origin)) {
                    href = href.replace(origin, '');
                }
                
                // Process relative paths starting with / or simple strings
                // Check if it's internal (relative or same domain)
                var isInternal = (href.startsWith('/') || !href.startsWith('http'));
                
                if (isInternal) {
                    // Normalize href
                    var checkHref = href.startsWith('/') ? href : '/' + href;
                    
                    // Don't prefix if valid file extension (exclude .html/php/xml)
                    if (checkHref.match(/\.(jpg|jpeg|png|gif|css|js|pdf|ico|woff|woff2|ttf|svg)$/i)) return;

                    // Avoid double prefixing
                    if (!checkHref.startsWith(langPrefix)) {
                        // Special handling for clean slash
                        var newHref = langPrefix + (href.startsWith('/') ? '' : '/') + href;
                        link.setAttribute('href', newHref);
                    }
                }
            });
        }
    });
	</script>
	
	<script>
    // Google Translate Auto-Redirect (Widget -> URL)
    function getCookie(name) {
        var v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
        return v ? v[2] : null;
    }

    function checkLanguageAndRedirect() {
        var cookieVal = getCookie('googtrans');
        var lang = 'tr';
        
        if (cookieVal) {
            // Cookie format usually: /from/to or /auto/to
            var parts = cookieVal.split('/');
            // Take the last part which is usually the target language
            if(parts.length > 0) lang = parts[parts.length-1];
            if(!lang) lang = 'tr';
        }

        var path = window.location.pathname;
        var search = window.location.search;
        var pfx = ['en', 'ar']; // Supported languages
        
        // Log for debug (optional)
        // console.log('GT Check:', lang, path);

        if (pfx.indexOf(lang) > -1) {
            // Target is a foreign language (en or ar)
            // Check if URL already starts with /lang
            if (path.indexOf('/' + lang) !== 0) {
                // Determine new path
                var newPath = path;
                
                // Remove any existing supported prefix
                for (var i = 0; i < pfx.length; i++) {
                    if (newPath.indexOf('/' + pfx[i]) === 0) {
                        newPath = newPath.replace('/' + pfx[i], '');
                        break;
                    }
                }
                
                // Normalize slash
                if (newPath.charAt(0) !== '/') newPath = '/' + newPath;
                
                // Construct final URL
                var finalUrl = '/' + lang + newPath + search;
                
                // Redirect
                window.location.href = finalUrl;
            }
        } else {
            // Target is default (tr) or unknown/auto (treat as default)
            // If URL has a foreign prefix, strip it
            var hasPrefix = false;
            var newPath = path;
            
            for (var i = 0; i < pfx.length; i++) {
                if (newPath.indexOf('/' + pfx[i]) === 0) {
                    newPath = newPath.replace('/' + pfx[i], '');
                    hasPrefix = true;
                    break;
                }
            }
            
            if (hasPrefix) {
                if (newPath.charAt(0) !== '/') newPath = '/' + newPath;
                // Redirect back to root/default
                window.location.href = newPath + search;
            }
        }
    }

    // Check periodically
    setInterval(checkLanguageAndRedirect, 1000);

    // Also check on mutation (if GT modifies DOM)
    var observer = new MutationObserver(function(mutations) {
        checkLanguageAndRedirect();
    });
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class', 'lang'] });
	</script>
	<script>
function toggleLangMenu(event) {
    event.stopPropagation(); 
    document.getElementById("langMenu").classList.toggle("show-menu");
}

function toggleDesktopLangMenu(event) {
    event.stopPropagation();
    var menu = document.getElementById("desktopLangMenu");
    if (menu) menu.classList.toggle("show-menu");
}

function changeLang(countryCode, iconClass) {
    // Tüm dil butonlarındaki bayrak ikonunu güncelle (PC + mobil)
    var flagIcons = document.querySelectorAll('.lang-btn .current-flag, .lang-btn-desktop .current-flag');
    flagIcons.forEach(function(icon) {
        icon.className = 'flag-icon current-flag ' + iconClass;
    });
    
    var langMenu = document.getElementById("langMenu");
    if (langMenu) langMenu.classList.remove("show-menu");
}

window.onclick = function(event) {
    if (!event.target.matches('.lang-btn') && !event.target.matches('.lang-btn-desktop') && !event.target.closest('.lang-btn') && !event.target.closest('.lang-btn-desktop')) {
        var dropdowns = document.getElementsByClassName("lang-menu");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show-menu')) {
                openDropdown.classList.remove('show-menu');
            }
        }
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (!empty($_SESSION['swal_contact'])): ?>
<script>
Swal.fire({
    icon: '<?= $_SESSION['swal_contact']['type']; ?>',
    title: '<?= $_SESSION['swal_contact']['title']; ?>',
    text: '<?= $_SESSION['swal_contact']['message']; ?>',
    confirmButtonText: 'Tamam',
    timer: 3000,
    timerProgressBar: true
});
</script>
<?php unset($_SESSION['swal_contact']); endif; ?>

	<?php 
	cVCLmHLxbS_site_mesaj("mesajbtn",1,"yes",@$dil['txt11'],@$dil['txt13'],@$dil['txt8']);
	cVCLmHLxbS_site_mesaj("mesajbtn",2,"no",@$dil['txt14'],@$dil['txt15'],@$dil['txt8']);
	cVCLmHLxbS_site_mesaj("mesajbtn",3,"bos",@$dil['txt16'],@$dil['txt17'],@$dil['txt8']);
	cVCLmHLxbS_site_mesaj("sitedemo",3,"no",@$dil['txt6'],@$dil['txt18'],@$dil['txt8']);
	?>
</body>

</html>