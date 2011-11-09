<?php

class PanelHandler {

 var $rootpage = null;
 var $table = 'tx_csspanel_configs';

 function __construct($id) {
  $this->rootpage = $id;
 }

 public function load($conf) {
  $ar = array();
  foreach ($conf['CSS.'] as $key => $value) {
   if (isset($value['files.'])) {
    $value['files'] = $this->ReadCSSImages($value['files.']['path']);
    unset($value['files.']);
   }
   $ar[$value['selector'] . ':' . $value['type']] = $value;
  }

  $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('config', $this->table, 'pid="' . $this->rootpage . '"');
  if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
   $this->AddSavedValues(unserialize($row['config']), $ar);
  }

  return json_encode(array_values($ar));
 }

 private function ReadCSSImages($path) {
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

  $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', $this->table, 'pid="' . $this->rootpage . '"');
  if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
   $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->table, 'uid=' . intval($row['uid']), array('config' => serialize($test)));
  } else {
   $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->table, array('pid' => $this->rootpage, 'config' => serialize($test)));
  }


  echo $this->rootpage . ' OK';
  //print_r(json_decode($_POST['save']));
  exit;
 }

 function GenerateCSSStyles() {
  $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('config', $this->table, 'pid="' . $this->rootpage . '"');
  if (!$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
   return false;

  $cfgs = unserialize($row['config']);

  $s = '';
  foreach ($cfgs as $cfg) {
   $s .= $cfg->selector . ' { ' . $cfg->type . ': ' . $cfg->value . ' } ';
  }

  $GLOBALS['TSFE']->additionalHeaderData[$this->extKey] .= "<style>" . $s . "</style>";
 }

}

?>