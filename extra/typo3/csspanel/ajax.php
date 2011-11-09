<?php

// Exit, if script is called directly (must be included via eID in index_ts.php)
if (!defined('PATH_typo3conf'))
 die('Could not access this script directly!');

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(PATH_tslib . 'class.tslib_content.php');

require_once(PATH_tslib . 'class.tslib_fe.php');
require_once(PATH_t3lib . 'class.t3lib_page.php');

require_once(t3lib_extMgm::extPath('csspanel') . 'res/PanelHandler.php');

#require_once($GLOBALS['TYPO3_LOADED_EXT']['ajaxdemo']['siteRelPath'].'json/json_response.php');
// Connect to database:
tslib_eidtools::connectDB();

class tx_csspanel_json extends tslib_pibase {

 var $extKey = 'tx_csspanel'; // The extension key.
 var $prefixId = 'tx_txcsspanel_pi1';
 var $rootpage = null;

 function init() {
  // http://www.zoe.vc/2008/typoscript-auslesen/
  $this->rootpage = (int) $_GET['id'];

  $temp_TSFEclassName = t3lib_div::makeInstance('tslib_fe');
  $GLOBALS['TSFE'] = new $temp_TSFEclassName($TYPO3_CONF_VARS, $this->rootpage, 0, true);
  $GLOBALS['TSFE']->connectToDB();
  //$GLOBALS['TYPO3_DB'] = $GLOBALS['TSFE']->connectToDB();
  $GLOBALS['TSFE']->initFEuser();
  $GLOBALS['TSFE']->determineId();
  $GLOBALS['TSFE']->getCompressedTCarray();
  $GLOBALS['TSFE']->initTemplate();
  $GLOBALS['TSFE']->getConfigArray();
 }

 function main($actionName) {
  $this->init();

  $conf = $GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->prefixId . '.'];


  $css = new PanelHandler($this->rootpage);

  if (isset($_POST['save'])) {
   $css->save();
  } else {
   echo json_encode($css->load($conf));
  }
 }

}

$output = t3lib_div::makeInstance("tx_csspanel_json");
$output->main($action);

/*
  <?php

  if (isset($_POST['save'])) {
  #echo $_POST['save'];
  $test = json_decode(stripslashes($_POST['save']));
  file_put_contents('fileadmin/tset.log', stripslashes($_POST['save']));
  print_r($test);
  echo 'OK';
  //print_r(json_decode($_POST['save']));
  exit;
  }

  $files = array();
  foreach (glob("fileadmin/ext_files/background/*.png") as $filename) {


  if(!preg_match('/thumb/i' , $filename)) {
  $files[] = array(
  'thumbnail' => dirname($filename) . '/' . basename($filename, '.png') . '_thumb.png',
  'image' => $filename,
  );
  }
  }


  $ar = array(
  array('selector' => '#page', "type" => "background-color", "text" => "Page Background", 'description' => 'test'),
  array('selector' => 'h1,h2', "type" => "color", "text" => "Header"),
  array('selector' => 'body', "type" => "color", "text" => "Text-Color"),
  array('selector' => 'a, a:link, a:visited', "type" => "color", "text" => "Link Color"),
  array('selector' => '#header-wrapper', "type" => "background-color", "text" => "Header Background"),
  array('selector' => 'body', "type" => "background-color", "text" => "Background Color"),
  array('selector' => 'body', "type" => "background-image", "text" => "Background Images", "files" => $files),
  );

  echo json_encode($ar);

  ?>


  $GLOBALS['TSFE']->additionalHeaderData['tx_isstyleswitcher_pi1_css'] = $ss->printStyles();
  $GLOBALS['TSFE']->additionalHeaderData['tx_isstyleswitcher_pi1_js'] = "<script type='text/javascript' language='Javascript' src='/".t3lib_extMgm:: siteRelPath($this->extKey)."pi1/styleswitcher.js'></script>";

  //Get the 'Starting Point' PIDs
  if ($this->cObj->data["pages"] != null) {
  $this->sysfolderList = $this->cObj->data["pages"];
  }
  //If no starting point is given, then take the pid of the plugin page
  else{
  $this->sysfolderList = $GLOBALS['TSFE']->id;
  }


  $GET = t3lib_div::GParrayMerged("GET");


  $typoscript['conf'] = 'TEXT';
  $typoscript['conf.']['data'] = 'date:U';
  $typoscript['conf.']['strftime'] = '%Y';
  $typoscript['conf.']['noTrimWrap'] = '|(c) | www.domain.com|';

  $content = $this->cObj->cObjGetSingle($typoscript['conf'], $typoscript['conf.']);

 */
?>
