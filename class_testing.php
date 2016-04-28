<?php
header('Content-Type: application/json');
require_once("core/lib_myclass.php");

$today = date("Y/m/d");
$st = '/ui/simple/'.myClass::JQUERY_UI_CSS;
echo $st. "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, 'http://erp-2/ews/ManexWebService.asmx/GetCustomerByCutOffDate?Acct_Date=03/01/2016&status=');
$result = curl_exec($ch);
curl_close($ch);

$obj = json_decode($result);
echo $result;

$conf = parse_ini_file("conf.ini", true);
print_r($conf);

$qr = $conf["QUERY_LAST_UPDATE"];
print_r($qr);


$myInstance = new MyClass();
$myInstance->lambda->__invoke();

$loo = $myInstance->lambda;
$loo();
$too = $myInstance->beta;
$too('ph');
?>
