<?php
function addnewlogin($pin,$pass,$name,$dbh){
    // add new user to table person
    $q="insert into person (P_PIN, P_PASS,P_NAME)
select '{$pin}','{$pass}','{$_SERVER['REMOTE_ADDR']}' FROM PERSON
where TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP,(SELECT max(P_TIMESTAMP) from person))) > 100 limit 1;";
$GLOBALS['DB_DIE_ON_FAIL']=false;
    $res=  db_query($q,$dbh,"","","","INSERT NEW LOGIN");
    $GLOBALS['DB_DIE_ON_FAIL']=true;
    if ($res){
    if (mysqli_affected_rows($dbh)==1){
        return TRUE;
    }else{
        return "Ένας ένας, μόλις κάποιος έκανε έγγραφή λυπάμαι αλλα πρέπει να προσπαθήσεις αργότερα.";
    }
    }else{
        return "Το PIN είναι πιασμένο..";
    }
}
function setdefault(&$var, $default="") {
/* if $var is undefined, set it to $default.  otherwise leave it alone */

	if (! isset($var)) {
		$var = $default;
	}
}

function nvl(&$var, $default="") {
/* if $var is undefined, return $default, otherwise return $var */

	return isset($var) ? $var : $default;
}

function evl(&$var, $default="") {
/* if $var is empty, return $default, otherwise return $var */

	return empty($var) ? $var : $default;
}
function redirect($url, $message="", $delay=0) {
	/* redirects to a new URL using meta tags */
	echo "<meta http-equiv='Refresh' content='$delay; url=$url'>";
	if (!empty($message)) echo "<br><br><H1>$message</H1>";
	die;
}
function ov(&$var) {
/* returns $var with the HTML characters (like "<", ">", etc.) properly quoted,
 * or if $var is undefined, will return an empty string.  note this function
 * must be called with a variable, for normal strings or functions use o() */

	return o(nvl($var));
}

function pv(&$var) {
/* prints $var with the HTML characters (like "<", ">", etc.) properly quoted,
 * or if $var is undefined, will print an empty string.  note this function
 * must be called with a variable, for normal strings or functions use p() */

	p(nvl($var));
}

function o($var) {
/* returns $var with HTML characters (like "<", ">", etc.) properly quoted,
 * or if $var is empty, will return an empty string. */

	return empty($var) ? "" : htmlSpecialChars(stripslashes($var));
}

function p($var) {
/* prints $var with HTML characters (like "<", ">", etc.) properly quoted,
 * or if $var is empty, will print an empty string. */

	echo o($var);
}
function pr($var) {
/* print_r $var with HTML characters (like "<", ">", etc.) properly quoted,
 * or if $var is empty, will print an empty string. */

	echo "<PRE>".print_r($var)."</pre>";
}

function jstring($var) {
/* returns string that is quoted for javascript */

	return addslashes($var);
}

function db_query_loop($query, $prefix, $suffix, $found_str, $default="") {
/* this is an internal function and normally isn't called by the user.  it
 * loops through the results of a select query $query and prints HTML
 * around it, for use by things like listboxes and radio selections
 *
 * NOTE: this function uses dblib.php */

	$output = "";
	$result = db_query($query);
	while (list($val, $label) = db_fetch_row($result)) {
		if (is_array($default))
			$selected = empty($default[$val]) ? "" : $found_str;
		else
			$selected = $val == $default ? $found_str : "";

		$output .= "$prefix value='$val' $selected>$label$suffix";
	}

	return $output;
}

function db_listbox($query, $default="", $suffix="\n") {
/* generate the <option> statements for a <select> listbox, based on the
 * results of a SELECT query ($query).  any results that match $default
 * are pre-selected, $default can be a string or an array in the case of
 * multi-select listboxes.  $suffix is printed at the end of each <option>
 * statement, and normally is just a line break */

	return db_query_loop($query, "<option", $suffix, "selected", $default);
}
function get_referer() {
/* returns the URL of the HTTP_REFERER, less the querystring portion */

	return strip_querystring(nvl($_SERVER["HTTP_REFERER"]));
}

function me() {
/* returns the name of the current script, without the querystring portion.
 * this function is necessary because PHP_SELF and REQUEST_URI and PATH_INFO
 * return different things depending on a lot of things like your OS, Web
 * server, and the way PHP is compiled (ie. as a CGI, module, ISAPI, etc.) */

	if (isset($_SERVER["REQUEST_URI"])) {
		$me = $_SERVER["REQUEST_URI"];

	} elseif ($_SERVER["PATH_INFO"]) {
		$me = $_SERVER["PATH_INFO"];

	} elseif ($_SERVER["PHP_SELF"]) {
		$me = $_SERVER["PHP_SELF"];
	}

	return strip_querystring($me);
}

function qualified_me() {
/* like me() but returns a fully URL */

	$protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "https://" : "http://";
	$url_prefix = "$protocol$_SERVER[HTTP_HOST]";
	return $url_prefix . me();
}

function match_referer($good_referer = "") {
/* returns true if the referer is the same as the good_referer.  If
 * good_refer is not specified, use qualified_me as the good_referer */

	if ($good_referer == "") { $good_referer = qualified_me(); }
	return $good_referer == get_referer();
}

