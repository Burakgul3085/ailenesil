<?php


$gunler = [
    'Pazartesi' => $dil['txt314'],
    'Salı'       => $dil['txt315'],
    'Çarşamba'   => $dil['txt316'],
    'Perşembe'   => $dil['txt317'],
    'Cuma'       => $dil['txt318'],
    'Cumartesi'  => $dil['txt319'],
    'Pazar'      => $dil['txt320'],
];

// ---------------------------------------------------
// Filtreler
// ---------------------------------------------------
$selected_sinif = isset($_GET['sinif']) ? trim($_GET['sinif']) : 'all';
$selected_gun   = isset($_GET['gun'])   ? trim($_GET['gun'])   : 'all';

// Kategoriler (Sınıflar) - Kategori tablosundan çek
$kategoriler = $db->query("SELECT * FROM derslik_kategorileri WHERE durum = 1 AND dil = '{$_SESSION['k_dil']}' ORDER BY sira ASC, adi ASC")->fetchAll(PDO::FETCH_ASSOC);

// Eğer sinif parametresi kategori adı olarak gelmişse, kategori_id'ye çevir
$kategori_id = NULL;
if ($selected_sinif !== 'all' && $selected_sinif !== '') {
    // Önce kategori adına göre kontrol et
    foreach($kategoriler as $kat) {
        if($kat['adi'] == $selected_sinif || $kat['seo'] == $selected_sinif) {
            $kategori_id = $kat['id'];
            break;
        }
    }
    // Eğer kategori bulunamadıysa, eski sistem gibi sinif alanına göre ara
}

// Sorgu - Kategori sistemine göre revize edildi
$sql = "SELECT d.*, k.adi as kategori_adi, p.baslik as program_baslik 
        FROM derslik_durumlari d 
        LEFT JOIN derslik_kategorileri k ON d.kategori_id = k.id 
        LEFT JOIN programlar p ON d.program_id = p.id 
        WHERE d.aktif=1";
$params = [];

if ($kategori_id !== NULL) {
    $sql .= " AND d.kategori_id = :kategori_id";
    $params[':kategori_id'] = $kategori_id;
} elseif ($selected_sinif !== 'all' && $selected_sinif !== '') {
    // Eski sistem uyumluluğu için sinif alanına göre de arama yap
    $sql .= " AND (d.sinif = :sinif OR d.kategori_id IS NULL)";
    $params[':sinif'] = $selected_sinif;
}

if ($selected_gun !== 'all' && $selected_gun !== '') {
    $sql .= " AND d.gun = :gun";
    $params[':gun'] = $selected_gun;
}

$sql .= " ORDER BY FIELD(d.gun,'Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi','Pazar'), d.saat ASC";
$q = $db->prepare($sql);
$q->execute($params);
$derslikler = $q->fetchAll(PDO::FETCH_ASSOC);

// Yardımcılar
function is_full_card(array $row): bool {
    // Öncelik: durum=1 ise kesin kırmızı
    if (isset($row['durum']) && (string)$row['durum'] === '1') return true;
    // Alternatif mantık: (kontenjan - katilimci) <= 0 ise de dolu say
    if (isset($row['kontenjan'], $row['katilimci']) && ((int)$row['kontenjan'] - (int)$row['katilimci']) <= 0) return true;
    return false;
}
function status_text(array $row, array $dil): string {
    if (isset($row['durum']) && (string)$row['durum'] === '1') return $dil['txt302'];       // DOLU
    if (isset($row['durum']) && (string)$row['durum'] === '0') return $dil['txt303'];       // Müsait
    return $dil['txt304']; // Yedek
}

?>


