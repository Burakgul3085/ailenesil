
<?php
// Bildirim ayarlarını al
$bildirim_ayar = $db->query("SELECT * FROM bildirim_ayarlari WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
if(!$bildirim_ayar) {
    // Yoksa oluştur
    $db->exec("INSERT INTO bildirim_ayarlari (sms_aktif, email_aktif, whatsapp_aktif, otomatik_gonderim, durum) VALUES (1, 1, 0, 1, 1)");
    $bildirim_ayar = $db->query("SELECT * FROM bildirim_ayarlari WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
}

// WhatsApp ayarlarını al
$whatsapp_ayar = $db->query("SELECT * FROM whatsapp_ayarlar WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
?>

<style>
.nav-pills .nav-link {
    border-radius: 50px;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
}
.stat-card {
    border-radius: 12px;
    padding: 20px;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
.template-card {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}
.template-card:hover {
    border-color: #14b8a6;
    box-shadow: 0 4px 12px rgba(20, 184, 166, 0.2);
}
.badge-success-custom {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
}
.badge-danger-custom {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
}
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="font-weight-bold">
                                <i class="mdi mdi-bell-ring text-primary"></i> Bildirim Yönetim Sistemi
                            </h3>
                            <button class="btn btn-primary btn-sm" onclick="$('#istatistikModal').modal('show'); loadStats();">
                                <i class="mdi mdi-chart-bar"></i> İstatistikler
                            </button>
                        </div>
                    </div>
                </div>

                <!-- İstatistik Kartları -->
                <div class="row mb-4">
                    <?php
                    // Bugünkü bildirim istatistikleri
                    $bugun_sms = $db->query("SELECT COUNT(*) as adet FROM bildirim_log WHERE tip = 'sms' AND DATE(tarih) = CURDATE()")->fetch()['adet'] ?? 0;
                    $bugun_email = $db->query("SELECT COUNT(*) as adet FROM bildirim_log WHERE tip = 'email' AND DATE(tarih) = CURDATE()")->fetch()['adet'] ?? 0;
                    $bugun_whatsapp = $db->query("SELECT COUNT(*) as adet FROM bildirim_log WHERE tip = 'whatsapp' AND DATE(tarih) = CURDATE()")->fetch()['adet'] ?? 0;
                    $toplam_bugun = $bugun_sms + $bugun_email + $bugun_whatsapp;
                    ?>
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary text-white mr-3">
                                    <i class="mdi mdi-bell-ring"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-muted">Bugün Toplam</h6>
                                    <h3 class="mb-0 font-weight-bold"><?= $toplam_bugun ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-info text-white mr-3">
                                    <i class="mdi mdi-message-text"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-muted">SMS (Bugün)</h6>
                                    <h3 class="mb-0 font-weight-bold"><?= $bugun_sms ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-success text-white mr-3">
                                    <i class="mdi mdi-email"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-muted">E-posta (Bugün)</h6>
                                    <h3 class="mb-0 font-weight-bold"><?= $bugun_email ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-warning text-white mr-3">
                                    <i class="mdi mdi-whatsapp"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-muted">WhatsApp (Bugün)</h6>
                                    <h3 class="mb-0 font-weight-bold"><?= $bugun_whatsapp ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills mb-4" id="bildirimTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="genel-tab" data-toggle="pill" href="#genel" role="tab">
                                    <i class="mdi mdi-cog"></i> Genel Ayarlar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="sms-tab" data-toggle="pill" href="#sms" role="tab">
                                    <i class="mdi mdi-message-text"></i> SMS Şablonları
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="email-tab" data-toggle="pill" href="#email" role="tab">
                                    <i class="mdi mdi-email"></i> E-posta Şablonları
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="whatsapp-tab" data-toggle="pill" href="#whatsapp" role="tab">
                                    <i class="mdi mdi-whatsapp"></i> WhatsApp Ayarları
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="log-tab" data-toggle="pill" href="#log" role="tab">
                                    <i class="mdi mdi-file-document"></i> Bildirim Logları
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content" id="bildirimTabContent">
                            <!-- GENEL AYARLAR TAB -->
                            <div class="tab-pane fade show active" id="genel" role="tabpanel">
                                <h4 class="mb-4"><i class="mdi mdi-cog text-primary"></i> Genel Bildirim Ayarları</h4>
                                
                                <form id="genelAyarForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h5 class="card-title">Bildirim Tipleri</h5>
                                                    <div class="form-group">
                                                        <div class="form-check form-check-success">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input" name="sms_aktif" value="1" <?= $bildirim_ayar['sms_aktif'] == 1 ? 'checked' : '' ?>>
                                                                <i class="input-helper"></i>
                                                                SMS Bildirimleri
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-success">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input" name="email_aktif" value="1" <?= $bildirim_ayar['email_aktif'] == 1 ? 'checked' : '' ?>>
                                                                <i class="input-helper"></i>
                                                                E-posta Bildirimleri
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-success">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input" name="whatsapp_aktif" value="1" <?= $bildirim_ayar['whatsapp_aktif'] == 1 ? 'checked' : '' ?>>
                                                                <i class="input-helper"></i>
                                                                WhatsApp Bildirimleri
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h5 class="card-title">Gönderim Ayarları</h5>
                                                    <div class="form-group">
                                                        <div class="form-check form-check-primary">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input" name="otomatik_gonderim" value="1" <?= $bildirim_ayar['otomatik_gonderim'] == 1 ? 'checked' : '' ?>>
                                                                <i class="input-helper"></i>
                                                                Otomatik Gönderim (Ödeme sonrası)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Gecikme Süresi (saniye)</label>
                                                        <input type="number" class="form-control" name="gecikme_suresi" value="<?= $bildirim_ayar['gecikme_suresi'] ?? 0 ?>" min="0" max="300">
                                                        <small class="text-muted">Ödeme sonrası kaç saniye beklenecek (0 = anında)</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="mdi mdi-check"></i> Ayarları Kaydet
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- SMS ŞABLONLARI TAB -->
                            <div class="tab-pane fade" id="sms" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4><i class="mdi mdi-message-text text-info"></i> SMS Şablonları</h4>
                                    <button class="btn btn-info btn-sm" onclick="yeniSmsSablon()">
                                        <i class="mdi mdi-plus"></i> Yeni Şablon
                                    </button>
                                </div>

                                <div id="smsSablonlariListesi">
                                    <?php
                                    $sms_sablonlar = $db->query("SELECT * FROM sms_sablonlar ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($sms_sablonlar as $sablon):
                                    ?>
                                    <div class="template-card">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h5 class="font-weight-bold mb-2">
                                                    <?= htmlspecialchars($sablon['sablon_adi']) ?>
                                                    <?php if($sablon['varsayilan'] == 1): ?>
                                                    <span class="badge badge-primary">Varsayılan</span>
                                                    <?php endif; ?>
                                                    <?php if($sablon['durum'] == 1): ?>
                                                    <span class="badge-success-custom"><i class="mdi mdi-check"></i> Aktif</span>
                                                    <?php else: ?>
                                                    <span class="badge-danger-custom"><i class="mdi mdi-close"></i> Pasif</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <p class="text-muted mb-2"><strong>Tip:</strong> <?= $sablon['sablon_tip'] ?></p>
                                                <div class="bg-light p-3 rounded">
                                                    <code><?= htmlspecialchars($sablon['mesaj']) ?></code>
                                                </div>
                                                <small class="text-muted d-block mt-2">
                                                    Kullanılabilir: {AD_SOYAD}, {TUTAR}, {PARA_BIRIMI}, {KAMPANYA}, {SIPARIS_NO}, {TARIH}
                                                </small>
                                            </div>
                                            <div class="ml-3">
                                                <button class="btn btn-warning btn-sm mb-2" onclick="smsSablonDuzenle(<?= $sablon['id'] ?>)">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="smsSablonSil(<?= $sablon['id'] ?>)">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- E-POSTA ŞABLONLARI TAB -->
                            <div class="tab-pane fade" id="email" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4><i class="mdi mdi-email text-success"></i> E-posta Şablonları</h4>
                                    <button class="btn btn-success btn-sm" onclick="yeniEmailSablon()">
                                        <i class="mdi mdi-plus"></i> Yeni Şablon
                                    </button>
                                </div>

                                <div id="emailSablonlariListesi">
                                    <?php
                                    $email_sablonlar = $db->query("SELECT * FROM mail_sablonlar ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($email_sablonlar as $sablon):
                                    ?>
                                    <div class="template-card">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h5 class="font-weight-bold mb-2">
                                                    <?= htmlspecialchars($sablon['sablon_adi']) ?>
                                                    <?php if($sablon['varsayilan'] == 1): ?>
                                                    <span class="badge badge-primary">Varsayılan</span>
                                                    <?php endif; ?>
                                                    <?php if($sablon['durum'] == 1): ?>
                                                    <span class="badge-success-custom"><i class="mdi mdi-check"></i> Aktif</span>
                                                    <?php else: ?>
                                                    <span class="badge-danger-custom"><i class="mdi mdi-close"></i> Pasif</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <p class="text-muted mb-2">
                                                    <strong>Konu:</strong> <?= htmlspecialchars($sablon['konu']) ?>
                                                </p>
                                                <p class="text-muted mb-2">
                                                    <strong>Tip:</strong> <?= $sablon['sablon_tip'] ?>
                                                    <?php if($sablon['sertifika_ekle'] == 1): ?>
                                                    <span class="badge badge-info ml-2"><i class="mdi mdi-certificate"></i> Sertifika Ekli</span>
                                                    <?php endif; ?>
                                                </p>
                                                <div class="bg-light p-3 rounded" style="max-height: 150px; overflow-y: auto;">
                                                    <?= substr(strip_tags($sablon['icerik']), 0, 200) ?>...
                                                </div>
                                                <small class="text-muted d-block mt-2">
                                                    Kullanılabilir: {AD_SOYAD}, {TUTAR}, {PARA_BIRIMI}, {KAMPANYA}, {SIPARIS_NO}, {TARIH}, {SITE_ADI}
                                                </small>
                                            </div>
                                            <div class="ml-3">
                                                <button class="btn btn-warning btn-sm mb-2" onclick="emailSablonDuzenle(<?= $sablon['id'] ?>)">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button class="btn btn-info btn-sm mb-2" onclick="emailOnizle(<?= $sablon['id'] ?>)">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="emailSablonSil(<?= $sablon['id'] ?>)">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- WHATSAPP AYARLARI TAB -->
                            <div class="tab-pane fade" id="whatsapp" role="tabpanel">
                                <h4 class="mb-4"><i class="mdi mdi-whatsapp text-warning"></i> WhatsApp Business API Ayarları</h4>
                                
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information"></i> WhatsApp Business API kullanmak için Meta Business Suite'de hesap oluşturmanız gerekir.
                                    <a href="https://business.facebook.com/" target="_blank" class="alert-link">Business Suite'e Git</a>
                                </div>

                                <form id="whatsappAyarForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>API URL</label>
                                                <input type="text" class="form-control" name="api_url" value="<?= htmlspecialchars($whatsapp_ayar['api_url'] ?? 'https://graph.facebook.com/v18.0') ?>" required>
                                                <small class="text-muted">Örn: https://graph.facebook.com/v18.0</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>API Token</label>
                                                <input type="password" class="form-control" name="api_token" value="<?= htmlspecialchars($whatsapp_ayar['api_token'] ?? '') ?>" required>
                                                <small class="text-muted">Meta Business Suite'den alın</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Phone Number ID</label>
                                                <input type="text" class="form-control" name="phone_number_id" value="<?= htmlspecialchars($whatsapp_ayar['phone_number_id'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Business Account ID</label>
                                                <input type="text" class="form-control" name="business_account_id" value="<?= htmlspecialchars($whatsapp_ayar['business_account_id'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="form-check form-check-primary">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input" name="durum" value="1" <?= $whatsapp_ayar['durum'] == 1 ? 'checked' : '' ?>>
                                                        <i class="input-helper"></i>
                                                        Aktif
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="form-check form-check-warning">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input" name="test_modu" value="1" <?= $whatsapp_ayar['test_modu'] == 1 ? 'checked' : '' ?>>
                                                        <i class="input-helper"></i>
                                                        Test Modu
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-warning btn-lg">
                                            <i class="mdi mdi-check"></i> WhatsApp Ayarlarını Kaydet
                                        </button>
                                    </div>
                                </form>

                                <!-- WhatsApp Şablonları -->
                                <hr class="my-5">
                                <h5 class="mb-4">WhatsApp Mesaj Şablonları</h5>
                                
                                <div class="alert alert-warning">
                                    <i class="mdi mdi-alert"></i> WhatsApp şablonlarının Meta tarafından onaylanması gerekir. Şablonları Meta Business Suite'de oluşturun.
                                </div>

                                <div id="whatsappSablonlariListesi">
                                    <?php
                                    $whatsapp_sablonlar = $db->query("SELECT * FROM whatsapp_sablonlar ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($whatsapp_sablonlar as $sablon):
                                    ?>
                                    <div class="template-card">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h5 class="font-weight-bold mb-2">
                                                    <?= htmlspecialchars($sablon['sablon_adi']) ?>
                                                    <?php if($sablon['onay_durumu'] == 'onaylandi'): ?>
                                                    <span class="badge badge-success"><i class="mdi mdi-check-circle"></i> Onaylandı</span>
                                                    <?php elseif($sablon['onay_durumu'] == 'beklemede'): ?>
                                                    <span class="badge badge-warning"><i class="mdi mdi-clock"></i> Beklemede</span>
                                                    <?php else: ?>
                                                    <span class="badge badge-danger"><i class="mdi mdi-close-circle"></i> Reddedildi</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <p class="text-muted mb-2">
                                                    <strong>Template Name:</strong> <?= $sablon['template_name'] ?>
                                                    <span class="ml-3"><strong>Dil:</strong> <?= strtoupper($sablon['template_language']) ?></span>
                                                </p>
                                                <div class="bg-light p-3 rounded">
                                                    <strong>Parametreler:</strong>
                                                    <pre class="mb-0"><?= htmlspecialchars($sablon['parametreler']) ?></pre>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <button class="btn btn-warning btn-sm" onclick="whatsappSablonDuzenle(<?= $sablon['id'] ?>)">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- BİLDİRİM LOGLARI TAB -->
                            <div class="tab-pane fade" id="log" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4><i class="mdi mdi-file-document text-dark"></i> Bildirim Logları</h4>
                                    <div>
                                        <select class="form-control form-control-sm d-inline-block w-auto" id="logFilterTip" onchange="loadLogs()">
                                            <option value="">Tüm Tipler</option>
                                            <option value="sms">SMS</option>
                                            <option value="email">E-posta</option>
                                            <option value="whatsapp">WhatsApp</option>
                                        </select>
                                        <select class="form-control form-control-sm d-inline-block w-auto ml-2" id="logFilterDurum" onchange="loadLogs()">
                                            <option value="">Tüm Durumlar</option>
                                            <option value="gonderildi">Gönderildi</option>
                                            <option value="hata">Hata</option>
                                            <option value="beklemede">Beklemede</option>
                                        </select>
                                        <button class="btn btn-primary btn-sm ml-2" onclick="loadLogs()">
                                            <i class="mdi mdi-refresh"></i> Yenile
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tarih</th>
                                                <th>Tip</th>
                                                <th>Alıcı</th>
                                                <th>Durum</th>
                                                <th>Bağış ID</th>
                                                <th>İşlem</th>
                                            </tr>
                                        </thead>
                                        <tbody id="logTableBody">
                                            <?php
                                            $loglar = $db->query("SELECT * FROM bildirim_log ORDER BY tarih DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($loglar as $log):
                                            ?>
                                            <tr>
                                                <td><?= date('d.m.Y H:i', strtotime($log['tarih'])) ?></td>
                                                <td>
                                                    <?php if($log['tip'] == 'sms'): ?>
                                                    <span class="badge badge-info"><i class="mdi mdi-message-text"></i> SMS</span>
                                                    <?php elseif($log['tip'] == 'email'): ?>
                                                    <span class="badge badge-success"><i class="mdi mdi-email"></i> E-posta</span>
                                                    <?php else: ?>
                                                    <span class="badge badge-warning"><i class="mdi mdi-whatsapp"></i> WhatsApp</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($log['alici']) ?></td>
                                                <td>
                                                    <?php if($log['durum'] == 'gonderildi'): ?>
                                                    <span class="badge badge-success">Gönderildi</span>
                                                    <?php elseif($log['durum'] == 'hata'): ?>
                                                    <span class="badge badge-danger">Hata</span>
                                                    <?php else: ?>
                                                    <span class="badge badge-secondary">Beklemede</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $log['bagis_id'] ?? '-' ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" onclick="logDetay(<?= $log['id'] ?>)">
                                                        <i class="mdi mdi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SMS Şablon Modal -->
    <div class="modal fade" id="smsSablonModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="mdi mdi-message-text"></i> SMS Şablon Düzenle</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="smsSablonForm">
                        <input type="hidden" name="id" id="sms_sablon_id">
                        <div class="form-group">
                            <label>Şablon Adı *</label>
                            <input type="text" class="form-control" name="sablon_adi" id="sms_sablon_adi" required>
                        </div>
                        <div class="form-group">
                            <label>Şablon Tipi *</label>
                            <select class="form-control" name="sablon_tip" id="sms_sablon_tip" required>
                                <option value="bagis_tesekkur">Bağış Teşekkür</option>
                                <option value="bagis_hatirlatma">Bağış Hatırlatma</option>
                                <option value="bagis_onay">Bağış Onay</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Mesaj İçeriği * <small class="text-muted">(Maks. 160 karakter)</small></label>
                            <textarea class="form-control" name="mesaj" id="sms_mesaj" rows="4" maxlength="160" required></textarea>
                            <small class="text-muted">Karakter: <span id="smsKarakterSayisi">0</span>/160</small>
                        </div>
                        <div class="alert alert-light">
                            <strong>Kullanılabilir Değişkenler:</strong>
                            <span class="badge badge-secondary ml-2">{AD_SOYAD}</span>
                            <span class="badge badge-secondary ml-2">{TUTAR}</span>
                            <span class="badge badge-secondary ml-2">{PARA_BIRIMI}</span>
                            <span class="badge badge-secondary ml-2">{KAMPANYA}</span>
                            <span class="badge badge-secondary ml-2">{SIPARIS_NO}</span>
                            <span class="badge badge-secondary ml-2">{TARIH}</span>
                        </div>
                        <div class="form-check form-check-success">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="durum" value="1" id="sms_durum" checked>
                                <i class="input-helper"></i>
                                Aktif
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-info" onclick="smsSablonKaydet()">
                        <i class="mdi mdi-check"></i> Kaydet
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- E-posta Şablon Modal -->
    <div class="modal fade" id="emailSablonModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="mdi mdi-email"></i> E-posta Şablon Düzenle</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="emailSablonForm">
                        <input type="hidden" name="id" id="email_sablon_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Şablon Adı *</label>
                                    <input type="text" class="form-control" name="sablon_adi" id="email_sablon_adi" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>E-posta Konusu *</label>
                                    <input type="text" class="form-control" name="konu" id="email_konu" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>E-posta İçeriği (HTML) *</label>
                            <textarea class="form-control" name="icerik" id="email_icerik" rows="15"></textarea>
                        </div>
                        <div class="alert alert-light">
                            <strong>Kullanılabilir Değişkenler:</strong>
                            <span class="badge badge-secondary ml-2">{AD_SOYAD}</span>
                            <span class="badge badge-secondary ml-2">{TUTAR}</span>
                            <span class="badge badge-secondary ml-2">{PARA_BIRIMI}</span>
                            <span class="badge badge-secondary ml-2">{KAMPANYA}</span>
                            <span class="badge badge-secondary ml-2">{SIPARIS_NO}</span>
                            <span class="badge badge-secondary ml-2">{TARIH}</span>
                            <span class="badge badge-secondary ml-2">{SITE_ADI}</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-check-info">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name="sertifika_ekle" value="1" id="email_sertifika">
                                        <i class="input-helper"></i>
                                        Sertifika Ekle
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-check-success">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name="durum" value="1" id="email_durum" checked>
                                        <i class="input-helper"></i>
                                        Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-success" onclick="emailSablonKaydet()">
                        <i class="mdi mdi-check"></i> Kaydet
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Detay Modal -->
    <div class="modal fade" id="logDetayModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="mdi mdi-file-document"></i> Bildirim Detayları</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="logDetayIcerik">
                    <!-- AJAX ile doldurulacak -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <!-- İstatistik Modal -->
    <div class="modal fade" id="istatistikModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="mdi mdi-chart-bar"></i> Bildirim İstatistikleri</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <select class="form-control w-auto d-inline-block" id="istatistikGun" onchange="loadStats()">
                                <option value="7">Son 7 Gün</option>
                                <option value="30" selected>Son 30 Gün</option>
                                <option value="90">Son 90 Gün</option>
                            </select>
                        </div>
                    </div>
                    <div id="istatistikIcerik">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2">Yükleniyor...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="d-sm-flex justify-content-center">
            <span class="text-muted text-center d-block d-sm-inline-block">
                Copyright © <?= date('Y') ?> - Bildirim Yönetim Sistemi v1.0
            </span>
        </div>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// ========================================
// GENEL AYARLAR
// ========================================
$('#genelAyarForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '../_class/bildirim_islem.php',
        type: 'POST',
        data: {
            islem: 'bildirim_ayar_guncelle',
            sms_aktif: $('input[name="sms_aktif"]').is(':checked') ? 1 : 0,
            email_aktif: $('input[name="email_aktif"]').is(':checked') ? 1 : 0,
            whatsapp_aktif: $('input[name="whatsapp_aktif"]').is(':checked') ? 1 : 0,
            otomatik_gonderim: $('input[name="otomatik_gonderim"]').is(':checked') ? 1 : 0,
            gecikme_suresi: $('input[name="gecikme_suresi"]').val()
        },
        success: function(response) {
            const result = JSON.parse(response);
            if(result.success) {
                swal({
                    title: "Başarılı!",
                    text: result.message,
                    icon: "success"
                });
            } else {
                swal({
                    title: "Hata!",
                    text: result.message,
                    icon: "error"
                });
            }
        }
    });
});

// ========================================
// SMS ŞABLON YÖNETİMİ
// ========================================
function yeniSmsSablon() {
    $('#sms_sablon_id').val('');
    $('#sms_sablon_adi').val('');
    $('#sms_sablon_tip').val('bagis_tesekkur');
    $('#sms_mesaj').val('');
    $('#sms_durum').prop('checked', true);
    $('#smsSablonModal').modal('show');
}

function smsSablonDuzenle(id) {
    $.ajax({
        url: '../_class/bildirim_islem.php',
        type: 'POST',
        data: {
            islem: 'sms_sablon_getir',
            id: id
        },
        success: function(response) {
            const result = JSON.parse(response);
            if(result.success) {
                const sablon = result.data;
                $('#sms_sablon_id').val(sablon.id);
                $('#sms_sablon_adi').val(sablon.sablon_adi);
                $('#sms_sablon_tip').val(sablon.sablon_tip);
                $('#sms_mesaj').val(sablon.mesaj);
                $('#sms_durum').prop('checked', sablon.durum == 1);
                $('#smsSablonModal').modal('show');
            }
        }
    });
}

function smsSablonKaydet() {
    const formData = $('#smsSablonForm').serialize();
    
    $.ajax({
        url: '../_class/bildirim_islem.php',
        type: 'POST',
        data: formData + '&islem=sms_sablon_kaydet&durum=' + ($('#sms_durum').is(':checked') ? 1 : 0),
        success: function(response) {
            const result = JSON.parse(response);
            if(result.success) {
                swal({
                    title: "Başarılı!",
                    text: result.message,
                    icon: "success"
                }).then(() => {
                    location.reload();
                });
            } else {
                swal({
                    title: "Hata!",
                    text: result.message,
                    icon: "error"
                });
            }
        }
    });
}

function smsSablonSil(id) {
    swal({
        title: "Emin misiniz?",
        text: "Bu şablon silinecek!",
        icon: "warning",
        buttons: ["İptal", "Sil"],
        dangerMode: true
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: '../_class/bildirim_islem.php',
                type: 'POST',
                data: {
                    islem: 'sms_sablon_sil',
                    id: id
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if(result.success) {
                        swal("Başarılı!", result.message, "success").then(() => {
                            location.reload();
                        });
                    } else {
                        swal("Hata!", result.message, "error");
                    }
                }
            });
        }
    });
}

// Karakter sayacı
$('#sms_mesaj').on('input', function() {
    $('#smsKarakterSayisi').text($(this).val().length);
});

// ========================================
// E-POSTA ŞABLON YÖNETİMİ
// ========================================
function yeniEmailSablon() {
    $('#email_sablon_id').val('');
    $('#email_sablon_adi').val('');
    $('#email_konu').val('');
    $('#email_icerik').val('');
    $('#email_sertifika').prop('checked', false);
    $('#email_durum').prop('checked', true);
    $('#emailSablonModal').modal('show');
}

function emailSablonDuzenle(id) {
    $.ajax({
        url: '../_class/bildirim_islem.php',
        type: 'POST',
        data: {
            islem: 'email_sablon_getir',
            id: id
        },
        success: function(response) {
            const result = JSON.parse(response);
            if(result.success) {
                const sablon = result.data;
                $('#email_sablon_id').val(sablon.id);
                $('#email_sablon_adi').val(sablon.sablon_adi);
                $('#email_konu').val(sablon.konu);
                $('#email_icerik').val(sablon.icerik);
                $('#email_sertifika').prop('checked', sablon.sertifika_ekle == 1);
                $('#email_durum').prop('checked', sablon.durum == 1);
                $('#emailSablonModal').modal('show');
            }
        }
    });
}

function emailSablonKaydet() {
    const formData = $('#emailSablonForm').serialize();
    
    $.ajax({
        url: '../_class/bildirim_islem.php',
        type: 'POST',
        data: formData + '&islem=email_sablon_kaydet&durum=' + ($('#email_durum').is(':checked') ? 1 : 0) + '&sertifika_ekle=' + ($('#email_sertifika').is(':checked') ? 1 : 0),
        success: function(response) {
            const result = JSON.parse(response);
            if(result.success) {
                swal({
                    title: "Başarılı!",
                    text: result.message,
                    icon: "success"
                }).then(() => {
                    location.reload();
                });
            } else {
                swal({
                    title: "Hata!",
                    text: result.message,
                    icon: "error"
                });
            }
        }
    });
}

function emailSablonSil(id) {
    swal({
        title: "Emin misiniz?",
        text: "Bu şablon silinecek!",
        icon: "warning",
        buttons: ["İptal", "Sil"],
        dangerMode: true
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: '../_class/bildirim_islem.php',
                type: 'POST',
                data: {
                    islem: 'email_sablon_sil',
                    id: id
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if(result.success) {
                        swal("Başarılı!", result.message, "success").then(() => {
                            location.reload();
                        });
                    } else {
                        swal("Hata!", result.message, "error");
                    }
                }
            });
        }
    });
}

