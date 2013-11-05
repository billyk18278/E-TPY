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

//YOUR EDIT CODE HERE
//garbage collector
     db_query("DELETE FROM outcome WHERE O_P_PIN=0 AND O_TIMESTAMP < now() - interval 30 Minute and O_SESSIONID not like '".session_id()."'",$ab_dbh);
 if (isset($_GET['delete'])){
     db_query("DELETE FROM outcome where O_ID={$_GET['delete']};",$ab_dbh);
 }       
 if (isset($_POST["add"])){
     $pin=$_POST["pin"];
     if ($pin==0){
    
    $sssiontext=" AND `O_SESSIONID` like '".session_id()."'";
}
     if (db_num_rows(db_query("SELECT O_ID FROM outcome where O_P_PIN={$pin} and O_AA='{$_POST["frm_aa"]}' and O_EE_ID='{$_POST["frm_employer"]}'  {$sssiontext}", $ab_dbh))>0){
print "<h4>Προσοχή ΤΙΜΟΛΟΓΙΟ με τον ίδιο αριθμό υπάρχει ήδη καταχωρημένο.</h4>";         
     }else{
         if (!isset($_POST["frm_rebates"])){
             $_POST["frm_rebates"]="false";
         }
     $q="INSERT INTO outcome(`O_VAT`,`O_REBATES`,`O_VALUE`,`O_TYPE`,`O_DETAILS`,`O_P_PIN`,`O_EE_ID`,`O_DATE`,`O_AA`,`O_SESSIONID`) VALUES ({$_POST["frm_vat"]},{$_POST["frm_rebates"]},{$_POST["frm_value"]},'".$_POST["frm_type"]."','".db_real_escape_string($_POST["frm_details"],$ab_dbh)."',{$_POST["pin"]},{$_POST["frm_employer"]},'{$_POST["frm_date"]}','".db_real_escape_string($_POST["frm_aa"],$ab_dbh)."','".session_id()."');";
$res=db_query($q, $ab_dbh);    
     }
    
    
}
// view code here
 $pin=$USER["user"]["P_PIN"];
