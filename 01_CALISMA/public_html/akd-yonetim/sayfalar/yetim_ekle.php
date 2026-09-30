<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php 
if(isset($_GET['islem']) && $_GET['islem'] == "duzenle" && isset($_GET['id'])) {
    $islem = "duzenle";
    $id = intval($_GET['id']);
    $Sorgu = $db->prepare("SELECT * FROM yetimler WHERE id = ?");
    $Sorgu->execute(array($id));
    if($Sorgu->rowCount()) { 
        $Sonuc = $Sorgu->fetch(PDO::FETCH_ASSOC); 
    } else { 
        header("Location:yetim-listele.html"); exit; 
    }
    
    // Mevcut kardeşleri çek
    $kardeslerSorgu = $db->prepare("SELECT * FROM yetim_kardesler WHERE yetim_id = ?");
    $kardeslerSorgu->execute([$id]);
    $mevcutKardesler = $kardeslerSorgu->fetchAll(PDO::FETCH_ASSOC);

    // Mevcut ek dosyaları çek
    $dosyaSorgu = $db->prepare("SELECT * FROM yetim_dosyalar WHERE yetim_id = ?");
    $dosyaSorgu->execute([$id]);
    $mevcutDosyalar = $dosyaSorgu->fetchAll(PDO::FETCH_ASSOC);

} else { 
    $islem = "ekle"; 
    $mevcutKardesler = [];
    $mevcutDosyalar = [];
}
?>

