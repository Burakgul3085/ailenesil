<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
$ayar_dizi = $db->prepare("SELECT * FROM paytr WHERE id = ?");
$ayar_dizi->execute(array(1));
if($ayar_dizi->rowCount()){
	$Sonuc = $ayar_dizi->fetch(PDO::FETCH_ASSOC);
}else{
	header("Location:".$url."/404.html");
	exit;
}

// Vakıfbank ayarları
$vakifbank_dizi = $db->prepare("SELECT * FROM vakifbank WHERE id = ?");
$vakifbank_dizi->execute(array(1));
if($vakifbank_dizi->rowCount()){
	$VakifbankSonuc = $vakifbank_dizi->fetch(PDO::FETCH_ASSOC);
}else{
	// Varsayılan değerler
	$VakifbankSonuc = [
		'host_merchant_id' => '',
		'merchant_password' => '',
		'host_terminal_id' => '',
		'gateway_url' => '',
		'test_modu' => '1',
		'hata_mesaj' => '0',
		'taksit' => '0'
	];
}
?>
<?php // @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
 cemetery_f(); ?>
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3><?=@$admindil['txt101'];?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt2'];?></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt101'];?></a></li>
			</ul>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<form class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
					<div class="form-group">
						<label for="aktif_odeme_yontemi">Aktif Ödeme Yöntemi</label>
						<select class="js-example-basic-single form-control-sm" name="aktif_odeme_yontemi" id="aktif_odeme_yontemi" style="width:100%">
							<option value="paytr" <?php echo(!isset($Sonuc['aktif_odeme_yontemi']) || $Sonuc['aktif_odeme_yontemi'] == "paytr" ? 'selected' : '');?>>PayTR Sanal POS</option>
							<option value="vakifbank" <?php echo(isset($Sonuc['aktif_odeme_yontemi']) && $Sonuc['aktif_odeme_yontemi'] == "vakifbank" ? 'selected' : '');?>>Vakıfbank Sanal POS</option>
						</select>
						<small class="form-text text-muted">Web sitesinde kullanılacak varsayılan ödeme yöntemini seçin.</small>
					</div>
					<div class="form-group">
						<label for="magaza_no">Mağaza No</label>
						<input type="text" class="form-control form-control-sm" name="magaza_no" id="magaza_no" value="<?php echo $Sonuc['magaza_no'];?>" />
					</div>
					<div class="form-group">
						<label for="magaza_parola">Mağaza Parola</label>
						<input type="text" class="form-control form-control-sm" name="magaza_parola" id="magaza_parola" value="<?php echo $Sonuc['magaza_parola'];?>" />
					</div>
					<div class="form-group">
						<label for="magaza_anahtar">Mağza Gizli Anahtar</label>
						<input type="text" class="form-control form-control-sm" name="magaza_anahtar" id="magaza_anahtar" value="<?php echo $Sonuc['magaza_anahtar'];?>" />
					</div>
					<div class="form-group">
						<label for="hata_mesaj">Hata mesajlarının ekrana basılması</label>
						<select class="js-example-basic-single selectyeni form-control-sm" name="hata_mesaj" id="hata_mesaj" style="width:100%">
							<option value="1" <?php echo($Sonuc['hata_mesaj'] == "1" ? 'selected' : '');?>>Evet</option>
							<option value="0" <?php echo($Sonuc['hata_mesaj'] == "0" ? 'selected' : '');?>>Hayır</option>
						</select>
					</div>
					<div class="form-group">
						<label for="test_modu">Mağazayı test moduna al</label>
						<select class="js-example-basic-single selectyeni form-control-sm" name="test_modu" id="test_modu" style="width:100%">
							<option value="1" <?php echo($Sonuc['test_modu'] == "1" ? 'selected' : '');?>>Evet</option>
							<option value="0" <?php echo($Sonuc['test_modu'] == "0" ? 'selected' : '');?>>Hayır</option>
						</select>
					</div>
					<div class="form-group">
						<label for="taksit">Taksit Yapılsınmı ?</label>
						<select class="js-example-basic-single selectyeni form-control-sm" name="taksit" id="taksit" style="width:100%">
							<option value="0" <?php echo($Sonuc['taksit'] == "0" ? 'selected' : '');?>>Evet</option>
							<option value="1" <?php echo($Sonuc['taksit'] == "1" ? 'selected' : '');?>>Hayır</option>
						</select>
					</div>
					<p class="mb-2"><strong>Not: Paytr Mağaza Panelinde Bildirim Url'si siteadi.com/paytr.php Olmalıdır.</strong></p>
					<button type="submit" name="paytr_ayarlar" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-spin mdi-loading"></i>                                                   
						AYARLARI KAYDET
					</button>
				</form>
			</div>												
		</div>
	</div>

	<!-- Vakıfbank Ayarları -->
	<div class="col-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title mb-4">Vakıfbank Sanal Pos Ayarları</h4>
				<form class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
					<div class="form-group">
						<label for="vakifbank_host_merchant_id">Host Merchant ID</label>
						<input type="text" class="form-control form-control-sm" name="vakifbank_host_merchant_id" id="vakifbank_host_merchant_id" value="<?php echo $VakifbankSonuc['host_merchant_id'];?>" />
					</div>
					<div class="form-group">
						<label for="vakifbank_user_name">API Kullanıcı Adı (UserName)</label>
						<input type="text" class="form-control form-control-sm" name="vakifbank_user_name" id="vakifbank_user_name" value="<?php echo isset($VakifbankSonuc['user_name']) ? $VakifbankSonuc['user_name'] : '';?>" />
						<small class="form-text text-muted">Vakıf Katılım gibi entegrasyonlarda zorunludur. Boş bırakırsanız standart Vakıfbank entegrasyonu çalışır.</small>
					</div>
					<div class="form-group">
						<label for="vakifbank_merchant_password">Merchant Password / API Şifresi</label>
						<input type="text" class="form-control form-control-sm" name="vakifbank_merchant_password" id="vakifbank_merchant_password" value="<?php echo $VakifbankSonuc['merchant_password'];?>" />
					</div>
					<div class="form-group">
						<label for="vakifbank_host_terminal_id">Host Terminal ID</label>
						<input type="text" class="form-control form-control-sm" name="vakifbank_host_terminal_id" id="vakifbank_host_terminal_id" value="<?php echo $VakifbankSonuc['host_terminal_id'];?>" />
					</div>
					<div class="form-group">
						<label for="vakifbank_gateway_url">Gateway URL (Prod CommonPaymentPage)</label>
						<input type="text" class="form-control form-control-sm" name="vakifbank_gateway_url" id="vakifbank_gateway_url" value="<?php echo htmlspecialchars(isset($VakifbankSonuc['gateway_url']) ? $VakifbankSonuc['gateway_url'] : '');?>" placeholder="https://boa.vakifkatilim.com.tr/VirtualPOS.Gateway/..." />
						<small class="form-text text-muted">Banka dokümanındaki doğru prod gateway URL'sini girin. Boş bırakırsanız varsayılan URL kullanılır.</small>
					</div>
					<div class="form-group">
						<label for="vakifbank_hata_mesaj">Hata mesajlarının ekrana basılması</label>
						<select class="js-example-basic-single selectyeni form-control-sm" name="vakifbank_hata_mesaj" id="vakifbank_hata_mesaj" style="width:100%">
							<option value="1" <?php echo($VakifbankSonuc['hata_mesaj'] == "1" ? 'selected' : '');?>>Evet</option>
							<option value="0" <?php echo($VakifbankSonuc['hata_mesaj'] == "0" ? 'selected' : '');?>>Hayır</option>
						</select>
					</div>
					<div class="form-group">
						<label for="vakifbank_test_modu">Test Modu</label>
						<select class="js-example-basic-single selectyeni form-control-sm" name="vakifbank_test_modu" id="vakifbank_test_modu" style="width:100%">
							<option value="1" <?php echo($VakifbankSonuc['test_modu'] == "1" ? 'selected' : '');?>>Evet (Test)</option>
							<option value="0" <?php echo($VakifbankSonuc['test_modu'] == "0" ? 'selected' : '');?>>Hayır (Canlı)</option>
						</select>
					</div>
					<div class="form-group">
						<label for="vakifbank_taksit">Taksit Yapılsınmı ?</label>
						<select class="js-example-basic-single selectyeni form-control-sm" name="vakifbank_taksit" id="vakifbank_taksit" style="width:100%">
							<option value="0" <?php echo($VakifbankSonuc['taksit'] == "0" ? 'selected' : '');?>>Evet</option>
							<option value="1" <?php echo($VakifbankSonuc['taksit'] == "1" ? 'selected' : '');?>>Hayır</option>
						</select>
					</div>
					<p class="mb-2"><strong>Not: Vakıfbank Bildirim Url'si siteadi.com/vakifbank_callback.php Olmalıdır.</strong></p>
					<button type="submit" name="vakifbank_ayarlar" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-spin mdi-loading"></i>                                                   
						VAKIFBANK AYARLARINI KAYDET
					</button>
				</form>
			</div>												
		</div>
	</div>

</div>
<?php
cVCLmHLxbS_mesaj("paytr_ayarlar",1,"yes","Başarı ile guncellenmiştir.");	
cVCLmHLxbS_mesaj("paytr_ayarlar",2,"no","Hata oluştu tekrar deneyiniz.!");	
cVCLmHLxbS_mesaj("vakifbank_ayarlar",1,"yes","Vakıfbank ayarları başarı ile guncellenmiştir.");	
cVCLmHLxbS_mesaj("vakifbank_ayarlar",2,"no","Vakıfbank ayarları güncellenirken hata oluştu tekrar deneyiniz.!");	
?>	