/* login.php (c) 2000 Ying Zhang (ying@zippydesign.com)
 *
 * TERMS OF USAGE:
 * This file was written and developed by Ying Zhang (ying@zippydesign.com)
 * for educational and demonstration purposes only.  You are hereby granted the
 * rights to use, modify, and redistribute this file as you like.  The only
 * requirement is that you must retain this notice, without modifications, at
 * the top of your source code.  No warranties or guarantees are expressed or
 * implied. DO NOT use this code in a production environment without
 * understanding the limitations and weaknesses pretaining to or caused by the
 * use of these scripts, directly or indirectly. USE AT YOUR OWN RISK!
 */

/******************************************************************************
 * MAIN
 *****************************************************************************/
function is_logged_in() {
/* this function will return true if the user has logged in.  a user is logged
 * in if the $USER["user"] is set (by the login.php page) and also if the
 * remote IP address matches what we saved in the session ($USER["ip"])
 * from login.php -- this is not a robust or secure check by any means, but it
 * will do for now */

	global $USER;

	return isset($USER["user"])
		&& !empty($USER["user"]["P_PIN"])
                && $USER["user"]["P_PIN"]!="0000"
		&& nvl($USER["ip"]) == $_SERVER["REMOTE_ADDR"];
}

function require_login() {
/* this function checks to see if the user is logged in.  if not, it will show
 * the login screen before allowing the user to continue */

	global $CFG, $USER;

	if (! is_logged_in()) {
		$USER["wantsurl"] = qualified_me();
		redirect("index.php","Μόνο για τους εγγεγραμμένους λέμε....",3);
	}
}

function require_priv($priv) {
/* this function checks to see if the user has the privilege $priv.  if not,
 * it will display an Insufficient Privileges page and stop */

	global $USER;


	if ($USER["user"]["priv"] < $priv) {
		$USER["wantsurl"] = qualified_me();
	redirect("insufficient_priviledges.php");
		
	}
}

function has_priv($priv) {
/* returns true if the user has the privilege $priv */

	global $USER;

	return $USER["user"]["priv"] == $priv;
} 
function strip_querystring($url) {
	/* takes a URL and returns it without the querystring portion */

	if ($commapos = strpos($url, '?')) {
		return substr($url, 0, $commapos);
	} else {
		return $url;
	}
}
function verify_login($username, $password,$adminlink) {
/* verify the username and password.  if it is a valid login, return an array
 * with the username, firstname, lastname, and email address of the user */

	if (empty($username) || empty($password)) return false;

	$qid = db_query("SELECT *
	FROM person
where P_PIN like '$username' and P_PASS like '$password'
	",$adminlink);

	return db_fetch_array($qid);
}

function money_format($format, $number) 
{ 
    $regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'. 
              '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/'; 
    if (setlocale(LC_MONETARY, 0) == 'C') { 
        setlocale(LC_MONETARY, ''); 
    } 
    $locale = localeconv(); 
    preg_match_all($regex, $format, $matches, PREG_SET_ORDER); 
    foreach ($matches as $fmatch) { 
        $value = floatval($number); 
        $flags = array( 
            'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ? 
                           $match[1] : ' ', 
            'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0, 
            'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ? 
                           $match[0] : '+', 
            'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0, 
            'isleft'    => preg_match('/\-/', $fmatch[1]) > 0 
        ); 
        $width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0; 
        $left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0; 
        $right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits']; 
        $conversion = $fmatch[5]; 

        $positive = true; 
        if ($value < 0) { 
            $positive = false; 
            $value  *= -1; 
        } 
        $letter = $positive ? 'p' : 'n'; 

        $prefix = $suffix = $cprefix = $csuffix = $signal = ''; 

        $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign']; 
        switch (true) { 
            case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+': 
                $prefix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+': 
                $suffix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+': 
                $cprefix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+': 
                $csuffix = $signal; 
                break; 
            case $flags['usesignal'] == '(': 
            case $locale["{$letter}_sign_posn"] == 0: 
                $prefix = '('; 
                $suffix = ')'; 
                break; 
        } 
        if (!$flags['nosimbol']) { 
            $currency = $cprefix . 
                        ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) . 
                        $csuffix; 
        } else { 
            $currency = ''; 
        } 
        $space  = $locale["{$letter}_sep_by_space"] ? ' ' : ''; 

        $value = number_format($value, $right, $locale['mon_decimal_point'], 
                 $flags['nogroup'] ? '' : $locale['mon_thousands_sep']); 
        $value = @explode($locale['mon_decimal_point'], $value); 

        $n = strlen($prefix) + strlen($currency) + strlen($value[0]); 
        if ($left > 0 && $left > $n) { 
            $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0]; 
        } 
        $value = implode($locale['mon_decimal_point'], $value); 
        if ($locale["{$letter}_cs_precedes"]) { 
            $value = $prefix . $currency . $space . $value . $suffix; 
        } else { 
            $value = $prefix . $value . $space . $currency . $suffix; 
        } 
        if ($width > 0) { 
            $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ? 
                     STR_PAD_RIGHT : STR_PAD_LEFT); 
        } 

        $format = str_replace($fmatch[0], $value, $format); 
    } 
    return $format; 
} 


?>