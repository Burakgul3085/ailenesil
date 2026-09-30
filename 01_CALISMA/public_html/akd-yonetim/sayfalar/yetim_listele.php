<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php cemetery_f(); ?>
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Yetim Listesi</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>">Yetim Listesi</a></li>
			</ul>
		</div>
	</div>
</div>

<!-- İstatistik Kartları -->
<?php
// Database bağlantısı
require_once dirname(dirname(__DIR__)) . '/_class/baglan.php';

// İstatistikleri güvenli şekilde al
$istatistikler = [
    'toplam_yetim' => 0,
    'sponsorlu_yetim' => 0,
    'sponsor_bekleyen' => 0,
    'mezun_yetim' => 0,
    'pasif_yetim' => 0,
    'aktif_sponsor' => 0,
    'bu_ay_eklenen' => 0,
    'bu_ay_sponsor' => 0,
    'yas_gruplari' => [
        '0-5' => 0,
        '6-10' => 0,
        '11-15' => 0,
        '16+' => 0
    ]
];

// Basit istatistik sorguları - güvenli yöntem
try {
    // Toplam yetim sayısı
    $sorgu = "SELECT COUNT(*) FROM yetimler WHERE durum = 1";
    $sonuc = $db->query($sorgu);
    $istatistikler['toplam_yetim'] = (int)$sonuc->fetchColumn();

    // Sponsor bekleyen yetim sayısı
    $sorgu = "SELECT COUNT(*) FROM yetimler WHERE durum = 1 AND hami_durumu = 0";
    $sonuc = $db->query($sorgu);
    $istatistikler['sponsor_bekleyen'] = (int)$sonuc->fetchColumn();

    // Mezun yetim sayısı
    $sorgu = "SELECT COUNT(*) FROM yetimler WHERE durum = 1 AND hami_durumu = 'mezun'";
    $sonuc = $db->query($sorgu);
    $istatistikler['mezun_yetim'] = (int)$sonuc->fetchColumn();

    // Pasif yetim sayısı
    $sorgu = "SELECT COUNT(*) FROM yetimler WHERE durum = 0";
    $sonuc = $db->query($sorgu);
    $istatistikler['pasif_yetim'] = (int)$sonuc->fetchColumn();

    // Aktif sponsor sayısı
    $sorgu = "SELECT COUNT(DISTINCT bagisci_id) FROM hamilikler WHERE durum = 'aktif'";
    $sonuc = $db->query($sorgu);
    $istatistikler['aktif_sponsor'] = (int)$sonuc->fetchColumn();

    // Sponsorlu yetim sayısı (aktif hamilikleri say)
    $sorgu = "SELECT COUNT(DISTINCT yetim_id) FROM hamilikler WHERE durum = 'aktif'";
    $sonuc = $db->query($sorgu);
    $istatistikler['sponsorlu_yetim'] = (int)$sonuc->fetchColumn();

    // Bu ay eklenen yetim sayısı
    $sorgu = "SELECT COUNT(*) FROM yetimler WHERE durum = 1 AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())";
    $sonuc = $db->query($sorgu);
    $istatistikler['bu_ay_eklenen'] = (int)$sonuc->fetchColumn();

    // Bu ay başlayan sponsorluk sayısı
    $sorgu = "SELECT COUNT(*) FROM hamilikler WHERE durum = 'aktif' AND MONTH(baslangic_tarihi) = MONTH(NOW()) AND YEAR(baslangic_tarihi) = YEAR(NOW())";
    $sonuc = $db->query($sorgu);
    $istatistikler['bu_ay_sponsor'] = (int)$sonuc->fetchColumn();

    // Yaş grupları
    $sorgu = "SELECT 
        CASE 
            WHEN TIMESTAMPDIFF(YEAR, dogum_tarihi, CURDATE()) BETWEEN 0 AND 5 THEN '0-5'
            WHEN TIMESTAMPDIFF(YEAR, dogum_tarihi, CURDATE()) BETWEEN 6 AND 10 THEN '6-10'
            WHEN TIMESTAMPDIFF(YEAR, dogum_tarihi, CURDATE()) BETWEEN 11 AND 15 THEN '11-15'
            ELSE '16+'
        END as yas_grubu,
        COUNT(*) as sayi
        FROM yetimler 
        WHERE durum = 1
        GROUP BY yas_grubu";
    
    $sonuc = $db->query($sorgu);
    while($row = $sonuc->fetch(PDO::FETCH_ASSOC)) {
        $istatistikler['yas_gruplari'][$row['yas_grubu']] = (int)$row['sayi'];
    }

} catch (Exception $e) {
    error_log("İstatistik hatası: " . $e->getMessage());
}
?>

