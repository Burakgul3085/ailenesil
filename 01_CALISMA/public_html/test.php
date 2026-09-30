<?php
require_once('_class/baglan.php');

$logDir = __DIR__ . '/logs/vakifbank/';
$transactions = [];

if (is_dir($logDir)) {
    $files = array_reverse(glob($logDir . "*.log"));
    foreach ($files as $file) {
        $content = file_get_contents($file);
        preg_match_all('/\{(?:[^{}]|(?R))*\}/x', $content, $matches);
        foreach ($matches[0] as $jsonStr) {
            $decoded = json_decode($jsonStr, true);
            if ($decoded && isset($decoded['order_id'])) {
                $oid = $decoded['order_id'];
                $logType = $decoded['type'] ?? '';
                if ($logType === 'PAYMENT_REQUEST' || $logType === 'REQUEST') {
                    if (!isset($transactions[$oid]['request'])) $transactions[$oid]['request'] = $decoded;
                } elseif ($logType === 'BANK_RESPONSE' || $logType === 'CALLBACK_RECEPTION') {
                    $transactions[$oid]['response'] = $decoded;
                } elseif ($logType === 'EXCEPTION') {
                    if (!isset($transactions[$oid]['response'])) {
                        $transactions[$oid]['response'] = [
                            'type' => 'EXCEPTION',
                            'order_id' => $oid,
                            'status' => 'FAILED',
                            'response_code' => 'EXCEPTION',
                            'response_message' => $decoded['message'] ?? $decoded['exception_class'] ?? 'Bilinmeyen hata',
                            'timestamp' => $decoded['timestamp'] ?? '',
                            'stage' => $decoded['stage'] ?? '',
                            'trace' => $decoded['trace'] ?? '',
                            'context' => $decoded['context'] ?? []
                        ];
                    }
                }
            }
        }
    }
}

try {
    $stmt = $db->query("SELECT * FROM vakifbank_transaction_logs ORDER BY id DESC LIMIT 100");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data = json_decode($row['log_data'], true);
        if (!$data) continue;
        $oid = $row['order_id'];
        if ($row['log_type'] === 'REQUEST') {
            if (!isset($transactions[$oid]['request'])) $transactions[$oid]['request'] = $data;
        } else {
            $transactions[$oid]['response'] = $data;
        }
    }
} catch (Exception $e) {
    // Tablo yoksa devam et
}

// Tarihe göre sıralama (en yeni üstte)
uasort($transactions, function($a, $b) {
    $t1 = $a['request']['timestamp'] ?? $a['response']['timestamp'] ?? '';
    $t2 = $b['request']['timestamp'] ?? $b['response']['timestamp'] ?? '';
    return strcmp($t2, $t1);
});

