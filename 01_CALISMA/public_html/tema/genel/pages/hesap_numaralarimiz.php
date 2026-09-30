<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php 
$HSorgu = $db->prepare("SELECT * FROM hesaplar ORDER BY id ASC");
$HSorgu->execute();
$Hesaplar = $HSorgu->fetchAll(PDO::FETCH_ASSOC);
?>
<br/><br/><br/>

<section class="page-section">
    <div class="bg-white">
        <div class="col-12 p-0 banner">
            <div class="slide-overlay"></div>
        </div>
        <div class="container banner-fix">
            <div class="row">
                <div class="col-12 z-index-9">
                    <ol class="breadcrumb">
                        <li><a href="<?php echo $htc['anaurl'];?><?php echo $html;?>"> <i class="fa fa-home"></i> </a></li>
                        <li><?=@$dil['txt426'];?></li>
                    </ol>
                </div>
                <div class="col-12 z-index-9">
                    <div class="page-content">
                        <h2 class="page-title"><?=@$dil['txt426'];?></h2>
                        <div class="row">
                        <?php foreach($Hesaplar as $h){ 
                            // Para birimi kontrolü
                            $para_birimi = !empty($h['para_birimi']) ? $h['para_birimi'] : 'TRY';
                            $swift_kodu = !empty($h['swift_kodu']) ? $h['swift_kodu'] : '';
                            $para_birimi_label = '';
                            if($para_birimi == 'USD') $para_birimi_label = ' (USD - Dolar)';
                            elseif($para_birimi == 'EUR') $para_birimi_label = ' (EUR - Euro)';
                            elseif($para_birimi == 'TRY') $para_birimi_label = ' (TRY - Türk Lirası)';
                        ?>
                            <div class="col-md-6 mb-3">
                                <div class="card p-3 d-flex align-items-center">
                                    <div class="d-flex align-items-center w-100">
                                        <div style="width:56px; height:56px; border-radius:8px; overflow:hidden; background:#f5f5f5; display:flex; align-items:center; justify-content:center; margin-right:12px;">
                                            <?php if(!empty($h['logo'])){ ?>
                                                <img src="<?php echo tema; ?>/uploads/hesaplar/<?php echo $h['logo']; ?>" style="max-width:100%; max-height:100%;"/>
                                            <?php } else { ?>
                                                <i class="fas fa-university" style="font-size:24px; color:#74a2d1;"></i>
                                            <?php } ?>
                                        </div>
                                        <div class="flex-fill">
                                            <div style="font-weight:700; cursor:pointer;" class="copyable-text" data-copy="<?php echo htmlspecialchars($h['banka_adi']); ?>" onclick="copyToClipboard('<?php echo htmlspecialchars($h['banka_adi']); ?>')" title="Kopyalamak için tıklayın">
                                                <?php echo htmlspecialchars($h['banka_adi']); ?><?php echo $para_birimi_label; ?>
                                                <i class="far fa-copy" style="font-size:11px; margin-left:5px; opacity:0.6;"></i>
                                            </div>
                                            <div class="text-muted" style="font-size:14px; cursor:pointer; margin-top:5px;" onclick="copyToClipboard('<?php echo htmlspecialchars($h['iban']); ?>')" title="Kopyalamak için tıklayın">
                                                <?=@$dil['txt427'];?> <span class="iban-text copyable-text" data-copy="<?php echo htmlspecialchars($h['iban']); ?>"><?php echo htmlspecialchars($h['iban']); ?></span>
                                                <i class="far fa-copy" style="font-size:11px; margin-left:5px; opacity:0.6;"></i>
                                            </div>
                                            <?php if(!empty($h['hesap_no'])){ ?>
                                            <div class="text-muted" style="font-size:14px; cursor:pointer; margin-top:5px;" onclick="copyToClipboard('<?php echo htmlspecialchars($h['hesap_no']); ?>')" title="Kopyalamak için tıklayın">
                                                <?=@$dil['txt428'];?> <span class="hesapno-text copyable-text" data-copy="<?php echo htmlspecialchars($h['hesap_no']); ?>"><?php echo htmlspecialchars($h['hesap_no']); ?></span>
                                                <i class="far fa-copy" style="font-size:11px; margin-left:5px; opacity:0.6;"></i>
                                            </div>
                                            <?php } ?>
                                            <?php if(!empty($h['sube'])){ ?>
                                            <div class="text-muted" style="font-size:14px; margin-top:5px;">
                                                <?=@$dil['txt429'];?> <?php echo htmlspecialchars($h['sube']); ?>
                                            </div>
                                            <?php } ?>
                                            <div class="text-muted" style="font-size:13px; cursor:pointer; margin-top:5px;" onclick="copyToClipboard('<?php echo htmlspecialchars($h['hesap_sahibi']); ?>')" title="Kopyalamak için tıklayın">
                                                <?=@$dil['txt430'];?> <span class="copyable-text" data-copy="<?php echo htmlspecialchars($h['hesap_sahibi']); ?>"><?php echo htmlspecialchars($h['hesap_sahibi']); ?></span>
                                                <i class="far fa-copy" style="font-size:11px; margin-left:5px; opacity:0.6;"></i>
                                            </div>
                                            <?php if(!empty($swift_kodu)){ ?>
                                            <div class="text-muted" style="font-size:13px; cursor:pointer; margin-top:5px;" onclick="copyToClipboard('<?php echo htmlspecialchars($swift_kodu); ?>')" title="Kopyalamak için tıklayın">
                                                SWIFT (BIC): <span class="copyable-text" data-copy="<?php echo htmlspecialchars($swift_kodu); ?>"><?php echo htmlspecialchars($swift_kodu); ?></span>
                                                <i class="far fa-copy" style="font-size:11px; margin-left:5px; opacity:0.6;"></i>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-primary copy-btn" data-copy="<?php echo htmlspecialchars($h['iban']); ?>"><i class="far fa-copy"></i> <?=@$dil['txt431'];?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function(){
    function copyText(text){
        try{
            navigator.clipboard.writeText(text).then(function(){
                Swal.fire({icon:'success', title:'<?=@$dil['txt432'];?>', timer:1200, showConfirmButton:false});
            }).catch(function(){
                fallbackCopy(text);
            });
        }catch(e){ fallbackCopy(text); }
    }
    function fallbackCopy(text){
        var t = document.createElement('textarea');
        t.value = text; document.body.appendChild(t); t.select();
        try{ document.execCommand('copy'); Swal.fire({icon:'success', title:'<?=@$dil['txt432'];?>', timer:1200, showConfirmButton:false}); }catch(err){}
        document.body.removeChild(t);
    }
    window.copyToClipboard = function(text){
        copyText(text);
    };
    $(document).on('click', '.copy-btn', function(){
        var v = $(this).data('copy');
        if(v){ copyText(v); }
    });
    $(document).on('click', '.copyable-text', function(e){
        e.stopPropagation();
        var v = $(this).data('copy');
        if(v){ copyText(v); }
    });
})();
</script>

<style>
.copyable-text {
    transition: all 0.2s ease;
    position: relative;
}
.copyable-text:hover {
    color: #007bff !important;
    text-decoration: underline;
}
.copyable-text:hover i.fa-copy {
    opacity: 1 !important;
    color: #007bff;
}
div[onclick*="copyToClipboard"] {
    transition: all 0.2s ease;
}
div[onclick*="copyToClipboard"]:hover {
    background-color: rgba(0, 123, 255, 0.05);
    padding-left: 5px;
    border-left: 2px solid #007bff;
}
</style>


