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
//$input = __DIR__ . "/files/multi_level.doc";

$soffice_bin = "/Applications/LibreOffice.app/Contents/MacOS/soffice";
$unoconv_python = "/Applications/LibreOffice.app/Contents/Resources/python";

$converter = new \Colombo\Converters\Helpers\Converter();

$converter->setInput($input);

$tmp = __DIR__ . "/tmp";

// force custom converter
//$converter->setForceConverter( new \Colombo\Converters\Drivers\Soffice($soffice_bin, $tmp) );
$converter->setForceConverter( new \Colombo\Converters\Drivers\Unoconv($unoconv_python, $tmp) );
$converter->setOutputFormat( 'html');


$result = $converter->run();

unset( $converter );

if($result->isSuccess()){
	$result->saveTo( 'tmp/test3' . microtime() . '.html', true);
//	$result->saveTo( 'tmp/multi_level.html', true);
//	$result->saveAsZip('tmp/ocr.zip','ocr.pdf');
	print_r( $result->getMessages());
}else{
	print_r($result->getErrors());
}