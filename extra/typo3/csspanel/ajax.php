<?php

// Exit, if script is called directly (must be included via eID in index_ts.php)
if (!defined('PATH_typo3conf'))
  die('Could not access this script directly!');

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(PATH_tslib . 'class.tslib_content.php');

require_once(PATH_tslib . 'class.tslib_fe.php');
require_once(PATH_t3lib . 'class.t3lib_page.php');

#require_once($GLOBALS['TYPO3_LOADED_EXT']['ajaxdemo']['siteRelPath'].'json/json_response.php');

class tx_csspanel_json extends tslib_pibase {

  var $extKey = 'tx_csspanel'; // The extension key.
  var $prefixId = 'tx_txcsspanel_pi1';

  function init() {
    // http://www.zoe.vc/2008/typoscript-auslesen/
    $temp_TSFEclassName = t3lib_div::makeInstance('tslib_fe');
    $GLOBALS['TSFE'] = new $temp_TSFEclassName($TYPO3_CONF_VARS, $pid, 0, true);
    $GLOBALS['TSFE']->connectToDB();
    $GLOBALS['TSFE']->initFEuser();
    $GLOBALS['TSFE']->determineId();
    $GLOBALS['TSFE']->getCompressedTCarray();
    $GLOBALS['TSFE']->initTemplate();
    $GLOBALS['TSFE']->getConfigArray();
  }

  function main($actionName) {
    $this->init();

    $conf = $GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->prefixId . '.'];

    if (isset($_POST['save'])) {
      $this->save();
    } else {
      $this->load($conf);
    }


    exit;

    /*
      $this->request = $this->getRequest();
      $this->response = new tx_json_response();

      $content = $this->processRequest();

      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
      header('Last-Modified: ' . gmdate( "D, d M Y H:i:s" ) . 'GMT');
      header('Cache-Control: no-cache, must-revalidate');
      header('Pragma: no-cache');
      $charset = $GLOBALS["cfgApplCharset"]."";
      header('Content-Type: text/javascript; charset='.$charset.'');
      header('Content-Disposition: inline; filename=json.js');
      header('Content-Length: '.strlen($content));
      echo $content; */
  }

  function load($conf) {
    $ar = array();
    foreach ($conf['CSS.'] as $key => $value) {
      if (isset($value['files.'])) {
        $value['files'] = $this->ReadCSSImages($value['files.']['path']);
        unset($value['files.']);
      }
      $ar[$value['selector'] . ':' . $value['type']] = $value;
    }

    $name = 'test';
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('config', 'tx_txcsspanel_configs', 'name="' . $name . '"');
    if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
      $this->AddSavedValues(unserialize($row['config']), $ar);
    }

    echo json_encode(array_values($ar));
  }

  private function AddSavedValues($saved, &$ar) {
    foreach ($saved as $value) {
      $value = (array) $value;
      if (isset($ar[$value['selector'] . ':' . $value['type']])) {
        $ar[$value['selector'] . ':' . $value['type']]['value'] = $value['value'];
      }
    }
  }

  function save() {

    #echo $_POST['save'];
    $test = json_decode(stripslashes($_POST['save']));

    //$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('name', $this->extKey . '_configs', $where);
    $name = 'test';

    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_txcsspanel_configs', 'name="' . $name . '"');
    if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
      $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_txcsspanel_configs', 'uid=' . intval($row['uid']), array('config' => serialize($test)));
    } else {
      $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_txcsspanel_configs', array('name' => $name, 'config' => serialize($test)));
    }



    //$extKey
#	file_put_contents('fileadmin/tset.log', stripslashes($_POST['save']));
    print_r($test);
    echo 'OK';
    //print_r(json_decode($_POST['save']));
    exit;
  }

  function ReadCSSImages($path) {
    $files = array();
    foreach (glob($path . "/*.png") as $filename) {

      if (!preg_match('/thumb/i', $filename)) {
        $files[] = array(
          'thumbnail' => dirname($filename) . '/' . basename($filename, '.png') . '_thumb.png',
          'image' => $filename,
        );
      }
    }

    return $files;
  }

  function getRequest() {
    $input = fopen("php://input", 'r');
    $content = fread($input, $this->MAX_REQUEST_LENGTH);

    return json_decode($content, true);
  }

  function processRequest() {
    // prepare a demo response
    $this->response->isSuccess = true;
    $this->response->addMessage("Request successful");
    $this->response->data = Array();
    $this->response->data["stringValue"] = "my string value";
    $this->response->data["num"] = 42;
    return $this->response->asString();
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