function getBankMessage($res) {
    if (!$res) return 'Yanıt Alınamadı';
    $msg = $res['response_message'] ?? '';
    $code = $res['response_code'] ?? $res['bank_rc'] ?? '';
    $err = $res['ErrorCode'] ?? ($res['full_response']['ErrorCode'] ?? '');
    $parts = array_filter([$msg, $code ? "($code)" : '', $err]);
    return trim(implode(' ', $parts)) ?: 'Yanıt Alınamadı';
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vakıfbank VPOS Monitor | Detaylı Görünüm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; }
        .glass-card { background: #ffffff; border: 1px solid #e2e8f0; }
        .json-pre { background: #0f172a; color: #38bdf8; scrollbar-width: thin; }
        .status-line { width: 6px; height: 100%; position: absolute; left: 0; top: 0; }
    </style>
</head>
<body class="p-4 md:p-8">

    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">İşlem Analizi</h1>
                <p class="text-slate-500 text-sm italic">Ham veriler varsayılan olarak açık gösterilmektedir.</p>
            </div>
            <button onclick="window.location.reload()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-xl transition-all flex items-center gap-2 shadow-lg shadow-indigo-200 font-semibold">
                <i class="fas fa-sync-alt"></i> Listeyi Güncelle
            </button>
        </div>

        <div class="space-y-6">
            <?php foreach ($transactions as $orderId => $pair): 
                $req = $pair['request'] ?? null;
                $res = $pair['response'] ?? null;
                $isSuccess = (isset($res['status']) && $res['status'] === 'SUCCESS') || (isset($res['response_code']) && $res['response_code'] === '00');
                $isPending = !$res;
            ?>
            
            <div id="card-<?php echo $orderId; ?>" class="glass-card rounded-2xl shadow-sm relative overflow-hidden">
                <div class="status-line <?php echo $isPending ? 'bg-amber-400' : ($isSuccess ? 'bg-emerald-500' : 'bg-rose-500'); ?>"></div>

                <div class="p-6">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 border border-slate-200">
                                <i class="fas fa-file-invoice-dollar text-xl"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">ORDER ID</span>
                                <h3 class="text-xl font-black text-slate-800 tracking-tight"><?php echo $orderId; ?></h3>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-right px-4">
                                <span class="text-[10px] font-bold text-slate-400 uppercase block tracking-widest">İŞLEM TUTARI</span>
                                <span class="text-2xl font-black text-slate-900"><?php echo $req['amount'] ?? '0.00'; ?> <small class="text-slate-400 text-sm font-normal">TRY</small></span>
                            </div>
                            <button onclick="toggleDetails('<?php echo $orderId; ?>')" id="btn-<?php echo $orderId; ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2 px-4 rounded-lg transition-all text-xs border border-slate-300">
                                <i class="fas fa-eye-slash mr-1"></i> DETAYI KAPAT
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                            <span class="text-[10px] text-slate-400 block font-bold mb-1">DURUM</span>
                            <?php if ($isPending): ?>
                                <span class="text-amber-600 font-bold text-sm"><i class="fas fa-clock mr-1"></i> Beklemede</span>
                            <?php elseif ($isSuccess): ?>
                                <span class="text-emerald-600 font-bold text-sm"><i class="fas fa-check-circle mr-1"></i> Başarılı</span>
                            <?php else: ?>
                                <span class="text-rose-600 font-bold text-sm"><i class="fas fa-times-circle mr-1"></i> Hatalı (<?php echo htmlspecialchars($res['response_code'] ?? $res['bank_rc'] ?? 'ERR'); ?>)</span>
                            <?php endif; ?>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 text-sm">
                            <span class="text-[10px] text-slate-400 block font-bold mb-1">ZAMAN</span>
                            <b class="text-slate-700"><?php $ts = $req['timestamp'] ?? $res['timestamp'] ?? ''; echo $ts ? substr($ts, 11, 8) : 'N/A'; ?></b>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 text-sm">
                            <span class="text-[10px] text-slate-400 block font-bold mb-1">MÜŞTERİ IP</span>
                            <b class="text-slate-700"><?php echo $req['ip_address'] ?? 'N/A'; ?></b>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 text-sm">
                            <span class="text-[10px] text-slate-400 block font-bold mb-1">BANKA MESAJI</span>
                            <b class="block <?php echo $isSuccess ? 'text-emerald-600' : 'text-rose-600'; ?> text-xs break-words" title="<?php echo htmlspecialchars(getBankMessage($res)); ?>"><?php echo htmlspecialchars(getBankMessage($res)); ?></b>
                        </div>
                    </div>

                    <div id="json-<?php echo $orderId; ?>" class="block space-y-4 pt-4 border-t border-dashed border-slate-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <label class="text-[10px] font-black text-indigo-500 uppercase tracking-tighter italic">>> İstek Verisi (Request)</label>
                                    <span class="text-[9px] text-slate-400 font-mono">JSON</span>
                                </div>
                                <pre class="json-pre p-4 rounded-xl text-[11px] overflow-x-auto shadow-inner max-h-96 ring-1 ring-slate-700"><?php echo htmlspecialchars(json_encode($req ?: [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <label class="text-[10px] font-black text-emerald-500 uppercase tracking-tighter italic">>> Banka Yanıtı (Response)</label>
                                    <span class="text-[9px] text-slate-400 font-mono">JSON</span>
                                </div>
                                <pre class="json-pre p-4 rounded-xl text-[11px] overflow-x-auto shadow-inner max-h-96 ring-1 ring-slate-700"><?php echo htmlspecialchars(json_encode($res ?? ['_note' => 'Yanıt kaydı bulunamadı'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                            </div>
                        </div>
                        <?php
                        $rawResp = $res['raw_response'] ?? ($res['full_response']['raw_response'] ?? null);
                        if ($rawResp && is_string($rawResp)): ?>
                        <div class="space-y-2 border-t border-slate-200 pt-4">
                            <label class="text-[10px] font-black text-amber-500 uppercase tracking-tighter italic block">>> Ham Banka Yanıtı (raw_response) — Hata Ayıklama</label>
                            <pre class="json-pre p-4 rounded-xl text-[11px] overflow-x-auto overflow-y-auto shadow-inner max-h-64 ring-1 ring-amber-800 whitespace-pre-wrap break-all"><?php echo htmlspecialchars($rawResp); ?></pre>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($res['context']) || !empty($res['trace'])): ?>
                        <div class="space-y-2 border-t border-slate-200 pt-4">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-tighter italic block">>> Ek Bilgi (context / trace)</label>
                            <pre class="json-pre p-4 rounded-xl text-[11px] overflow-x-auto overflow-y-auto shadow-inner max-h-48 ring-1 ring-slate-700 whitespace-pre-wrap"><?php
                            if (!empty($res['trace'])) echo htmlspecialchars($res['trace']);
                            if (!empty($res['context'])) echo "\n\nContext:\n" . htmlspecialchars(json_encode($res['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                            ?></pre>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function toggleDetails(id) {
            const el = document.getElementById('json-' + id);
            const btn = document.getElementById('btn-' + id);
            
            if(el.style.display === 'none') {
                el.style.display = 'block';
                btn.innerHTML = '<i class="fas fa-eye-slash mr-1"></i> DETAYI KAPAT';
                btn.className = "bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2 px-4 rounded-lg transition-all text-xs border border-slate-300";
            } else {
                el.style.display = 'none';
                btn.innerHTML = '<i class="fas fa-eye mr-1"></i> DETAYI GÖSTER';
                btn.className = "bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold py-2 px-4 rounded-lg transition-all text-xs border border-indigo-200";
            }
        }
    </script>
</body>
</html>