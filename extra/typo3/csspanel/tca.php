<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_csspanel_configs'] = array (
	'ctrl' => $TCA['tx_csspanel_configs']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'name,config'
	),
	'feInterface' => $TCA['tx_csspanel_configs']['feInterface'],
	'columns' => array (
		'name' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:csspanel/locallang_db.xml:tx_csspanel_configs.name',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'config' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:csspanel/locallang_db.xml:tx_csspanel_configs.config',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'name;;;;1-1-1, config')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>