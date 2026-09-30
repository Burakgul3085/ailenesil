<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem']) && $_GET['islem'] == 'duzenle' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $durum_mod = "duzenle";
    $sorgu = $db->prepare("SELECT * FROM dosyalar WHERE id = ?");
    $sorgu->execute(array($_GET['id']));
    if($sorgu->rowCount() > 0) {
        $kayit = $sorgu->fetch(PDO::FETCH_ASSOC);
    } else {
        header("Location: dosya-listele.html");
        exit;
    }
} else {
    $durum_mod = "ekle";
    $kayit = array('id'=>0,'sira'=>0,'baslik'=>'','kategori'=>'','aciklama'=>'','dosya'=>'','orijinal_ad'=>'','dosya_boyut'=>'','dosya_tip'=>'','durum'=>1);
}
?>
<div class="page-header">
    <div class="page-title mt-0 mb-0">
        <h3><?php echo $durum_mod == 'duzenle' ? 'Dosya Düzenle' : 'Yeni Dosya Ekle'; ?></h3>
        <div class="crumbs">
            <ul id="breadcrumbs" class="breadcrumb">
                <li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
                <li><a href="dosya-listele.html">Dosya Yönetimi</a></li>
                <li class="active"><?php echo $durum_mod == 'duzenle' ? 'Düzenle' : 'Ekle'; ?></li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 grid-margin">
        <div class="card">
            <div class="card-body">
                <form class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $kayit['id']; ?>">

                    <div class="form-group">
                        <label for="sira">Sıra</label>
                        <input type="number" class="form-control form-control-sm" min="0" name="sira" id="sira" value="<?php echo $kayit['sira']; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="baslik">Başlık <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="baslik" id="baslik" value="<?php echo htmlspecialchars($kayit['baslik']); ?>" required />
                    </div>

                    <div class="form-group">
                        <label for="kategori">Kategori <small class="text-muted">(opsiyonel)</small></label>
                        <input type="text" class="form-control form-control-sm" name="kategori" id="kategori" placeholder="örn: Yönetmelik, Rapor, Form" value="<?php echo htmlspecialchars($kayit['kategori']); ?>" />
                    </div>

                    <div class="form-group">
                        <label for="aciklama">Açıklama <small class="text-muted">(opsiyonel)</small></label>
                        <textarea name="aciklama" id="aciklama" class="form-control form-control-sm" rows="3"><?php echo htmlspecialchars($kayit['aciklama']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Dosya <?php if($durum_mod == 'ekle') echo '<span class="text-danger">*</span>'; ?></label>
                        <?php if($durum_mod == 'duzenle' && $kayit['dosya']): ?>
                        <div class="mb-2 p-2 bg-light rounded d-flex align-items-center">
                            <i class="mdi mdi-file-document-outline mr-2 text-primary" style="font-size:20px;"></i>
                            <div>
                                <strong><?php echo htmlspecialchars($kayit['orijinal_ad'] ?: $kayit['dosya']); ?></strong>
                                <small class="text-muted ml-2"><?php echo $kayit['dosya_tip']; ?> &bull; <?php echo $kayit['dosya_boyut']; ?></small>
                            </div>
                            <a href="../../uploads/dosyalar/<?php echo $kayit['dosya']; ?>" target="_blank" class="btn btn-xs btn-outline-primary ml-auto">İndir</a>
                        </div>
                        <small class="text-muted">Değiştirmek için yeni dosya seçin (boş bırakırsanız mevcut dosya korunur)</small>
                        <?php endif; ?>
                        <div class="input-group mt-1">
                            <input type="file" name="dosya" id="dosya" class="file-upload-default" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.txt">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm file-upload-info" id="dosyaAd" disabled placeholder="Dosya seçiniz (PDF, DOC, XLS, PPT, ZIP, RAR, TXT)">
                                <div class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary btn-sm" type="button">
                                        <i class="icon-cloud-upload"></i> Dosya Seç
                                    </button>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">İzin verilen formatlar: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, RAR, TXT &bull; Maks: 20MB</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="switch">
                            <input type="checkbox" name="durum" id="durum" value="1" <?php echo $kayit['durum'] == 1 ? 'checked' : ''; ?>>
                            <span class="slider round"></span>
                        </label>
                        <label class="d-inline-block" style="line-height:34px;" for="durum">Durum (Aktif/Pasif)</label>
                    </div>

                    <?php if($durum_mod == 'duzenle'): ?>
                    <button type="submit" name="dosya_guncelle" class="btn btn-success btn-icon-text btn-sm">
                        <i class="mdi mdi-reload btn-icon-prepend"></i> GÜNCELLE
                    </button>
                    <?php else: ?>
                    <button type="submit" name="dosya_ekle" class="btn btn-primary btn-icon-text btn-sm">
                        <i class="mdi mdi-file-check btn-icon-prepend"></i> KAYDET
                    </button>
                    <?php endif; ?>
                    <a href="dosya-listele.html" class="btn btn-secondary btn-sm ml-2">İptal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('dosya').addEventListener('change', function() {
    var fileName = this.files.length > 0 ? this.files[0].name : '';
    document.getElementById('dosyaAd').value = fileName;
});
</script>

<?php
cVCLmHLxbS_mesaj("dosya_ekle", 1, "yes", "Dosya başarıyla eklendi.");
cVCLmHLxbS_mesaj("dosya_ekle", 2, "no", "Hata oluştu, tekrar deneyiniz!");
cVCLmHLxbS_mesaj("dosya_guncelle", 1, "yes", "Dosya başarıyla güncellendi.");
cVCLmHLxbS_mesaj("dosya_guncelle", 2, "no", "Hata oluştu, tekrar deneyiniz!");
?>
