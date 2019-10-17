<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 15:39
 */

namespace Colombo\Converters\Process;


use Symfony\Component\Process\Process;

abstract class CanRunCommand {
	protected $process;
	protected $process_options = [];
	private $command;
	protected $bin;
	protected $timeout = 300;
	protected $output_when_error_length = 500;
	protected $valid_error_codes = [0];
	
	/**
	 * CanRunCommand constructor.
	 *
	 * @param string $bin
	 * @param string $tmp
	 */
	public function __construct($bin = '', $tmp = '') {
		$this->bin($bin);
		$this->process = new Process('');
		$this->process->setTimeout($this->timeout);
		if(method_exists( $this, 'setTmp')){
		    $this->setTmp($tmp);
        }
	}
	
	public function addValidCode($code){
	    if(!in_array( $code, $this->valid_error_codes, true)){
	        $this->valid_error_codes[] = $code;
        }
    }
    
	public function removeValidCode($code){
	    if($index = array_search( $code, $this->valid_error_codes)){
	        unset($this->valid_error_codes[$index]);
        }
    }
    
	public function resetValidCode(){
        $this->valid_error_codes = [0];
    }
	
	
	protected function validateRun()
	{
		$status = $this->process->getExitCode();
		$error  = $this->process->getErrorOutput();
		
		if (!in_array( $status, $this->valid_error_codes, true) and $error !== '') {
			throw new \RuntimeException(
				sprintf(
					"The exit status code %s says something went wrong:\n stderr: %s\n stdout: %s\ncommand: %s.",
					$status,
					$error,
					mb_substr( $this->process->getOutput(), 0, $this->output_when_error_length),
					$this->command
				)
			);
		}
	}
	
	protected function buildCommand($append = ''){
		$command = $this->bin;
		$command .= " " . $this->buildOptions();
		return $command . $append;
	}
	
	protected function buildOptions($use_equal_symbol = false){
	    $pair_combinator = $use_equal_symbol ? "=" : " ";
		$options = ' ';
		foreach($this->process_options as $k => $v){
			if(is_array( $v ) && count($v) > 0){
				foreach ($v as $_v){
					$options .= $k . $pair_combinator . $_v . " ";
				}
				continue;
			}
			
			if($v === false){
			    continue;
			}
			
			if($v !== true){
				$options .= $k . $pair_combinator . $v . " ";
			}else{
                $options .= $k . " ";
            }
		}
		
		return $options;
	}
	
	public function run($command, $callback = null)
	{
//		$this->command = escapeshellcmd($command);
		$this->command = $command;
		$this->process->setCommandLine($this->command);
		$this->process->run($callback);
		$this->validateRun();
		
		return $this;
	}
	public function bin($bin = ''){
		if(!empty($bin)){
			$this->bin = $bin;
		}
		return $this->bin;
	}
	public function timeout($timeout = ''){
		if(!empty($timeout)){
			$this->timeout = $timeout;
			$this->process->setTimeout( $timeout );
		}
		return $this->timeout;
	}
	
	public function options($key = null, $value = null){
		if(is_array($key)){
			if($value === true){// overwrite all option
				$this->options = $key;
			}else{
				foreach ($key as $k => $v){
					$this->options($k, $v);
				}
			}
		}elseif($key == 'bin'){ // custom bin path
			$this->bin($value);
		}elseif ($key != null){
			if($value !== null){
				$this->process_options[$key] = $value;
			}
			return $this->process_options[$key];
		}
		return $this->process_options;
	}
	
	public function output()
	{
		return $this->process->getOutput();
	}
}