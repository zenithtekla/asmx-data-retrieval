<?php
header('Content-Type: application/json');
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, 'http://erp-2/ews/ManexWebService.asmx/GetSalesOrderAndWorkOrder?WorkOrderNo=00091519A&status=');
$result = curl_exec($ch);
curl_close($ch);
$obj = json_decode($result);
echo $result;
?>