<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3 cursor-pointer" onclick="filtreleTablo('')">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><?php echo number_format($istatistikler['toplam_yetim']); ?></h4>
                        <p class="mb-0">Toplam Yetim</p>
                    </div>
                    <i class="icon-users font-3x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3 cursor-pointer" onclick="filtreleTablo('sponsorlu_filtre')">
        <div class="card bg-success text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><?php echo number_format($istatistikler['sponsorlu_yetim']); ?></h4>
                        <p class="mb-0">Sponsorlu Yetim</p>
                    </div>
                    <i class="icon-heart font-3x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3 cursor-pointer" onclick="filtreleTablo('Sponsor Yok')">
        <div class="card bg-warning text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><?php echo number_format($istatistikler['sponsor_bekleyen']); ?></h4>
                        <p class="mb-0">Sponsor Bekleyen</p>
                    </div>
                    <i class="icon-user-follow font-3x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filtreleTablo(deger) {
    var table = $('#order-listingg').DataTable();
    
    if(deger === 'sponsorlu_filtre') {
        // Sunucuya özel bir anahtar kelime gönderiyoruz
        table.search('FILTER_SPONSORLU').draw();
    } else {
        // Normal arama (Toplam Yetim için boş gönderilir)
        table.search(deger).draw();
    }
    
    // Tabloya yumuşak kaydırma
    $('html, body').animate({ scrollTop: $("#order-listingg").offset().top - 100 }, 500);
}
</script>

