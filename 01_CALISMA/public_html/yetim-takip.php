<?php
// Hata raporlamayı sadece geliştirme aşamasında açık tutun
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "_class/baglan.php";

$takip_hash = isset($_GET['hash']) ? $_GET['hash'] : '';

if(empty($takip_hash)) {
    header("Location: /");
    exit;
}

try {
    // 1. Ana Yetim ve Sponsor Bilgileri
    $yetim_sorgu = $db->prepare("
        SELECT y.*, h.id as hamilik_id, h.bagisci_id, bo.ad, bo.soyad, bo.email, bo.telefon,
               (SELECT COUNT(*) FROM yetim_takip_loglari ytl WHERE ytl.yetim_id = y.id) as log_sayisi
        FROM yetimler y
        LEFT JOIN hamilikler h ON h.yetim_id = y.id AND h.durum = 1
        LEFT JOIN bagis_odeme bo ON bo.id = h.bagisci_id
        WHERE y.takip_link_hash = ? AND y.takip_link_acik = 1
    ");
    $yetim_sorgu->execute([$takip_hash]);
    $yetim = $yetim_sorgu->fetch(PDO::FETCH_ASSOC);

    if(!$yetim) {
        echo '<div style="background:#fee2e2; color:#b91c1c; padding:2rem; text-align:center; font-family:sans-serif;">Kayıt bulunamadı veya erişime kapalı.</div>';
        exit;
    }

    // 2. Kardeş Bilgilerini Çekme
    $kardesler_sorgu = $db->prepare("SELECT * FROM yetim_kardesler WHERE yetim_id = ? ORDER BY id ASC");
    $kardesler_sorgu->execute([$yetim['id']]);
    $kardesler = $kardesler_sorgu->fetchAll(PDO::FETCH_ASSOC);

    // 3. Ek Dosyaları Çekme
    $dosya_sorgu = $db->prepare("SELECT * FROM yetim_dosyalar WHERE yetim_id = ?");
    $dosya_sorgu->execute([$yetim['id']]);
    $ek_dosyalar = $dosya_sorgu->fetchAll(PDO::FETCH_ASSOC);

    // 4. Raporları (Loglar) Çekme
    $log_sorgu = $db->prepare("SELECT * FROM yetim_takip_loglari WHERE yetim_id = ? ORDER BY created_at DESC LIMIT 15");
    $log_sorgu->execute([$yetim['id']]);
    $loglar = $log_sorgu->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Sorgu Hatası: " . $e->getMessage());
}

// Yaş Hesaplama
$yas = "---";
if (!empty($yetim['dogum_tarihi'])) {
    $dogum_tarihi = new DateTime($yetim['dogum_tarihi']);
    $bugun = new DateTime();
    $yas = $bugun->diff($dogum_tarihi)->y;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($yetim['ad_soyad']) ?> - Yetim Takip Portalı</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { dark: '#0f172a', primary: '#059669', secondary: '#10b981', soft: '#f1f5f9' }
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        .hero-pattern { background-color: #0f172a; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 86c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm66 3c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm-46-43c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm0 20c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm20-10c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm58 10c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1z' fill='%231e293b' fill-opacity='0.4' fill-rule='evenodd'%3E%3C/path%3E%3C/svg%3E"); }
    </style>
</head>
<body class="bg-brand-soft antialiased">

    <header class="hero-pattern pt-12 pb-32 px-4 relative overflow-hidden">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between relative z-10 text-white">
            <div class="flex items-center gap-4 mb-6 md:mb-0">
                <div class="w-12 h-12 bg-brand-primary rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-hand-holding-heart text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight uppercase">NİSAUL AKSA</h1>
                    <p class="text-xs font-bold text-brand-secondary tracking-widest uppercase">Yetim Takip Portalı</p>
                </div>
            </div>
            <a href="/" class="bg-white/10 hover:bg-white/20 backdrop-blur-md px-5 py-2.5 rounded-xl text-sm font-bold transition-all border border-white/10">
                <i class="fas fa-home mr-2"></i> Anasayfa
            </a>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 -mt-20 relative z-20 pb-20">
        
        <div class="bg-white rounded-[2.5rem] shadow-2xl p-6 md:p-10 mb-8 border border-white">
            <div class="flex flex-col md:flex-row gap-10 items-center">
                <div class="relative">
                    <div class="w-52 h-52 rounded-[2.5rem] overflow-hidden ring-8 ring-slate-50 shadow-xl">
                        <?php if(!empty($yetim['foto'])): ?>
                            <img src="../img/yetimler/<?= $yetim['foto'] ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300">
                                <i class="fas fa-user-circle fa-6x"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="absolute -bottom-4 inset-x-0 flex justify-center">
                        <span class="bg-emerald-600 text-white text-[11px] font-black px-5 py-2 rounded-full shadow-lg ring-4 ring-white uppercase tracking-wider">
                            AKTİF TAKİPTE
                        </span>
                    </div>
                </div>

                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-4xl font-black text-slate-900 mb-2"><?= htmlspecialchars($yetim['ad_soyad']) ?></h2>
                    <?php if(!empty($yetim['tc_no'])): ?>
                        <p class="text-slate-400 font-bold text-sm mb-4 tracking-widest">TC NO: <?= $yetim['tc_no'] ?></p>
                    <?php endif; ?>
                    
                    <div class="inline-flex items-center gap-2 bg-brand-dark text-white px-4 py-2 rounded-2xl mb-6 shadow-lg">
                        <i class="fas fa-shield-heart text-brand-secondary"></i>
                        <span class="text-xs font-bold uppercase tracking-widest opacity-60">Sponsor:</span>
                        <span class="text-sm font-black italic"><?= htmlspecialchars(($yetim['ad'] ?? '').' '.($yetim['soyad'] ?? 'Bilinmiyor')) ?></span>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center md:text-left">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Yaş / Cinsiyet</span>
                            <span class="text-lg font-bold text-slate-700"><?= $yas ?> Yaş / <?= $yetim['cinsiyet'] ?></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center md:text-left">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Eğitim</span>
                            <span class="text-lg font-bold text-slate-700"><?= ucfirst(str_replace('_', ' ', $yetim['egitim_durumu'] ?? 'Belirtilmedi')) ?></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center md:text-left">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Konum</span>
                            <span class="text-lg font-bold text-slate-700"><?= htmlspecialchars($yetim['il'] ?? 'Gazze') ?></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center md:text-left">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Kardeş</span>
                            <span class="text-lg font-bold text-slate-700"><?= $yetim['kardes_sayisi'] ?> Kardeş</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="space-y-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <h4 class="text-slate-900 font-bold mb-4 flex items-center gap-2 text-sm uppercase">
                        <i class="fas fa-graduation-cap text-brand-primary"></i> Eğitim & Sağlık
                    </h4>
                    <div class="space-y-3">
                        <p class="text-slate-600 text-sm"><strong>Okul:</strong> <?= htmlspecialchars($yetim['okul_ismi'] ?? 'Kayıtlı Değil') ?></p>
                        <div class="pt-3 border-t">
                            <p class="text-slate-400 text-[10px] uppercase font-bold mb-1">Sağlık Durumu / Gözlemler</p>
                            <p class="text-slate-600 text-sm italic"><?= nl2br(htmlspecialchars($yetim['saglik_durumu'] ?? 'Bilgi yok.')) ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <h4 class="text-slate-900 font-bold mb-4 flex items-center gap-2 text-sm uppercase">
                        <i class="fas fa-users text-blue-500"></i> Aile Durumu
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm border-b pb-2">
                            <span class="text-slate-500 text-xs">Baba: <?= htmlspecialchars($yetim['baba_adi']) ?></span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $yetim['baba_durum'] == 'Vefat' ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' ?>"><?= $yetim['baba_durum'] ?></span>
                        </div>
                        <div class="flex justify-between items-center text-sm border-b pb-2">
                            <span class="text-slate-500 text-xs">Anne: <?= htmlspecialchars($yetim['anne_adi']) ?></span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $yetim['anne_durum'] == 'Vefat' ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' ?>"><?= $yetim['anne_durum'] ?></span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2 italic">Vasi Yakınlığı: <?= htmlspecialchars($yetim['vasi_yakinlik']) ?></p>
                    </div>
                </div>

                <?php if(count($kardesler) > 0): ?>
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <h4 class="text-slate-900 font-bold mb-4 flex items-center gap-2 text-sm uppercase">
                        <i class="fas fa-child text-orange-500"></i> Kardeş Detayları
                    </h4>
                    <div class="space-y-3">
                        <?php foreach($kardesler as $k): ?>
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <p class="text-xs font-bold text-slate-800"><?= htmlspecialchars($k['ad_soyad']) ?></p>
                            <div class="flex justify-between mt-1 text-[10px] text-slate-400 font-medium">
                                <span><?= $k['cinsiyet'] ?></span>
                                <span><?= $k['egitim_durumu'] ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                                <div class="bg-indigo-600 rounded-3xl p-6 shadow-xl text-white">
                    <h4 class="text-white font-bold mb-4 flex items-center gap-2 text-sm uppercase tracking-widest">
                        <i class="fas fa-tshirt"></i> İhtiyaç & Beden Ölçüleri
                    </h4>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                            <p class="opacity-60 mb-1 text-[9px]">Ayak No</p>
                            <p class="text-base font-bold"><?= $yetim['ayak_no'] ?? '-' ?></p>
                        </div>
                        <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                            <p class="opacity-60 mb-1 text-[9px]">Mont</p>
                            <p class="text-base font-bold"><?= $yetim['mont_beden'] ?? '-' ?></p>
                        </div>
                        <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                            <p class="opacity-60 mb-1 text-[9px]">Pantolon</p>
                            <p class="text-base font-bold"><?= $yetim['pantolon_beden'] ?? '-' ?></p>
                        </div>
                        <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                            <p class="opacity-60 mb-1 text-[9px]">Genel Beden</p>
                            <p class="text-base font-bold"><?= $yetim['beden_bilgisi'] ?? '-' ?></p>
                        </div>
                    </div>
                </div>

                <?php if(!empty($yetim['yetim_video'])): ?>
                <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-200 overflow-hidden text-center">
                    <h4 class="text-slate-900 font-bold p-2 mb-2 flex items-center gap-2 text-sm">
                        <i class="fas fa-video text-red-500"></i> Yetim Tanıtım Videosu
                    </h4>
                    <div class="aspect-video bg-slate-900 rounded-2xl overflow-hidden shadow-inner">
                        <video controls class="w-full h-full object-cover">
                            <source src="../img/yetimler/<?= $yetim['yetim_video'] ?>" type="video/mp4">
                        </video>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="lg:col-span-2 space-y-8">
                
                <?php if(count($ek_dosyalar) > 0): ?>
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-200">
                    <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-images text-emerald-500"></i> Ek Fotoğraflar & Belgeler
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php foreach($ek_dosyalar as $file): ?>
                            <a href="../img/yetimler/<?= $file['view_path'] ?>" target="_blank" class="block aspect-square rounded-2xl overflow-hidden border border-slate-100 hover:ring-4 ring-emerald-100 transition-all shadow-sm">
                                <img src="../img/yetimler/<?= $file['view_path'] ?>" class="w-full h-full object-cover">
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-12">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-1">Gelişim Raporları</h3>
                            <p class="text-slate-400 text-xs font-medium">Bağışlanan desteklerin yansımaları</p>
                        </div>
                        <div class="bg-emerald-50 text-emerald-700 px-4 py-2 rounded-2xl text-[10px] font-black tracking-widest">GÜNCEL</div>
                    </div>

                    <div style=" max-height: 40vh; overflow-y: scroll; " class="relative space-y-10">
                        <div class="absolute left-6 top-2 bottom-2 w-0.5 bg-slate-100"></div>
                        
                        <?php if(count($loglar) > 0): ?>
                            <?php foreach($loglar as $log): 
                                $icon = "fa-check"; $colorClass = "bg-slate-100 text-slate-500";
                                if(stripos($log['log_tipi'], 'saglik') !== false) { $icon = "fa-heartbeat"; $colorClass = "bg-red-50 text-red-500"; }
                                elseif(stripos($log['log_tipi'], 'egitim') !== false) { $icon = "fa-graduation-cap"; $colorClass = "bg-blue-50 text-blue-500"; }
                                elseif(stripos($log['log_tipi'], 'gida') !== false) { $icon = "fa-utensils"; $colorClass = "bg-orange-50 text-orange-500"; }
                            ?>
                            <div class="relative pl-14 group">
                                <div class="absolute left-0 top-0 flex items-center justify-center w-12 h-12 rounded-2xl border-4 border-white <?=$colorClass?> shadow-sm z-10 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas <?=$icon?> text-sm"></i>
                                </div>
                                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm group-hover:border-emerald-200 group-hover:shadow-md transition-all duration-300">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-[11px] font-black text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-xl uppercase tracking-wider"><?= str_replace('_', ' ', $log['log_tipi']) ?></span>
                                        <time class="text-slate-400 text-xs font-bold"><?= date('d.m.Y', strtotime($log['created_at'])) ?></time>
                                    </div>
                                    <div class="text-slate-600 text-sm leading-relaxed font-medium"><?= nl2br(htmlspecialchars($log['aciklama'])) ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="flex flex-col items-center justify-center py-24 text-center opacity-40">
                                <i class="fas fa-clipboard-list fa-3x mb-4"></i>
                                <p class="text-sm font-bold">Henüz periyodik rapor girilmemiş.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <footer class="mt-16 text-center text-slate-400 py-10">
            <p class="text-[10px] font-black uppercase tracking-[0.5em] mb-2">Aile ve Nesil Derneği</p>
            <p class="text-xs">Yetim takip sistemi bağışçı özel alanıdır.</p>
        </footer>
    </main>
</body>
</html>