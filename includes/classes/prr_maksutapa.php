<?php

// Käytä tätä luokkaa maksutavan tietojen hakemiseen.
// Käyttö:
//
// require_once(DIR_FS_CATALOG . '/' . DIR_WS_CLASSES . 'prr_maksutapa.php');
// prr_maksutapa::get($tilausnumero)
//
// Tilausnumero voi olla myös array jonka arvot ovat tilausnumeroita:
// prr_maksutapa::get(array(10,20,30,40,5));
//
// Palautettava arvo on array jonka avaimina ovat tilausnumerot:
// $return[<tilausnumero>]['method'] = maksutapa
// $return[<tilausnumero>]['reference'] = viitenumero

if (!defined('TABLE_SUOMENPANKIT')) {
  define('TABLE_SUOMENPANKIT', DB_PREFIX . 'prr_suomenpankit');
}

class prr_maksutapa {

	private static $orders = array();

	public static function get($oid) {
		global $db;
		$oids = array();
		if (!is_array($oid)) $oid = array($oid);
		foreach ($oid as $id) {
			// Check if exists
			if (!isset(self::$orders[$id])) $oids[] = $id;
		}
		if (count($oids)) { // Something to search for...
			$sql = 'SELECT * FROM `' . TABLE_SUOMENPANKIT . '` WHERE `orders_id` IN (:oids)';
			$sql = $db->bindVars($sql, ':oids', implode(',',$oids), 'string');
			$data = $db->execute($sql);
			if ($data->RecordCount() > 0) {
				self::$orders[$data->fields['orders_id']]['method'] = $data->fields['method'];
				self::$orders[$data->fields['orders_id']]['reference'] = $data->fields['referid'];
			}
		}
		return $self::orders;
	}

}