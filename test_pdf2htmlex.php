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
$pdf2htmlex = new \Colombo\Converters\Drivers\Pdf2HtmlEx();
$pdf2htmlex->setTmp( __DIR__ . "/tmp/");
$converter->setForceConverter( $pdf2htmlex );
$converter->setOutputFormat( 'html');
$converter->setStartPage( 1);
$converter->setEndPage( 2);


$result = $converter->run();

if($result->isSuccess()){
	$result->saveAsZip('xxx/a.zip');
	print_r( $result->getMessages());
}else{
	print_r($result->getErrors());
}