function emailOnizle(id) {
    $.ajax({
        url: '../_class/bildirim_islem.php',
        type: 'POST',
        data: {
            islem: 'email_sablon_getir',
            id: id
        },
        success: function(response) {
            const result = JSON.parse(response);
            if(result.success) {
                const sablon = result.data;
                const win = window.open('', '_blank', 'width=800,height=600');
                win.document.write(sablon.icerik);
                win.document.close();
            }
        }
    });
}

// ========================================
// WHATSAPP AYARLAR
// ========================================
$('#whatsappAyarForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '../_class/bildirim_islem.php',
        type: 'POST',
        data: {
            islem: 'whatsapp_ayar_guncelle',
            api_url: $('input[name="api_url"]').val(),
            api_token: $('input[name="api_token"]').val(),
            phone_number_id: $('input[name="phone_number_id"]').val(),
            business_account_id: $('input[name="business_account_id"]').val(),
            durum: $('input[name="durum"]').is(':checked') ? 1 : 0,
            test_modu: $('input[name="test_modu"]').is(':checked') ? 1 : 0
        },
        success: function(response) {
            const result = JSON.parse(response);
            if(result.success) {
                swal({
                    title: "Başarılı!",
                    text: result.message,
                    icon: "success"
                });
            } else {
                swal({
                    title: "Hata!",
                    text: result.message,
                    icon: "error"
                });
            }
        }
    });
});

