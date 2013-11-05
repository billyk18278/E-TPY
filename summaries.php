<?php
require_once("config.php");
require_once($CFG->libdir ."/sessionstart.php");
require_once($CFG->libdir . "/dblib.php");
require_once($CFG->libdir . "/loginlib.php");
//test!
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>



        <?php

    



        $ab_dbh = db_connect($CFG->dbhost, $CFG->dbname, $CFG->dbuser, $CFG->dbpass);

        if (!$ab_dbh) {
            print "The system could not connect to your local database<p>Please contact your system administrator ";          
            print "<p><A HREF=\"logout.php\">Log Out</A>";

            die();
        }
include("./template/header.php");
include("Numbers/Words.php");
require_login();
//YOUR EDIT CODE HERE
//garbage collector
//period select
$period="";
$curyear=date("Y");
if ($_GET["ola"]==1){
    $periodtext="συνολικά";
}else{
    
if ($_GET["yr"]>2000 && $_GET["yr"]<2050){
    if (round($_GET["tr"])>0 && round($_GET["tr"])<5){
    $period=" AND ETOS={$_GET["yr"]} "."AND TRIMHNO=".round($_GET["tr"]);
    $periodtext="για ".round($_GET["tr"])."ο τρίμηνο του {$_GET["yr"]}";
    }else{
        $period=" AND ETOS={$_GET["yr"]} ";
        $periodtext="για ολόκληρο το {$_GET["yr"]}";
    }
}else if (round($_GET["tr"])>0 && round($_GET["tr"])<5){

    $period=" AND ETOS={$curyear} AND TRIMHNO=".round($_GET["tr"]);
    $periodtext="για ".round($_GET["tr"])."ο τρίμηνο του ".$curyear;
}else{
    $period=" AND ETOS={$curyear} AND TRIMHNO=NOW_TRIMHNO";
    $periodtext="για το τρέχον τρίμηνο";
}

}

//$period="AND TRIMHNO=NOW_TRIMHNO ";
// view code here
 $pin=$USER["user"]["P_PIN"];
$name=$USER["user"]["P_NAME"];
$disp="block";
 

    $sssiontext="";

