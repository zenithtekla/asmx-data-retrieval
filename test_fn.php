<?php
header('Content-Type: application/json');
require_once("core/util_fn.php");
require_once("core/date_time.php");
// TODO: include db_conn if needed

echo getDateTime(). "\n\n"; // 86400 for daily update, 3600 for hourly updatek, 2764800 for monthly update

$t_unix_today = strtotime(getDateTime());
echo $t_unix_today. "\n\n";
echo date("m/d/Y h:i:s a", $t_unix_today). "\n\n";
echo date("m/d/Y", $t_unix_today). "\n\n";

// load query strings
$qr = HelperUTILS::load_conf();

$t_query = HelperUTILS::input_string_escape($_POST["query_trigger"]);

// query trigger is the word the user enters on an input field in the view page
// $t_update_trigger now represents the word the user entered, aka. search key or user input. We will verify if query result is < 1 and $t_query.length() match a specific number of chars to perform db_update

// perform length check of $t_query input here or inject into $args and perform condition check together with $count

// with new approach to query directly on the Manex server, there ain't any need to query Mantis DB. The following can be commented out.
$response = HelperUTILS::mantis_search_result($qr["MANTIS_QUERY_CUSTOMER_FIND"], $t_query);
// use json_encode($response["RESULT"]); to prepare Typeahead selectives

$t_process = new dbUTILS();
$args = [$qr, $t_unix_today, $response["COUNT"]];
$t_process->fn_database_update_customer($args);
/*$t_temp = $t_process->fn_db_update_customer;
$t_temp();*//*
$t_process->setRetrievalDate("04/04/2016");
$t_retrieve_date= $t_process->getRetrievalDate();
echo $t_retrieve_date;
$t_getCurlData = $t_process->getCurlData();
echo $t_getCurlData;*/

?>
