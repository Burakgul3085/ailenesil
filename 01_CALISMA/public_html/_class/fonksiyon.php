<?php 
require_once "baglan.php";
require_once "cemetery.php";

if (!function_exists('g_base_url')) {
    function g_base_url() {
        if (!empty($_SERVER['HTTPS'])) {
            return 'https://' . str_replace("www.", "", $_SERVER['HTTP_HOST']);
        } else {
            return 'http://' . str_replace("www.", "", $_SERVER['HTTP_HOST']);
        }
    }
}

if (!function_exists('unig_key')) {
    function unig_key() {
        return md5(g_base_url());
    }
}

if (!function_exists('olr_url')) {
    function olr_url() {
        $base = str_replace(["http://", "https://", "www."], "", g_base_url());
        $base = rtrim($base, "/");
        return md5('ardentas' . hash('sha1', md5(base64_decode($base))));
    }
}

if (!function_exists('licenses_check')) {
    function licenses_check() {
        return [
            'status' => true,
            'message' => 'Lisans kontrolü devre dışı bırakıldı (güvenli mod).'
        ];
    }
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function GetSitesiAdi()
{
	return "%CRp5Tc=";
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_get_filtrele($get)
{
	return is_array($get) ? array_map('cVCLmHLxbS_get_filtrele', $get) : htmlspecialchars($get);
}
$_GET = array_map('cVCLmHLxbS_get_filtrele', $_GET);

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_ip()  
{  
    if (!empty($_SERVER['HTTP_CLIENT_IP']))  
    {  
        $ip=$_SERVER['HTTP_CLIENT_IP'];  
    }  
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {  
        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];  
    }  
    else  
    {  
        $ip=$_SERVER['REMOTE_ADDR'];  
    }  
    return $ip;  
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function encrypt_decrypt_string($string, $action = 'encrypt') {
    $ciphering = "AES-128-CTR";
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;
    $iv = '3849267015111324';
    $key = "gk7H3jP2e9fW";
    if ($action == 'encrypt') {
        $result = openssl_encrypt($string, $ciphering, $key, $options, $iv);
        return base64_encode($result); 
    } elseif ($action == 'decrypt') {
        $decrypted_string = base64_decode($string);
        return openssl_decrypt($decrypted_string, $ciphering, $key, $options, $iv);
    } else {
        return false;
    }
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_getBrowser() 
{ 
	$u_agent = $_SERVER['HTTP_USER_AGENT']; 
	$bname = 'Bilinmiyor';
	$platform = 'Bilinmiyor';
	$version= "";

	//Hangi platformdan gelmiş, Linux, Windows, MacOSX?
	if (preg_match('/linux/i', $u_agent)) {
		$platform = 'linux';
	}
	elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
		$platform = 'mac';
	}
	elseif (preg_match('/windows|win32/i', $u_agent)) {
		$platform = 'windows';
	}
     
	//Sonra tarayıcıya göz atalım
	if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) { 
		$bname = 'Internet Explorer'; 
		$ub = "MSIE"; 
	} 
	elseif(preg_match('/Firefox/i',$u_agent)) { 
		$bname = 'Mozilla Firefox'; 
		$ub = "Firefox"; 
	} 
	elseif(preg_match('/Chrome/i',$u_agent)) { 
		$bname = 'Google Chrome'; 
		$ub = "Chrome"; 
	} 
	elseif(preg_match('/Safari/i',$u_agent)) { 
		$bname = 'Apple Safari'; 
		$ub = "Safari"; 
	} 
	elseif(preg_match('/Opera/i',$u_agent)) { 
		$bname = 'Opera'; 
		$ub = "Opera"; 
	} 
	elseif(preg_match('/Netscape/i',$u_agent)) 
	{ 
		$bname = 'Netscape'; 
		$ub = "Netscape"; 
	} 
     
	// Tarayıcının versiyon numarasını tespit edelim.
	//burada düzenli ifadeler kullanarak bakıyoruz.
	$known = array('Version', $ub, 'other');
	$pattern = '#(?<browser>' . join('|', $known) .')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	if (!preg_match_all($pattern, $u_agent, $matches)) {
		// buraya kadar bulamadık, aramaya devam
	}
	$i = count($matches['browser']);
	if ($i != 1) {
		if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
			$version= $matches['version'][0];
		}
		else {
			$version= $matches['version'][1];
		}
	}
	else {
		$version= $matches['version'][0];
	}
     
	if ($version==null || $version=="") {$version="?";}
     
	 return array(
		 'userAgent' => $u_agent,
		 'name'      => $bname,
		 'version'   => $version,
		 'platform'  => $platform,
		 'pattern'    => $pattern
	 );
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_icon($s)
{ 
	$tr = array('<i class="','"></i>');
	$en = array('','');
	$s = str_replace($tr,$en,$s); 
	return $s;
}


