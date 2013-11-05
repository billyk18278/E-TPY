<?php
header('Content-type: application/json; charset=utf-8');

require_once("../config.php");
require_once('sessionstart.php');
require_once("dblib.php");
require_once("loginlib.php");

$ME = qualified_me();
//require_priv(1);


        $ab_dbh = db_connect($CFG->dbhost, $CFG->dbname, $CFG->dbuser, $CFG->dbpass);

        if (!$ab_dbh) {
            print "The system could not connect to your local database<p>Please contact your system administrator ";          
            print "<p><A HREF=\"logout.php\">Log Out</A>";

            die();
        }

$cc_dbh=$ab_dbh;
//
//  sample.php
//  Legal entities' details from VAT Number using the GSIS Web Service for legal entities
//
//	Copyright 2011 Yannis Lianeris
//	
//	Licensed under the Apache License, Version 2.0 (the "License"); 
//	you may not use this file except in compliance with the License. 
//	You may obtain a copy of the License at
//	
//	http://www.apache.org/licenses/LICENSE-2.0
//	
//	Unless required by applicable law or agreed to in writing, software 
//	distributed under the License is distributed on an "AS IS" BASIS, 
//	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. 
//	See the License for the specific language governing permissions and 
//	limitations under the License.
//
 switch ($_GET["func"]){
       case "search":
if ($_GET["afm"]){
	$inAFM=$_GET["afm"];
        $json=array();
      
        $qid=db_query("SELECT * from employee WHERE EE_AFM={$inAFM};", $ab_dbh,"","",true,"Selecting employee");
        if ($row=db_fetch_assoc($qid)){
           array_push($json,array('onomasia'=>$row["EE_NAME"],'postalAddress'=>$row["EE_ADDR"],'facActivity'=>$row["EE_OCCUPATION"],'doy'=>$row["EE_DOY"],"EE_ID"=>$row["EE_ID"],"RESULT"=>"Υπάρχει ήδη!"));        
           print json_encode($json);
         die();
        }
         
         $res=  simplexml_load_file("http://vatid.eu/check/EL/{$inAFM}");
         if ($res->valid=="true"){
             $n= strval($res->name);
             $a=strval($res->address);
             array_push($json,array('onomasia'=>$n,'postalAddress'=>$a));       
                     print json_encode($json);
         die();
         }
         
        try{
	// set trace = 1 for debugging
	$client = new SoapClient("https://www1.gsis.gr/wsgsis/RgWsBasStoixN/RgWsBasStoixNSoapHttpPort?wsdl", array('trace' => 0));
	// we set the location manually, since the one in the WSDL is wrong
	$client->__setLocation('https://www1.gsis.gr/wsgsis/RgWsBasStoixN/RgWsBasStoixNSoapHttpPort');
	
	$pAfm = $inAFM;
	
	$pBasStoixNRec_out = array('actLongDescr' => '',
								'postalZipCode' => '', 
								'facActivity' => 0,
								'registDate' => '2011-01-01',
								'stopDate' => '2021-01-01',
								'doyDescr' => '',
								'parDescription' => '',
								'deactivationFlag' => 1,
								'postalAddressNo' => '',
								'postalAddress' => '',
								'doy' => '',
								'firmPhone' => '',
								'onomasia' => '',
								'firmFax' => '',
								'afm' => '',
								'commerTitle' => '');
	
	$pCallSeqId_out = 0;
	
	$pErrorRec_out = array('errorDescr' => '', 'errorCode' => '');
	          
	try {
		$result = $client->rgWsBasStoixN($pAfm, $pBasStoixNRec_out, $pCallSeqId_out, $pErrorRec_out);
		$labels = array('actLongDescr' => 'Περιγραφή Κύριας Δραστηριότητας',
						'postalZipCode' => 'Ταχ. κωδικός Αλληλογραφίας',
						'facActivity' => 'Κύρια Δραστηριότητα',
						'registDate' => 'Ημ/νία Έναρξης',
						'stopDate' => 'Ημ/νία Διακοπής',
						'doyDescr' => 'Περιγραφή ΔΟΥ',
						'parDescription' => 'Περιοχή Αλληλογραφίας',
						'deactivationFlag' => 'Ένδειξη Απενεργ. ΑΦΜ',
						'postalAddressNo' => 'Αριθμός Αλληλογραφίας',
						'postalAddress' => 'Οδός Αλληλογραφίας',
						'doy' => 'Κωδικός ΔΟΥ',
						'firmPhone' => 'Τηλέφωνο Επιχείρησης',
						'onomasia' => 'Επωνυμία',
						'firmFax' => 'Fax Επιχείρησης',
						'afm' => 'ΑΦΜ',
						'commerTitle' => 'Τίτλος');
		
		
		if (!$result['pErrorRec_out']->errorCode)
		{
         
			foreach($result['pBasStoixNRec_out'] as $k=>$v)
                            array_push($json,array($k=>$v));
			//	echo $labels[$k].': '.$v.'<br />';
                        
				
		} else {
                      array_push($json,array($result['pErrorRec_out']->errorCode=>$result['pErrorRec_out']->errorDescr));
			
		}
            
	         print json_encode($json);
    die();	
	} catch(SoapFault $fault) {
		// <xmp> tag displays xml output in html
		//echo 'Request: <br /><xmp>', $client->__getLastRequest(), '</xmp><br /><br /> Error Message: <br />', $fault->getMessage();
                      array_push($json,array("SERVICE ERROR"=>$client->__getLastRequest().$fault->getMessage()));
                     print json_encode($json);
    die();
	}
        }catch(SoapFault $fault) {
                      array_push($json,array("SERVICE ERROR"=>$fault->getMessage()));
                     print json_encode($json);
    die();
        }
	
}
break;
case "addEmployee":
     $json=array();
    //add and return ee_id
           $qid=db_query("select EE_ID from employee where  EE_AFM like '".db_real_escape_string($_GET["EE_AFM"],$ab_dbh)."';",$ab_dbh,"","",true,"Selecting employeet");
           $totalrows=db_num_rows($qid);
           if ($totalrows>0){
               $row=  db_fetch_assoc($qid);
$ee_id= $row["EE_ID"];
    array_push($json,array("EE_ID"=>$ee_id,"RESULT"=>"Υπάρχει ήδη!"));
                   print json_encode($json);
    die();               
           }
    db_query("INSERT INTO `employee` (`EE_NAME`, `EE_ADDR`, `EE_DOY`, `EE_AFM`, `EE_OCCUPATION`) VALUES ('".db_real_escape_string($_GET["EE_NAME"],$ab_dbh)."', '".db_real_escape_string($_GET["EE_ADDR"],$ab_dbh)."', '".db_real_escape_string($_GET["EE_DOY"],$ab_dbh)."', '".db_real_escape_string($_GET["EE_AFM"],$ab_dbh)."', '".db_real_escape_string($_GET["EE_OCCUPATION"],$ab_dbh)."');", $ab_dbh,"","",true,"Adding employee");
    $ee_id= db_insert_id($ab_dbh);
    array_push($json,array("EE_ID"=>$ee_id));
                   print json_encode($json);
    die();

break;
case "getEmployee":
    $json=array();
           $qid=db_query("select * from employee where EE_NAME like '%{$_GET["param1"]}%' or EE_AFM like '%{$_GET["param1"]}%';",$ab_dbh,"","",true,"Selecting employee list");
           $totalrows=db_num_rows($qid);
           for ($i=0;$i<min(array($totalrows,8));$i++){
    $row = db_fetch_array($qid);
    array_push($json,array("EE_ID"=>$row["EE_ID"],"EE_FULLDESC"=>$row["EE_NAME"]."(".$row["EE_AFM"].")"));
    }
    print json_encode($json);
    die();
    break;
default:
    break;
    
 }
?>