<?php
ob_start();
session_start();
require_once('_class/baglan.php');
require_once('_class/fonksiyon.php');
require_once('_class/class.upload.php');
require_once('_class/simple_html_dom.php');
if(!file_exists('language/dil_'.$_SESSION['k_dil'].".php")) die("Mevcut dilin dosyası bulunamadı!");
require_once('language/dil_'.$_SESSION['k_dil'].".php");
require_once('_class/seo.php');
if($moduller['alan20'] == "1"){
	$html = ".html";
}
else
{
	$html = "";
}

// Para birimi sembolleri
$paraBirimiSembolleri = [
    'TRY' => '₺',
    'USD' => '$',
    'EUR' => '€'
];

// Seçili para birimi
$seciliParaBirimi = isset($_SESSION['para_birimi']) ? $_SESSION['para_birimi'] : 'TRY';
$paraBirimiSembolu = $paraBirimiSembolleri[$seciliParaBirimi];

if (isset($_POST['id']) && isset($_POST['price'])) 
{
	// Bağış bilgisini çek (modul bilgisi ile birlikte)
	$Sorgu = $db->prepare("SELECT b.*, bm.adi as modul_adi, bm.id as modul_id 
							FROM bagislar b 
							LEFT JOIN bagis_moduller bm ON b.modul_id = bm.id 
							WHERE b.id = ?");
	$Sorgu->execute(array($_POST['id']));
	if($Sorgu->rowCount() == 0)
	{
		echo json_encode(['hata' => true,'msg' => @$dil['txt114']]);
	}
	else
	{
		$islem = $Sorgu->fetch(PDO::FETCH_ASSOC);
		$_SESSION['sepet'] = (is_array(@$_SESSION['sepet'])) 
		? array_merge($_SESSION['sepet'],[ ['id' => $islem['id'],'adi' => $islem['adi'],'tutar' => floatval($_POST['price']),'sesID' => uniqid(),'modul_id' => $islem['modul_id'],'modul_adi' => $islem['modul_adi'], 'para_birimi' => $seciliParaBirimi ] ]) 
		: [ ['id' => $islem['id'],'adi' => $islem['adi'],'tutar' => floatval($_POST['price']),'sesID' => uniqid(),'modul_id' => $islem['modul_id'],'modul_adi' => $islem['modul_adi'], 'para_birimi' => $seciliParaBirimi ] ];
	}

}
elseif(isset($_POST['sil']))
{
	if (is_array($_SESSION['sepet'])) 
	{
		foreach ($_SESSION['sepet'] as $k => $v) 
		{
			if($v['sesID'] == $_POST['sil'])
			{
				unset($_SESSION['sepet'][$k]);
				break;
			}
		}
	}
}else
{
	if (!is_array($_SESSION['sepet']) || empty($_SESSION['sepet'])) 
	{
		echo '<h4>'.@$dil['txt117'].'</h4>
			 <li>'.@$dil['txt115'].'</li>
			 ';
	}
	else
	{
		foreach ($_SESSION['sepet'] as $k => $v) 
		{
			// Sepetteki öğe için para birimi sembolünü belirle
			$sepetParaBirimi = isset($v['para_birimi']) ? $v['para_birimi'] : $seciliParaBirimi;
			$sepetParaSembolu = $paraBirimiSembolleri[$sepetParaBirimi];
			
			echo '<div class="row basket-line">
					<div class="col-md-9">
						<a href="javascript:;" data-sil="'.$v['sesID'].'" class="float-left delete nameless">
							<i class="fas fa-times-circle"></i>
						</a>
						<div class="product-name">'.$v['adi'].'</div>
					</div>
					<div class="col-md-3">
						<div class="product-price">'.$v['tutar'].' '.$sepetParaSembolu.'</div>
					</div>
				</div>';
			
		}
		echo "<a href='".$htc['bagissepeturl']."".$html."'  class='form-button iletisim-page m-0 mt-2 p-2 d-inline-block float-right'><i class='fas fa-shopping-basket'></i> ".@$dil['txt116']."</a>";
	}
}
?>