$inres=db_query("SELECT a.I_ID,I_AA,E_AFM,E_NAME,E_ADDR,E_DOY,E_OCCUPATION,I_VAT,I_VALUE,I_TAX,I_DETAILS,P_PIN,DATE(I_DATE) as DATE,ROUND(I_VALUE/(1+I_VAT/100),2) as PAY,
ROUND(ROUND(I_VALUE/(1+I_VAT/100),2)*(I_TAX/100),2) as TAX,ROUND(ROUND(I_VALUE/(1+I_VAT/100),2)*(I_VAT/100),2) as FPA,
ETOS,TRIMHNO 
FROM income a join person on a.I_P_PIN=P_PIN join employer on a.I_E_ID=E_ID 
join 
(SELECT YEAR(I_DATE) as ETOS,I_ID,CASE WHEN MONTH(NOW())<3 then '1' when MONTH(NOW())>3 and MONTH(NOW())<7  then '2' when MONTH(NOW())>6 and MONTH(NOW())<10 then '3' when MONTH(NOW())>9 then '4' else 'error' end as NOW_TRIMHNO,CASE WHEN MONTH(I_DATE)<3 then '1' when MONTH(I_DATE)>3 and MONTH(I_DATE)<7  then '2' when MONTH(I_DATE)>6 and MONTH(I_DATE)<10 then '3' when MONTH(I_DATE)>9 then '4' else 'error' end as TRIMHNO FROM income) b on a.I_ID=b.I_ID where P_PIN={$pin} {$period} {$sssiontext} order by I_AA;", $ab_dbh);    

$outres=db_query("SELECT a.O_ID,O_TYPE,O_AA,EE_NAME,EE_AFM,EE_OCCUPATION,O_VAT,O_VALUE,O_REBATES,O_DETAILS,P_PIN,DATE(O_DATE) as DATE,ROUND(O_VALUE/(1+O_VAT/100),2) as PAY,ROUND(ROUND(O_VALUE/(1+O_VAT/100),2)*(O_VAT/100),2) as FPA,ETOS,TRIMHNO FROM outcome a join person on O_P_PIN=P_PIN join employee on O_EE_ID=EE_ID
join 
(SELECT YEAR(O_DATE) as ETOS,O_ID,CASE WHEN MONTH(NOW())<3 then '1' when MONTH(NOW())>3 and MONTH(NOW())<7  then '2' when MONTH(NOW())>6 and MONTH(NOW())<10 then '3' when MONTH(NOW())>9 then '4' else 'error' end as NOW_TRIMHNO,
CASE WHEN MONTH(O_DATE)<3 then '1' when MONTH(O_DATE)>3 and MONTH(O_DATE)<7  then '2' when MONTH(O_DATE)>6 and MONTH(O_DATE)<10 then '3' when MONTH(O_DATE)>9 then '4' else 'error' end as TRIMHNO FROM outcome) b on a.O_ID=b.O_ID  where P_PIN={$pin} {$period} {$sssiontext} order by O_DATE;", $ab_dbh);

print "<h1 data-toggle=\"modal\" href=\"#dmexample\">Σύνολα {$periodtext}</h1>";

print   "<hr>";


$fpasum=0;
$paysum=0;
$taxsum=0;
$nofpapaysum=0;
while($rows=db_fetch_array($inres)){
$ival=  money_format('%!.2n', $rows['I_VALUE'])."<i class=\"icon-eur\"></i>";
if ($rows['FPA']==0){
 $fpa="-";  $nofpapaysum+=$rows['PAY'];
}else{
    $fpasum+=$rows['FPA'];
$fpa=  money_format('%!.2n', $rows['FPA'])."<i class=\"icon-eur\"></i>";
}
$paysum+=$rows['PAY'];

$taxsum+=$rows['TAX'];
//$perAFMdetails[$rows["E_AFM"]]=$rows['E_NAME'].", ".$rows['E_OCCUPATION'].", ".$rows['E_ADDR'].", ".$rows['E_DOY'];
$perAFMdetails[$rows["E_AFM"]]=$rows['E_NAME'].", ".$rows['E_OCCUPATION'];
$perAFM[$rows["E_AFM"]]+=$rows['PAY'];
$perAFMinvs[$rows["E_AFM"]]++;


}
$withfpapaysum=money_format('%!.2n', $paysum-$nofpapaysum)."<i class=\"icon-eur\"></i>";
$paysum=  money_format('%!.2n', $paysum)."<i class=\"icon-eur\"></i>";
$taxsum=  money_format('%!.2n', $taxsum)."<i class=\"icon-eur\"></i>";
$fpasum=  money_format('%!.2n', $fpasum)."<i class=\"icon-eur\"></i>";
$nofpapaysum=money_format('%!.2n', $nofpapaysum)."<i class=\"icon-eur\"></i>";

//SUMMARY

//INPUT

if (is_array($perAFM)){
PRINT<<<_HTML
<h2>Έσοδα ανά πελάτη</h2>
    <table class="table table-striped table-condensed">
    <tbody>
_HTML;
foreach ($perAFM as $afm=>$amoibh){
   echo "<tr><td>".$afm."</td><td>".$perAFMdetails[$afm]."</td><td>".money_format('%!.2n', $amoibh)."<i class=\"icon-eur\"></i>"."</td><td>".$perAFMinvs[$afm]."</td></tr>";
}
print<<<_HTML
</tbody>
    <thead>
    <td>ΑΦΜ</td><td>Πληροφορίες</td><td>Ποσό</td><td>Αρ. Τιμολογίων</td>
    </thead>
</table>
_HTML;
}
//outcome------------------------------------------------------------------------
$owithfpapaysum=0;
$ofpasum=0;

while($rows=db_fetch_array($outres)){
    
$expperAFMinvs[$rows["EE_AFM"]]++;
$perAFMdetails[$rows["EE_AFM"]]=$rows['EE_NAME'].", ".$rows['EE_OCCUPATION'];
if ($rows['FPA']==0){
  $expenses[$rows["O_TYPE"]]+=$rows['O_VALUE'];
    $expperAFM[$rows["EE_AFM"]]+=$rows['O_VALUE'];

}else{
    if ($rows["O_REBATES"]==1){
    $ofpasum+=$rows['FPA'];
    $expenses[$rows["O_TYPE"]]+=$rows['PAY'];
    $expensesthatrebate+=$rows['PAY'];
    $expperAFM[$rows["EE_AFM"]]+=$rows['PAY'];
    }else{
    $expenses[$rows["O_TYPE"]]+=$rows['O_VALUE'];
    $expperAFM[$rows["EE_AFM"]]+=$rows['O_VALUE'];
    }

}

}
$textsum='';
$totalexpenses=0;
if ($expenses!=array())foreach ($expenses as $k=>$v){
    $totalexpenses+=$v;
$expences[$k]=  money_format('%!.2n', $v)."<i class=\"icon-eur\"></i>";
$textsum.="<p>".$k.':'.$expences[$k]."</p>";
}
$ofpasum=  money_format('%!.2n', $ofpasum)."<i class=\"icon-eur\"></i>";
$totalexpenses=  money_format('%!.2n', $totalexpenses)."<i class=\"icon-eur\"></i>";
$textsum.="<p> Συνολικά έξοδα: {$totalexpenses}</p><p> Έξοδα με ΦΠΑ που εκπίπτει {$expensesthatrebate} (ΦΠΑ:{$ofpasum})</p>";

if (is_array($expperAFM)){
PRINT<<<_HTML
<h2>Έξοδα ανά πελάτη</h2>
    <table class="table table-striped table-condensed">
    <tbody>
_HTML;

foreach ($expperAFM as $afm=>$amoibh){
   echo "<tr><td>".$afm."</td><td>".$perAFMdetails[$afm]."</td><td>".money_format('%!.2n', $amoibh)."<i class=\"icon-eur\"></i>"."</td><td>".$expperAFMinvs[$afm]."</td></tr>";
}
print<<<_HTML
</tbody>
    <thead>
    <td>ΑΦΜ</td><td>Πληροφορίες</td><td>Ποσό</td><td>Αρ. Τιμολογίων</td>
    </thead>
</table>
_HTML;
}

PRINT<<<_HTML
<table style="width:100%"><tr><td>
<p>Έσοδα χωρίς ΦΠΑ:{$nofpapaysum}</p><p>Έσοδα με ΦΠΑ:{$withfpapaysum} Φ.Π.Α:{$fpasum}<p>
<p>Σύνολο ακαθάριστων εσόδων: {$paysum}<p><p>Σύνολο παρακρατιθέντος φόρου 20%: {$taxsum}<p>
    </td><td>
{$textsum} </td></tr></table>
_HTML;

print<<<_HTML
<form id="newTPY" action="{$_SERVER["PHP_SELF"]}" method="POST">
    <input type="hidden" value="{$pin}" name="pin">
    </form>
    
<div id="dmexample" class="modal hide fade in" style="display: none; ">
<div class="modal-header">
<a class="close" data-dismiss="modal">x</a>
<h3>Επιλογή περιόδου προβολής στοιχείων:</h3>
</div>
<div class="modal-body">
   
    <form class="form-horizontal" method="GET" action={$_SERVER["PHP_SELF"]}>
        <label>Επιλέξτε έτος (προαιρετικά και τρίμηνο)</label>
        <label class="control-label">Έτος:</label> <input type="number" id="etos"name="yr" min=2000 max=2050 value={$curyear}><br><br>
        <label class="control-label">Τρίμηνο:</label><input type="number" min=1 max=4 id="tr" name="tr"><br><br>
     
   <button type="submit" class="btn btn-primary" id="periodchangebtn" >Προβολή για την περίοδο</button>
   </form>
    <label>ή μία από τις δύο επιλογές</label>
    <form class="form-horizontal" method="GET" action={$_SERVER["PHP_SELF"]}>
   <button type="submit" class="btn btn-primary" id="periodchangebtn" >Τρέχον τρίμηνο</button>
   </form>
   <form class="form-horizontal" method="GET" action={$_SERVER["PHP_SELF"]}>
       <input type=hidden value=1 name="ola">
   <button type="submit" class="btn btn-primary" id="periodchangebtn" >Όλα</button>
    </form>
</div>
<div class="modal-footer">
<a href="#" class="m-btn" data-dismiss="modal">Επιστροφή</a>
</div>
</div>
_HTML;



        include("./template/footer.php");
        ?>
   
    </body>
</html>