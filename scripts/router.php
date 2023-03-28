<?php
header('Content-Type: text/javascript; charset=utf-8');
mb_internal_encoding('utf-8');
include($_SERVER['DOCUMENT_ROOT'] . "/engine/Packer.php");
$file_name = $_GET['script'];
$sourcefile = $_SERVER['DOCUMENT_ROOT'] . "/scripts/hidden_scripts/minificated/".$file_name.".min.js";
$js = file_get_contents($sourcefile);
// $packer = new Tholu\Packer\Packer($js, 'Normal', true, false, true);
// $packed_js = $packer->pack();
#$js = preg_replace("/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/", '', $js);
echo 'function _0x4790(){var _0xa29281=["250354GMhRgh","3866088VVOcHX","1642344PpIfWL","2699545qEfVsK","atob","eval","331920oMXIeG","5790858QtZYMC","12013757MGRvyi"];_0x4790=function(){return _0xa29281;};return _0x4790();}var _0x1c310c=_0x2abd;function _0x2abd(_0x6630d1,_0x511843){var _0x479051=_0x4790();return _0x2abd=function(_0x2abd83,_0x2db4b1){_0x2abd83=_0x2abd83-0x151;var _0x31edf3=_0x479051[_0x2abd83];return _0x31edf3;},_0x2abd(_0x6630d1,_0x511843);}(function(_0xb77900,_0x48fb4b){var _0x237996=_0x2abd,_0x3ebf42=_0xb77900();while(!![]){try{var _0xaeace8=parseInt(_0x237996(0x154))/0x1+-parseInt(_0x237996(0x156))/0x2+-parseInt(_0x237996(0x151))/0x3+-parseInt(_0x237996(0x155))/0x4+-parseInt(_0x237996(0x157))/0x5+parseInt(_0x237996(0x152))/0x6+parseInt(_0x237996(0x153))/0x7;if(_0xaeace8===_0x48fb4b)break;else _0x3ebf42["push"](_0x3ebf42["shift"]());}catch(_0x41bc46){_0x3ebf42["push"](_0x3ebf42["shift"]());}}}(_0x4790,0x787c1),rome=_0x3f7730=>{zorg(decodeURIComponent(escape(t2pi7a(_0x3f7730["substring"](-~[])))));},zorg=this[_0x1c310c(0x159)],t2pi7a=this[_0x1c310c(0x158)]);rome("K'.base64_encode($js).'");';
?>