<!-- Detaylı İstatistikler -->
<div class="row mb-4">
    <div class="col-lg-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Yaş Gruplarına Göre Dağılım</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <div class="p-2 border rounded">
                            <h5 class="text-primary mb-1">0-5 Yaş</h5>
                            <h4 class="mb-0"><?php echo $istatistikler['yas_gruplari']['0-5']; ?></h4>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 border rounded">
                            <h5 class="text-success mb-1">6-10 Yaş</h5>
                            <h4 class="mb-0"><?php echo $istatistikler['yas_gruplari']['6-10']; ?></h4>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 border rounded">
                            <h5 class="text-warning mb-1">11-15 Yaş</h5>
                            <h4 class="mb-0"><?php echo $istatistikler['yas_gruplari']['11-15']; ?></h4>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 border rounded">
                            <h5 class="text-info mb-1">16+ Yaş</h5>
                            <h4 class="mb-0"><?php echo $istatistikler['yas_gruplari']['16+']; ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Durum Dağılımı</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <div class="p-2 border rounded">
                            <h5 class="text-success mb-1">Aktif</h5>
                            <h4 class="mb-0"><?php echo number_format($istatistikler['toplam_yetim'] - $istatistikler['pasif_yetim'] - $istatistikler['mezun_yetim']); ?></h4>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 border rounded">
                            <h5 class="text-warning mb-1">Sponsorlu</h5>
                            <h4 class="mb-0"><?php echo number_format($istatistikler['sponsorlu_yetim']); ?></h4>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 border rounded">
                            <h5 class="text-danger mb-1">Pasif</h5>
                            <h4 class="mb-0"><?php echo number_format($istatistikler['pasif_yetim']); ?></h4>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 border rounded">
                            <h5 class="text-info mb-1">Mezun</h5>
                            <h4 class="mb-0"><?php echo number_format($istatistikler['mezun_yetim']); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bu Ay İstatistikleri -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Bu Ay İstatistikleri</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-2">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Bu Ay Eklenen Yetim</h6>
                            <h3 class="text-primary mb-0"><?php echo number_format($istatistikler['bu_ay_eklenen']); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Bu Ay Sponsor Olan</h6>
                            <h3 class="text-success mb-0"><?php echo number_format($istatistikler['bu_ay_sponsor']); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Sponsorluk Oranı</h6>
                            <h3 class="text-info mb-0">
                                <?php 
                                $oran = $toplamYetim > 0 ? ($istatistikler['sponsorlu_yetim'] / $toplamYetim) * 100 : 0;
                                echo number_format($oran, 1); ?>%
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Sponsor/Yetim Oranı</h6>
                            <h3 class="text-warning mb-0">
                                <?php 
                                $oran = $sponsorBekleyen > 0 ? ($istatistikler['aktif_sponsor'] / $sponsorBekleyen) : 1;
                                echo number_format($oran, 1); ?>:1
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card">
	<form action="../_class/yonetim_islem.php" method="POST">
	<div class="card-body">
		<div class="row mb-3">
			<div class="col-lg-12">
				<div class="btn-toolbar" role="toolbar">					
					<a href="yetim-ekle.html" class="btn btn-primary btn-sm mr-1">
						<i class="icon-plus font-12"></i> Yeni Yetim Ekle
					</a>
					<button type="button" class="btn btn-warning btn-sm mr-1" data-toggle="modal" data-target="#giyimModal">
						<i class="icon-magic-wand font-12"></i> Giyim Eşleştirme
					</button>
					<div class="dropdown mr-1">
						<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuSizeButton3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="icon-options-vertical font-12"></i> Seçilenlere Uygula
						</button>
						<div class="dropdown-menu p-0 min-width-full" aria-labelledby="dropdownMenuSizeButton3">
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="yetim_aktif"><i class="icon-check"></i> Seçilenleri Aktif Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="yetim_pasif"><i class="icon-close"></i> Seçilenleri Pasif Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="yetim_sil"><i class="icon-trash"></i> Seçilenleri Sil</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-12">
				<div class="table-responsive">
					<table id="order-listingg" class="table table-bordered table-hover">
						<thead class="headbg">
							<tr>
								<th class="noshort" style="width:20px;" data-toggle="tooltip" data-placement="top" title="Tümünü Seç">
									<input id="checkbox-4" class="select-all checkbox-custom" type="checkbox" style="width:100px;">
									<label for="checkbox-4" class="checkbox-custom-label mb-0"><span class="checktext"></span></label>
								</th>
								<th style="width:30px;">ID</th>
								<th>Ad Soyad</th>
								<th>Doğum Tarihi</th>
								<th style="width:100px;">Durum</th>
								<th style="width:120px;">Sponsor Bilgisi</th>
								<th style="width:150px;">İşlem</th>
							</tr>
						</thead>
						<tbody id="sortable"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	</form>
</div>

