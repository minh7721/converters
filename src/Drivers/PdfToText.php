<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 17:53
 */

namespace Colombo\Converters\Drivers;


use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Process\CanRunCommand;

class PdfToText extends CanRunCommand implements ConverterInterface {
	
	protected $process_options = [
	
	];
	
	public function __construct( $bin = 'pdftotext', $tmp = '' ) {
		parent::__construct( $bin );
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
		$command = $this->buildCommand($path);
		try{
			$this->run( $command);
			$result->setContent( $this->output() );
		}catch (\RuntimeException $ex){
			$result->addErrors( $ex->getMessage(), $ex->getCode());
		}
		return $result;
	}
	
	protected function buildCommand($path = ''){
		$command = $this->bin . " " . $path;
		$command .= " " . $this->buildOptions() . " -";
		return $command;
	}
	
	public function startPage( int $page ) {
		$this->options('-f', $page);
	}
	
	public function endPage( int $page ) {
		$this->options('-l', $page);
	}
}