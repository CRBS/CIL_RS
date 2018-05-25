<?php

/**
 * This class contains the helper functions for the Rest class to handle
 * the array or the JSON object.
 * 
 * PHP version 5.6+
 * 
 * @author Willy Wong
 * @license https://github.com/slash-segmentation/CIL_RS/blob/master/LICENSE.txt
 * @version 1.0
 * 
 */
class JSONUtil
{
    /**
     * This function sets the Status properties in the array.
     * 
     * @param type $array
     * 
     */
    public function setExpStatus($array, $owner)
    {
        
        if(!isset($array->Status))
            $array->Status = array();
        
        
        $array->Status['Owner'] = $owner;
        $array->Status['Deleted'] = false;
        $array->Status['Last_modified'] = round(microtime(true) * 1000);
        
    }
    
    /**
     * This functions set the Status properties in the CIL_CCDB property
     * of the array.
     * 
     * @param type $array
     * 
     */
    public function setStatus($array, $owner)
    {
        
        if(!isset($array->CIL_CCDB->Status))
            $array->CIL_CCDB->Status = array();
        
        
        $array->CIL_CCDB->Status['Owner'] = $owner;
        $array->CIL_CCDB->Status['Is_public'] = false;
       $array->CIL_CCDB->Status['Deleted'] = false;
        $array->CIL_CCDB->Status['Last_modified'] = round(microtime(true) * 1000);
        
    }
    
    /**
     * This function returns the current time in micro seconds.
     * 
     * @return type integer
     */
    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}



?>

