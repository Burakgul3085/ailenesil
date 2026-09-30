<?php
session_start();
require_once('../_class/baglan.php');
require_once('../_class/fonksiyon.php');

// Filtreler
$sql ="SELECT b.*, bm.adi AS kampanya_adi
       FROM bagis_odeme b
       LEFT JOIN bagis_moduller bm ON b.modul_id = bm.id
       WHERE 1=1";

// Modül filtre
if(isset($_GET['modul_filtre']) && $_GET['modul_filtre'] != ''){
    $sql.=" AND b.modul_id = '".intval($_GET['modul_filtre'])."'";
}

// Tarih başlangıç filtresi
if(isset($_GET['tarih_baslangic']) && $_GET['tarih_baslangic'] != ''){
    $sql.=" AND b.tarih >= '".$_GET['tarih_baslangic']."'";
}

// Tarih bitiş filtresi
if(isset($_GET['tarih_bitis']) && $_GET['tarih_bitis'] != ''){
    $sql.=" AND b.tarih <= '".$_GET['tarih_bitis']."'";
}

// Bağışçı adı filtresi
if(isset($_GET['bagisci_adi']) && $_GET['bagisci_adi'] != ''){
    $bagisci = htmlspecialchars($_GET['bagisci_adi']);
    $sql.=" AND (b.ad LIKE '%".$bagisci."%' OR b.soyad LIKE '%".$bagisci."%')";
}

// Search
if(isset($_GET['search']) && $_GET['search'] != ''){
    $search = htmlspecialchars($_GET['search']);
    $sql.=" AND (b.spno LIKE '%$search%' OR b.ad LIKE '%$search%' OR b.soyad LIKE '%$search%' OR b.email LIKE '%$search%')";
}

$sql.=" ORDER BY b.id DESC";
$rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// TARAYICI BİLGİLENDİRİCİ HEADER
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=bagis_listesi_".date('d-m-Y').".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "\xEF\xBB\xBF"; // UTF-8 BOM

echo "<table border='1' style='font-family:Arial; font-size:13px;'>";

echo "
<tr style='background:#ddd; font-weight:bold;'>
<td>ID</td>
<td>Sipariş No</td>
<td>Kampanya</td>
<td>Ad Soyad</td>
<td>Bağış Türü</td>
<td>Tutar</td>
<td>Durum</td>
<td>Tarih</td>
<td>Email</td>
<td>Telefon</td>
<td>İl</td>
<td>İlçe</td>
<td>Adres</td>
<td>Hediye Bağış</td>
<td>Anonim Bağış</td>
<td>Hediye Adı</td>
<td>Hediye Email</td>
<td>Not</td>
</tr>
";

foreach($rows as $r){

    // Durum
    $durum = ($r['paytronay'] == 1) ? 'Onaylandı' : 'Onaylanmadı';

    // Bağış tipi
    $turMap = [
        "hizli"=>"Hızlı",
        "tek"=>"Tek Seferlik",
        "surekli"=>"Sürekli"
    ];
    $bagis_tipi = $turMap[$r['bagis_tipi']] ?? "Normal";

    echo "<tr>
        <td>{$r['id']}</td>
        <td>{$r['spno']}</td>
        <td>{$r['kampanya_adi']}</td>
        <td>{$r['ad']} {$r['soyad']}</td>
        <td>{$bagis_tipi}</td>
        <td>{$r['tutar']} {$r['para_birimi']}</td>
        <td>{$durum}</td>
        <td>{$r['tarih']}</td>
        <td>{$r['email']}</td>
        <td>{$r['telefon']}</td>
        <td>{$r['il']}</td>
        <td>{$r['ilce']}</td>
        <td>{$r['adres']}</td>
        <td>".($r['hediye_bagis']?'Var':'Yok')."</td>
        <td>".($r['anonim_bagis']?'Görünür':'Gizli')."</td>
        <td>{$r['hediye_adi']}</td>
        <td>{$r['hediye_email']}</td>
        <td>{$r['not_bilgisi']}</td>
    </tr>";
}

echo "</table>";
exit;
?>