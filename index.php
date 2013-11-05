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

//YOUR EDIT CODE HERE
//garbage collector
     db_query("DELETE FROM income WHERE I_P_PIN=0 AND I_TIMESTAMP < now() - interval 30 Minute and I_SESSIONID not like '".session_id()."'",$ab_dbh);
 if (isset($_GET['delete'])){
     db_query("DELETE FROM income where I_ID={$_GET['delete']};",$ab_dbh);
 }       
 if (isset($_POST["add"])){
     $pin=$_POST["pin"];
     if ($pin==0){
    
    $sssiontext=" AND `I_SESSIONID` like '".session_id()."'";
}
     if (db_num_rows(db_query("SELECT I_ID FROM income where I_P_PIN={$pin} and I_AA={$_POST["frm_aa"]} {$sssiontext}", $ab_dbh))>0){
print "<h4>Προσοχή ΤΠΥ με τον ίδιο αριθμό υπάρχει ήδη καταχωρημένο.</h4>";         
     }else{
     $q="INSERT INTO income(`I_VAT`,`I_TAX`,`I_VALUE`,`I_DETAILS`,`I_P_PIN`,`I_E_ID`,`I_DATE`,`I_AA`,`I_SESSIONID`) VALUES ({$_POST["frm_vat"]},{$CFG->TAX},{$_POST["frm_value"]},'".db_real_escape_string($_POST["frm_details"],$ab_dbh)."',{$_POST["pin"]},{$_POST["frm_employer"]},'{$_POST["frm_date"]}',{$_POST["frm_aa"]},'".session_id()."');";
$res=db_query($q, $ab_dbh);    
     }
    
    
}
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
if (($_POST["addstamp"]==1)){
     
     $fieldname="stamp";
//     echo "<h3>".$_FILES[$fieldname]['size']."</h3>";
 			if ($_FILES[$fieldname]['name']!="" && $_FILES[$fieldname]['size']<1000000 )
													{
					

								$fp=fopen($_FILES[$fieldname]['tmp_name'],'rb');
								$fir=fread($fp,$_FILES[$fieldname]['size']);
								$ext=strtok($_FILES[$fieldname]['name'],".");
								while ($ext=strtok(".")){
									$ftype=$ext;
								}
                                                                if ($ftype=='png' || $ftype=='jpg'){
				$pin=$USER["user"]["P_PIN"];
                                                                    if (!isset($USER["user"]["P_PIN"])){$pin="0";}	
			$query="UPDATE  Person SET `P_STAMP`='".addslashes($fir)."' where P_PIN={$pin};";

							$qeid=db_query($query,$ab_dbh,"","","");
$uploadmessage="";
						}else{
                                                    $uploadmessage="Λάθος τύπος αρχείου";
                                                }
                                                                                                        }else{
                                                    $uploadmessage="Πρόβλημα με την εικόνα.";
                                                }
    
}
// view code here
 $pin=$USER["user"]["P_PIN"];