<!-- Giyim Eşleştirme Modalı -->
<div class="modal fade" id="giyimModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Akıllı Toplu Giyim Eşleştirme</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="giyimForm" class="row">
                    <div class="form-group col-md-4">
                        <label>Eşleştirilecek Stok Adedi</label>
                        <input type="number" name="stok_adedi" class="form-control" value="10" min="1">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Kıyafet Türü</label>
                        <select name="kiyafet_turu" class="form-control" id="kiyafet_turu" onchange="bedenSecenekleri()">
                            <option value="genel">Genel Kıyafet</option>
                            <option value="mont">Mont / Kaban</option>
                            <option value="pantolon">Pantolon</option>
                            <option value="ayakkabi">Ayakkabı</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Cinsiyet</label>
                        <select name="cinsiyet" class="form-control">
                            <option value="">Farketmez</option>
                            <option value="erkek">Erkek</option>
                            <option value="kiz">Kız</option>
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label>Beden / Numara</label>
                        <div id="beden_container">
                            <input type="text" name="beden" class="form-control" placeholder="Örn: M, 38, 42...">
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-block" onclick="giyimEslestir()">Eşleşen Yetimleri Bul</button>
                    </div>
                </form>

                <div id="eslesmeSonuclari" class="mt-4" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6>Eşleşen Yetimler</h6>
                        <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                            <i class="ti-download"></i> Excel'e Aktar
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="eslesmeTablosu">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ad Soyad</th>
                                    <th>Yaş</th>
                                    <th>Beden Bilgisi</th>
                                </tr>
                            </thead>
                            <tbody id="eslesmeListesi"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bildirim Modalı -->
<div class="modal fade" id="bildirimModal" tabindex="-1" role="dialog" aria-labelledby="bildirimModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bildirimModalLabel">Hamiline Bildirim Gönder</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="../_class/yonetim_islem.php" method="POST">
          <div class="modal-body">
            <input type="hidden" name="yetim_id" id="modal_yetim_id">
            <input type="hidden" name="islem" value="yetim_bildirim_gonder">
            
            <div class="form-group">
                <label>Yetim Adı:</label>
                <input type="text" class="form-control" id="modal_yetim_adi" disabled>
            </div>
            
            <div class="form-group">
                <label>Bildirim Tipi</label>
                <select name="bildirim_tipi" class="form-control" id="bildirim_tipi" required onchange="bildirimTipiDegisti()">
                    <option value="yetim_takip_log">Yetim Takip Loguna Ekle</option>
                    <option value="sertifika">Sertifika Gönder</option>
                </select>
            </div>
            
            <!-- Sertifika Seçimi -->
            <div class="form-group" id="sertifika_secimi_div" style="display: none;">
                <label>Sertifika Seçiniz</label>
                <select name="sertifika_id" class="form-control" id="sertifika_secimi">
                    <option value="">Yükleniyor...</option>
                </select>
            </div>
            
            <!-- Gönderim Yöntemi -->
            <div class="form-group" id="gonderim_yontemi_div" style="display: none;">
                <label>Gönderim Yöntemi</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gonderim_yontemi" id="mail_gonderim" value="email" checked>
                    <label class="form-check-label" for="mail_gonderim">
                        E-posta ile gönder
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gonderim_yontemi" id="sms_gonderim" value="sms">
                    <label class="form-check-label" for="sms_gonderim">
                        SMS ile gönder
                    </label>
                </div>
            </div>
            
<div id="not_alani_konteynir" class="form-group" style="margin-bottom: 25px; font-family: 'Segoe UI', Tahoma, sans-serif;">
    <label style="display: flex; align-items: center; margin-bottom: 10px; font-weight: bold; color: #5a5a5a; font-size: 15px;">
        <span style="width: 10px; height: 10px; background-color: #ff4d4d; border-radius: 50%; margin-right: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.2);"></span>
        Yetim Takip Loguna yazılacak yazı
    </label>
    <textarea 
        name="detay" 
        class="form-control" 
        rows="6" 
        placeholder="Notlarınızı buraya yazın..."
        style="width: 100%; padding: 30px 20px 10px 45px; font-size: 16px; line-height: 31px; color: #333; background: #fff linear-gradient(transparent, transparent 30px, #e0e0e0 30px) repeat-y; background-size: 100% 31px; border: 1px solid #ccc; border-left: 5px solid #ff4d4d; border-radius: 4px; box-shadow: 2px 4px 10px rgba(0,0,0,0.1); outline: none; resize: none; font-family: 'Courier New', Courier, monospace;"
        onfocus="this.style.borderColor='#999';"
        onblur="this.style.borderColor='#ccc';"
    ></textarea>
