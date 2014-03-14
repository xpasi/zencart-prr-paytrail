<?php

include('includes/application_top.php');

require_once((IS_ADMIN_FLAG === true ? DIR_FS_CATALOG_MODULES : DIR_WS_MODULES) . '/payment/prr_paytrail/Verkkomaksut_Module_Rest.php');

$module = new Verkkomaksut_Module_Rest(CFG_PRR_PAYTRAIL_SELLER_ID, CFG_PRR_PAYTRAIL_PASSWORD);
if ($module->confirmPayment($_GET["ORDER_NUMBER"], $_GET["TIMESTAMP"], $_GET["PAID"], $_GET["METHOD"], $_GET["RETURN_AUTHCODE"])) {
	// Maksukuittaus on validi
	// Maksussa käytetty maksutapa löytyy tarvittaessa muuttujasta $_GET["METHOD"]
	// ja kuitattavan maksun tilausnumero muuttujasta $_GET["ORDER_NUMBER"]
	// Päivitä tilauksen tila!
	$order_id =  zen_db_input($_GET["ORDER_NUMBER"]);
	$f = $db->Execute('SELECT `orders_id` FROM `' . TABLE_SUOMENPANKIT . '` WHERE `referid`="' . $order_id . '"');
	if ($f->fields['orders_id'] != '') { // MUUTA TÄMÄ RECORD COUNTIKSI!=!=!=#")%
		$real_id = (int) $f->fields['orders_id'];
		$sql_data_array = array(
			'orders_id' => $real_id,
			'orders_status_id' => (int) CFG_PRR_PAYTRAIL_ORDER_STATUS_ON_CONFIRM,
			'date_added' => 'now()',
			'customer_notified' => 0,
			'comments' => ''
		);
		zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
		$db->Execute('UPDATE `' . TABLE_ORDERS . '` SET `orders_status` = "' . (int) CFG_PRR_PAYTRAIL_ORDER_STATUS_ON_CONFIRM . '" WHERE `orders_id` = ' . $real_id);
	}

} else {
	// Maksukuittaus ei ollut validi, mahdollinen huijausyritys
	$prr_entry = date('d.m.Y H:i:s') . ' ' . $_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['HTTP_USER_AGENT'] . "\nPOST:";
	foreach ($_POST as $k => $v) {
		$prr_entry .= $k . '=' . $v;
	}
	$prr_entry .= "\nGET:";
	foreach ($_GET as $k => $v) {
		$prr_entry .= $k . '=' . $v;
	}
	$prr_entry .= "\n\n--------------------------------------------\n";

	// Tallenna tieto lokiin
	if (is_dir(DIR_FS_LOGS) && is_writable(DIR_FS_LOGS)) {
		$prr_file = DIR_FS_LOGS . '/paytrail-handler.log';
		if (!is_file($prr_file)) touch($prr_file);
	} else {
		$prr_file = DIR_FS_SQL_CACHE . '/paytrail-handler.log';
		if (is_writable(DIR_FS_SQL_CACHE) && !is_file($prr_file)) touch($prr_file);
	}

	if (is_writable($prr_file)) {
		file_put_contents($prr_file,$prr_entry,FILE_APPEND);
	}
}            