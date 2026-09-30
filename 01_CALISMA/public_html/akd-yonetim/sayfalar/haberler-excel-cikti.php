<?php
	session_start();
	require_once('../../_class/baglan.php');
	require_once('../../_class/fonksiyon.php');
	require_once('../../_class/Classes/PHPExcel.php');

	// Class Başlattık
	$excel = new PHPExcel();

	// Gereksiz Ayarlar
	$excel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("PHPExcel Test Document")
							 ->setSubject("PHPExcel Test Document")
							 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
							 ->setKeywords("office PHPExcel php")
							 ->setCategory("Test result file");
	
	// Sayfanın Başlığı
	$excel->getActiveSheet()->setTitle('Haberler V7');
	
	// Veri Girişi
	$excel->getActiveSheet()->setCellValue('A1', 'Haber ID');
	$excel->getActiveSheet()->setCellValue('B1', 'Sıra');
	$excel->getActiveSheet()->setCellValue('C1', 'Kategori');
	$excel->getActiveSheet()->setCellValue('D1', 'Adı');	
	$excel->getActiveSheet()->setCellValue('E1', 'Açıklama');	
	$excel->getActiveSheet()->setCellValue('F1', 'Video');
	$excel->getActiveSheet()->setCellValue('G1', 'Spot');
	$excel->getActiveSheet()->setCellValue('H1', 'Keywords');
	$excel->getActiveSheet()->setCellValue('I1', 'Description');
	$excel->getActiveSheet()->setCellValue('J1', 'Durum');
	$excel->getActiveSheet()->setCellValue('K1', 'Manşet');
	$excel->getActiveSheet()->setCellValue('L1', 'Manset Yanı');
	$excel->getActiveSheet()->setCellValue('M1', 'Resim');
	$excel->getActiveSheet()->setCellValue('N1', 'Tarih');
	$excel->getActiveSheet()->setCellValue('O1', 'Güncelleme Tarihi');
	$excel->getActiveSheet()->setCellValue('P1', 'Dil');
	
	$arttir = 2;
	
	$Sorgu = $db->prepare("SELECT * FROM haberler");
	$Sorgu->execute();
	$islem = $Sorgu->fetchALL(PDO::FETCH_ASSOC);
	foreach ( $islem as $Sonuc )
	{
		$excel->getActiveSheet()->setCellValue('A'. $arttir, $Sonuc['id']);
		$excel->getActiveSheet()->setCellValue('B'. $arttir, $Sonuc['sira']);
		$excel->getActiveSheet()->setCellValue('C'. $arttir, $Sonuc['kategori']);
		$excel->getActiveSheet()->setCellValue('D'. $arttir, $Sonuc['adi']);
		$excel->getActiveSheet()->setCellValue('E'. $arttir, $Sonuc['aciklama']);
		$excel->getActiveSheet()->setCellValue('F'. $arttir, $Sonuc['videoid']);
		$excel->getActiveSheet()->setCellValue('G'. $arttir, $Sonuc['spot']);
		$excel->getActiveSheet()->setCellValue('H'. $arttir, $Sonuc['keywords']);
		$excel->getActiveSheet()->setCellValue('I'. $arttir, $Sonuc['description']);
		$excel->getActiveSheet()->setCellValue('J'. $arttir, $Sonuc['durum']);
		$excel->getActiveSheet()->setCellValue('K'. $arttir, $Sonuc['manset']);
		$excel->getActiveSheet()->setCellValue('L'. $arttir, $Sonuc['manset_yani']);
		$excel->getActiveSheet()->setCellValue('M'. $arttir, $Sonuc['resim']);
		$excel->getActiveSheet()->setCellValue('N'. $arttir, $Sonuc['tarih']);
		$excel->getActiveSheet()->setCellValue('O'. $arttir, $Sonuc['tarihg']);
		$excel->getActiveSheet()->setCellValue('P'. $arttir, $Sonuc['dil']);
		$arttir++;
	}
	
	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="haberler-v7.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');
	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
	$kaydet = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$kaydet->save('php://output');
	
?>