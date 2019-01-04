<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 18:24
 */

ini_set( 'display_errors', 1);

require __DIR__ . "/vendor/autoload.php";

$input = __DIR__ . "/files/ocr.pdf";

$converter = new \Colombo\Converters\Helpers\Converter();

$converter->setInput($input);

// force custom converter
$ocrConverter = new \Colombo\Converters\Drivers\OcrMyPdf();
$converter->setForceConverter( $ocrConverter );
$converter->setOutputFormat( 'pdf');


$result = $converter->run();

if($result->isSuccess()){
	$result->saveTo( 'tmp/ocr.pdf');
	$result->saveAsZip('tmp/ocr.zip','ocr.pdf');
	print_r( $result->getMessages());
}else{
	print_r($result->getErrors());
}