</div>
            

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
            <button type="submit" class="btn btn-primary" name="yetim_bildirim_gonder">Gönder</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- AI Sonuç Modalı - Geliştirilmiş Versiyon -->
<div class="modal fade" id="aiResultModal" tabindex="-1" role="dialog" aria-labelledby="aiResultModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl" role="document" style="max-width: 90%; margin: 30px auto;">
    <div class="modal-content" style="border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0; padding: 20px 30px;">
        <h5 class="modal-title" id="aiResultTitle" style="font-size: 1.5rem; font-weight: 600; display: flex; align-items: center; gap: 10px;">
          <i class="fas fa-robot" style="font-size: 1.8rem;"></i>
          <span>AI Analizi</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9; font-size: 1.5rem;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="aiResultContent" style="padding: 30px; min-height: 400px; max-height: 70vh; overflow-y: auto; background: #f8f9fa;">
        <!-- Loading Animation -->
        <div id="aiLoadingAnimation" class="text-center" style="display: none; padding: 60px 20px;">
          <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem; border-width: 4px;">
            <span class="sr-only">Yükleniyor...</span>
          </div>
          <p class="mt-4" style="font-size: 1.1rem; color: #667eea; font-weight: 500;">AI analiz ediyor, lütfen bekleyin...</p>
        </div>
        <!-- AI Sonucu Buraya Gelecek -->
      </div>
      <div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #dee2e6; border-radius: 0 0 12px 12px; padding: 15px 30px;">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 10px 20px;">
          <i class="icon-close mr-2"></i> Kapat
        </button>
        <button type="button" class="btn btn-info" onclick="window.print()" style="border-radius: 8px; padding: 10px 20px;">
          <i class="icon-printer mr-2"></i> Yazdır / PDF Kaydet
        </button>
        <button type="button" class="btn btn-success" onclick="copyAIContent()" style="border-radius: 8px; padding: 10px 20px;">
          <i class="icon-docs mr-2"></i> Kopyala
        </button>
      </div>
    </div>
  </div>
</div>

<style>
/* Modal Pozisyon Düzeltmesi */
#aiResultModal {
  z-index: 9999 !important;
}

#aiResultModal .modal-dialog {
  position: relative;
  top: 0;
  margin: 30px auto;
}

/* AI İçerik Stilleri */
.ai-content-wrapper {
  background: white;
  border-radius: 10px;
  padding: 25px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  line-height: 1.8;
  font-size: 1.05rem;
  color: #2d3748;
}

.ai-content-wrapper h1,
.ai-content-wrapper h2,
.ai-content-wrapper h3 {
  color: #667eea;
  margin-top: 25px;
  margin-bottom: 15px;
  font-weight: 600;
}

.ai-content-wrapper h1 {
  font-size: 1.8rem;
  border-bottom: 3px solid #667eea;
  padding-bottom: 10px;
}

.ai-content-wrapper h2 {
  font-size: 1.5rem;
  border-left: 4px solid #764ba2;
  padding-left: 15px;
}

.ai-content-wrapper h3 {
  font-size: 1.3rem;
  color: #764ba2;
}

.ai-content-wrapper p {
  margin-bottom: 15px;
  text-align: justify;
}

.ai-content-wrapper ul,
.ai-content-wrapper ol {
  margin: 15px 0;
  padding-left: 30px;
}

.ai-content-wrapper li {
  margin-bottom: 10px;
  line-height: 1.7;
}

.ai-content-wrapper strong {
  color: #667eea;
  font-weight: 600;
}

.ai-content-wrapper code {
  background: #f1f5f9;
  padding: 2px 6px;
  border-radius: 4px;
  font-family: 'Courier New', monospace;
  color: #e53e3e;
}

