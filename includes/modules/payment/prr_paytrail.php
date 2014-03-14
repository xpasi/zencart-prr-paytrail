<?php

require_once((IS_ADMIN_FLAG === true ? DIR_FS_CATALOG_MODULES : DIR_WS_MODULES) . '/payment/prr_paytrail/Verkkomaksut_Module_Rest.php');

if (!defined('TABLE_SUOMENPANKIT')) {
  define('TABLE_SUOMENPANKIT', DB_PREFIX . 'prr_suomenpankit');
}

require_once('prr_paytrail/init.php');