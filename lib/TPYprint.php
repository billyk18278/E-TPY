<?php
ini_set("display_errors", false);
require('fpdf/fpdf.php');
require('fpdi/fpdi.php'); 
require('mem_image.php');
require_once("../config.php");
require_once("sessionstart.php");
require_once("dblib.php");
require_once("loginlib.php");
include("Numbers/Words.php");

if ($_GET["pin"]!=$USER["user"]["P_PIN"]){
    $pin="0";
}else{
    $pin=$USER["user"]["P_PIN"];
}
if ($_GET['I_ID']!== strval(intval($_GET['I_ID']))){
    redirect("../index.php","Κακή επιλογή διάλεξε ένα τιμολόγιο για εκτύπωση!",2);
}
  $ab_dbh = db_connect($CFG->dbhost, $CFG->dbname, $CFG->dbuser, $CFG->dbpass);

        if (!$ab_dbh) {
            print "The system could not connect to your local database<p>Please contact your system administrator ";          
            print "<p><A HREF=\"logout.php\">Log Out</A>";

            die();
        }
        $iid=  db_real_escape_string($_GET["I_ID"], $ab_dbh);
        $row=  db_fetch_assoc(db_query("SELECT *,DATE(I_DATE) as DATE,Round(I_VALUE/(1+I_VAT/100),2) as PAY,I_VALUE*(I_TAX/100) as TAX,I_VALUE*(I_VAT/100) as FPA FROM block.income join person on I_P_PIN=P_PIN join employer on I_E_ID=E_ID WHERE P_PIN={$pin} and I_ID={$iid};",$ab_dbh));
        
     ini_restore();
$apof=iconv("utf-8", "ISO-8859-7","ΑΘΕΩΡΗΤΟ ΒΑΣΕΙ 1004/4-1-2013 ΑΠΟΦΑΣΗ ΥΠ. ΟΙΚΟΝΟΜΙΚΩΝ");
$pdf = new PDF_COMBO('P','mm','A4');
$pagecount = $pdf->setSourceFile('template.pdf');
$tplidx = $pdf->importPage(1, '/MediaBox');



$pdf->addPage();
$pdf->useTemplate($tplidx);

//echo '<img src="data:image/jpeg;base64,' . base64_encode( $stampr['P_STAMP'] ) . '" />';
$logo = $row['P_STAMP'];
//$logo=  file_get_contents("Barcode-icon.png");
//Output it
$pdf->MemImage($logo, 13, 13,60,30);

$pname=iconv("utf-8", "ISO-8859-7",$row["P_NAME"] );
$idetails="   ".iconv("utf-8", "ISO-8859-7",$row["I_DETAILS"] );
$idetails=preg_replace( "/\r|\n/", "", $idetails );
if ($row["I_VAT"]>0){
$ivat=iconv("utf-8", "ISO-8859-7",$row["I_VAT"] )."%";
  
}else{
    $ivat="";  
}
$iaa=iconv("utf-8", "ISO-8859-7",$row["I_AA"] );
if ($row["I_TAX"]>0){
$itax=iconv("utf-8", "ISO-8859-7",$row["I_TAX"] )."%";
}
$ivalue=  money_format('%!.2n', $row['I_VALUE']);
if ($row['FPA']==0){
 $fpa="";  $apal=iconv("utf-8", "ISO-8859-7","Απαλλαγή Φ.Π.Α Βάσει ΠΟΛ.1128/97." );
}else{
$fpa=  money_format('%!.2n', $row['FPA']);
}
$pay=  money_format('%!.2n', $row['PAY']);
$tax=  money_format('%!.2n', $row['TAX']);
//$fpa=iconv("utf-8", "ISO-8859-7",$row["FPA"] );
//$ivalue=iconv("utf-8", "ISO-8859-7",$row["I_VALUE"] );
//$tax=iconv("utf-8", "ISO-8859-7",$row["TAX"] );
//$pay=iconv("utf-8", "ISO-8859-7",$row["PAY"] );
$date=iconv("utf-8", "ISO-8859-7",date("d/m/y", strtotime($row["I_DATE"])));
$olog=iconv("utf-8", "ISO-8859-7",Numbers_Words::toCurrency($row["I_VALUE"], "el_GR", 'EUR') );
if (strlen($olog)>78){
    $olog="";
}
$ename=iconv("utf-8", "ISO-8859-7",$row["E_NAME"] );
$eafm=iconv("utf-8", "ISO-8859-7",$row["E_AFM"] );
$edoy=iconv("utf-8", "ISO-8859-7",$row["E_DOY"] );
$eaddr=iconv("utf-8", "ISO-8859-7",$row["E_ADDR"] );
$eoccu=iconv("utf-8", "ISO-8859-7",$row["E_OCCUPATION"] );
//$pdf->Image('Barcode-icon.png',38,10,112,14);
	// Arial bold 15
