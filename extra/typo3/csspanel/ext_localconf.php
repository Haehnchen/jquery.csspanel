<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_csspanel_pi1.php', '_pi1', 'includeLib', 1);
$TYPO3_CONF_VARS['FE']['eID_include']['tx_csspanel'] = 'EXT:csspanel/ajax.php';
?>