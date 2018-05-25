<?php

class JSONUtil
{
    
    public function setExpStatus($array, $owner)
    {
        
        if(!isset($array->Status))
            $array->Status = array();
        
        
        $array->Status['Owner'] = $owner;
        $array->Status['Deleted'] = false;
        $array->Status['Last_modified'] = round(microtime(true) * 1000);
        
    }
    
    public function setStatus($array, $owner)
    {
        
        if(!isset($array->CIL_CCDB->Status))
            $array->CIL_CCDB->Status = array();
        
        
        $array->CIL_CCDB->Status['Owner'] = $owner;
        $array->CIL_CCDB->Status['Is_public'] = false;
       $array->CIL_CCDB->Status['Deleted'] = false;
        $array->CIL_CCDB->Status['Last_modified'] = round(microtime(true) * 1000);
        
    }
    
    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}



?>