$pdf->AddFont('SegoeScript','','2b29d7e0aa48044144e8efd97f0147d1_segoesc.php');
$pdf->AddFont('SegoeUI-Light','','1e768bbdf87762ebf73871f712468317_segoeuil.php');
$pdf->AddFont('SegoeUI-Semilight','','31a5477d7f020de55fe95237020d4a11_segoeuisl.php');
	$pdf->SetFont('SegoeUI-Semilight','',15);
	
	   $pdf->SetY(37);
         $pdf->SetX(110);
         $pdf->Cell(79,8, $ivalue ,0,0,'R');
	// Title
        $pdf->SetFont('SegoeUI-Semilight','',12);
        //$pdf->SetY(31);
        $pdf->Text(115, 30.5, $date);
        //$pdf->Cell(115,8,$date  ,0,0,'C');
	//$pdf->Cell(150,8, iconv("utf-8","ISO-8859-7","ΤΙΜΟΛΟΓΙΟ ΠΑΡΟΧΗΣ ΥΠΗΡΕΣΙΩΝ") ,0,0,'C');
        $pdf->Text(177, 30.5, $iaa);
      
         
          $pdf->SetFont('SegoeScript','',10);
         $pdf->SetY(54);
         $pdf->SetX(39);
         $pdf->Cell(150,8, $ename ,0,0,'L');
         
         $pdf->SetY(64);
         $pdf->SetX(39);
         $pdf->Cell(64,8, $eoccu ,0,0,'L');
         $pdf->SetX(114);
         $pdf->Cell(32,8, $eafm ,0,0,'C');
           $pdf->SetX(156);
         $pdf->Cell(32,8, $edoy ,0,0,'L');
         
         $pdf->SetY(74);
         $pdf->SetX(39);
         $pdf->Cell(125,8, $eaddr ,0,0,'C');
         
         $pdf->SetY(84);
         $pdf->SetX(39);
         $pdf->Cell(150,8, $olog ,0,0,'C');
         
         $pdf->SetY(93);
         $pdf->SetX(18);
        $pdf->MultiCell(175, 10, $idetails);
               $pdf->SetFont('SegoeUI-Semilight','',12); 
         $pdf->SetY(103);
         $pdf->SetX(138);
         $pdf->Cell(50,8, $pay ,0,0,'R');
         $pdf->SetY(108.35);
         $pdf->SetX(138);
         $pdf->Cell(50,8, $tax ,0,0,'R');
         
         $pdf->SetY(119.0);
         $pdf->SetX(138);
         $pdf->Cell(50,8, $fpa ,0,0,'R');
         
          $pdf->SetY(124.4);
         $pdf->SetX(138);
         $pdf->Cell(50,8, $ivalue ,0,0,'R');
         
        $pdf->SetFont('SegoeUI-Semilight','',9); 
        $pdf->Text(130, 113,$itax);
        $pdf->Text(130, 124,$ivat);
        $pdf->SetFont('SegoeScript','',9);
         $pdf->SetY(113);
         $pdf->SetX(68);
        $pdf->MultiCell(43, 5, $apal);
               
           $pdf->SetFont('SegoeUI-Light','',7); 
                   $pdf->Text(25, 137.5,$apof);

                   
                   
                   //DEYTERO
                   $offset=141;
                   $pdf->SetFont('SegoeUI-Semilight','',15);
	
	   $pdf->SetY(37+$offset);
         $pdf->SetX(110);
         $pdf->Cell(79,8, $ivalue ,0,0,'R');
	// Title
        $pdf->SetFont('SegoeUI-Semilight','',12);
        //$pdf->SetY(31);
        $pdf->Text(115, 30.5+$offset, $date);
        //$pdf->Cell(115,8,$date  ,0,0,'C');
	//$pdf->Cell(150,8, iconv("utf-8","ISO-8859-7","ΤΙΜΟΛΟΓΙΟ ΠΑΡΟΧΗΣ ΥΠΗΡΕΣΙΩΝ") ,0,0,'C');
        $pdf->Text(177, 30.5+$offset, $iaa);
      
         
          $pdf->SetFont('SegoeScript','',10);
         $pdf->SetY(54+$offset);
         $pdf->SetX(39);
         $pdf->Cell(150,8, $ename ,0,0,'L');
         
         $pdf->SetY(64+$offset);
         $pdf->SetX(39);
         $pdf->Cell(64,8, $eoccu ,0,0,'L');
         $pdf->SetX(114);
         $pdf->Cell(32,8, $eafm ,0,0,'C');
           $pdf->SetX(156);
         $pdf->Cell(32,8, $edoy ,0,0,'L');
         
         $pdf->SetY(74+$offset);
         $pdf->SetX(39);
         $pdf->Cell(125,8, $eaddr ,0,0,'C');
         
         $pdf->SetY(84+$offset);
         $pdf->SetX(39);
         $pdf->Cell(150,8, $olog ,0,0,'C');
         
         $pdf->SetY(93+$offset);
         $pdf->SetX(18);
        $pdf->MultiCell(175, 10, $idetails);
               $pdf->SetFont('SegoeUI-Semilight','',12); 
         $pdf->SetY(103+$offset);
         $pdf->SetX(138);
         $pdf->Cell(50,8, $pay ,0,0,'R');
         $pdf->SetY(108.35+$offset);
         $pdf->SetX(138);
         $pdf->Cell(50,8, $tax ,0,0,'R');
         
         $pdf->SetY(119.0+$offset);
         $pdf->SetX(138);
         $pdf->Cell(50,8, $fpa ,0,0,'R');
         
          $pdf->SetY(124.4+$offset);
         $pdf->SetX(138);
         $pdf->Cell(50,8, $ivalue ,0,0,'R');
         
        $pdf->SetFont('SegoeUI-Semilight','',9); 
        $pdf->Text(130, 113+$offset,$itax);
        $pdf->Text(130, 124+$offset,$ivat);
        $pdf->SetFont('SegoeScript','',9);
         $pdf->SetY(113+$offset);
         $pdf->SetX(68);
        $pdf->MultiCell(43, 5, $apal);
               
           $pdf->SetFont('SegoeUI-Light','',7); 
                   $pdf->Text(25, 137.5+$offset,$apof);
$pdf->Output();


?>