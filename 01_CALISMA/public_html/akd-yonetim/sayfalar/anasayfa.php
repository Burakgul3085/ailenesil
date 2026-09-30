<?php
$bugun			=	date("d"); // bugünün tarihi 
$ay				=	date("m"); // bu ay
$yil			=	date("Y"); // bu yıl 
$onlineSuresi	=	time()-60;
$ip				=	cVCLmHLxbS_ip(); // ziyaretçinin ip si  
// online Kişi 
$online	=	$db->query("SELECT * FROM hit WHERE simdi > '{$onlineSuresi}'")->rowCount(); // onlnie kişilerimiz
// çoğul hitler 
$bugunx=$db->query("SELECT SUM(sayac) FROM hit WHERE gun='{$bugun}' AND ay='{$ay}' AND yil='{$yil}' ORDER BY id DESC")->fetch();
$bugun_cogul=$bugunx['SUM(sayac)']; // bugün çoğul
$dunx=$db->query("SELECT SUM(sayac) FROM hit WHERE gun='".($bugun-1)."' AND ay='{$ay}' AND yil='{$yil}' ORDER BY id DESC")->fetch();
$dun_cogul=$dunx['SUM(sayac)']; // dün Çoğul 
$ayx=$db->query("SELECT SUM(sayac) FROM hit WHERE ay='{$ay}' AND yil='{$yil}' ORDER BY id DESC")->fetch();
$buay_cogul=$ayx['SUM(sayac)']; // bu ay çoğul
$toplamx=$db->query("SELECT SUM(sayac) FROM hit ORDER BY id DESC")->fetch();
$toplam_cogul=$toplamx['SUM(sayac)']; // toplam çoğulumuz
// tekil hitler 
$bugun_tekil=$db->query("SELECT * FROM hit WHERE gun='{$bugun}' AND ay='{$ay}' AND yil='{$yil}'")->rowCount(); // bugün tekil
$dun_tekil=$db->query("SELECT * FROM hit WHERE gun='".($bugun-1)."' AND ay='{$ay}' AND yil='{$yil}'")->rowCount(); // dün tekil
$buay_tekil=$db->query("SELECT * FROM hit WHERE  ay='{$ay}' AND yil='{$yil}'")->rowCount(); // dün tekil
$toplam_tekil=$db->query("SELECT * FROM hit")->rowCount(); // dün tekil
?>
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Yönetim Paneli</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>">Yönetim Paneli</a></li>
			</ul>
		</div>
	</div>
	<div class="col col-auto mt-5 d-inline-block float-right">
		<label class="badge badge-secondary m-t-10">Toplam Tekil : <big><b><?php echo $toplam_tekil;?></b></big></label> 
		<label class="badge badge-dark m-t-10">Toplam Gösterim : <big><b><?php echo $toplam_cogul;?></b></big></label>
	</div>
