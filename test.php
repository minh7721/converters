<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 18:24
 */

ini_set( 'display_errors', 1);

require __DIR__ . "/vendor/autoload.php";

$input = __DIR__ . "/files/test3.doc";

$soffice_bin = "/Applications/LibreOffice.app/Contents/MacOS/soffice";

$converter = new \Colombo\Converters\Helpers\Converter();

$converter->setInput($input);

$tmp = __DIR__ . "/tmp";

// force custom converter
$converter->setForceConverter( new \Colombo\Converters\Drivers\Soffice($soffice_bin, $tmp) );
$converter->setOutputFormat( 'html');


$result = $converter->run();

unset( $converter );

if($result->isSuccess()){
	$result->saveTo( 'tmp/test3.html');
//	$result->saveAsZip('tmp/ocr.zip','ocr.pdf');
	print_r( $result->getMessages());
}else{
	print_r($result->getErrors());
}