$name=$USER["user"]["P_NAME"];
$disp="block";
if ($pin==""){
    $pin="0000";
    $name="";
    $sssiontext=" AND `O_SESSIONID` like '".session_id()."'";
    $disp="none";
    
}else{
    $sssiontext="";
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


$inres=db_query("SELECT a.O_ID,O_TYPE,O_AA,EE_NAME,O_VAT,O_VALUE,O_REBATES,O_DETAILS,P_PIN,DATE(O_DATE) as DATE,ROUND(O_VALUE/(1+O_VAT/100),2) as PAY,ROUND(ROUND(O_VALUE/(1+O_VAT/100),2)*(O_VAT/100),2) as FPA,ETOS,TRIMHNO FROM outcome a join person on O_P_PIN=P_PIN join employee on O_EE_ID=EE_ID
join 
(SELECT YEAR(O_DATE) as ETOS,O_ID,CASE WHEN MONTH(NOW())<3 then '1' when MONTH(NOW())>3 and MONTH(NOW())<7  then '2' when MONTH(NOW())>6 and MONTH(NOW())<10 then '3' when MONTH(NOW())>9 then '4' else 'error' end as NOW_TRIMHNO,
CASE WHEN MONTH(O_DATE)<3 then '1' when MONTH(O_DATE)>3 and MONTH(O_DATE)<7  then '2' when MONTH(O_DATE)>6 and MONTH(O_DATE)<10 then '3' when MONTH(O_DATE)>9 then '4' else 'error' end as TRIMHNO FROM outcome) b on a.O_ID=b.O_ID  where P_PIN={$pin} {$period} {$sssiontext} order by O_DATE;", $ab_dbh);

    
print "<h1 data-toggle=\"modal\" href=\"#dmexample\">Έξοδα {$periodtext}</h1>";

print<<<HTML_
 
    <hr>
<table class="table table-striped table-condensed">
<thead>
<tr>
<th >ΤΔΑ</th>
<th>HM/NIA</th>
<th>ΕΠΩΝΥΜΙΑ</th>
<th>ΕΙΔΟΣ</th>
<th>ΠΟΣΟ</th>
<th>Φ.Π.Α. %</th>
<th>Φ.Π.Α. ΕΚΠΙΠΤΕΙ</th>
<th>Φ.Π.Α.</th>
<th>ΚΟΣΤΟΣ</th>
<th></th>
<th></th>
</tr>
</thead>
<tbody>
HTML_;
$withfpapaysum=0;
$fpasum=0;

while($rows=db_fetch_array($inres)){
    if ($rows["O_REBATES"]==1){
        $rebates="<i class=\"icon-check\"></i>";
    }else{
        $rebates="<i class=\"icon-check-empty\"></i>";              
    }

if ($rows['FPA']==0){
 $fpa="-";   

}else{
    if ($rows["O_REBATES"]==1){
    $fpasum+=$rows['FPA'];
    $expenses[$rows["O_TYPE"]]+=$rows['PAY'];
    $expensesthatrebate+=$rows['PAY'];
    }else{
    $expenses[$rows["O_TYPE"]]+=$rows['O_VALUE'];
    }
$fpa=  money_format('%!.2n', $rows['FPA'])."<i class=\"icon-eur\"></i>";
}

$pay=  money_format('%!.2n', $rows['PAY'])."<i class=\"icon-eur\"></i>";
$ival=  money_format('%!.2n', $rows['O_VALUE'])."<i class=\"icon-eur\"></i>";
$type=$CFG->INVTYPES[$rows["O_TYPE"]];
PRINT<<<_HTML

<tr>
<td >{$rows["O_AA"]}</td>
<td><input type=date value="{$rows["DATE"]}" class="input-medium" disabled></td>
<td>{$rows["EE_NAME"]}</td>
<td>{$type} {$rows["O_DETAILS"]}</td>
<td>{$ival}</td>
<td>
_HTML;
if ($rows['O_VAT']==0){
    print '-';
}else{
    print $rows['O_VAT']."%";   
}
print<<<_HTML
</td>   
<td>{$rebates}</td>    
<td>{$fpa}</td>
<td>{$pay}</td>



<td><a href="{$_SERVER["PHP_SELF"]}?pin={$pin}&delete={$rows['O_ID']}" onclick="return confirm('Η εγγραφή αυτή πρόκειται να διαγραφεί!');"> <i class="icon-trash icon-2x"></i></a></td>
<td></td>
</tr>

_HTML;
//   $nextAA=$rows["I_AA"]+1;
}
$textsum='<td colspan=9 class="bg-color-grayDark fg-color-white"><center>';
$totalexpenses=0;
if ($expenses!=array())foreach ($expenses as $k=>$v){
    $totalexpenses+=$v;
$expences[$k]=  money_format('%!.2n', $v)."<i class=\"icon-eur\"></i>";
$textsum.=$k.':'.$expences[$k].". ";
}
$fpasum=  money_format('%!.2n', $fpasum)."<i class=\"icon-eur\"></i>";
$totalexpenses=  money_format('%!.2n', $totalexpenses)."<i class=\"icon-eur\"></i>";
$textsum.=" Συνολικά έξοδα: {$totalexpenses}. Έξοδα με ΦΠΑ που εκπίπτει {$expensesthatrebate} (ΦΠΑ:{$fpasum})</center></td>";
PRINT<<<_HTML
<tr ><td colspan=1  class="bg-color-grayDark fg-color-white">ΣΥΝΟΛA </td>{$textsum}</tr>
<tr valign="middle">
<td ><input form="newTPY" type="text"  name="frm_aa" class="input-mini" value="" ></td>
<td style="white-space: nowrap;"><input form="newTPY" id="frm_date" type="date"  class="input-medium" name="frm_date"></td>
<td><input type=text class="typeahead input-medium" placeholder="Αναζήτηση..." id="searchquestion" onclick="this.value='';" onfocus="this.value='';"><button data-toggle="modal" href="#mexample"><i class="icon-plus"></i></button><input type=hidden name="frm_employer" form="newTPY" id="frm_employer">
</td>
<td><textarea form="newTPY" required rows=3  maxlength="127" style="height:40px" name="frm_details"></textarea>
<select name="frm_type" form="newTPY">

_HTML;
foreach ($CFG->INVTYPES as $invtypes=>$icons){
print   '<option value="'.$invtypes.'">'.$invtypes.'</option>';
}
print<<<_HTML



</td>
<td><input form="newTPY" type="text" required id="frm_value" class="input-mini" name="frm_value" onchange="updateValues();"></td>
<td><select  form="newTPY" id="vat" class="input-mini" name="frm_vat" onchange="updateValues();"><option value="{$CFG->VAT}">{$CFG->VAT}%</option><option value="13">13%</option><option value="0">0%</option></select></td>
<td><input form="newTPY" type=checkbox name="frm_rebates" class="input-mini" value="true"></td>
<td><input type=text disabled id="fpa" class="input-mini"></td>
<td><input type=text disabled id="pay" class="input-small"></td>


<td colspan=3><button form="newTPY" name="add" value=1 class="btn btn-primary"  type="submit" onclick="return validateTPY();">Προσθήκη</button></td>

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
        <label class="control-label">Έτος:</label> <input type="number" id="etos"name="yr" min=2000 max=2020 value={$curyear}><br><br>
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
        
<div id="mexample" class="modal hide fade in" style="display: none; ">
<div class="modal-header">
<a class="close" data-dismiss="modal">x</a>
<h3>Αναζήτηση στοιχείων Επιχειρήσεων:</h3>
</div>
<div class="modal-body">
   
    <form class="form-horizontal">
        <label class="control-label">Α.Φ.Μ.:</label> <input type=text id="afmsearch" placeholder="Α.Φ.Μ για αναζήτηση" onfocus="$('#addbtn').attr('disabled', 'disabled');"><button type="button" class="btn btn-primary" onclick="searchafm();">Αναζήτηση</button><br><br>
        <label class="control-label">Επωνυμία(Τίτλος):</label><input type="text" id="onomasia"><br>
    <label class="control-label">ΔΟΥ:</label><input type="text" id="doyDescr"><br>
    <label class="control-label">Διεύθυνση:</label><input type="text" id="postalAddress"><br>
   <label class="control-label"> Κύρια Δραστηριότητα:</label><input type="text" id="facActivity"><br><br>
   <button type="button" class="btn btn-primary" id="addbtn" onclick="addEmployee();" disabled>Προσθήκη και επιλογή</button>
    </form>
</div>
<div class="modal-footer">
<a href="#" class="m-btn" data-dismiss="modal">Επιστροφή</a>
</div>
</div>
        <script>
    document.getElementById('frm_date').value = new Date().toJSON().slice(0,10);

function updateValues(){
        var valu=$('#frm_value').val();
        if (valu!=parseFloat(valu)){
            alert('Δώστε μια σωστή τιμή για ΠΟΣΟ');
            $('#frm_value').focus();
            return false;
        }
        var vat=$('#vat').val();
        $('#pay').val(Math.round(valu/(1+vat/100)*100)/100);
        $('#fpa').val(Math.round($('#pay').val()*vat)/100);

    }    
function validateTPY(){
   
    if ($('#frm_employer').val()==''){
        alert("Επιλογή προμηθευτή είναι υποχρεωτική");
        
        $('#frm_employer').focus();
    return false;
}
if ($('#frm_date').val()==''){
        alert("Επιλογή ημερομηνίας είναι υποχρεωτική");
        
        $('#frm_date').focus();
        return false;
    }
}

 function searchafm(){
    
 
 $.ajax({
        type: "GET",
        url: "lib/ajax-func.php",
        async: false,
         data:{func:"search", afm: $('#afmsearch').val()}
    }).done(function (data) {
         $.each(data, function(i,item){
             
         if (typeof item['SERVICE ERROR'] !== "undefined" ){
          alert('ή δεν υπάρχει αυτό το ΑΦΜ (πιθανότατα) ή εγώ δεν το βρίσκω (χλωμό)');
         }else{
      $('#onomasia').val(item['onomasia']);
      $('#doyDescr').val(item['doyDescr']);
      $('#postalAddress').val(item['postalAddress']);
      $('#facActivity').val(item['facActivity']);
      $('#addbtn').removeAttr('disabled');
         }
         });
    });
 
     

 };
 
 function addEmployee(){
    
 
 $.ajax({
        type: "GET",
        url: "lib/ajax-func.php",
        async: false,
         data:{func:"addEmployee", EE_AFM: $('#afmsearch').val(),EE_NAME:$('#onomasia').val(),EE_ADDR:$('#postalAddress').val(),EE_DOY:$('#doyDescr').val(),EE_OCCUPATION:$('#facActivity').val()}
    }).done(function (data) {
         $.each(data, function(i,item){
      
        $('#mexample').modal('hide');
        if (typeof item['RESULT'] !== "undefined" ){
          alert(item['RESULT']);
         }
        $('#searchquestion').val($('#onomasia').val());
        $('#frm_employer').val(item["EE_ID"]);
         });
    });
 
     

 };
 $('#searchquestion').typeahead({
    property:"EE_FULLDESC",
    source: function (typeahead, query) {
        return $.get("lib/ajax-func.php", { param1: query,func:"getEmployee" }, function (data) {
            return typeahead.process( data);
        });
    },
     onselect: function (obj) {
    
 $('#frm_employer').val(obj["EE_ID"]);
}
 });
 
    </script>
        
        
    </body>
</html>