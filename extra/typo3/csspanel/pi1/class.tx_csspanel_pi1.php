<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Daniel Espendiller <daniel@espendiller.net>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */
require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('csspanel') . 'res/PanelHandler.php');

/**
 * Plugin '' for the 'csspanel' extension.
 *
 * @author	Daniel Espendiller <daniel@espendiller.net>
 * @package	TYPO3
 * @subpackage	tx_csspanel
 */
class tx_csspanel_pi1 extends tslib_pibase {

 var $prefixId = 'tx_csspanel_pi1';  // Same as class name
 var $scriptRelPath = 'pi1/class.tx_csspanel_pi1.php'; // Path to this script relative to the extension dir.
 var $extKey = 'csspanel'; // The extension key.
 var $pi_checkCHash = true;
 var $rootpage = null;

 /**
  * The main method of the PlugIn
  *
  * @param	string		$content: The PlugIn content
  * @param	array		$conf: The PlugIn configuration
  * @return	The content that is displayed on the website
  */
 function main($content, $conf) {
  #print_r($conf);

  $this->rootpage = $this->cObj->cObjGetSingle('TEXT', array('data' => 'leveluid:0'));


  $css = new PanelHandler($this->rootpage);

  $GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '';
  if (isset($conf['enable']) AND $conf['enable'] == 1) {
   $GLOBALS['TSFE']->additionalHeaderData[$this->extKey] .= "<script>jQuery.noConflict();
        jQuery(document).ready(function(){
          var test = " . $css->load($conf) . "
		  jQuery('body').append('<div id=\"panel\"/>');
          jQuery('#panel').CSSPanel({'jsonpath': '/?eID=tx_csspanel&id=" . $this->rootpage . "', 'csselements': test})
        })
        </script>";
  }


  $css->GenerateCSSStyles();
 }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/csspanel/pi1/class.tx_csspanel_pi1.php']) {
 include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/csspanel/pi1/class.tx_csspanel_pi1.php']);
}
?>