<form action="../_class/yonetim_islem.php" method="POST" enctype="multipart/form-data">
    <?php if($islem == "duzenle"){ ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>">
    <?php } ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title text-primary"><i class="fas fa-id-card mr-2"></i> Temel Bilgiler</h4>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Adı Soyadı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="ad_soyad" value="<?php echo ($islem == "duzenle" ? $Sonuc['ad_soyad'] : ''); ?>" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>TC Kimlik No</label>
                            <input type="text" class="form-control" name="tc_no" value="<?php echo ($islem == "duzenle" ? $Sonuc['tc_no'] : ''); ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Doğum Tarihi</label>
                            <input type="date" class="form-control" name="dogum_tarihi" value="<?php echo ($islem == "duzenle" ? $Sonuc['dogum_tarihi'] : ''); ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Cinsiyet</label>
                            <select class="form-control" name="cinsiyet">
                                <option value="Erkek" <?php echo ($islem == "duzenle" && $Sonuc['cinsiyet'] == 'Erkek' ? 'selected' : ''); ?>>Erkek</option>
                                <option value="Kız" <?php echo ($islem == "duzenle" && $Sonuc['cinsiyet'] == 'Kız' ? 'selected' : ''); ?>>Kız</option>
                            </select>
                        </div>
                    </div>

                    <h4 class="card-title text-primary mt-4"><i class="fas fa-graduation-cap mr-2"></i> Eğitim ve Sağlık</h4>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Eğitim Durumu</label>
                            <select class="form-control" name="egitim_durumu">
                                <?php $egitimler = ['okul_oncesi'=>'Okul Öncesi','ilkokul'=>'İlkokul','ortaokul'=>'Ortaokul','lise'=>'Lise','universite'=>'Üniversite','yok'=>'Yok']; 
                                foreach($egitimler as $key => $val): ?>
                                    <option value="<?=$key?>" <?php echo ($islem == "duzenle" && $Sonuc['egitim_durumu'] == $key ? 'selected' : ''); ?>><?=$val?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Okul İsmi / Sınıf</label>
                            <input type="text" class="form-control" name="okul_ismi" value="<?php echo ($islem == "duzenle" ? $Sonuc['okul_ismi'] : ''); ?>">
                        </div>
                        <div class="col-md-12 form-group">
                            <label>Sağlık Durumu / Gözlemler</label>
                            <textarea class="form-control" name="saglik_durumu" rows="2"><?php echo ($islem == "duzenle" ? $Sonuc['saglik_durumu'] : ''); ?></textarea>
                        </div>
                    </div>

                    <h4 class="card-title text-primary mt-4"><i class="fas fa-users mr-2"></i> Aile ve Vasi Bilgileri</h4>
                    <hr>
                    <div class="row">
                        <div class="col-md-4 form-group"><label>Baba Adı</label><input type="text" class="form-control" name="baba_adi" value="<?php echo ($islem == "duzenle" ? $Sonuc['baba_adi'] : ''); ?>"></div>
                        <div class="col-md-4 form-group">
                            <label>Baba Durumu</label>
                            <select class="form-control" name="baba_durum" id="baba_durum">
                                <option value="Hayatta" <?php echo ($islem == "duzenle" && $Sonuc['baba_durum'] == 'Hayatta' ? 'selected' : ''); ?>>Hayatta</option>
                                <option value="Vefat" <?php echo ($islem == "duzenle" && $Sonuc['baba_durum'] == 'Vefat' ? 'selected' : ''); ?>>Vefat</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group"><label>Baba Ölüm Nedeni</label><input type="text" class="form-control" name="baba_olum_nedeni" value="<?php echo ($islem == "duzenle" ? $Sonuc['baba_olum_nedeni'] : ''); ?>"></div>
                        
                        <div class="col-md-4 form-group"><label>Anne Adı</label><input type="text" class="form-control" name="anne_adi" value="<?php echo ($islem == "duzenle" ? $Sonuc['anne_adi'] : ''); ?>"></div>
                        <div class="col-md-4 form-group">
                            <label>Anne Durumu</label>
                            <select class="form-control" name="anne_durum" id="anne_durum">
                                <option value="Hayatta" <?php echo ($islem == "duzenle" && $Sonuc['anne_durum'] == 'Hayatta' ? 'selected' : ''); ?>>Hayatta</option>
                                <option value="Vefat" <?php echo ($islem == "duzenle" && $Sonuc['anne_durum'] == 'Vefat' ? 'selected' : ''); ?>>Vefat</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group"><label>Anne Ölüm Nedeni</label><input type="text" class="form-control" name="anne_olum_nedeni" value="<?php echo ($islem == "duzenle" ? $Sonuc['anne_olum_nedeni'] : ''); ?>"></div>
                        
                        <div class="col-md-6 form-group">
                            <label>Vasi Yakınlığı (Örn: Amca, Teyze)</label>
                            <input type="text" class="form-control" name="vasi_yakinlik" value="<?php echo ($islem == "duzenle" ? $Sonuc['vasi_yakinlik'] : ''); ?>">
                        </div>
                        <div class="col-md-6 form-group" id="vasi_tel_alani">
                            <label id="lbl_vasi_tel">Vasi / Aile İletişim No</label>
                            <input type="text" class="form-control" name="vasi_tel" value="<?php echo ($islem == "duzenle" ? $Sonuc['vasi_tel'] : ''); ?>" placeholder="05xx XXX XX XX">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Kardeş Sayısı</label>
                            <select class="form-control" id="kardes_sayisi" name="kardes_sayisi">
                                <option value="0">Yok / Tek Çocuk</option>
                                <?php for($i=1; $i<=10; $i++): ?>
                                    <option value="<?=$i?>" <?php echo ($islem == "duzenle" && $Sonuc['kardes_sayisi'] == $i ? 'selected' : ''); ?>><?=$i?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-8 form-group">
                            <label>Aile Ekonomik Durumu / Adres Detay</label>
                            <textarea class="form-control" name="aile_bilgisi" rows="2"><?php echo ($islem == "duzenle" ? $Sonuc['aile_bilgisi'] : ''); ?></textarea>
                        </div>
                    </div>

                    <div id="kardes_konteyner" style="<?php echo ($islem == "duzenle" && count($mevcutKardesler) > 0) ? '' : 'display:none;'; ?>">
                        <h5 class="mt-4 text-info"><i class="fas fa-child mr-2"></i> Kardeş Detayları</h5>
                        <hr>
                        <div id="kardes_listesi">
                            <?php if($islem == "duzenle"): ?>
                                <?php foreach($mevcutKardesler as $idx => $k): ?>
                                    <div class="row mb-2 border-bottom pb-2">
                                        <div class="col-md-4"><label><small>Ad Soyad</small></label><input type="text" name="kardes_ad[]" class="form-control form-control-sm" value="<?=$k['ad_soyad']?>"></div>
                                        <div class="col-md-3"><label><small>Doğum</small></label><input type="date" name="kardes_dogum[]" class="form-control form-control-sm" value="<?=$k['dogum_tarihi']?>"></div>
                                        <div class="col-md-2"><label><small>Cinsiyet</small></label>
                                            <select name="kardes_cinsiyet[]" class="form-control form-control-sm">
                                                <option value="Erkek" <?=$k['cinsiyet']=='Erkek'?'selected':''?>>Erkek</option>
                                                <option value="Kız" <?=$k['cinsiyet']=='Kız'?'selected':''?>>Kız</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3"><label><small>Eğitim</small></label><input type="text" name="kardes_egitim[]" class="form-control form-control-sm" value="<?=$k['egitim_durumu']?>"></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title text-primary"><i class="fas fa-camera mr-2"></i> Görseller</h4>
                    <hr>
                    <div class="mb-3">
                        <label>Profil Fotoğrafı</label>
                        <?php if($islem == "duzenle" && !empty($Sonuc['foto'])): ?>
                            <div class="mb-2 text-center">
                                <img src="../img/yetimler/<?php echo $Sonuc['foto']; ?>" class="img-fluid rounded mb-2 border" style="max-height:150px">
                                                        <button type="button" class="btn btn-danger btn-sm position-absolute" style="top:5px; right:5px;" onclick="dosyaSil('<?php echo $id; ?>', 'ana_foto', '<?php echo $Sonuc['foto']; ?>', 'main_photo_area')">
                            <i class="fas fa-trash"></i>
                        </button>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="foto" class="form-control-file border p-1 rounded">
                    </div>
                    
                    <div class="mb-3 border-top pt-2">
                        <label>Tanıtım Videosu</label>
                        <?php if($islem=="duzenle" && !empty($Sonuc['yetim_video'])): ?>
                            <div class="alert alert-light border py-1 px-2 mb-1 d-flex justify-content-between align-items-center" id="area_video">
                                <small class="text-success"><i class="fas fa-check"></i> Video Yüklü</small>
                                <button type="button" class="btn btn-danger btn-xs" onclick="dosyaSil('<?=$id?>','video','<?=$Sonuc['yetim_video']?>','area_video')"><i class="fas fa-trash"></i></button>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="yetim_video" class="form-control-file border p-1 rounded">
                    </div>

                    <div class="form-group mt-3 border-top pt-3">
                        <label>Ek Fotoğraflar / Belgeler</label>
                        <input type="file" name="ek_dosyalar[]" class="form-control-file border p-1 rounded" multiple>
                        <small class="text-muted d-block mt-1">Birden fazla dosya seçebilirsiniz.</small>
                        
                        <?php if($islem == "duzenle" && !empty($mevcutDosyalar)): ?>
                            <div class="row mt-3">
                                <?php foreach($mevcutDosyalar as $file): ?>
                                    <div class="col-4 mb-2 text-center">
                                        <div style="position:relative">
                                            <img src="../img/yetimler/<?php echo $file['view_path']; ?>" class="img-thumbnail" style="width:100%; height:60px; object-fit:cover;">
                                                                                <button type="button" class="btn btn-danger btn-xs position-absolute" style="top:0; right:0;" onclick="dosyaSil('<?php echo $file['id']; ?>', 'ek_dosya', '<?php echo $file['view_path']; ?>', 'ek_dosya_<?php echo $file['id']; ?>')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
                        <div class="card shadow-sm mb-4 border-left-info">
                <div class="card-body">
                    <h4 class="card-title text-primary"><i class="fas fa-tshirt mr-2"></i> Beden Bilgileri</h4>
                    <hr>
                    <div class="row">
                        <div class="col-6 form-group"><label>Genel Beden</label><input type="text" class="form-control" name="beden_bilgisi" value="<?php echo ($islem == "duzenle" ? $Sonuc['beden_bilgisi'] : ''); ?>"></div>
                        <div class="col-6 form-group"><label>Ayak No</label><input type="text" class="form-control" name="ayak_no" value="<?php echo ($islem == "duzenle" ? $Sonuc['ayak_no'] : ''); ?>"></div>
                        <div class="col-6 form-group"><label>Mont Beden</label><input type="text" class="form-control" name="mont_beden" value="<?php echo ($islem == "duzenle" ? $Sonuc['mont_beden'] : ''); ?>"></div>
                        <div class="col-6 form-group"><label>Pantolon</label><input type="text" class="form-control" name="pantolon_beden" value="<?php echo ($islem == "duzenle" ? $Sonuc['pantolon_beden'] : ''); ?>"></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title text-primary"><i class="fas fa-map-marker-alt mr-2"></i> Konum</h4>
                    <hr>
                    <div class="form-group">
                        <label>İl</label>
                        <input type="text" class="form-control" name="il" value="<?php echo ($islem == "duzenle" ? $Sonuc['il'] : ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>İlçe</label>
                        <input type="text" class="form-control" name="ilce" value="<?php echo ($islem == "duzenle" ? $Sonuc['ilce'] : ''); ?>">
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 bg-light">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-cog mr-2"></i> Yayın & Takip</h4>
                    <hr>
                    <div class="form-group custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" name="durum" id="durum" value="1" <?php echo ($islem == "duzenle" && $Sonuc['durum'] == 1 ? 'checked' : ($islem == "ekle" ? 'checked' : '')); ?>>
                        <label class="custom-control-label" for="durum">Aktif Kayıt</label>
                    </div>

                    <div class="form-group custom-control custom-switch mt-2">
                        <input type="checkbox" class="custom-control-input" name="takip_link_acik" id="takipLinkAcik" value="1" <?php echo ($islem == "duzenle" && $Sonuc['takip_link_acik'] == 1 ? 'checked' : 'checked'); ?>>
                        <label class="custom-control-label" for="takipLinkAcik">Sponsor Takip Linki Aktif</label>
                    </div>

                    <?php if($islem == "duzenle" && !empty($Sonuc['takip_link_hash'])): ?>
                        <div class="mt-3 p-2 border rounded bg-white">
                            <small class="text-muted d-block mb-1">Takip Linki:</small>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="copyUrl" readonly value="<?php echo $ayar['site_url']; ?>/yetim-takip.php?hash=<?php echo $Sonuc['takip_link_hash']; ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" onclick="copyLink()">Kopyala</button>
                                </div>
                            </div>
                            <a href="../yetim-takip.php?hash=<?php echo $Sonuc['takip_link_hash']; ?>" target="_blank" class="btn btn-link btn-sm btn-block mt-1">Sayfayı Görüntüle</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" name="<?php echo ($islem == "duzenle" ? "yetim_guncelle" : "yetim_ekle"); ?>" class="btn btn-success btn-block btn-lg shadow">
                <i class="fas fa-save mr-2"></i> <?php echo ($islem == "duzenle" ? "DEĞİŞİKLİKLERİ KAYDET" : "YETİMİ KAYDET"); ?>
            </button>
        </div>
    </div>
