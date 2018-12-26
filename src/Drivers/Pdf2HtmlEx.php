<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/26/18
 * Time: 13:35
 */

namespace Colombo\Converters\Drivers;


use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Exceptions\ConvertException;
use Colombo\Converters\Helpers\TemporaryDirectory;
use Colombo\Converters\Process\CanRunCommand;

class Pdf2HtmlEx extends CanRunCommand implements ConverterInterface{
	
	protected $bin = 'pdf2htmlEX';
	protected $docker_bin = '';
	protected $process_options = [
		'-f' => '1', // default 1
		'-l' => '2147483647', // default 2147483647
//		'--embed-image' => '0', // default 1
//		'--bg-format' => 'jpg',
		'--embed-css' => '0',// default 1
		'--embed-font' => '1',// default 1
		'--css-filename' => 'style.css',
		'--page-filename' => 'page.html',
		'--printing' => '0',
		'--split-pages' => '1',
//		'--svg-embed-bitmap' => '0',
//		'--process-nontext' => '1',
		'--process-outline' => 1,// default 1
//		'--process-nontext' => 0,
//		'--auto-hint' => 1,
		'--stretch-narrow-glyph' => 1,
		'--squeeze-wide-glyph' => 0,

//		'--fallback' => 1,
	];
	
	/** @var  TemporaryDirectory */
	protected $tmpFolder;
	
	/**
	 * Pdf2HtmlEx constructor.
	 *
	 * @param string $bin
	 */
	public function __construct( $bin = '' ) {
		parent::__construct( $bin );
		$this->setTmp('');
	}
	
	public function setTmp( $location ) {
		if($this->tmpFolder){
			$this->tmpFolder->empty();
		}
		$this->tmpFolder = new TemporaryDirectory( $location );
		$this->tmpFolder->create();
		return $this->tmpFolder->path();
	}
	
	
	/**
	 * @param $path
	 * @param $outputFormat
	 * @param $inputFormat
	 *
	 * @return ConvertedResult
	 */
	public function convert( $path, $outputFormat, $inputFormat = '' ): ConvertedResult {
		$result = new ConvertedResult();
		
		if($outputFormat != 'html'){
			throw new ConvertException($outputFormat . " was not supported by pdf2htmlex converter");
		}
		
		$output = $this->tmpFolder->path('output/index.html');
		$output_dir = str_replace( "/index.html", "", $output);
		$output_file = "index.html";
		$this->options('--dest-dir', $output_dir);
		
		$command = $this->buildCommand($path . " " . $output_file);
		try{
			$this->run( $command );
			$files = glob( $output_dir . "/*");
			foreach ($files as $file){
				$result->addContent( file_get_contents( $file ), basename( $file));
			}
		}catch (\RuntimeException $ex){
			$result->addErrors( $ex->getMessage(), $ex->getCode());
		}
		return $result;
	}
	
	/**
	 * Custom options
	 *
	 * @param null $key
	 * @param null $value
	 *
	 * @return mixed
	 *
	 */
	public function options( $key = null, $value = null ) {
		if ( $key == 'tmp' ) {
			$this->setTmp( $value );
		} else {
			return parent::options( $key, $value );
		}
	}
	
	public function startPage( int $page ) {
		$this->options('-f', $page);
	}
	
	public function endPage( int $page ) {
		$this->options('-l', $page);
	}
}