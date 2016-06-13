<pre>
<?php
	define('__ROOT__', dirname(__FILE__).DIRECTORY_SEPARATOR);
	define('__CFG_FILE__', __ROOT__.'cfg\manextis_conf.ini');
	require_once __ROOT__.'core\manextis_utils.php';

	$t_arr = ["UNIQ_KEY" => "_X01","WO_NO" => "wono1","SO_NO" => "sono1","DUE_DATE" => 100166, "ASSY_NO" => '      "  " " 00 " \{\{{/+  ', "REVISION" => "    ", "QTY" => 99, "CUST_PO_NO" => " pono2      ", "CUST_NAME" => "Fuji_ko"];
	$x_arr = ["UNIQ_KEY" => "_X02","WO_NO" => "wono2","SO_NO" => "sono2","DUE_DATE" => 200266, "ASSY_NO" => 11, "REVISION" => "    R1   ", "QTY" => 88, "CUST_PO_NO" => "        poNo2 ", "CUST_NAME" => "Fuji_ko"];
	print_r(HelperUTILS::array_diff_pairs($t_arr,$x_arr));
?>
</pre>