</form>

<script>
function copyLink() {
  var copyText = document.getElementById("copyUrl");
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  document.execCommand("copy");
  alert("Link kopyalandı!");
}

$(document).ready(function() {
    $('#kardes_sayisi').on('change', function() {
        let count = parseInt($(this).val());
        let container = $('#kardes_listesi');
        
        // Eğer kullanıcı sayısı azalttıysa sadece fazlalıkları silmek için veya 
        // pratik olması adına yeniden oluşturmak için (mevcutları saklamaz)
        container.empty();
        
        if(count > 0) {
            $('#kardes_konteyner').show();
            for(let i = 1; i <= count; i++) {
                container.append(`
                    <div class="row mb-2 border-bottom pb-2">
                        <div class="col-md-4">
                            <label><small>${i}. Kardeş Ad Soyad</small></label>
                            <input type="text" name="kardes_ad[]" class="form-control form-control-sm" placeholder="Ad Soyad">
                        </div>
                        <div class="col-md-3">
                            <label><small>Doğum Tarihi</small></label>
                            <input type="date" name="kardes_dogum[]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label><small>Cinsiyet</small></label>
                            <select name="kardes_cinsiyet[]" class="form-control form-control-sm">
                                <option value="Erkek">Erkek</option>
                                <option value="Kız">Kız</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label><small>Eğitim</small></label>
                            <input type="text" name="kardes_egitim[]" class="form-control form-control-sm" placeholder="Sınıf/Okul">
                        </div>
                    </div>
                `);
            }
        } else {
            $('#kardes_konteyner').hide();
        }
    });

    $('#baba_durum, #anne_durum').change(function() {
        var bDurum = $('#baba_durum').val();
        var aDurum = $('#anne_durum').val();
        if(bDurum === 'Vefat' && aDurum === 'Vefat') {
            $('#lbl_vasi_tel').html('Vasi Telefon (Zorunlu) <span class="text-danger">*</span>');
        } else {
            $('#lbl_vasi_tel').text('Vasi / Aile İletişim No');
        }
    }).trigger('change');
});
</script>
<script>
function dosyaSil(id, tip, dosya, elementId) {
    if(confirm('Bu dosyayı silmek istediğinize emin misiniz?')) {
        $.ajax({
            url: '../_class/yonetim_islem.php',
            type: 'POST',
            data: {
                'yetim_dosya_sil': '1',
                'id': id,
                'tip': tip,
                'dosya': dosya
            },
            success: function(response) {
                if(response.trim() == "ok") {
                    $('#' + elementId).fadeOut(400, function(){
                        location.reload(); 
                    });
                    

                } else {
                    alert("Hata oluştu: " + response);
                }
            }
        });
    }
}
</script>