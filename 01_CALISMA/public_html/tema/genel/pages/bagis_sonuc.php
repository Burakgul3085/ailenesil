<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php 
$menubul = $db->query("SELECT * FROM menu WHERE menu_url = '".$htc['bagissonucurl']."' OR link = '".$htc['bagissonucurl']."' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
$menubas = $db->query("SELECT * FROM menu WHERE id = '{$menubul['menu_ust']}' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
if($_SESSION['sepet'] == null && !isset($_GET['sonuc'])) {
    header("Location:".$htc['anaurl']."".$html."");
    exit();
}

// Para birimi bilgisi
$seciliParaBirimi = isset($_SESSION['para_birimi']) ? $_SESSION['para_birimi'] : 'TRY';
$paraBirimiSembolleri = [
    'TRY' => '₺',
    'USD' => '$',
    'EUR' => '€'
];
$paraBirimiSembolu = $paraBirimiSembolleri[$seciliParaBirimi];
?>
<!-- Tailwind CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<section class="relative bg-slate-50 min-h-screen py-16 lg:py-24 overflow-hidden">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-teal-50 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-2xl mx-auto">
            
            <?php if(strip_tags($_GET['sonuc']) == 'basarili'): ?>
                <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-teal-900/5 border border-white overflow-hidden transition-all duration-500 hover:shadow-teal-900/10">
                    <div class="p-8 md:p-12 text-center">
                        <div class="relative inline-block mb-10">
                            <div class="absolute inset-0 bg-green-200 rounded-full animate-ping opacity-25"></div>
                            <div class="relative w-24 h-24 bg-gradient-to-br from-green-400 to-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-green-200">
                                <i class="fas fa-check text-4xl text-white"></i>
                            </div>
                        </div>

                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-4 tracking-tight leading-tight">
                            <?=@$dil['txt49'];?>
                        </h1>
                        <p class="text-lg text-slate-500 mb-10 leading-relaxed font-medium">
                            <?=@$dil['txt50'];?>
                        </p>
                        
                        <div class="bg-gradient-to-b from-emerald-50 to-white border border-emerald-100/50 rounded-3xl p-8 mb-10">
                            <p class="text-emerald-900 text-[1.05rem] leading-loose italic">
                                "Bağışınız başarıyla alınmıştır. Aksa Kadınları Derneği’ne göstermiş olduğunuz bu kıymetli destek için teşekkür ederiz. Yapmış olduğunuz bağış; Kudüs ve Mescid-i Aksa odaklı çalışmalarımızın sürdürülebilirliğine önemli katkı sağlamaktadır."
                            </p>
                            
                            <?php if(isset($_GET['oid'])): ?>
                                <div class="mt-6 pt-6 border-t border-emerald-100 flex items-center justify-center space-x-2">
                                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-600/60">İşlem No:</span>
                                    <span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-sm font-mono font-bold">
                                        #<?php echo strip_tags($_GET['oid']); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 mb-10">
                            <a href="<?php echo $htc['bagisurl'];?><?php echo $html;?>" class="flex-1 flex items-center justify-center px-8 py-4 bg-white border-2 border-slate-100 text-slate-700 font-bold rounded-2xl hover:border-emerald-500 hover:text-emerald-600 transition-all transform hover:-translate-y-1 active:scale-95">
                                <i class="fas fa-heart mr-3 text-rose-500"></i> Yeni Bağış
                            </a>
                        </div>

                        <div class="pt-8 border-t border-slate-50">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] block mb-6"><?=@$dil['txt481'];?></span>
                            <div class="flex justify-center gap-4">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($htc['anaurl']); ?>" target="_blank" class="group w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-[#1877F2] hover:text-white transition-all duration-300 transform hover:rotate-6">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($dil['txt482']); ?>&url=<?php echo urlencode($htc['anaurl']); ?>" target="_blank" class="group w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-[#1DA1F2] hover:text-white transition-all duration-300 transform hover:-rotate-6">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://wa.me/?text=<?php echo urlencode($dil['txt483'] . ' ' . $htc['anaurl']); ?>" target="_blank" class="group w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-[#25D366] hover:text-white transition-all duration-300 transform hover:rotate-6">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['sepet']); ?>

            <?php elseif(strip_tags($_GET['sonuc']) == 'hata'): ?>
                <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-rose-900/5 border border-white overflow-hidden">
                    <div class="p-8 md:p-12 text-center">
                        <div class="w-24 h-24 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-8 ring-8 ring-rose-50/50">
                            <i class="fas fa-exclamation-triangle text-4xl"></i>
                        </div>
                        
                        <h1 class="text-3xl font-extrabold text-slate-800 mb-4"><?=@$dil['txt52'];?></h1>
                        <p class="text-slate-500 mb-8 font-medium"><?=@$dil['txt53'];?></p>
                        
                        <div class="bg-rose-50/50 border-2 border-dashed border-rose-100 rounded-3xl p-6 mb-10 text-left">
                            <h3 class="text-sm font-bold text-rose-900 uppercase tracking-wider mb-2 flex items-center">
                                <i class="fas fa-info-circle mr-2"></i> Sistem Mesajı
                            </h3>
                            <p class="text-rose-700/80 text-sm leading-relaxed mb-3"><?=@$dil['txt54'];?></p>
                            <?php if(isset($_GET['msg'])): ?>
                                <code class="block bg-white/80 p-3 rounded-xl text-rose-600 text-xs font-mono break-all border border-rose-100">
                                    <?php echo strip_tags(urldecode($_GET['msg'])); ?>
                                </code>
                            <?php endif; ?>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <a href="<?php echo $htc['bagisurl'];?><?php echo $html;?>" class="flex items-center justify-center px-8 py-4 bg-rose-600 text-white font-bold rounded-2xl hover:bg-rose-700 transition-all shadow-lg shadow-rose-200 transform hover:-translate-y-1">
                                <i class="fas fa-sync-alt mr-2"></i> Tekrar Dene
                            </a>
                            <a href="<?php echo $htc['iletisimurl'];?><?php echo $html;?>" class="flex items-center justify-center px-8 py-4 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all transform hover:-translate-y-1">
                                <i class="fas fa-headset mr-2"></i> Destek Al
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>
<style>
@keyframes bounce-short {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}
.animate-bounce-short {
  animation: bounce-short 1s ease-in-out infinite;
}
</style>

<?php include('slider_menu.php');?>