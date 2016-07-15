<?php
header('Content-Type: application/json');
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
<<<<<<< HEAD:model/xt_test_acquire_date.php
curl_setopt($ch, CURLOPT_URL, 'http://erp-2/ews/ManexWebService.asmx/GetCustomerByCutOffDate?Acct_Date=03/01/2016&status=');
=======
curl_setopt($ch, CURLOPT_URL, 'http://erp-2/ews/ManexWebService.asmx/GetCustomerByCutOffDate?Acct_Date=06/01/2016&status=');
>>>>>>> a8847e7... r40 final polish:model/xt_test_acquire_date.php
$result = curl_exec($ch);
curl_close($ch);
$obj = json_decode($result);
echo $result;
?>