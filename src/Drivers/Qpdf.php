<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2019-09-25
 * Time: 15:39
 */

namespace Colombo\Converters\Drivers;


use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Exceptions\ConvertException;
use Colombo\Converters\Process\CanRunCommand;

class Qpdf extends CanRunCommand implements ConverterInterface {
    
    protected $bin = 'qpdf';
    
    protected $process_options = [
        '--linearize' => true,
        '--min-version=1.5' => true,
        '--object-streams' => "generate",
    ];
    
    protected $start_page = 0;
    protected $end_page = 0;
    
    /**
     * qpdf --linearize --collate --empty --min-version=1.5 --pages qpdf.pdf 1-10 -- qpdf.o.pdf
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
        $command = $this->bin . $this->buildOptions(true);
        
        if($this->start_page || $this->end_page){
            $this->start_page = $this->start_page ? $this->start_page : 1;
            $this->end_page = $this->end_page ? $this->end_page : 'z';
            $command .= " --collate --empty --pages " . $path . " " . $this->start_page . "-" . $this->end_page . " -- -";
        }else{
            $command .= " " . $path . " -";
        }
        
        return $command;
    }
    
    public function startPage( int $page ) {
        $this->start_page = $page;
    }
    
    public function endPage( int $page ) {
        $this->end_page = $page;
    }
}