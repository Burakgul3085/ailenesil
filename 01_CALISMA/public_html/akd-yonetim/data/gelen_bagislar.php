<?php 
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')  
{ 	
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        session_start();
        require_once('../../_class/baglan.php');
        require_once('../../_class/fonksiyon.php');

        $request = $_REQUEST;
        $params = array();

        // 1. ADIM: SİSTEMDEKİ AKTİF ÖDEME YÖNTEMİNİ ÇEKELİM
        // paytr tablosundaki aktif_odeme_yontemi (vakifbank veya paytr)
        $ayarSorgu = $db->query("SELECT aktif_odeme_yontemi FROM paytr LIMIT 1");
        $aktifAyar = $ayarSorgu->fetch(PDO::FETCH_ASSOC);
        $sistemdekiAktifYontem = strtolower($aktifAyar['aktif_odeme_yontemi'] ?? 'paytr');

        // 2. ADIM: ANA SORGUMUZ
        // bagis_odeme.kategori_id üzerinden kategori tablosuna bağlanıyoruz
        $sql = "SELECT b.*, 
                       bk.adi as kategori_adi,
                       bm.adi as kampanya_adi
                FROM bagis_odeme b 
                LEFT JOIN bagis_kategori bk ON b.kategori_id = bk.id 
                LEFT JOIN bagis_moduller bm ON bk.modul_id = bm.id
                WHERE 1=1";
        
        // Sponsor bağışlarını hariç tut (Normal bağış sekmesi olduğu için)
        $sql .= " AND (b.yetim_id IS NULL OR b.yetim_id = 0)";
        
        // --- FİLTRELEME ---

        // Kampanya Filtresi
        if(!empty($request['modul_filtre'])){
            $sql .= " AND bk.modul_id = :modul_filtre";
            $params['modul_filtre'] = $request['modul_filtre'];
        }
        
        // Kategori Filtresi (Yeni sütun: kategori_id)
        if(!empty($request['kategori_filtre'])){
            $sql .= " AND b.kategori_id = :kategori_filtre";
            $params['kategori_filtre'] = $request['kategori_filtre'];
        }
        
        // Tarih Filtreleri
        if(!empty($request['tarih_baslangic'])){
            $sql .= " AND b.tarih >= :tarih_baslangic";
            $params['tarih_baslangic'] = $request['tarih_baslangic'];
        }
        if(!empty($request['tarih_bitis'])){
            $sql .= " AND b.tarih <= :tarih_bitis";
            $params['tarih_bitis'] = $request['tarih_bitis'];
        }
        
        // Arama (Ad, Soyad, Sipariş No)
        if(!empty($request['bagisci_adi'])){
            $arama = "%" . $request['bagisci_adi'] . "%";
            $sql .= " AND (b.ad LIKE :search OR b.soyad LIKE :search OR b.spno LIKE :search)";
            $params['search'] = $arama;
        }

        // Toplam Kayıt Sayısı
        $totalData = $db->query("SELECT COUNT(*) FROM bagis_odeme WHERE (yetim_id IS NULL OR yetim_id = 0)")->fetchColumn();
        
        // Filtreli Kayıt Sayısı
        $stmtCount = $db->prepare($sql);
        $stmtCount->execute($params);
        $totalFilter = $stmtCount->rowCount();

        // Sıralama ve Limit
        $col = array(0 => 'b.id', 1 => 'b.spno', 2 => 'bk.adi', 3 => 'b.ad', 5 => 'b.tarih', 6 => 'b.tutar');
        $orderBy = $col[$request['order'][0]['column']] ?? 'b.id';
        $orderDir = $request['order'][0]['dir'] ?? 'DESC';
        $start = intval($request['start']);
        $length = intval($request['length']);
        
        $sql .= " ORDER BY $orderBy $orderDir LIMIT $start, $length";
        
        $query = $db->prepare($sql);
        $query->execute($params);
        $bagislar = $query->fetchAll(PDO::FETCH_ASSOC);

        $data = array();
        foreach($bagislar as $row) {
            
            // 1. Checkbox
            $check = '<div class="form-check m-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input"><i class="input-helper"></i></label></div>';

            // 2. Kampanya & Kategori Badges
            $kampanya_kategori = '
                <div class="d-flex flex-column">
                    <span style="display:none;" class="badge badge-primary mb-1" style="font-size:11px;">'.($row['kampanya_adi'] ?? 'Genel Kampanya').'</span>
                    <span class="badge badge-primary" style="font-size:10px;">'.($row['kategori_adi'] ?? 'Kategorisiz').'</span>
                </div>';

            // 3. Ödeme Onay Durumu
            if($row['paytronay'] == 1){
                $durum = '<span class="badge badge-pill badge-success"><i class="fa fa-check-circle mr-1"></i> Onaylandı</span>';
            } else {
                $durum = '<span class="badge badge-pill badge-warning text-dark"><i class="fa fa-clock-o mr-1"></i> Beklemede</span>';
            }

            // 4. DİNAMİK ÖDEME YÖNTEMİ (Sistem ayarına göre renklendirme)
            $bagisYontemi = strtolower($row['odeme_yontemi']);
            
            if($bagisYontemi == 'vakifbank') {
                // Eğer bu bağış vakıfbank ise ve sistemde de o aktifse yeşil yapıyoruz
                $class = ($sistemdekiAktifYontem == 'vakifbank') ? 'badge-success' : 'badge-outline-danger';
                $yontem = '<span class="badge '.$class.'"><i class="fa fa-university mr-1"></i> VAKIFBANK</span>';
            } else {
                // Eğer bu bağış paytr ise ve sistemde o aktifse mavi yapıyoruz
                $class = ($sistemdekiAktifYontem == 'paytr') ? 'badge-primary' : 'badge-outline-danger';
                $yontem = '<span class="badge '.$class.'"><i class="fa fa-credit-card mr-1"></i> VAKIFBANK</span>';
            }

            // 5. Bağış Türü
$tur = '';
if ($row['bagis_tipi'] == 'yetim_sponsorluk') {
    // Yetim sponsorluğu durumu
    $tur = '<span class="badge badge-primary small">YETİME SPONSOR</span>';
} elseif ($row['bagis_tipi'] == 'tek') {
    // Tek adlı veri geldiğinde
    $tur = '<span class="badge badge-info small">KAMPANYA BAĞIŞ</span>';
} elseif ($row['bagis_tipi'] == 'normal') {
    // Normal adlı veri geldiğinde sepete ödeme olarak yazacak
    $tur = '<span class="badge badge-success small">SEPETE ÖDEME</span>';
} elseif ($row['bagis_tipi'] == 'hizli') {
    // Hızlı bağış durumu
    $tur = '<span class="badge badge-secondary small">HIZLI BAĞIŞ</span>';
} else {
    // Belirtilenler dışındaki diğer durumlar
    $tur = '<span class="badge badge-light small">' . strtoupper($row['bagis_tipi']) . '</span>';
}

            // 6. İşlem Butonları
            $islemler = '
                <div class="btn-group">
                    <a href="gelen-bagis-detay.html?id='.$row["id"].'" class="btn btn-sm btn-outline-primary" title="Detay">
                        <i class="ti-eye"></i>
                    </a>
                    <a href="../_class/yonetim_islem.php?gelen_bagissil=ok&id='.$row["id"].'" class="btn btn-sm btn-outline-danger popconfirm" title="Sil">
                        <i class="ti-trash"></i>
                    </a>
                </div>';

            $subdata = array();
            $subdata[] = $check;
            $subdata[] = '<span class="font-weight-bold text-dark">#'.$row['spno'].'</span>';
            $subdata[] = $kampanya_kategori;
            $subdata[] = '<b>'.$row['ad'].' '.$row['soyad'].'</b><br><small class="text-muted">'.$row['telefon'].'</small>';
            $subdata[] = $tur;
            $subdata[] = date('d.m.Y', strtotime($row['tarih'])).'<br><small class="text-muted">'.date('H:i', strtotime($row['tarih'])).'</small>';
            $subdata[] = '<h6 class="mb-0 font-weight-bold">'.number_format($row['tutar'], 2, ',', '.').' '.$row['para_birimi'].'</h6>';
            $subdata[] = $durum;
            $subdata[] = $yontem;
            $subdata[] = $islemler;
            
            $data[] = $subdata;
        }

        echo json_encode(array(
            "draw"            => intval($request['draw']),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFilter),
            "data"            => $data
        ), JSON_UNESCAPED_UNICODE);

    } catch(Exception $e) {
        echo json_encode(array("error" => $e->getMessage()));
    }
}
?>