.ai-content-wrapper blockquote {
  border-left: 4px solid #667eea;
  padding-left: 20px;
  margin: 20px 0;
  font-style: italic;
  color: #4a5568;
  background: #f7fafc;
  padding: 15px 20px;
  border-radius: 5px;
}

/* Typing Effect */
.typing-effect {
  border-right: 2px solid #667eea;
  animation: blink 1s infinite;
}

@keyframes blink {
  0%, 50% { border-color: #667eea; }
  51%, 100% { border-color: transparent; }
}

/* Print Styles */
@media print {
  body * {
    visibility: hidden;
  }
  #aiResultModal, #aiResultModal * {
    visibility: visible;
  }
  #aiResultModal {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    margin: 0;
    padding: 0;
    background: white;
  }
  .modal-footer, .close {
    display: none !important;
  }
  .ai-content-wrapper {
    box-shadow: none;
    padding: 20px;
  }
}

/* Scrollbar Styling */
#aiResultContent::-webkit-scrollbar {
  width: 8px;
}

#aiResultContent::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

#aiResultContent::-webkit-scrollbar-thumb {
  background: #667eea;
  border-radius: 10px;
}

#aiResultContent::-webkit-scrollbar-thumb:hover {
  background: #764ba2;
}
</style>
<script>
	$(document).ready(function(){
		var dataTable=$('#order-listingg').DataTable({
			"processing": true,
			"serverSide":true,
			"ajax":{
				url:"data/yetimler.php",
				type:"post"
			},
			"start": 1,
			"order": [[ 1, "asc" ]],
			"aLengthMenu": [
				[5, 10, 15, -1],
				[5, 10, 15, "Tümü"]
			],
			"columnDefs": [
				{ "orderable": false, "targets": [0, 5] },
				{ "targets": [ 1 ],"visible": false },
				{  "className": "secili", targets: [1, 2, 3] },
				{  "className": "secili text-center", targets: [4] },
				{  "className": "text-center", targets: [5] }
			],
			"iDisplayLength": 10,
			"language": {
				"url":"js/Turkish.json"
			},
			"fnCreatedRow": function( nRow, aData, iDataIndex ) {
				$(nRow).attr('id', 'item-'+aData[1]);
			},
			"fnDrawCallback": function( oSettings ) {
			    $(".popconfirm").popConfirm();
		    }
		});
		jQuery(".select-all").click(function () {
		    jQuery("input:checkbox").not(this).prop('checked', this.checked);
	    });
	});
    
    function bildirimGonder(id, ad) {
        $('#modal_yetim_id').val(id);
        $('#modal_yetim_adi').val(ad);
        $('#bildirimModal').modal('show');
    }
    
    // Markdown'ı HTML'e çevir
    function markdownToHtml(text) {
        if (!text) return '';
        
        // Başlıkları işle
        text = text.replace(/^### (.*$)/gim, '<h3>$1</h3>');
        text = text.replace(/^## (.*$)/gim, '<h2>$1</h2>');
        text = text.replace(/^# (.*$)/gim, '<h1>$1</h1>');
        
        // Kalın yazı
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/__(.*?)__/g, '<strong>$1</strong>');
        
        // İtalik
        text = text.replace(/\*(.*?)\*/g, '<em>$1</em>');
        text = text.replace(/_(.*?)_/g, '<em>$1</em>');
        
        // Liste
        text = text.replace(/^\* (.*$)/gim, '<li>$1</li>');
        text = text.replace(/^- (.*$)/gim, '<li>$1</li>');
        text = text.replace(/^(\d+)\. (.*$)/gim, '<li>$2</li>');
        
        // Liste wrapper
        text = text.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');
        
        // Paragraflar
        text = text.split('\n\n').map(para => {
            if (para.trim() && !para.match(/^<[hul]/)) {
                return '<p>' + para.trim() + '</p>';
            }
            return para;
        }).join('\n');
        
        // Satır sonları
        text = text.replace(/\n/g, '<br>');
        
        return text;
    }
    
    // Typing effect ile metin göster
    function typeWriter(element, text, speed = 30) {
        element.innerHTML = '';
        let i = 0;
        const wrapper = document.createElement('div');
        wrapper.className = 'ai-content-wrapper';
        element.appendChild(wrapper);
        
        function type() {
            if (i < text.length) {
                wrapper.innerHTML = markdownToHtml(text.substring(0, i + 1)) + '<span class="typing-effect">|</span>';
                i++;
                setTimeout(type, speed);
            } else {
                wrapper.innerHTML = markdownToHtml(text);
            }
        }
        type();
    }
    
    // AI içeriğini kopyala
    function copyAIContent() {
        const content = document.getElementById('aiResultContent').innerText;
        const textarea = document.createElement('textarea');
        textarea.value = content;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        
        // Başarı mesajı
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="icon-check mr-2"></i> Kopyalandı!';
        btn.classList.remove('btn-success');
        btn.classList.add('btn-primary');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
        }, 2000);
    }
    
    function aiRapor(id) {
        // Modal'ı göster ve loading animasyonunu başlat
        $('#aiResultModal').modal('show');
        $('#aiResultTitle').html('<i class="fas fa-robot mr-2"></i> AI Raporu');
        $('#aiResultContent').html('<div id="aiLoadingAnimation" class="text-center" style="padding: 60px 20px;"><div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem; border-width: 4px;"><span class="sr-only">Yükleniyor...</span></div><p class="mt-4" style="font-size: 1.1rem; color: #667eea; font-weight: 500;">AI analiz ediyor, lütfen bekleyin...</p></div>');
        
        $.post('../_class/yonetim_islem.php', {ai_islem: 1, islem_tipi: 'rapor_olustur', yetim_id: id}, function(response){
            if(response.success) {
                // Typing effect ile göster
                typeWriter(document.getElementById('aiResultContent'), response.text, 20);
            } else {
                $('#aiResultContent').html('<div class="alert alert-danger"><i class="icon-close mr-2"></i>' + (response.message || 'Bir hata oluştu') + '</div>');
            }
        }, 'json').fail(function() {
            $('#aiResultContent').html('<div class="alert alert-danger"><i class="icon-close mr-2"></i>Sunucu hatası oluştu. Lütfen tekrar deneyin.</div>');
        });
    }
    
    function aiOneri(id) {
        // Modal'ı göster ve loading animasyonunu başlat
        $('#aiResultModal').modal('show');
        $('#aiResultTitle').html('<i class="fas fa-lightbulb mr-2"></i> Akıllı İhtiyaç Önerisi');
        $('#aiResultContent').html('<div id="aiLoadingAnimation" class="text-center" style="padding: 60px 20px;"><div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem; border-width: 4px;"><span class="sr-only">Yükleniyor...</span></div><p class="mt-4" style="font-size: 1.1rem; color: #667eea; font-weight: 500;">AI geçmiş yardımları ve mevsimi analiz ediyor...</p></div>');
        
        $.post('../_class/yonetim_islem.php', {ai_islem: 1, islem_tipi: 'ihtiyac_oneri', yetim_id: id}, function(response){
            console.log('AI İhtiyaç Önerisi Yanıtı:', response);
            if(response && response.success) {
                // Typing effect ile göster
                typeWriter(document.getElementById('aiResultContent'), response.text, 20);
            } else {
                const errorMsg = (response && response.message) ? response.message : 'Bir hata oluştu';
                $('#aiResultContent').html('<div class="alert alert-danger"><i class="icon-close mr-2"></i>' + errorMsg + '</div>');
            }
        }, 'json').fail(function(xhr, status, error) {
            console.error('AJAX Hatası:', {xhr: xhr, status: status, error: error, responseText: xhr.responseText});
            let errorMsg = 'Sunucu hatası oluştu. Lütfen tekrar deneyin.';
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                if(errorResponse && errorResponse.message) {
                    errorMsg = errorResponse.message;
                }
            } catch(e) {
                // JSON parse hatası, varsayılan mesajı kullan
            }
            $('#aiResultContent').html('<div class="alert alert-danger"><i class="icon-close mr-2"></i>' + errorMsg + '</div>');
        });
    }

    function bedenSecenekleri() {
        var tur = $('#kiyafet_turu').val();
        var container = $('#beden_container');
        
        if(tur == 'ayakkabi') {
            container.html('<input type="number" name="beden" class="form-control" placeholder="Ayak Numarası (Örn: 36)">');
        } else if(tur == 'genel') {
            container.html('<select name="beden" class="form-control"><option value="XS">XS</option><option value="S">S</option><option value="M">M</option><option value="L">L</option><option value="XL">XL</option></select>');
        } else {
            container.html('<input type="text" name="beden" class="form-control" placeholder="Beden Giriniz">');
        }
    }