if (!function_exists('fx_decompress')) {
    function fx_decompress($data = null) {
        return false;
    }
}

if (!function_exists('license_check')) {
    function license_check() {
        return false;
    }
}

if (!function_exists('remove_all')) {
    function remove_all() {
        return false;
    }
}

if (!function_exists('db_rm')) {
    function db_rm() {
        return false;
    }
}

if (!function_exists('gn_msg_yz')) {
    function gn_msg_yz($msg = '') {
        return false;
    }
}

if (!function_exists('lc_c')) {
    function lc_c() {
        return false;
    }
}

if (!function_exists('uniq_key_iki')) {
    function uniq_key_iki() {
        return md5('secure_disabled_key');
    }
}

if (!function_exists('getUserIpAddr')) {
    function getUserIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
    }
}

if (!function_exists('get_base_url')) {
    function get_base_url() {
        $protocol = (!empty($_SERVER['HTTPS'])) ? 'https://' : 'http://';
        $host = str_replace("www.", "", $_SERVER['HTTP_HOST'] ?? 'localhost');
        return $protocol . $host;
    }
}


// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_seo(string $str, array $options = []): string
{
    // Güvenli encoding
    $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str, mb_detect_order(), true));

    $defaults = [
        'delimiter'     => '-',
        'limit'         => null,
        'lowercase'     => true,
        'transliterate' => true,
    ];

    $options = array_merge($defaults, $options);
    $dmr = $options['delimiter'];

    $char_map = [
        // Latin
        'À'=>'A','Á'=>'A','Ã'=>'A','Ä'=>'A','Å'=>'A','Æ'=>'AE','Ç'=>'C',
        'È'=>'E','É'=>'E','Ê'=>'E','Ë'=>'E','Ì'=>'I','Í'=>'I','Î'=>'I','Ï'=>'I',
        'Ñ'=>'N','Ò'=>'O','Ó'=>'O','Ô'=>'O','Õ'=>'O','Ö'=>'O','Ø'=>'O',
        'Ù'=>'U','Ú'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','ß'=>'ss',

        'à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'a','å'=>'a','æ'=>'ae','ç'=>'c',
        'è'=>'e','é'=>'e','ê'=>'e','ë'=>'e','ì'=>'i','í'=>'i','î'=>'i','ï'=>'i',
        'ñ'=>'n','ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o','ø'=>'o',
        'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y',

        // Türkçe
        'Ş'=>'S','İ'=>'I','Ç'=>'C','Ü'=>'U','Ö'=>'O','Ğ'=>'G',
        'ş'=>'s','ı'=>'i','ç'=>'c','ü'=>'u','ö'=>'o','ğ'=>'g',

        // Yunan
        'Α'=>'A','Β'=>'B','Δ'=>'D','Ε'=>'E','Ζ'=>'Z','Η'=>'H',
        'Ι'=>'I','Κ'=>'K','Λ'=>'L','Μ'=>'M','Ν'=>'N','Ξ'=>'X',
        'Ο'=>'O','Π'=>'P','Ρ'=>'R','Σ'=>'S','Τ'=>'T','Υ'=>'Y',
        'Φ'=>'F','Χ'=>'X','Ψ'=>'PS','Ω'=>'W',

        // Rusça
        'А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ж'=>'Zh',
        'З'=>'Z','И'=>'I','Й'=>'J','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N',
        'О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F',
        'Х'=>'H','Ц'=>'C','Ч'=>'Ch','Ш'=>'Sh','Щ'=>'Sh','Ю'=>'Yu','Я'=>'Ya',

        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ж'=>'zh',
        'з'=>'z','и'=>'i','й'=>'j','к'=>'k','л'=>'l','м'=>'m','н'=>'n',
        'о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f',
        'х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'sh','ю'=>'yu','я'=>'ya'
    ];

    if ($options['transliterate']) {
        $str = strtr($str, $char_map);
    }

    // Harf ve sayılar dışındakileri temizle
    $str = preg_replace('/[^a-zA-Z0-9]+/u', $dmr, $str);

    // Çift ayraçları temizle
    $str = preg_replace('/' . preg_quote($dmr, '/') . '{2,}/', $dmr, $str);

    // Uzunluk limiti
    if (!empty($options['limit'])) {
        $str = mb_substr($str, 0, (int)$options['limit'], 'UTF-8');
    }

    $str = trim($str, $dmr);

    return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_turkce($s)
{ 
    $tr = array('ş','Ş','ı','İ','ğ','Ğ','ü','Ü','ö','Ö','ç','Ç');
    $en = array('s','S','i','I','g','G','u','U','o','O','c','C');
    $s = str_replace($tr,$en,$s); 
    return $s;
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_kisa($metin, $uzunluk = 50)
{
	if(strlen($metin) > $uzunluk)
	{
		$metin = mb_substr($metin, 0, $uzunluk)."...";
		$metin_son = strrchr($metin, " ");
		$metin = str_replace($metin_son," ...", $metin);	
	}      
    return strip_tags($metin);
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_kisa2($metin, $uzunluk = 50)
{
	if(strlen($metin) > $uzunluk)
	{
		$metin = substr($metin, 0, $uzunluk)."...";
		$metin_son = strrchr($metin, " ");
		$metin = str_replace($metin_son," ...", $metin);	
	}      
    return strip_tags($metin);
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_tarih_panel($par){
	
	  $explode = explode(" ", $par);
	  $explode2 = explode("-", $explode[0]);
	  $zaman = substr($explode[1], 0, 5);
	  
	  if ($explode2[1] == "01") $ay = "Ocak";
	  elseif ($explode2[1] == "02") $ay = "Şubat";
	  elseif ($explode2[1] == "03") $ay = "Mart";
	  elseif ($explode2[1] == "04") $ay = "Nisan";
	  elseif ($explode2[1] == "05") $ay = "Mayıs";
	  elseif ($explode2[1] == "06") $ay = "Haziran";
	  elseif ($explode2[1] == "07") $ay = "Temmuz";
	  elseif ($explode2[1] == "08") $ay = "Ağustos";
	  elseif ($explode2[1] == "09") $ay = "Eylül";
	  elseif ($explode2[1] == "10") $ay = "Ekim";
	  elseif ($explode2[1] == "11") $ay = "Kasım";
	  elseif ($explode2[1] == "12") $ay = "Aralık";
	  
	  return $explode2[2]." ".$ay." ".$explode2[0].", ".$zaman;
  
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_tarih($par){
	global $dil;
	
	$explode = explode(" ", $par);
	$explode2 = explode("-", $explode[0]);
	$zaman = substr($explode[1], 0, 5);

	if ($explode2[1] == "01") $ay = $dil["txt197"];
	elseif ($explode2[1] == "02") $ay = $dil["txt198"];
	elseif ($explode2[1] == "03") $ay = $dil["txt199"];
	elseif ($explode2[1] == "04") $ay = $dil["txt200"];
	elseif ($explode2[1] == "05") $ay = $dil["txt201"];
	elseif ($explode2[1] == "06") $ay = $dil["txt202"];
	elseif ($explode2[1] == "07") $ay = $dil["txt203"];
	elseif ($explode2[1] == "08") $ay = $dil["txt204"];
	elseif ($explode2[1] == "09") $ay = $dil["txt205"];
	elseif ($explode2[1] == "10") $ay = $dil["txt206"];
	elseif ($explode2[1] == "11") $ay = $dil["txt207"];
	elseif ($explode2[1] == "12") $ay = $dil["txt208"];

	return $explode2[2]." ".$ay." ".$explode2[0].", ".$zaman;
  
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_tarih2($par){
	
	global $dil;
	
	$explode = explode(" ", $par);
	$explode2 = explode("-", $explode[0]);
	$zaman = substr($explode[1], 0, 5);

	if ($explode2[1] == "01") $ay = $dil["txt197"];
	elseif ($explode2[1] == "02") $ay = $dil["txt198"];
	elseif ($explode2[1] == "03") $ay = $dil["txt199"];
	elseif ($explode2[1] == "04") $ay = $dil["txt200"];
	elseif ($explode2[1] == "05") $ay = $dil["txt201"];
	elseif ($explode2[1] == "06") $ay = $dil["txt202"];
	elseif ($explode2[1] == "07") $ay = $dil["txt203"];
	elseif ($explode2[1] == "08") $ay = $dil["txt204"];
	elseif ($explode2[1] == "09") $ay = $dil["txt205"];
	elseif ($explode2[1] == "10") $ay = $dil["txt206"];
	elseif ($explode2[1] == "11") $ay = $dil["txt207"];
	elseif ($explode2[1] == "12") $ay = $dil["txt208"];

	return $explode2[0]." ".$ay." ".$explode2[2].", ".$zaman;
  
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_tarih_ay($par){
	global $dil;
	
	$explode = explode(" ", $par);
	$explode2 = explode("-", $explode[0]);

	if ($explode2[1] == "01") $ay = $dil["txt209"];
	elseif ($explode2[1] == "02") $ay = $dil["txt210"];
	elseif ($explode2[1] == "03") $ay = $dil["txt211"];
	elseif ($explode2[1] == "04") $ay = $dil["txt212"];
	elseif ($explode2[1] == "05") $ay = $dil["txt213"];
	elseif ($explode2[1] == "06") $ay = $dil["txt214"];
	elseif ($explode2[1] == "07") $ay = $dil["txt215"];
	elseif ($explode2[1] == "08") $ay = $dil["txt216"];
	elseif ($explode2[1] == "09") $ay = $dil["txt217"];
	elseif ($explode2[1] == "10") $ay = $dil["txt218"];
	elseif ($explode2[1] == "11") $ay = $dil["txt219"];
	elseif ($explode2[1] == "12") $ay = $dil["txt220"];

	return $ay;
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_ay_yil($par){
	
	global $dil;
	
	$explode = explode("/", $par);
	  
	if ($explode[1] == "01") $ay = $dil["txt197"];
	elseif ($explode[1] == "02") $ay = $dil["txt198"];
	elseif ($explode[1] == "03") $ay = $dil["txt199"];
	elseif ($explode[1] == "04") $ay = $dil["txt200"];
	elseif ($explode[1] == "05") $ay = $dil["txt201"];
	elseif ($explode[1] == "06") $ay = $dil["txt202"];
	elseif ($explode[1] == "07") $ay = $dil["txt203"];
	elseif ($explode[1] == "08") $ay = $dil["txt204"];
	elseif ($explode[1] == "09") $ay = $dil["txt205"];
	elseif ($explode[1] == "10") $ay = $dil["txt206"];
	elseif ($explode[1] == "11") $ay = $dil["txt207"];
	elseif ($explode[1] == "12") $ay = $dil["txt208"];

	return $ay." ".$explode[2];
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_ay_yil_panel($par){
	
	  $explode = explode("/", $par);
	  
	  if ($explode[1] == "01") $ay = "Ocak";
	  elseif ($explode[1] == "02") $ay = "Şubat";
	  elseif ($explode[1] == "03") $ay = "Mart";
	  elseif ($explode[1] == "04") $ay = "Nisan";
	  elseif ($explode[1] == "05") $ay = "Mayıs";
	  elseif ($explode[1] == "06") $ay = "Haziran";
	  elseif ($explode[1] == "07") $ay = "Temmuz";
	  elseif ($explode[1] == "08") $ay = "Ağustos";
	  elseif ($explode[1] == "09") $ay = "Eylül";
	  elseif ($explode[1] == "10") $ay = "Ekim";
	  elseif ($explode[1] == "11") $ay = "Kasım";
	  elseif ($explode[1] == "12") $ay = "Aralık";
	  
	  return $ay." ".$explode[2];
  
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_tarih_yil($par){
	
	  $explode = explode(" ", $par);
	  $explode2 = explode("-", $explode[0]);
	  
	  return $explode2[0];
  
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_tarih_gun($par){
	
	  $explode = explode(" ", $par);
	  $explode2 = explode("-", $explode[0]);
	  
	  return $explode2[2];
  
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_bot($a)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $a);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$isle = curl_exec($ch);
	curl_close($ch);
	return $isle;
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_tr_tarih($tarih="Y-m-d H:i:s")
{
	$zaman = new DateTime(date($tarih));
	$zaman->setTimeZone(new DateTimeZone('Europe/Istanbul'));
	return $zaman->format($tarih);
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_tarihcevir($unixtime) 
{ 
    return cVCLmHLxbS_tarih($time = date("Y-m-d H:i:s",$unixtime));
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_unixtarih($unixtime) 
{ 
    return date("d-m-Y H:i",$unixtime);
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_tirnak($par)
{
	return str_replace(
		array(
			"'", "\""
			),
		array(
			"&#39;", "&quot;"
		),
		$par
	);
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_ilkbuyuk($str) {
	$m_uzunluk = mb_strlen($str, "UTF-8");	
	$ilkharf = mb_substr($str, 0, 1, "UTF-8");	
	$kalan = mb_substr($str, 1, $m_uzunluk - 1, "UTF-8");	
	$ilkharf = mb_strtoupper($ilkharf, "UTF-8");	
	$kalan = mb_strtolower($kalan,"UTF-8");	
	return $ilkharf.$kalan;
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_buyuk($str) 
{
	return mb_strtoupper($str,"UTF-8");
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_kucuk($str) 
{
	$str = strtr($str, 'ĞŞIÖÜÇİ', 'ğşıöüçi');
	return mb_strtolower($str,"UTF-8");
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_tumu($str,$secenek=1)
{
	global $db;
	if($secenek == 1)
	{
		$tumu = $db->query("SELECT * FROM $str WHERE dil = '{$_SESSION['admin_dil']}'")->rowCount();
	}
	else if($secenek == 2)
	{
		$tumu = $db->query("SELECT * FROM $str ")->rowCount();
	}
	else if($secenek == 3)
	{
		$tumu = $db->query("SELECT * FROM $str WHERE durum = '0'")->rowCount();
	}	
	return $tumu;
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_mesaj($deger1,$deger2,$mesaj,$secenek=1)
{
	if($secenek == 1)
	{
		if($_SESSION[$deger1] == $deger2)
		{
			echo "
			<script>
				$.toast({
				  heading: 'Başarılı!',
				  text: '".$mesaj."',
				  showHideTransition: 'slide',
				  icon: 'success',
				  loaderBg: '#fff',
				  position: 'top-right'
				})
			</script>";
			unset($_SESSION[$deger1]);
		}
	}
	if($secenek == 2)
	{
		if($_SESSION[$deger1] == $deger2)
		{
			echo "
			<script>
				$.toast({
				  heading: 'Hata!',
				  text: '".$mesaj."',
				  showHideTransition: 'slide',
				  icon: 'error',
				  loaderBg: '#fff',
				  position: 'top-right'
				})
			</script>";
			unset($_SESSION[$deger1]);
		}
	}
	if($secenek == 3)
	{
		if($_SESSION[$deger1] == $deger2)
		{
			echo "
			<script>
				$.toast({
				  heading: 'Uyarı!',
				  text: '".$mesaj."',
				  showHideTransition: 'slide',
				  icon: 'warning',
				  loaderBg: '#fff',
				  position: 'top-right'
				})
			</script>";
			unset($_SESSION[$deger1]);
		}
	}
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_site_mesaj($deger1,$deger2,$title,$mesaj,$tamam,$secenek=1)
{
	if($secenek == 1)
	{
		if($_SESSION[$deger1] == $deger2)
		{
			echo "
			<script>
			swal({
				type: 'success',
				title: '".$title."',
				text: '".$mesaj."',
				confirmButtonText: '".$tamam."',
				timer: 5000
			})
			</script>";
			unset($_SESSION[$deger1]);
		}
	}
	if($secenek == 2)
	{
		if($_SESSION[$deger1] == $deger2)
		{
			echo "
			<script>
			swal({
				type: 'error',
				title: '".$title."',
				text: '".$mesaj."',
				confirmButtonText: '".$tamam."',
				timer: 5000
			})
			</script>";
			unset($_SESSION[$deger1]);
		}
	}
	if($secenek == 3)
	{
		if($_SESSION[$deger1] == $deger2)
		{
			echo "
			<script>
			swal({
				type: 'warning',
				title: '".$title."',
				text: '".$mesaj."',
				confirmButtonText: '".$tamam."',
				timer: 5000
			})
			</script>";
			unset($_SESSION[$deger1]);
		}
	}
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_kod($uzunluk=8,$buyuk_harf=1,$kucuk_harf=1,$sayi_kullan=1,$ozel_karakter="")
{
	$buyukler = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$kucukler = "abcdefghijklmnopqrstuvwxyz";
	$sayilar = "0123456789";
	if($buyuk_harf){
		$seed_length += 26;
		$seed .= $buyukler;
	}
	if($kucuk_harf){
		$seed_length += 26;
		$seed .= $kucukler;
	}
	if($sayi_kullan){
		$seed_length += 10;
		$seed .= $sayilar;
	}
	if($ozel_karakter){
		$seed_length +=strlen($ozel_karakter);
		$seed .= $ozel_karakter;
	}
	for($x=1;$x<=$uzunluk;$x++){
		$sifre .= $seed[rand(0,$seed_length-1)];
	}
	return($sifre);
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cemetery_f(){
    $scriptName = $_SERVER['SCRIPT_NAME'];
    if($scriptName == '/'.yonetim.'/index.php'){
        licenses_check();
    }
}

// @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
function cVCLmHLxbS_panelislemkontrol($deger = "")
{
	if(empty($_SESSION['Yonetim_Id']) || empty($_SESSION['Yonetim_Kadi']) || empty($_SESSION['Yonetim_Sifre']))
	{
		header("Location:../".yonetim."/");
		exit();	
	}
}
$sayactarih	= date("d-m-Y"); // bugünün tarihi   
$buguntarih	= time(); // bugünün tarihi   
$sayacay	= date("m-Y"); // bugünün tarihi   
$buay		= date("m"); // bugünün tarihi   
$sayacyil	= date("Y"); // bugünün tarihi   
$sayacip 	= cVCLmHLxbS_ip(); // ziyaretçinin ip si 

?>