$name=$USER["user"]["P_NAME"];
$disp="block";
if ($pin==""){
    $pin="0000";
    $name="";
    $sssiontext=" AND `I_SESSIONID` like '".session_id()."'";
    $disp="none";
    print<<<HTML_
   <pre>Τι κάνω με το E-TPY;
Αφού καταχωρίσω το ποσό (με . για δεκαδικά) την αιτιολογία και αν έχει ΦΠΑ, μπορώ να τυπώσω (πατώντας το εικονίδιο στο τέλος της γραμμής) το συγκεκριμένο τιμολόγιο (π.χ το 3) 
με καθαρά γραμμένα τα ποσά επίσης το ποσό ολογράφως και την αιτιολογία και να βάλω μετά σφραγίδα και υπογραφή
στο ένα από τα δύο που βγαίνουν στην σελίδα και να το δώσω.
Αν εχω μπλοκάκι βιβλιοπωλίου (και δεν το εβαλα ακόμα στην ανακύκλωση) μπορώ να συρράψω πάνω το αντίγραφο αν 
όχι το κρατάω απλά για το αρχείο μου (ή τον λογιστή μου).

Τα δεδομένα που καταχωρούνται σε αυτή την εφαρμογή θα σβηστούν όταν κλείσει ο φυλομετρητής ιστού (γνωστός και ως γουέμπ μπρόουζερ) ή μετά από 30λεπτά.
(εκτός αν είστε διαπιστευμένος χρηστης)</pre>
HTML_;
    
}else{
    $sssiontext="";
}
$stampr= db_fetch_assoc(db_query("Select P_STAMP from person where P_PIN={$pin}", $ab_dbh));
$inres=db_query("SELECT a.I_ID,I_AA,E_NAME,I_VAT,I_VALUE,I_TAX,I_DETAILS,P_PIN,DATE(I_DATE) as DATE,ROUND(I_VALUE/(1+I_VAT/100),2) as PAY,
ROUND(ROUND(I_VALUE/(1+I_VAT/100),2)*(I_TAX/100),2) as TAX,ROUND(ROUND(I_VALUE/(1+I_VAT/100),2)*(I_VAT/100),2) as FPA,
ETOS,TRIMHNO 
FROM income a join person on a.I_P_PIN=P_PIN join employer on a.I_E_ID=E_ID 
join 
(SELECT YEAR(I_DATE) as ETOS,I_ID,CASE WHEN MONTH(NOW())<3 then '1' when MONTH(NOW())>3 and MONTH(NOW())<7  then '2' when MONTH(NOW())>6 and MONTH(NOW())<10 then '3' when MONTH(NOW())>9 then '4' else 'error' end as NOW_TRIMHNO,CASE WHEN MONTH(I_DATE)<3 then '1' when MONTH(I_DATE)>3 and MONTH(I_DATE)<7  then '2' when MONTH(I_DATE)>6 and MONTH(I_DATE)<10 then '3' when MONTH(I_DATE)>9 then '4' else 'error' end as TRIMHNO FROM income) b on a.I_ID=b.I_ID where P_PIN={$pin} {$period} {$sssiontext} order by I_AA;", $ab_dbh);
print<<<HTML_

    <div id="stamprel" style="display:{$disp};">
HTML_;
    if (sizeof($stampr['P_STAMP'])>0) echo '<img src="data:image/jpeg;base64,' . base64_encode( $stampr['P_STAMP'] ) . '" style="height:100px;" />';
    print<<<HTML_
    <div class="text-error"> {$uploadmessage}</div>
    <form class="form-inline" action="{$_SERVER["PHP_SELF"]}" method="POST" id="frm_stamp" enctype="multipart/form-data">
    <div id="file" class="btn btn-primary small">Επιλέξτε αρχείο σφραγίδας</div>
    <input type="file"  name="stamp">
    <input type="hidden" name="addstamp" value="1">
    </form>
</div>
HTML_;
    
print "<h1 data-toggle=\"modal\" href=\"#dmexample\">Έσοδα {$periodtext}</h1>";

print<<<HTML_
    <hr>
<table class="table table-striped table-condensed">
<thead>
<tr>
<th >ΤΠΥ</th>
<th>HM/NIA</th>
<th>ΕΡΓΟΔΟΤΗΣ</th>
<th>ΑΙΤΙΟΛΟΓΙΑ</th>
<th>ΠΟΣΟ</th>
<th>Φ.Π.Α. %</th>
<th>Φ.Π.Α.</th>
<th>ΑΜΟΙΒΗ</th>
<th>ΦΟΡΟΣ</th>
<th></th>
<th></th>
</tr>
</thead>
<tbody>
HTML_;

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
$pay=  money_format('%!.2n', $rows['PAY'])."<i class=\"icon-eur\"></i>";
$taxsum+=$rows['TAX'];
$tax=  money_format('%!.2n', $rows['TAX'])."<i class=\"icon-eur\"></i>";
PRINT<<<_HTML

<tr>
<td >{$rows["I_AA"]}</td>
<td><input type=date value="{$rows["DATE"]}" class="input-medium" disabled></td>
<td>{$rows["E_NAME"]}</td>
<td>{$rows["I_DETAILS"]}</td>
<td>{$ival}</td>
<td>
_HTML;
if ($rows['I_VAT']==0){
    print '-';
}else{
    print $rows['I_VAT']."%";
    //print Numbers_Words::toCurrency($rows['FPA'], "el_GR", 'EUR');
}

print<<<_HTML
</td>   
    <td>{$fpa}</td>
<td>{$pay}</td>

<td>{$tax}</td>

<td><a href="{$_SERVER["PHP_SELF"]}?pin={$pin}&delete={$rows['I_ID']}" onclick="return confirm('Η εγγραφή αυτή πρόκειται να διαγραφεί!');"> <i class="icon-trash icon-2x"></i></a></td>
<td><a target="_blank" href="lib/TPYprint.php?pin={$pin}&I_ID={$rows['I_ID']}"><i class="icon-print icon-2x"></i></a></td>
</tr>

_HTML;
   $nextAA=$rows["I_AA"]+1;
}
$withfpapaysum=money_format('%!.2n', $paysum-$nofpapaysum)."<i class=\"icon-eur\"></i>";
$paysum=  money_format('%!.2n', $paysum)."<i class=\"icon-eur\"></i>";
$taxsum=  money_format('%!.2n', $taxsum)."<i class=\"icon-eur\"></i>";
$fpasum=  money_format('%!.2n', $fpasum)."<i class=\"icon-eur\"></i>";
$nofpapaysum=money_format('%!.2n', $nofpapaysum)."<i class=\"icon-eur\"></i>";