<style>
/* Kart renkleri, görseldeki tondan esinlenildi */
.ws-card.full   { background-color:#d13a3a; }   /* Kırmızı */
.ws-card.avail  { background-color:#b2cb38; }   /* Yeşil */
.ws-card .fade  { background: rgba(255,255,255,0.22); }

.tab-item { @apply px-3 py-2 text-sm border-b-2 border-transparent text-gray-600 hover:text-blue-600; }
.tab-item.active { @apply text-blue-600 border-blue-600 font-semibold; }

/* Küçük dokunuşlar */
.badge-chip { @apply inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold bg-white/30 text-white; }
.info-key   { @apply block text-[10px] uppercase tracking-wide opacity-90; }
.info-val   { @apply text-sm font-semibold; }
.btn-join   { @apply inline-flex items-center justify-center rounded-full bg-white text-gray-900 text-xs font-bold px-4 py-2; }
.btn-closed { @apply text-white/90 text-xs font-bold; }
</style>


<!-- Tailwind -->
<script src="https://cdn.tailwindcss.com"></script>

<br/><br/><br/>
<br/><br/><br/>
<!-- Page Title -->
<section class="text-center py-10">
    <h1 class="text-4xl font-extrabold text-gray-800 drop-shadow-md">
        <?php echo $dil['txt300']; ?>
    </h1>
    <p class="text-gray-500 mt-2 text-lg">
        <?php echo $dil['txt247']; ?>
    </p>
</section>

<!-- Gün Sekmeleri -->
<div style=" height: 65px; " class="max-w-7xl mx-auto px-4 mb-8 overflow-x-auto">
    <div class="flex justify-center gap-2 whitespace-nowrap">
        <?php
        $tabs = ['all'=>$dil['txt545']] + $gunler;
        foreach($tabs as $key=>$label):
            $active = ($selected_gun==$key) || ($key=='all'&&($selected_gun=='all'||$selected_gun==''));
            $qs = http_build_query(['gun'=>$key, 'sinif'=>$selected_sinif]);
        ?>
        <a href="<?php echo $htc['derslerurl'];?><?php echo $html;?>?<?=$qs?>"
        class="px-4 py-2 rounded-full text-sm font-semibold transition 
        <?= $active ? "bg-blue-600 text-white shadow-md scale-105" : "bg-gray-200 text-gray-700 hover:bg-gray-300" ?>">
            <?=$label?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Kategori (Sınıf) Filtre -->
<div class="max-w-7xl mx-auto px-4 mb-8 text-right">
<select id="sinifFilter" onchange="applySinif()"
    class="bg-white border border-gray-300 px-4 py-2 rounded-lg shadow-sm text-sm">
    <option value="all" <?=($selected_sinif=='all')?'selected':''?>><?=$dil['txt306']?></option>
    <?php foreach($kategoriler as $kat): 
        $katValue = $kat['adi'];
        $selected = ($selected_sinif == $katValue || $selected_sinif == $kat['seo']) ? 'selected' : '';
    ?>
        <option value="<?=htmlspecialchars($katValue)?>" <?=$selected?>><?=htmlspecialchars($kat['adi'])?></option>
    <?php endforeach; ?>
</select>
</div>

<!-- Kartlar -->
<section class="max-w-7xl mx-auto px-4 pb-20">
<?php if(empty($derslikler)) : ?>
    <div class="text-center p-8 bg-red-50 border border-red-200 rounded-lg text-red-600 font-medium">
        <?=$dil['txt311']?>
    </div>
<?php else: ?>
<div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">

<?php foreach($derslikler as $row):
    $full = is_full_card($row);
    $class = $full ? 'full' : 'avail';
    $status = status_text($row,$dil);

    // Görünen sütunları belirle
    $fields = [];

    if(!empty($row['sehir'])) {
        $fields[] = [
            'label' => $dil['txt546'],
            'value' => $row['sehir']
        ];
    }

    if(!empty($row['sinif'])) {
        $fields[] = [
            'label' => $dil['txt547'],
            'value' => $row['sinif']
        ];
    }

    if(isset($row['kontenjan']) && $row['kontenjan'] !== '') {
        $fields[] = [
            'label' => $dil['txt548'],
            'value' => $row['kontenjan']
        ];
    }

    if(isset($row['katilimci']) && $row['katilimci'] !== '') {
        $fields[] = [
            'label' => $dil['txt549'],
            'value' => $row['katilimci']
        ];
    }

    if(isset($row['aile_katilim'])) {
        $fields[] = [
            'label' => $dil['txt550'],
            'value' => ($row['aile_katilim'] == 1) ? $dil['txt551'] : $dil['txt552']
        ];
    }

    // Dinamik grid kolon sayısı
    $cols = count($fields);
    if     ($cols <= 1) $grid = "grid-cols-1";
    elseif ($cols == 2) $grid = "grid-cols-2";
    elseif ($cols == 3) $grid = "grid-cols-3";
    else                $grid = "grid-cols-3"; // max 3 gösterim layout bozulmasın
?>

<div class="relative group transform transition hover:scale-[1.05]">
    <div class="ws-card rounded-2xl p-6 min-h-[300px] shadow-xl 
        flex flex-col gap-4 justify-between transition
        <?= $class=='full' 
            ? 'bg-red-500 bg-gradient-to-br from-red-500 to-red-600' 
            : 'bg-green-500 bg-gradient-to-br from-green-500 to-lime-500'; ?>">

        <!-- Durum Rozeti -->
        <span class="absolute top-3 right-3 bg-white/40 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow">
            <?=$status?>
        </span>

        <!-- Zaman -->
        <div class="text-center">
            <h4 class="text-white font-bold tracking-wide text-sm">
                <?=$row['gun']?> (<?=$row['saat']?>)
            </h4>
            <?php if(!empty($row['tarih'])): ?>
            <p class="text-white/90 text-xs"><?=$row['tarih']?></p>
            <?php endif; ?>
        </div>

        <!-- Başlık -->
        <h3 class="text-white font-extrabold text-lg tracking-wide uppercase text-center">
            <?=$row['adi']?>
        </h3>

        <!-- Dinamik Bilgi Alanları -->
        <div class="grid <?=$grid?> text-center text-white gap-3 text-sm">
            <?php foreach($fields as $f): ?>
            <div>
                <span class="block text-[11px] opacity-80 uppercase tracking-wide">
                    <?=htmlspecialchars($f['label'])?>
                </span>
                <strong><?=htmlspecialchars($f['value'])?></strong>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Buton -->
        <div class="text-center mt-2">
        <?php if(!$full): ?>
            <a target="_blank" href="<?=$htc['iletisimurl'].$html?>"
               class="bg-white text-gray-700 font-semibold text-xs px-5 py-2 rounded-full shadow hover:bg-gray-100 transition">
               <?=@$dil['txt553'];?>
            </a>
        <?php else: ?>
            <span class="text-white text-xs opacity-80"><?=@$dil['txt554'];?></span>
        <?php endif; ?>
        </div>

    </div>
</div>

<?php endforeach; ?>

</div>
<?php endif; ?>
</section>


<script>
function applySinif(){
    const val = document.getElementById('sinifFilter').value;
    const params = new URLSearchParams(window.location.search);
    val!=='all' ? params.set('sinif',val) : params.delete('sinif');
    window.location.search = params.toString();
}
</script>