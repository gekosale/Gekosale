<?php
namespace Gekosale\Tools;

\Gekosale\App::init();

/**
 * Base class for commandline tools
 * @author pkarwatka
 *
 */
abstract class Tool {
	
	public function run(){
		
	}
    
    /**
     * Replace all occurences of all keys in dicto to all values 
     */
    protected function renderString($tpl, array $dict) {

        foreach ($dict as $key => $val) {
            $tpl = str_replace(sprintf("{%s}", $key), $val, $tpl);
        }

        return $tpl;
    }

    /**
     * Recursive copy directory and render file names and content as template - using array $dict 
     */
    protected function rcopy($src, $dst, array $dict) {

        if (file_exists($dst))
            return -1;

        if (is_dir($src)) {
            mkdir($dst);
            $files = scandir($src);
            foreach ($files as $file)
                if ($file != "." && $file != "..")
                    $this->rcopy("$src/$file", "$dst/$file", $dict);
        } else
        if (file_exists($src)) {
            $content = $this->renderString(file_get_contents($src), $dict);
            $dst = $this->renderString($dst, $dict);

            $this->log('Created file %s.', array($dst));
            file_put_contents($dst, $content);
        }
    }    
    
 /** 
     * 
     * Get value of a given parameter name from command line in format param=value
     * 
     * @param $param name of the parameter looked for in CLI
     * @param $required if true it throws an error
     */
    protected function getParam($param, $required = true, $default = null) {
        global $argv;
        $value = null;
        
        foreach ($argv as $val) {
            if (preg_match('/^'.$param.'/',$val)) {
                $tokens = explode('=', $val);
                $value = trim($tokens[1]);
            }
        };
        
        if ($required && !$value) {
            die(sprintf("Required parameter \"%s\" missing; usage: %s=X   where X is the value of the param\n", $param, $param));
        }
        
        if($value == null)
            $value = $default;
        
        return $value;
    }
	
	public function log($message, array $params = array()){
		
		echo '['.date('Y-m-d H:i:s').'] '.vsprintf($message, $params)."\n";
	}
}