<?php

include 'bootstrap.php';

/**
 * Report error and exit 
 * @param string $message 
 */
function error($message){
    echo sprintf("\n** ERROR: %s\n\n", $message);
    exit(-1);
}

	if(php_sapi_name() == 'cli') {

		$toolName = @$argv[1];
        $actionName = @$argv[2] && strpos($argv[2], '=') === FALSE ? @$argv[2] : 'run'; // default action name is "run"
        
		$toolPath = ROOTPATH.DS.'tools'.DS.$toolName.'.php';
		if(!$toolName || !file_exists($toolPath)) {
			error("Usage:\n\ntool.php <toolName> <actionName - optional> param1=<value> param2=<value>\n\nExample: tool.php migrate");
		} else {
			
			require_once($toolPath);
			$className = "\\Gekosale\\Tools\\".ucfirst($toolName); 
			
			
			$toolInstance =  new $className();
            
            if(!($toolInstance instanceof \Gekosale\Tools\Tool))
            {
                error(sprintf('Class %s has to implement \Gekosale\Tools\Tool', $className));
            }
            
			if(!$toolName){
				error(sprintf("Invalid toolName: %s", $toolName));
			} else {
				
				try { 
                    
                    if(!method_exists($toolInstance, $actionName)){
                        error(sprintf('Class %s has to contain method %s', $className, $actionName));
                    }
                    
                    $toolInstance->$actionName();
                
                } catch(Exception $err) {
                    
                    error($err);
                }
				
				exit (0);
			}
		}
	
	
	}