function whatsappSablonDuzenle(id) {
    swal("Bilgi", "WhatsApp şablonları Meta Business Suite'den yönetilir.", "info");
}

// ========================================
// LOGLAR
// ========================================
function loadLogs() {
    const tip = $('#logFilterTip').val();
    const durum = $('#logFilterDurum').val();
    
    $.ajax({
        url: '../_class/bildirim_islem.php',
        type: 'POST',
        data: {
            islem: 'bildirim_log_getir',
            tip: tip,
            durum: durum,
            limit: 100
        },
        success: function(response) {
            const result = JSON.parse(response);
            if(result.success) {
                let html = '';
                result.data.forEach(log => {
                    const tipBadge = log.tip == 'sms' ? '<span class="badge badge-info"><i class="mdi mdi-message-text"></i> SMS</span>' :
                                     log.tip == 'email' ? '<span class="badge badge-success"><i class="mdi mdi-email"></i> E-posta</span>' :
                                     '<span class="badge badge-warning"><i class="mdi mdi-whatsapp"></i> WhatsApp</span>';
                    
                    const durumBadge = log.durum == 'gonderildi' ? '<span class="badge badge-success">Gönderildi</span>' :
                                       log.durum == 'hata' ? '<span class="badge badge-danger">Hata</span>' :
                                       '<span class="badge badge-secondary">Beklemede</span>';
                    
                    html += `
                        <tr>
                            <td>${log.tarih}</td>
                            <td>${tipBadge}</td>
                            <td>${log.alici}</td>
                            <td>${durumBadge}</td>
                            <td>${log.bagis_id || '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="logDetay(${log.id})">
                                    <i class="mdi mdi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#logTableBody').html(html);
            }
        }
    });
}

function logDetay(id) {
    $.ajax({
        url: '../_class/bildirim_islem.php',
        type: 'POST',
        data: {
            islem: 'bildirim_log_detay',
            id: id
        },
        success: function(response) {
            const result = JSON.parse(response);
            if(result.success) {
                const log = result.data;
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tip:</strong> ${log.tip.toUpperCase()}</p>
                            <p><strong>Alıcı:</strong> ${log.alici}</p>
                            <p><strong>Durum:</strong> ${log.durum}</p>
                            <p><strong>Tarih:</strong> ${log.tarih}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Bağış ID:</strong> ${log.bagis_id || '-'}</p>
                            ${log.konu ? '<p><strong>Konu:</strong> ' + log.konu + '</p>' : ''}
                            ${log.hata_mesaji ? '<p class="text-danger"><strong>Hata:</strong> ' + log.hata_mesaji + '</p>' : ''}
                        </div>
                    </div>
                    <hr>
                    <h6><strong>İçerik:</strong></h6>
                    <div class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                        <pre>${log.icerik}</pre>
                    </div>
                    ${log.api_response ? '<hr><h6><strong>API Yanıtı:</strong></h6><div class="bg-light p-3 rounded"><pre>' + log.api_response + '</pre></div>' : ''}
                `;
                $('#logDetayIcerik').html(html);
                $('#logDetayModal').modal('show');
            }
        }
    });
}

// ========================================
// İSTATİSTİKLER
// ========================================
function loadStats() {
    const gun = $('#istatistikGun').val();
    
    $.ajax({
        url: '../_class/bildirim_islem.php',
        type: 'POST',
        data: {
            islem: 'bildirim_istatistik',
            gun: gun
        },
        success: function(response) {
            const result = JSON.parse(response);
            if(result.success) {
                renderStatistics(result.toplam, result.gunluk);
            }
        }
    });
}

function renderStatistics(toplam, gunluk) {
    let html = '<div class="row mb-4">';
    
    // Toplam istatistikler
    toplam.forEach(stat => {
        const yuzde = stat.adet > 0 ? ((stat.basarili / stat.adet) * 100).toFixed(1) : 0;
        html += `
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-${stat.tip == 'sms' ? 'info' : stat.tip == 'email' ? 'success' : 'warning'}">
                            ${stat.adet}
                        </h3>
                        <p class="mb-0">${stat.tip.toUpperCase()}</p>
                        <small class="text-success">${stat.basarili} başarılı</small> / 
                        <small class="text-danger">${stat.hatali} hatalı</small>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar bg-success" style="width: ${yuzde}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    // Günlük grafik (opsiyonel)
    html += '<div class="card"><div class="card-body"><canvas id="gunlukGrafik" height="80"></canvas></div></div>';
    
    $('#istatistikIcerik').html(html);
    
    // Grafik çiz
    renderChart(gunluk);
}

function renderChart(gunluk) {
    // Günlük verileri işle ve grafik çiz
    const ctx = document.getElementById('gunlukGrafik');
    if(ctx) {
        // Chart.js ile grafik oluştur
        const tarihler = [...new Set(gunluk.map(g => g.tarih))];
        const smsData = tarihler.map(t => gunluk.filter(g => g.tarih == t && g.tip == 'sms')[0]?.adet || 0);
        const emailData = tarihler.map(t => gunluk.filter(g => g.tarih == t && g.tip == 'email')[0]?.adet || 0);
        const whatsappData = tarihler.map(t => gunluk.filter(g => g.tarih == t && g.tip == 'whatsapp')[0]?.adet || 0);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: tarihler,
                datasets: [
                    {
                        label: 'SMS',
                        data: smsData,
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'E-posta',
                        data: emailData,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'WhatsApp',
                        data: whatsappData,
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
}
</script>

