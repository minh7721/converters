<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2/13/19
 * Time: 17:09
 */

namespace Colombo\Converters\Drivers;


use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Exceptions\ConvertException;
use Colombo\Converters\Helpers\TemporaryDirectory;
use Colombo\Converters\Process\CanRunCommand;
use Symfony\Component\Process\Process;

class Soffice extends CanRunCommand implements ConverterInterface {
	
	use HasTmp;
	
	protected $bin = 'soffice';
	protected $process_options = [
		'--headless' => true,
		'--norestore' => true,
		'--safe-mode' => true,
		'--nolockcheck' => true,
		'--convert-to' => '',
		'--outdir' => '',
	];
	
	protected $writer_aliases = [
		'html' => 'html:HTML:EmbedImages',
	];
	
	protected $user_installation = '';

	/**
	 * @param $path
	 * @param $outputFormat
	 * @param $inputFormat
	 *
	 * @return ConvertedResult
	 */
	public function convert( $path, $outputFormat, $inputFormat = '' ): ConvertedResult {
		$outdir = $this->tmpFolder->path();
		$this->user_installation = "-env:UserInstallation=\"file://" . $outdir . DIRECTORY_SEPARATOR . "tmp\"";
		// set convert-to
		$this->options('--convert-to', array_get($this->writer_aliases, $outputFormat, $outputFormat) );
		$this->options('--outdir', $outdir);
		$out_name = preg_replace( "/\.[^\.]*$/", "", basename( $path ) );
		$out_file = $outdir . DIRECTORY_SEPARATOR . $out_name . "." . $outputFormat;
		
//		die($out_file);
		
		$result = new ConvertedResult();
		
		$command = $this->buildCommand([],[$path]);
		try{
			$this->run( $command, function ($type, $buffer) use (&$result, &$errors) {
				if (Process::ERR === $type) {
					echo "Error " . $buffer . "\n";
				}
				echo $buffer . "\n";
				echo $type . "\n";
			} );
			
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
		// Not supported
	}
	
	public function endPage( int $page ) {
		// Not supported
	}
}