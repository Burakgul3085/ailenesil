<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$sayfaid = $_GET['id'];	
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM menu WHERE id = ? AND dil = ?");
	$Sorgu->execute(array($sayfaid,$_SESSION['admin_dil']));
	if($Sorgu->rowCount()){
		$Sonuc = $Sorgu->fetch(PDO::FETCH_ASSOC);
	}
	else
	{
		header("Location:".$url."/404.html");
		exit;
	}
}
?>

<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3><?=@$admindil['txt19'];?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt17'];?></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt19'];?></a></li>
			</ul>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">                        
				<form class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
					<input id="id" name="id" type="hidden" value="<?php echo $Sonuc['id']; ?>">
					<div class="form-group">
						<label for="menu_ust">Üst Menü Seç</label>
						<select class="js-example-basic-single form-control-sm" name="menu_ust" id="menu_ust" required style="width:100%">
							<option value="0">Üst Menü</option>
							<?php $MENUSorgu = $db->prepare("SELECT * FROM menu WHERE menu_ust = ? AND dil = ? ORDER BY menu_sira ASC");
							$MENUSorgu->execute(array("0",$_SESSION['admin_dil']));
							$MENUislem = $MENUSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $MENUislem as $MENUSonuc ){?>
							<option value="<?php echo $MENUSonuc['id'];?>" <?php echo($Sonuc['menu_ust'] == $MENUSonuc['id'] ? 'selected' : '');?>><?php echo $MENUSonuc['menu_isim'];?></option>
							<?php }?>
						</select>
					</div>
					<div class="form-group tip">
						<label for="tip">Menü Tipi Seç <a href="" class="badge badge-warning" data-toggle="modal" data-target="#tip1">Menü Tipi 1</a> <a href="" class="badge badge-success" data-toggle="modal" data-target="#tip2">Menü Tipi 2</a> <a href="" class="badge badge-info" data-toggle="modal" data-target="#tip3">Menü Tipi 3</a></label>
						<select class="js-example-basic-single form-control-sm" name="tip" id="tip" required style="width:100%">					
							<option value="0" <?php echo($Sonuc['tip'] == 0 ? 'selected' : '');?> >Alt Menü Yok</option>
							<option value="1" <?php echo($Sonuc['tip'] == 1 ? 'selected' : '');?> >Tip 1</option>
							<option value="2" <?php echo($Sonuc['tip'] == 2 ? 'selected' : '');?> >Tip 2</option>
							<option value="3" <?php echo($Sonuc['tip'] == 3 ? 'selected' : '');?> >Tip 3</option>						
						</select>
					</div>
					
					<div class="modal fade" id="tip1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
						<div class="modal-dialog modal-md" role="document">
							<div class="modal-content">
								<div class="modal-header p-2">
									<h5 class="modal-title" id="exampleModalLabel-2">Menü Tipi 1 sitede açılış şekli</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span style="font-size: 30px;" aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body p-0">
									<img src="../<?php echo yonetim; ?>/images/tip1.png" style="max-width:100%;" alt="" />
								</div>
							</div>
						</div>
					</div>
					
					<div class="modal fade" id="tip2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
						<div class="modal-dialog modal-md" role="document">
							<div class="modal-content">
								<div class="modal-header p-2">
									<h5 class="modal-title" id="exampleModalLabel-2">Menü Tipi 2 sitede açılış şekli</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span style="font-size: 30px;" aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body p-0">
									<img src="../<?php echo yonetim; ?>/images/tip2.png" style="max-width:100%;" alt="" />
								</div>
							</div>
						</div>
					</div>
					
					<div class="modal fade" id="tip3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
						<div class="modal-dialog modal-md" role="document">
							<div class="modal-content">
								<div class="modal-header p-2">
									<h5 class="modal-title" id="exampleModalLabel-2">Menü Tipi 3 sitede açılış şekli</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span style="font-size: 30px;" aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body p-0">
									<img src="../<?php echo yonetim; ?>/images/tip3.png" style="max-width:100%;" alt="" />
								</div>
							</div>
						</div>
					</div>
					
					<div class="form-group tip2 tip">
						<label for="tipkat">Kategori kısmı çıkacak mı? <a href="" class="badge badge-primary" data-toggle="modal" data-target="#hkategori">Neresi ?</a></label>
						<select class="js-example-basic-single form-control-sm" name="tipkat" id="tipkat" required style="width:100%">
							<option value="0" <?php echo($Sonuc['tipkat'] == 0 ? 'selected' : '');?>>Evet</option>
							<option value="1" <?php echo($Sonuc['tipkat'] == 1 ? 'selected' : '');?>>Hayır</option>
						</select>
					</div>				
					
					<div class="form-group tip2 tip">
						<label for="kategori">Hangi kategori kısmı çıkacak ? <a href="" class="badge badge-primary" data-toggle="modal" data-target="#hkategori">Neresi ?</a></label>
						<select class="js-example-basic-single form-control-sm" name="kategori" id="kategori" required style="width:100%">
							<option value="0" <?php echo($Sonuc['kategori'] == 0 ? 'selected' : '');?>>Haberler Kategori</option>
							<option value="1" <?php echo($Sonuc['kategori'] == 1 ? 'selected' : '');?>>Projeler Kategori</option>
							<option value="2" <?php echo($Sonuc['kategori'] == 2 ? 'selected' : '');?>>Profil Kategori</option>
						</select>
					</div>	
					
					<div class="modal fade" id="hkategori" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
						<div class="modal-dialog modal-md" role="document">
							<div class="modal-content">
								<div class="modal-header p-2">
									<h5 class="modal-title" id="exampleModalLabel-2">Kategori kısmı sitede görünecek yeri</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span style="font-size: 30px;" aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body p-0">
									<img src="../<?php echo yonetim; ?>/images/kategori.png" style="max-width:100%;" alt="" />
								</div>
							</div>
						</div>
					</div>

					<div class="form-group tip2 tip katlimit">
						<label for="klimit">Menü Kategori Limiti</label>
						<input type="number" class="form-control form-control-sm" min="0" name="klimit" id="klimit" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['klimit'] : '');?>" />
					</div>
					
					<div class="form-group tip2 tip">
						<label for="ilimit">Menü İçerik Limiti</label>
						<input type="number" class="form-control form-control-sm" min="0" name="ilimit" id="ilimit" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['ilimit'] : '');?>" />
					</div>
					
					<div class="form-group tip1 tip">
						<label for="tbuton">Tümünü gör buton linki (Eğer tümünü gör butonunu istemiyorsanız bu alanı boş bırakınız) <a href="" class="badge badge-primary" data-toggle="modal" data-target="#tumunu_gor">Neresi ?</a></label>
						<input type="text" class="form-control form-control-sm" name="tbuton" id="tbuton" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['tbuton'] : '');?>" />
					</div>	
					<div class="modal fade" id="tumunu_gor" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
						<div class="modal-dialog modal-md" role="document">
							<div class="modal-content">
								<div class="modal-header p-2">
									<h5 class="modal-title" id="exampleModalLabel-2">Tümünü gör buton linki sitede görünecek yeri</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span style="font-size: 30px;" aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body p-0">
									<img src="../<?php echo yonetim; ?>/images/tumunu_gor.png" style="max-width:100%;" alt="" />
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="menu_sira">Menü Sıra</label>
						<input type="number" min="0" class="form-control form-control-sm" name="menu_sira" id="menu_sira" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['menu_sira'] : '');?>" />
					</div>
					<div class="form-group">
						<label for="menu_isim">Menü Adı</label>
						<input type="text" class="form-control form-control-sm" name="menu_isim" required id="menu_isim" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['menu_isim'] : '');?>" />
					</div>
					<div class="form-group" id="menu_icon_group">
						<label for="menu_icon">Menü İkonu</label>
						<div class="input-group">
							<input type="text" class="form-control form-control-sm" name="menu_icon" id="menu_icon" placeholder="fas fa-home" value="<?php echo(isset($_GET['islem'])=="duzenle" ? ($Sonuc['menu_icon'] ?? '') : '');?>" />
							<div class="input-group-append">
								<button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#ikonModal">
									<i class="ti-image"></i> İkon Seç
								</button>
							</div>
						</div>
						<small class="form-text text-muted">Font Awesome ikon sınıfını girin veya ikon seç butonuna tıklayın. Boş bırakabilirsiniz.</small>
						<div id="icon-preview" class="mt-2">
							<?php if(isset($_GET['islem'])=="duzenle" && !empty($Sonuc['menu_icon'])): ?>
								<small class="text-muted">Önizleme: </small> <i class="<?php echo htmlspecialchars($Sonuc['menu_icon'], ENT_QUOTES, 'UTF-8'); ?> fa-2x"></i>
							<?php endif; ?>
						</div>
					</div>
					<div class="form-group sayfalar">
						<label for="menu_url">Bağlantı Sayfası</label>
						<select name="menu_url" id="menu_url" class="js-example-basic-single form-control-sm" style="width:100%">
						<option value="0">Diğer (Manuel Link Ekle)</option>
						<optgroup label="Sayfalarım">
						<?php $SAYFASorgu = $db->prepare("SELECT * FROM sayfalar WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$SAYFASorgu->execute(array("1",$_SESSION['admin_dil']));
						$SAYFAislem = $SAYFASorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $SAYFAislem as $SAYFASonuc ){?>
							<option value="<?php echo $htc['sayfaurl'];?>/<?php echo $SAYFASonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['sayfaurl']."/".$SAYFASonuc['seo']."" ? 'selected' : '');?>><?php echo $SAYFASonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Proje Kategori">
						<?php $HSorgu = $db->prepare("SELECT * FROM proje_kategori WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['projekategoriurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['projekategoriurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Projeler">
						<?php $HSorgu = $db->prepare("SELECT * FROM projeler WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['projedetayurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['projedetayurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Haber Kategori">
						<?php $HSorgu = $db->prepare("SELECT * FROM haber_kategori WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['haberkategoriurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['haberkategoriurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Haberler">
						<?php $HSorgu = $db->prepare("SELECT * FROM haberler WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['haberdetayurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['haberdetayurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Profil Kategori">
						<?php $HSorgu = $db->prepare("SELECT * FROM profil_kategori WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['profilkategoriurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['profilkategoriurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Profiller">
						<?php $HSorgu = $db->prepare("SELECT * FROM profiller WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['profildetayurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['profildetayurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Hizmetler">
						<?php $HSorgu = $db->prepare("SELECT * FROM hizmetler WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['hizmetdetayurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['hizmetdetayurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Birimler">
						<?php $HSorgu = $db->prepare("SELECT * FROM birimler WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['birimdetayurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['birimdetayurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Etkinlikler">
						<?php $HSorgu = $db->prepare("SELECT * FROM etkinlikler WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['etkinlikdetayurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['etkinlikdetayurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Meclis Kararları">
						<?php $HSorgu = $db->prepare("SELECT * FROM meclis_kararlari WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['meclisdetayurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['meclisdetayurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Faaliyet Raporları">
						<?php $HSorgu = $db->prepare("SELECT * FROM faaliyet_raporlari WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['faaliyetdetayurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['faaliyetdetayurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="İhaleler">
						<?php $HSorgu = $db->prepare("SELECT * FROM ihaleler WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['ihaledetayurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['ihaledetayurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Foto Galeri">
						<?php $HSorgu = $db->prepare("SELECT * FROM foto_galeri WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['fotodetayurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['fotodetayurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Video Galeri">
						<?php $HSorgu = $db->prepare("SELECT * FROM video_galeri WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['videodetayurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['videodetayurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Programlar">
						<?php $HSorgu = $db->prepare("SELECT * FROM programlar WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="<?php echo $htc['programdetayurl'];?>/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['programdetayurl']."/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?>
						</optgroup>
						<optgroup label="Bağış Kategorileri">
						<?php $HSorgu = $db->prepare("SELECT * FROM bagis_kategori WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$HSorgu->execute(array("1",$_SESSION['admin_dil']));
						$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $Hislem as $HSonuc ){?>
							<option value="bagis/<?php echo $HSonuc['seo'];?>" <?php echo($Sonuc['menu_url'] == "bagis/".$HSonuc['seo']."" ? 'selected' : '');?>><?php echo $HSonuc['adi'];?></option>
							<?php }?> 
						</optgroup>
						<optgroup label="Sabit Sayfalar">
							<option value="<?php echo $htc['anaurl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['anaurl'] ? 'selected' : '');?>>Anasayfa</option>
							<option value="<?php echo $htc['projelerurl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['projelerurl']."" ? 'selected' : '');?>>Projeler</option>
							<option value="<?php echo $htc['haberurl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['haberurl']."" ? 'selected' : '');?>>Haberler</option>
							<option value="<?php echo $htc['profillerurl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['profillerurl']."" ? 'selected' : '');?>>Profiller</option>
							<option value="<?php echo $htc['hizmeturl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['hizmeturl']."" ? 'selected' : '');?>>Hizmetlerimiz</option>
							<option value="<?php echo $htc['birimurl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['birimurl']."" ? 'selected' : '');?>>Birimler</option>
							<option value="<?php echo $htc['kararurl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['kararurl']."" ? 'selected' : '');?>>Meclis Kararları</option>
							<option value="<?php echo $htc['faaliyeturl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['faaliyeturl']."" ? 'selected' : '');?>>Faaliyet Raporları</option>
							<option value="<?php echo $htc['duyuruurl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['duyuruurl']."" ? 'selected' : '');?>>Güncel Duyurular</option>
							<option value="<?php echo $htc['ihaleurl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['ihaleurl']."" ? 'selected' : '');?>>Güncel İhaleler</option>
							<option value="<?php echo $htc['ilanurl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['ilanurl']."" ? 'selected' : '');?>>Güncel İlanlar</option>
							<option value="<?php echo $htc['etkinlikurl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['etkinlikurl']."" ? 'selected' : '');?>>Etkinlikler</option>
							<option value="<?php echo $htc['bagisurl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['bagisurl']."" ? 'selected' : '');?>>Online Bağış</option>
							<option value="<?php echo $htc['aidaturl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['aidaturl']."" ? 'selected' : '');?>>Aidat Borcu Sorgulama</option>
							<option value="<?php echo $htc['fotourl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['fotourl']."" ? 'selected' : '');?>>Foto Galeri</option>
							<option value="<?php echo $htc['videourl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['videourl']."" ? 'selected' : '');?>>Video Galeri</option>
							<option value="<?php echo $htc['iletisimurl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['iletisimurl']."" ? 'selected' : '');?>>İletisim</option>
							<option value="<?php echo $htc['derslerurl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['derslerurl']."" ? 'selected' : '');?>>Dersler</option>
							<option value="<?php echo $htc['programlarurl'];?>" <?php echo($Sonuc['menu_url'] == "".$htc['programlarurl']."" ? 'selected' : '');?>>Programlar</option>
						</optgroup>
						</select>
					</div>
					<div class="form-group link sayfalar">
						<label for="link">Bağlantı Adresi (Manuel Link Ekle)</label>
						<input type="text" class="form-control form-control-sm" name="link" id="link" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['link'] : '');?>" />
					</div>
					<div class="form-group sayfalar">
						<div class="form-check form-check-flat form-check-primary">
							<label class="form-check-label">
							<input type="checkbox" name="sekme" <?php if(@$Sonuc['sekme'] == '1') {?> checked <?php } ?> class="form-check-input">
							Tıklandığında yeni sekmeye gitsin mi ?
							<i class="input-helper"></i></label>
						</div>
					</div>
					<div class="form-group">
						<label class="d-block" for="menu_durum">Menü Durumu</label>
						<?php if(isset($_GET['islem'])=="duzenle"){?>
						<label class="switch">
							<input type="checkbox" name="menu_durum" id="menu_durum" value="1" <?php if($Sonuc['menu_durum'] == '1') {?> checked <?php } ?>>
							<span class="slider"></span>
						</label>
						<?php }else{?>
						<label class="switch">
							<input type="checkbox" name="menu_durum" id="menu_durum" value="1" checked>
							<span class="slider"></span>
						</label>
						<?php } ?>								
					</div>
					<?php if(isset($_GET['islem'])=="duzenle"){?>
					<button type="submit" name="MenuGuncelle" class="btn btn-primary mr-2 btn-sm"><i class="mdi mdi-spin mdi-loading"></i> Güncelle</button>
					<a href="header-menu.html" class="btn btn-success btn-sm">
						<span class="btn-label"><i class="mdi mdi-spin mdi-loading"></i></span> Yeni Ekle
					</a>
					<?php }else{?>
					<button type="submit" name="MenuKaydet" class="btn btn-primary mr-2 btn-sm"><i class="mdi mdi-spin mdi-loading"></i>  Kaydet</button>
					<?php } ?>
				</form>
			</div>
		</div>
	</div>
	<div class="col-lg-6 grid-margin stretch-card ">
		<div class="card">
			<div class="card-body">    
				<div class="clearfix m-b-20">
					<div class="dd nestable-with-handle">
						<ol class="dd-list">
						<?php $USTMENUSorgu = $db->prepare("SELECT * FROM menu WHERE menu_ust = ? AND dil = ? ORDER BY menu_sira ASC");
						$USTMENUSorgu->execute(array("0",$_SESSION['admin_dil']));
						$USTMENUislem = $USTMENUSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $USTMENUislem as $USTMENUSonuc ){?>
							<li class="dd-item dd3-item">
								<div class="dd-handle dd3-handle"><?php echo $USTMENUSonuc['menu_sira']; ?></div>
								<div class="dd3-content <?php echo($USTMENUSonuc['menu_durum'] == 1 ? 'aktif-menu' : 'pasif-menu');?>"><?php echo $USTMENUSonuc['menu_isim']; ?> 
									<div class="nestablebtns">
										<a data-toggle="tooltip" data-placement="top" title="Düzenle" href="header-menu-duzenle/<?php echo $USTMENUSonuc['id'];?>.html"><i class="ti-pencil-alt"></i></a>
										<a data-toggle="tooltip" data-placement="top" title="Sil" class="popconfirm" href="../_class/yonetim_islem.php?menusil=ok&id=<?php echo $USTMENUSonuc['id'];?>"><i class="ti-trash"></i></a>
									</div>
								</div>
								<?php $ALTMENUSorgu = $db->prepare("SELECT * FROM menu WHERE menu_ust = ? AND dil = ? ORDER BY menu_sira ASC");
								$ALTMENUSorgu->execute(array($USTMENUSonuc['id'],$_SESSION['admin_dil']));
								$ALTMENUislem = $ALTMENUSorgu->fetchALL(PDO::FETCH_ASSOC);?>
								<?php foreach ( $ALTMENUislem as $ALTMENUSonuc ){?>
								<ol class="dd-list">
									<li class="dd-item dd3-item">
										<div class="dd-handle dd3-handle"><?php echo $ALTMENUSonuc['menu_sira']; ?></div>
										<div class="dd3-content <?php echo($ALTMENUSonuc['menu_durum'] == 1 ? 'aktif-menu' : 'pasif-menu');?>"><?php echo $ALTMENUSonuc['menu_isim']; ?>
											<div class="nestablebtns">
												<a data-toggle="tooltip" data-placement="top" title="Düzenle" href="header-menu-duzenle/<?php echo $ALTMENUSonuc['id'];?>.html"><i class="ti-pencil-alt"></i></a>
												<a data-toggle="tooltip" data-placement="top" title="Sil" class="popconfirm" href="../_class/yonetim_islem.php?menusil=ok&id=<?php echo $ALTMENUSonuc['id'];?>"><i class="ti-trash"></i></a>
											</div>
										</div>
									</li>
								</ol>
								<?php }?>
							</li>
							<?php }?>
						</ol>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
<!-- main-panel ends -->
<script> 
$(document).ready(function(e) {
	$('#menu_url').bind('change', linkGetir);
	$('#tip').bind('change', menutipi_getir);
	$('#menu_ust').bind('change', ust_getir);
	$('#tipkat').bind('change', tipkat);
});

function tipkat(){
	var katid = $(this).val();
	if(katid=='0'){
		$(".katlimit").css("display", "block");
	}else{
		$(".katlimit").css("display", "none");
	}
}

$('#tipkat').ready(function(){
	var katid = $("#tipkat").val();
	if(katid=='0'){
		$(".katlimit").css("display", "block");
	}else{
		$(".katlimit").css("display", "none");
	}
});

function ust_getir(){
	var ustid = $(this).val();
	if(ustid=='0'){
		$(".tip").css("display", "block");
	}else{
		$(".tip").css("display", "none");
	}
}

$('#menu_ust').ready(function(){
	var ustid = $("#menu_ust").val();
	if(ustid=='0'){
		$(".tip").css("display", "block");
	}else{
		$(".tip").css("display", "none");
	}
});

function linkGetir(){
	var value = $(this).val();
	if(value=='0'){
		 $('.link').removeClass('hidden');
		 $('.link input').attr('required','required');
	}else{
		 $('.link').addClass('hidden');
		 $('.link input').removeAttr('required');
	}
}
$('#menu_url').ready(function(){
	var value = $("#menu_url").val();
	if(value=='0'){
		 $('.link').removeClass('hidden');
		 $('.link input').attr('required','required');
	}else{
		 $('.link').addClass('hidden');
		 $('.link input').removeAttr('required');
	}
});	

function menutipi_getir(){
	var menutipi = $(this).val();
	if(menutipi=='0'){
		$(".sayfalar").css("display", "block");
		$(".tip1").css("display", "none");
		$(".tip2").css("display", "none");
	}else if(menutipi=='1'){
		$(".sayfalar").css("display", "block");
		$(".tip1").css("display", "block");
		$(".tip2").css("display", "none");
	}else if(menutipi=='2'){
		$(".sayfalar").css("display", "block");
		$(".tip1").css("display", "none");
		$(".tip2").css("display", "block");
		$('.link input').removeAttr('required');
	}else if(menutipi=='3'){
		$(".sayfalar").css("display", "block");
		$(".tip1").css("display", "none");
		$(".tip2").css("display", "none");
	}else{
		$(".sayfalar").css("display", "block");
		$(".tip1").css("display", "none");
		$(".tip2").css("display", "none");
	}
}

$('#tip').ready(function(){
	var menutipi = $("#tip").val();
	if(menutipi=='0'){
		$(".sayfalar").css("display", "block");
		$(".tip1").css("display", "none");
		$(".tip2").css("display", "none");
	}else if(menutipi=='1'){
		$(".sayfalar").css("display", "block");
		$(".tip1").css("display", "block");
		$(".tip2").css("display", "none");
	}else if(menutipi=='2'){
		$(".sayfalar").css("display", "block");
		$(".tip1").css("display", "none");
		$(".tip2").css("display", "block");
		$('.link input').removeAttr('required');
	}else if(menutipi=='3'){
		$(".sayfalar").css("display", "block");
		$(".tip1").css("display", "none");
		$(".tip2").css("display", "none");
	}else{
		$(".sayfalar").css("display", "block");
		$(".tip1").css("display", "none");
		$(".tip2").css("display", "none");
	}
});	

// İkon alanı her zaman görünür (ana menü ve alt menü için)
</script>

<!-- İkon Seçim Modal -->
<div class="modal fade" id="ikonModal" tabindex="-1" role="dialog" aria-labelledby="ikonModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ikonModalLabel">Font Awesome İkon Seçimi</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="text" id="ikonAra" class="form-control mb-3" placeholder="İkon ara...">
				<div class="icon-list" style="max-height: 500px; overflow-y: auto;"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
				<button type="button" class="btn btn-primary" id="ikonSec">Seç</button>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	// İkon listesini yükle
	$('#ikonModal').on('show.bs.modal', function () {
		// Font Awesome 5 ikonları
		var icons = [
			'fa-home', 'fa-user', 'fa-users', 'fa-building', 'fa-envelope', 'fa-phone', 'fa-map-marker-alt',
			'fa-info-circle', 'fa-question-circle', 'fa-bell', 'fa-cog', 'fa-wrench', 'fa-tools',
			'fa-calendar', 'fa-calendar-alt', 'fa-clock', 'fa-calendar-check', 'fa-calendar-day',
			'fa-file-alt', 'fa-file', 'fa-folder', 'fa-folder-open', 'fa-book', 'fa-book-open',
			'fa-newspaper', 'fa-newspaper', 'fa-bullhorn', 'fa-bullhorn', 'fa-megaphone',
			'fa-images', 'fa-image', 'fa-photo-video', 'fa-camera', 'fa-video',
			'fa-gift', 'fa-heart', 'fa-heartbeat', 'fa-hand-holding-heart', 'fa-donate',
			'fa-money-bill-wave', 'fa-credit-card', 'fa-piggy-bank', 'fa-wallet', 'fa-handshake',
			'fa-child', 'fa-baby', 'fa-school', 'fa-graduation-cap', 'fa-lightbulb',
			'fa-star', 'fa-thumbs-up', 'fa-smile', 'fa-smile-beam', 'fa-clipboard-check',
			'fa-chart-line', 'fa-chart-bar', 'fa-award', 'fa-medal', 'fa-trophy',
			'fa-leaf', 'fa-seedling', 'fa-tree', 'fa-water', 'fa-sun', 'fa-moon',
			'fa-globe', 'fa-globe-americas', 'fa-shield-alt', 'fa-shield', 'fa-check-circle',
			'fa-arrow-right', 'fa-arrow-left', 'fa-arrow-up', 'fa-arrow-down',
			'fa-search', 'fa-bars', 'fa-list', 'fa-th', 'fa-th-large',
			'fa-edit', 'fa-pencil-alt', 'fa-trash', 'fa-save', 'fa-download', 'fa-upload',
			'fa-link', 'fa-external-link-alt', 'fa-share', 'fa-share-alt',
			'fa-comment', 'fa-comments', 'fa-comment-dots', 'fa-reply',
			'fa-flag', 'fa-flag-checkered', 'fa-trophy', 'fa-medal',
			'fa-fire', 'fa-bolt', 'fa-magic', 'fa-sparkles',
			'fa-hand-paper', 'fa-hand-rock', 'fa-hand-scissors', 'fa-hand-spock',
			'fa-umbrella', 'fa-cloud', 'fa-cloud-sun', 'fa-cloud-rain',
			'fa-snowflake', 'fa-wind', 'fa-sun', 'fa-moon', 'fa-star'
		];
		
		var html = '';
		$.each(icons, function(index, icon) {
			html += '<div class="icon-item d-inline-block text-center p-2 m-1 border rounded" style="width: 100px; cursor: pointer;" data-icon="fas ' + icon + '">';
			html += '<i class="fas ' + icon + ' fa-2x mb-1"></i><br>';
			html += '<small class="text-muted">' + icon + '</small>';
			html += '</div>';
		});
		
		$('.icon-list').html(html);
		
		// İkon arama
		$('#ikonAra').on('keyup', function() {
			var value = $(this).val().toLowerCase();
			$('.icon-item').filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
			});
		});
		
		// İkon seçme
		$(document).off('click', '.icon-item').on('click', '.icon-item', function() {
			$('.icon-item').removeClass('bg-primary text-white');
			$(this).addClass('bg-primary text-white');
			$('#ikonSec').data('icon', $(this).data('icon'));
		});
	});
	
	// Seçilen ikonu forma ekle
	$('#ikonSec').on('click', function() {
		var selectedIcon = $(this).data('icon');
		if(selectedIcon) {
			$('#menu_icon').val(selectedIcon);
			$('#icon-preview').html('<small class="text-muted">Önizleme: </small> <i class="' + selectedIcon + ' fa-2x"></i>');
			$('#ikonModal').modal('hide');
		} else {
			alert('Lütfen bir ikon seçiniz.');
		}
	});
	
	// İkon input değiştiğinde preview güncelle
	$('#menu_icon').on('input', function() {
		var iconClass = $(this).val();
		if(iconClass) {
			$('#icon-preview').html('<small class="text-muted">Önizleme: </small> <i class="' + iconClass + ' fa-2x"></i>');
		} else {
			$('#icon-preview').html('');
		}
	});
});
</script>

<?php
cVCLmHLxbS_mesaj("MenuKaydet",1,"yes","Başarı ile eklenmiştir."); 
cVCLmHLxbS_mesaj("MenuKaydet",2,"no","Hata oluştu tekrar deneyiniz.!"); 
cVCLmHLxbS_mesaj("MenuGuncelle",1,"yes","Başarı ile guncellenmiştir."); 
cVCLmHLxbS_mesaj("MenuGuncelle",2,"no","Hata oluştu tekrar deneyiniz.!"); 
cVCLmHLxbS_mesaj("menusil",1,"yes","Başarı ile silinmiştir."); 
cVCLmHLxbS_mesaj("menusil",2,"no","Hata oluştu tekrar deneyiniz.!"); 	
?>