function giyimEslestir() {
    var form = $('#giyimForm').serialize();
    $('#eslesmeListesi').html('<tr><td colspan="4" class="text-center">Aranıyor...</td></tr>');
    $('#eslesmeSonuclari').show();
    
    // PHP tarafına ai_islem ve islem_tipi parametrelerini gönderiyoruz
    $.post('../_class/yonetim_islem.php', form + '&ai_islem=1&islem_tipi=giyim_onerisi', function(response){
        if(response.success) {
            var html = '';
            if(response.yetimler.length > 0) {
                $.each(response.yetimler, function(i, yetim){
                    html += '<tr>';
                    html += '<td>' + yetim.id + '</td>';
                    html += '<td>' + yetim.ad_soyad + '</td>';
                    html += '<td>' + yetim.yas + '</td>';
                    html += '<td>' + yetim.beden + '</td>'; // PHP'den dönen güncel beden
                    html += '</tr>';
                });
            } else {
                html = '<tr><td colspan="4" class="text-center text-danger">Uygun yetim bulunamadı.</td></tr>';
            }
            $('#eslesmeListesi').html(html);
        }
    }, 'json');
}

// Excel Dışa Aktarma Fonksiyonu
function exportToExcel() {
    let table = document.getElementById("eslesmeTablosu");
    let html = table.outerHTML;
    let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
    let link = document.createElement("a");
    link.download = "eslesen_yetimler_listesi.xls";
    link.href = url;
    link.click();
}

