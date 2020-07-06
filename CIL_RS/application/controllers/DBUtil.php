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
    
    private $id = 0;
    private $metadata = "metadata";
    private $image_name = "image_name";
    
    
    public function isTokenCorrect($username,$token)
    {
        $CI = CI_Controller::get_instance();
        $db_params = $CI->config->item('cil_metadata_db');
        $conn = pg_pconnect($db_params);
        if (!$conn) 
            return false;
        
        $sql = "select id from cil_auth_tokens where username = $1 and token = $2";
        $input = array();
        array_push($input,$username);
        array_push($input,$token);
    
        $result = pg_query_params($conn,$sql,$input);
        if (!$result) 
        {
            pg_close($conn);
            return false;
        }
        
        $isCorrect = false;
        if($row = pg_fetch_row($result))
        {
            $isCorrect = true;
        }
        pg_close($conn);
        return $isCorrect;
        
    }
    
    
    public function getMetadata($image_id)
    {
        $CI = CI_Controller::get_instance();
        $db_params = $CI->config->item('cil_metadata_db');
        $array = array();
        $conn = pg_pconnect($db_params);
        if (!$conn) 
        {   
            $array[$this->success] = false;
            $json_str = json_encode($array);
            $json = json_decode($json_str);
            return $json;
        }
        $sql = "select numeric_id, metadata, image_name from cil_metadata where image_id = $1";
        $input = array();
        array_push($input, $image_id);
        $result = pg_query_params($conn,$sql,$input);
        if(!$result) 
        {
            pg_close($conn);
            $array[$this->success] = false;
            $json_str = json_encode($array);
            $json = json_decode($json_str);
            return $json;
        }
        
        if($row = pg_fetch_row($result))
        {
            
            $array[$this->success] = true;
            $array[$this->id] = intval($row[0]);
            if(is_null($row[1]))
                $array[$this->metadata] = "";
            else
                $array[$this->metadata] = $row[1];
            
            if(is_null($row[2]))
                $array[$this->image_name] = "";
            else
                $array[$this->image_name] = $row[2];
            
        }
        else
        {
            $array[$this->success] = false;
            $json_str = json_encode($array);
            $json = json_decode($json_str);
            return $json;
        }

        pg_close($conn);
        $json_str = json_encode($array);
        $json = json_decode($json_str);
        return $json; 
    }
    
    
    public function insertImageViewerStatistics($json)
    {
        $CI = CI_Controller::get_instance();
        $db_params = $CI->config->item('db_params');
        
        if(is_null($json) || !isset($json->ID) || !isset($json->Ip_address))
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
        array_push($input,$json->ID);
        array_push($input,$json->Ip_address);
        
        $sql = "insert into cil_image_viewer_stats(id,image_id,ip_address,event_time) ".
                " values(nextval('general_sequence'),$1,$2,now())";
        
        
        $result = pg_query_params($conn,$sql,$input);
        if (!$result) 
        {
            
            pg_close($conn);
            $array = array();
            $array[$this->success] = false;
            $array[$this->error_type] = "DB";
            $array[$this->error_message] = pg_last_error(); 
            return $array;
        }
        pg_close($conn);
        
        $array = array();
        $array[$this->success] = true;
        return $array;
    }
    
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
            $array[$this->error_message] = pg_last_error(); 
            return $array;
        }
        pg_close($conn);
        
        $array = array();
        $array[$this->success] = true;
        return $array;
        
    }
    
    
}
