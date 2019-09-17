<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2/13/19
 * Time: 17:09
 */

namespace Colombo\Converters\Drivers;


use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Helpers\TemporaryDirectory;
use Colombo\Converters\Process\CanRunCommand;
use Symfony\Component\Process\Process;

class Unoconv extends CanRunCommand implements ConverterInterface {
	
	protected $bin = 'python';
	protected $process_options = [
		'--disable-html-update-links' => true,
//		'--stdout' => true,
	];
	
	protected $writer_aliases = [
		'html' => 'xhtml',
	];
	
	protected $user_installation = '';
	
	/** @var  TemporaryDirectory */
	protected $tmpFolder;
	
	protected $start_page = "-";
	protected $end_page = "-";
	
	public function __construct( $bin = '', $tmp = '') {
		$unoconv = __DIR__ . DIRECTORY_SEPARATOR . 'unoconv';
		parent::__construct( $bin . " " . $unoconv);
		if($tmp instanceof TemporaryDirectory){
			$this->tmpFolder = $tmp;
		}else{
			$this->setTmp($tmp);
		}
	}
	
	public function setTmp( $location ) {
		if($this->tmpFolder){
			$this->tmpFolder->empty();
		}
		$this->tmpFolder = new TemporaryDirectory( $location );
		$this->tmpFolder->create();
		return $this->tmpFolder->path();
	}
	
	public function options( $key = null, $value = null ) {
		if ( $key == 'tmp' ) {
			$this->setTmp( $value );
		} else {
			return parent::options( $key, $value );
		}
	}
	
	protected function buildCommand($path = ''){
		$command = $this->bin;
		$command .= " " . $this->buildOptions();
		$page_range = $this->start_page . "-" . $this->end_page;
		
		if($page_range != '---'){
			$command .= " -e PageRange=" . $page_range;
		}
		
		return $command . " " . $this->user_installation . " " . $path;
	}
	
	/**
	 * @param $path
	 * @param $outputFormat
	 * @param $inputFormat
	 *
	 * @return ConvertedResult
	 */
	public function convert( $path, $outputFormat, $inputFormat = '' ): ConvertedResult {
		$outdir = $this->tmpFolder->path();
//		$this->user_installation = "--user-profile=" . $outdir ;
		// set convert-to
		$this->options('-f', array_get($this->writer_aliases, $outputFormat, $outputFormat) );
		
		$out_name = preg_replace( "/\.[^\.]*$/", "", basename( $path ) );
		$out_file = $outdir . DIRECTORY_SEPARATOR . $out_name . "." . $outputFormat;
		$this->options('-o', $out_file);

//		die($out_file);
		
		$result = new ConvertedResult();
		
		$command = $this->buildCommand($path) . " -vvv";
		
		try{
			$this->run( $command, function ($type, $buffer) use (&$result, &$errors) {
				if (Process::ERR === $type) {
					echo "Error " . $buffer . "\n";
				}
				else {
					$result .= $buffer;
				}
			});
			
			if(!file_exists( $out_file )){
				$result->addErrors( "Can not convert", 500);
			}else{
				$result->setContent( file_get_contents( $out_file ));
			}
		}catch (\RuntimeException $ex){
			$result->addErrors( $ex->getMessage(), $ex->getCode());
		}
		return $result;
	}
	
	public function startPage( int $page ) {
		$this->start_page = $page;
	}
	
	public function endPage( int $page ) {
		$this->end_page = $page;
	}
}