function bildirimTipiDegisti() {
    var bildirimTipi = $('#bildirim_tipi').val();
    
    if(bildirimTipi === 'sertifika') {
        // Sertifika seçildiyse: Sertifika alanlarını göster, Not alanını gizle
        $('#sertifika_secimi_div').show();
        $('#gonderim_yontemi_div').show();
        $('#not_alani_konteynir').hide(); // Not alanını gizle
        
        // Sertifikaları yükle
        $.ajax({
            url: '../_class/yonetim_islem.php',
            type: 'POST',
            data: { 'sertifika_listesi_getir': '1' },
            dataType: 'json',
            success: function(response) {
                var selectHtml = '<option value="">Sertifika Seçiniz</option>';
                $.each(response.sertifikalar, function(index, sertifika) {
                    var tip = sertifika.gonderim_yontemi == 'email' ? 'E-posta' : 'SMS';
                    selectHtml += '<option value="' + sertifika.id + '" data-tip="' + sertifika.gonderim_yontemi + '">' + sertifika.baslik + ' (' + tip + ')</option>';
                });
                $('#sertifika_secimi').html(selectHtml);
            }
        });
    } else {
        // Yetim takip logu seçildiyse: Sertifika alanlarını gizle, Not alanını göster
        $('#sertifika_secimi_div').hide();
        $('#gonderim_yontemi_div').hide();
        $('#not_alani_konteynir').show(); // Not alanını geri getir
    }
}
</script>
