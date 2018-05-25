<?php
/**
 * This class contains the helper function to insert the download statistics
 * to a PostgreSQL database.
 * 
 * PHP version 5.6+
 * 
 * @author Willy Wong
 * @license https://github.com/slash-segmentation/CIL_RS/blob/master/LICENSE.txt
 * @version 1.0
 * 
 */
class DBUtil 
{
    private $success = "success";
    private $error_type = "error_type";
    private $error_message = "error_message";
    
    /**
     * This function inserts the download statistics into the PostgreSQL
     * database.
     * 
     * @param type $json
     * @return array
     */
    public function insertDownloadStatistics($json)
    {
        $CI = CI_Controller::get_instance();
        $db_params = $CI->config->item('db_params');
        
        if(is_null($json) || !isset($json->ID) || !isset($json->URL)
                || !isset($json->Size))
        {
            $array = array();
            $array[$this->success] = false;
            $array[$this->error_type] = "Input";
            $array[$this->error_message] = "Invalid inputs!";
            return $array;
        }
        
        
        $conn = pg_pconnect($db_params);
        if (!$conn) 
        {
            $array = array();
            $array[$this->success] = false;
            $array[$this->error_type] = "DB";
            $array[$this->error_message] = "Cannot establish connection!";
            return $array;
        }
        $input = array();
        array_push($input,$json->Ip_address);
        array_push($input,$json->ID);
        array_push($input,$json->URL);
        array_push($input,$json->Size);
        
        $sql = "insert into cil_download_statistics(id, ip_address ,image_id, ".
               " url,size, download_time) ".
               " values(nextval('general_sequence'),$1,$2".
               " ,$3,$4,now())";
        $result = pg_query_params($conn,$sql,$input);
        if (!$result) 
        {
            
            pg_close($conn);
            $array = array();
            $array[$this->success] = false;
            $array[$this->error_type] = "DB";
            $array[$this->error_message] = "pg_last_error()";
            return $array;
        }
        pg_close($conn);
        
        $array = array();
        $array[$this->success] = true;
        return $array;
        
    }
    
    
}