</div>
<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Online<span class="float-right text-success"><?php echo $online;?> Kişi</span></h4>
                        <div class="d-flex justify-content-between">
                            <p class="text-muted">Tekil Ziyaretçi</p>
                            <p class="text-muted"><?php echo $online;?></p>
                        </div>
                        <div class="progress progress-md">
                            <div class="progress-bar bg-success" style="width:<?php echo $online;?>%;" role="progressbar" aria-valuenow="<?php echo $online;?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Bugün<span class="float-right text-info"><?php echo $bugun_tekil;?> Kişi</span></h4>
                        <div class="d-flex justify-content-between">
                            <p class="text-muted">Gösterim</p>
                            <p class="text-muted"><?php echo intval($bugun_cogul);?></p>
                        </div>
                        <div class="progress progress-md">
                            <div class="progress-bar bg-info" style="width:<?php echo intval($bugun_cogul);?>%;" role="progressbar" aria-valuenow="<?php echo intval($bugun_cogul);?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Dün<span class="float-right text-danger"><?php echo $dun_tekil;?> Kişi</span></h4>
                        <div class="d-flex justify-content-between">
                            <p class="text-muted">Gösterim</p>
                            <p class="text-muted"><?php echo intval($dun_cogul);?></p>
                        </div>
                        <div class="progress progress-md">
                            <div class="progress-bar bg-danger" style="width:<?php echo intval($dun_cogul);?>%;" role="progressbar" aria-valuenow="<?php echo intval($dun_cogul);?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Bu Ay<span class="float-right text-warning"><?php echo $buay_tekil;?> Kişi</span></h4>
                        <div class="d-flex justify-content-between">
                            <p class="text-muted">Gösterim</p>
                            <p class="text-muted"><?php echo intval($buay_cogul);?></p>
                        </div>
                        <div class="progress progress-md">
                            <div class="progress-bar bg-warning" style="width:<?php echo intval($buay_cogul);?>%;" role="progressbar" aria-valuenow="<?php echo intval($buay_cogul);?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    Riskli Bağışçılar (Ödemesi Gecikenler) 
                    <button class="btn btn-sm btn-gradient-primary float-right" id="btnAiRiskAnalizi">
                        <i class="mdi mdi-robot"></i> AI Risk Analizi
                    </button>
                </h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Hamilik ID</th>
                                <th>Bağışçı</th>
                                <th>Yetim</th>
                                <th>Son Ödeme</th>
                                <th>Gecikme</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $riskSorgu = $db->query("
                                SELECT h.id, b.ad_soyad as bagisci_ad, y.ad_soyad as yetim_ad, MAX(bo.tarih) as son_odeme
                                FROM hamilikler h
                                JOIN bagiscilar b ON h.bagisci_id = b.id
                                JOIN yetimler y ON h.yetim_id = y.id
                                LEFT JOIN bagis_odeme bo ON h.yetim_id = bo.yetim_id
                                WHERE h.durum = 'aktif'
                                GROUP BY h.id
                                HAVING son_odeme < DATE_SUB(NOW(), INTERVAL 30 DAY) OR son_odeme IS NULL
                                LIMIT 5
                            ");
                            if($riskSorgu->rowCount() > 0){
                                foreach($riskSorgu as $risk){
                                    $sonOdeme = $risk['son_odeme'] ? date('d.m.Y', strtotime($risk['son_odeme'])) : 'Hiç Yapılmadı';
                                    $gecikme = $risk['son_odeme'] ? floor((time() - strtotime($risk['son_odeme'])) / (60 * 60 * 24)) . ' Gün' : '-';
                            ?>
                            <tr>
                                <td>#<?php echo $risk['id'];?></td>
                                <td><?php echo $risk['bagisci_ad'];?></td>
                                <td><?php echo $risk['yetim_ad'];?></td>
                                <td><?php echo $sonOdeme;?></td>
                                <td><label class="badge badge-danger"><?php echo $gecikme;?></label></td>
                            </tr>
                            <?php } } else { ?>
                            <tr>
                                <td colspan="5" class="text-center">Riskli bağışçı bulunmamaktadır.</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">

	<div class="col-md-7 grid-margin grid-margin-lg-0 grid-margin-md-0 stretch-card">
		<div class="card">
			<div class="card-body p-3">
				<h4 class="card-title anatitle">BUGÜN SON İŞLEMLER <a class="float-right text-danger popconfirm" title="Tüm Bildirimi Sil" href="../_class/yonetim_islem.php?bildirimtumunusil=ok">Tümünü Sil</a></h4>				
				<div class="scroll">
				<div class="table-responsive">
				<div class="d-flex flex-column">
					<?php
					$biltarih		= cVCLmHLxbS_tr_tarih('Y-m-d');
					$bilbuguntarih 	= strtotime($biltarih);
					$BILDIRIMSorgu = $db->prepare("SELECT * FROM bildirimler WHERE ktarih = ? ORDER BY id DESC");
					$BILDIRIMSorgu->execute(array($bilbuguntarih));
					$BILDIRIMislem = $BILDIRIMSorgu->fetchALL(PDO::FETCH_ASSOC);

					// Bugün gelen onaylanmış bağışları çek
					$BAGIS_Sorgu = $db->prepare("SELECT id, ad, soyad, tutar, para_birimi, tarih FROM bagis_odeme WHERE paytronay = 1 AND DATE(tarih) = CURDATE() ORDER BY tarih DESC");
					$BAGIS_Sorgu->execute();
					$BAGIS_islem = $BAGIS_Sorgu->fetchALL(PDO::FETCH_ASSOC);
					?>
						<?php if($BILDIRIMSorgu->rowCount() != "0" || $BAGIS_Sorgu->rowCount() != "0"){?>
						<?php foreach ( $BILDIRIMislem as $BILDIRIMSonuc ){?>
						<div class="d-flex mb-1" style="border-bottom: 1px solid #f2f6f9;">
							<div class="d-flex align-items-center justify-content-center mr-2">
								<i class="<?php echo $BILDIRIMSonuc['icon'];?> social-icon-outline"></i>
							</div>
							<div class="d-flex flex-column ml-1">
								<h6 class="font-weight-normal mt-2 islem_baslik"><?php echo $BILDIRIMSonuc['bildirim'];?></h6>
								<p class="text-muted islem_tarih"><?php echo cVCLmHLxbS_tarihcevir($BILDIRIMSonuc['tarih']);?></p>
							</div>
							<div class="d-flex align-items-center justify-content-center ml-auto">
								<a href="../_class/yonetim_islem.php?bildirimsil=ok&id=<?php echo $BILDIRIMSonuc['id'];?>" class="popconfirm" title="Bildirim Sil"><i class="mdi mdi-trash-can-outline text-black mr-2 icon-hover-red"></i></a>
							</div>
						</div>
						<?php }?>
						<?php foreach ( $BAGIS_islem as $BAGIS_Sonuc ){?>
						<div class="d-flex mb-1" style="border-bottom: 1px solid #f2f6f9;">
							<div class="d-flex align-items-center justify-content-center mr-2">
								<i class="ti-heart social-icon-outline" style="color: #e74c3c;"></i>
							</div>
							<div class="d-flex flex-column ml-1">
								<h6 class="font-weight-normal mt-2 islem_baslik"><?php echo $BAGIS_Sonuc['ad'];?> <?php echo $BAGIS_Sonuc['soyad'];?> tarafından <?php echo number_format($BAGIS_Sonuc['tutar'], 2, ',', '.');?> <?php echo $BAGIS_Sonuc['para_birimi'];?> bağış yapıldı.</h6>
								<p class="text-muted islem_tarih"><?php echo $BAGIS_Sonuc['tarih'];?></p>
							</div>
							<div class="d-flex align-items-center justify-content-center ml-auto">
								<a href="gelen-bagis-detay.html?id=<?php echo $BAGIS_Sonuc['id'];?>" title="Bağış Detayı"><i class="mdi mdi-eye text-black mr-2 icon-hover-blue"></i></a>
							</div>
						</div>
						<?php }?>
						<?php }else{?>
							<div class="alert alert-secondary" role="alert">
							Gösterilecek kayıt bulunamadı.
							</div>
						<?php }?>
					</div>

				</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-md-5 stretch-card">
		<div class="card">
			<div class="card-body p-3">
				<h4 class="card-title anatitle"> YAKLAŞAN İŞLER</h4>
				<div class="scroll">
				<?php 
				$bugun 		= date("Y-m-d H:i:s");
				$cevir 		= strtotime('-1 day',strtotime($bugun));
				$besgun 	= strtotime('+5 day',strtotime($bugun));
				$nottarih 	= date("Y-m-d H:i:s",$cevir);
				$besguntrh 	= date("Y-m-d H:i:s",$besgun);
				$Sorgu = $db->prepare("SELECT * FROM not_defteri WHERE (date(baslangic) > ?) AND (date(baslangic) < ?) ORDER BY (date(baslangic)) ASC");
				$Sorgu->execute(array($nottarih,$besguntrh));
				$islem = $Sorgu->fetchALL(PDO::FETCH_ASSOC);?>
				<?php if($Sorgu->rowCount() != "0"){?>
				<ul class="icon-line-list">				
					<?php foreach ( $islem as $Sonuc ){?>
					<style>
					.icon-line-list li#renk<?php echo $Sonuc['id']?>::before {
					  background: <?php echo $Sonuc['renk']?>;
					}
					</style>
					<li id="renk<?php echo $Sonuc['id']?>">
						<p class="text-muted pt-2 pl-1"><?php echo cVCLmHLxbS_tarih_panel($Sonuc['baslangic']);?></p>
						<h6 class="font-weight-normal mt-0 mb-1 pl-1"><?php echo $Sonuc['baslik']?></h6>
						<p class="text-muted mb-4 mt-0 pl-1"><?php echo $Sonuc['ekleyen']?></p>
					</li>
					<?php }?>				
				</ul>
				<?php }else{?>	
					<div class="alert alert-secondary" role="alert">
                    Gösterilecek kayıt bulunamadı.
                    </div>
				<?php }?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 grid-margin grid-margin-lg-0 grid-margin-md-0 stretch-card">
		<div class="card">
			<div class="card-body p-3">
				<h4 class="card-title anatitle">GÜNCELLEMELER</h4>
				<div class="scroll">
				<div class="table-responsive">
					<div class="d-flex flex-column">
						<iframe width="100%" height="500" src="https://www.web-ofisi.com/guncelleme/belediyev7/guncelleme.html" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
				</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- main-panel ends -->
<?php 
cVCLmHLxbS_mesaj("bildirimsil",1,"yes","Başarı ile silinmiştir.");
cVCLmHLxbS_mesaj("bildirimsil",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("bildirimtumunusil",1,"yes","Başarı ile silinmiştir.");
cVCLmHLxbS_mesaj("bildirimtumunusil",2,"no","Hata oluştu tekrar deneyiniz.!");
?>
<script>
$(document).ready(function() {
    $('#btnAiRiskAnalizi').on('click', function() {
        swal({
            title: "Analiz Yapılıyor...",
            text: "Lütfen bekleyiniz, yapay zeka verileri analiz ediyor.",
            icon: "info",
            buttons: false,
            closeOnClickOutside: false
        });

        $.ajax({
            type: "POST",
            url: "../_class/yonetim_islem.php",
            data: {
                ai_islem: 'ok',
                islem_tipi: 'risk_analizi'
            },
            dataType: "json",
            success: function(response) {
                if(response.success) {
                    swal({
                        title: "Risk Analiz Raporu",
                        text: response.content,
                        icon: "success",
                        buttons: {
                            confirm: {
                                text: "Tamam",
                                value: true,
                                visible: true,
                                className: "btn btn-success",
                                closeModal: true
                            }
                        }
                    });
                } else {
                    swal("Hata", response.message, "error");
                }
            },
            error: function() {
                swal("Hata", "Bir sorun oluştu!", "error");
            }
        });
    });
});
</script>