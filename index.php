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
    $name="Ανώνυμος Χρήστης";
    $sssiontext=" AND `I_SESSIONID` like '".session_id()."'";
    $disp="none";
    print<<<HTML_
   <pre>Τα δεδομένα που καταχωρούνται σε αυτή την εφαρμογή θα σβηστούν όταν κλείσει ο φυλομετρητής ιστού (γνωστός και ως γουέμπ μπρόουζερ) ή μετά από 30λεπτά.</pre>
HTML_;
    
}else{
    $sssiontext="";
}
$stampr= db_fetch_assoc(db_query("Select P_STAMP from person where P_PIN={$pin}", $ab_dbh));

$inres=db_query("SELECT I_ID,I_AA,E_NAME,I_VAT,I_VALUE,I_TAX,I_DETAILS,P_PIN,DATE(I_DATE) as DATE,ROUND(I_VALUE/(1+I_VAT/100),2) as PAY,ROUND(I_VALUE*(I_TAX/100),2) as TAX,ROUND(I_VALUE*(I_VAT/100),2) as FPA FROM block.income join person on I_P_PIN=P_PIN join employer on I_E_ID=E_ID where P_PIN={$pin} {$sssiontext} order by I_AA;", $ab_dbh);
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
<h1>$name</h1>
    <hr>
<table class="table table-striped table-condensed">
<thead>
<tr>
<th >ΤΠΥ</th>
<th>HM/NIA</th>
<th>ΠΕΛΑΤΗΣ</th>
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


while($rows=db_fetch_array($inres)){
$ival=  money_format('%!.2n', $rows['I_VALUE']);
if ($rows['FPA']==0){
 $fpa="-";   
}else{
$fpa=  money_format('%!.2n', $rows['FPA']);
}
$pay=  money_format('%!.2n', $rows['PAY']);
$tax=  money_format('%!.2n', $rows['TAX']);
PRINT<<<_HTML

<tr>
<td style="width:20px;">{$rows["I_AA"]}</td>
<td><input type=date value="{$rows["DATE"]}" disabled></td>
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

<td><a href="{$_SERVER["PHP_SELF"]}?pin={$pin}&delete={$rows['I_ID']}" onclick="return confirm('Η εγγραφή αυτή πρόκειται να διαγραφεί!');"> <i class="icon-trash"></i></a></td>
<td><a target="_blank" href="lib/TPYprint.php?pin={$pin}&I_ID={$rows['I_ID']}"><i class="icon-print"></i></a></td>
</tr>

_HTML;
   $nextAA=$rows["I_AA"]+1;
}
PRINT<<<_HTML

<tr valign="middle">
<td ><input form="newTPY" type="number"  name="frm_aa" class="input-small" value="{$nextAA}" min=1></td>
<td style="white-space: nowrap;"><input form="newTPY" id="frm_date" type="date"  class="input-medium" name="frm_date"></td>
<td><select form="newTPY" id="frm_employer" name="frm_employer"><option></option>
_HTML;
$res=db_query("Select E_ID,E_NAME from employer;", $ab_dbh);
while ($emrow=  db_fetch_assoc($res)){
    print "<option value=\"{$emrow["E_ID"]}\">{$emrow["E_NAME"]}</option>";
}
print<<<_HTML
</select>
</td>
<td><textarea form="newTPY" required rows=3  maxlength="127" name="frm_details"></textarea></td>
<td><input form="newTPY" type="text" required id="frm_value" class="input-small" name="frm_value" onchange="updateValues();"></td>
<td><select  form="newTPY" id="vat" class="input-small" name="frm_vat" onchange="updateValues();"><option value="{$CFG->VAT}">{$CFG->VAT}%</option><option value="0">Απαλλαγή</option></select></td>
<td><input type=text disabled id="fpa" class="input-small"></td>
<td><input type=text disabled id="pay" class="input-small"></td>

<td><input type=text disabled id="tax" class="input-small"></td>
<td colspan=3><button form="newTPY" name="add" value=1 class="btn btn-primary" type="submit" onclick="return validateTPY();">Προσθήκη</button></td>

</tr>
</TBODY></table>
<form id="newTPY" action="{$_SERVER["PHP_SELF"]}" method="POST">
    <input type="hidden" value="{$pin}" name="pin">
    </form>
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
        $('#pay').val(Math.round(valu/(1+vat/100)*100)/100);
        $('#fpa').val(Math.round(valu*vat/100*100)/100);
        $('#tax').val(Math.round(valu*0.20*100)/100);
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