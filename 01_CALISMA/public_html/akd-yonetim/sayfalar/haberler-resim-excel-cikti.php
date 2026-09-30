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
	$excel->getActiveSheet()->setTitle('Haber Resimler V7');
	
	// Veri Girişi
	$excel->getActiveSheet()->setCellValue('A1', 'ID');
	$excel->getActiveSheet()->setCellValue('B1', 'Resim ID');
	$excel->getActiveSheet()->setCellValue('C1', 'Resim');
	
	$arttir = 2;
	
	$Sorgu = $db->prepare("SELECT * FROM haberfoto");
	$Sorgu->execute();
	$islem = $Sorgu->fetchALL(PDO::FETCH_ASSOC);
	foreach ( $islem as $Sonuc )
	{
		$excel->getActiveSheet()->setCellValue('A'. $arttir, $Sonuc['id']);
		$excel->getActiveSheet()->setCellValue('B'. $arttir, $Sonuc['resimid']);
		$excel->getActiveSheet()->setCellValue('C'. $arttir, $Sonuc['resim']);
		$arttir++;
	}
	
	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="haber-resimler-v7.xlsx"');
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