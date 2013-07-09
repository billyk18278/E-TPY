<?php

class object {};

$CFG = new object;
$CFG->template="templatestyle_metro_teal";
/* administration centre phenotyping database configuration */
$CFG->dbhost = "127.0.0.1:3306";//"127.0.0.1:3307";
$CFG->dbname = "block";
$CFG->dbuser = "";
$CFG->dbpass = "";

$CFG->projectfullname="ΤΙΜΟΛΟΓΙΟ ΠΑΡΟΧΗΣ ΥΠΗΡΕΣΙΩΝ";
$CFG->projectshortname="ΤΠΥ";
$CFG->wwwroot     = "/block/";
$CFG->dirroot     = dirname(__FILE__);
$CFG->libdir      = "./lib";
$CFG->imagedir    = "$CFG->libdir/images";
$CFG->version     = "beta";
$CFG->sessionname = "block";

/* administration database configuration */

//$CFG->merged_enumerated_fields=array(array('ΑΝΣ1','ΑΝΣ2','ΑΝΣ3'),array('ΧΕΙΡ1','ΧΕΙΡ2','ΧΕΙΡ3'),array('ΑΝΕΞ','ΑΝΞ2'));//DO NOT USE KEYS!!! Otherwise when checking relative db data there might be conflict or X-file attidute...
//$CFG->merged_enumerated_fields_explanation=array(array('ΑΝΣ1'=>'Αναισθησιολόγος','ΑΝΣ2'=>'Αναισθησιολόγος2','ΑΝΣ3'=>'Αναισθησιολόγος3'/*,'ΑΝΕΞ','ΑΝΞ2'*/),array('ΧΕΙΡ1'=>'Χειρούργος','ΧΕΙΡ2'=>'Χειρούργος2','ΧΕΙΡ3'=>'Χειρούργος3'),array('ΑΝΕΞ','ΑΝΞ2'));//χρησιμοποιείται ώστε να πάρει από το view τα κατάλληλα πεδία(queries.php)

$GLOBALS['DB_DEBUG']=false; //now also prints $_POST
$GLOBALS['DB_DIE_ON_DEBUG']=false;
$GLOBALS['DB_DIE_ON_FAIL']=true;
//Για την επιλογή στην φόρμα
$CFG->TAX=20;
$CFG->VAT=23;
/* Set locale to greek */
//setlocale(LC_ALL, 'eng');
setlocale(LC_ALL, 'ell');
date_default_timezone_set('Europe/Athens');
//To enable multiple contacts that can be manipulated in list of forms
?>