//SUMMARY

//INPUT
PRINT<<<_HTML
<tr  ><td class="bg-color-grayDark fg-color-white">ΣΥΝΟΛA</td><td colspan="2" class="bg-color-grayDark fg-color-white">ΣΥΝΟΛΟ ΕΣΟΔΩΝ ΧΩΡΙΣ ΦΠΑ:{$nofpapaysum}</td><td colspan="3" class="bg-color-grayDark fg-color-white">ΣΥΝΟΛΟ ΕΣΟΔΩΝ ME ΦΠΑ:{$withfpapaysum}</td><td class="bg-color-grayDark fg-color-white">{$fpasum}</td><td class="bg-color-grayDark fg-color-white">{$paysum}</td><td class="bg-color-grayDark fg-color-white">{$taxsum}</td></tr>
<tr valign="middle">
<td ><input form="newTPY" type="number"  name="frm_aa" class="input-mini" value="{$nextAA}" min=1></td>
<td style="white-space: nowrap;"><input form="newTPY" id="frm_date" type="date"  class="input-medium" name="frm_date"></td>
<td><select class="input-medium" form="newTPY" id="frm_employer" name="frm_employer"><option></option>
_HTML;
$res=db_query("Select E_ID,E_NAME from employer;", $ab_dbh);
while ($emrow=  db_fetch_assoc($res)){
    print "<option value=\"{$emrow["E_ID"]}\">{$emrow["E_NAME"]}</option>";
}
print<<<_HTML
</select>
</td>
<td><textarea form="newTPY" required rows=3  maxlength="127" name="frm_details"></textarea></td>
<td><input form="newTPY" type="text" required id="frm_value" class="input-mini" name="frm_value" onchange="updateValues();"></td>
<td><select  form="newTPY" id="vat" class="input-mini" name="frm_vat" onchange="updateValues();"><option value="{$CFG->VAT}">{$CFG->VAT}%</option><option value="0">Απαλλαγή</option></select></td>
<td><input type=text disabled id="fpa" class="input-mini"></td>
<td><input type=text disabled id="pay" class="input-small"></td>

<td><input type=text disabled id="tax" class="input-small"></td>
<td colspan=3><button form="newTPY" name="add" value=1 class="btn btn-primary" type="submit" onclick="return validateTPY();">Προσθήκη</button></td>

</tr>
</TBODY></table>
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
        <script>
    document.getElementById('frm_date').value = new Date().toJSON().slice(0,10);
       var wrapper = $('<div/>').css({height:0,width:0,'overflow':'hidden'});
var fileInput = $(':file').wrap(wrapper);

fileInput.change(function(){
    $this = $(this);
    //$('#file').text($this.val());
    $('#frm_stamp').submit();
})

$('#file').click(function(){
    fileInput.click();
}).show();
    function updateValues(){
        var valu=$('#frm_value').val();
        if (valu!=parseFloat(valu)){
            alert('Δώστε μια σωστή τιμή για ΠΟΣΟ');
            $('#frm_value').focus();
            return false;
        }
        var vat=$('#vat').val();
        var p=Math.round(valu/(1+vat/100)*100)/100;
        $('#pay').val(p);
        $('#fpa').val(Math.round(p*vat/100*100)/100);
        $('#tax').val(Math.round(p*0.20*100)/100);
    }    
function validateTPY(){
   
    if ($('#frm_employer').val()==''){
        alert("Επιλογή εργοδότη είναι υποχρεωτική");
        
        $('#frm_employer').focus();
    return false;
}
if ($('#frm_date').val()==''){
        alert("Επιλογή ημερομηνίας είναι υποχρεωτική");
        
        $('#frm_date').focus();
        return false;
    }
}
    </script>
    </body>
</html>