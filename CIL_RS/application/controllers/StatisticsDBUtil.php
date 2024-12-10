<?php
class StatisticsDBUtil
{
    private $success = "success";
    private $error_type = "error_type";
    private $error_message = "error_message";
    
    private $id = 0;
    private $metadata = "metadata";
    private $image_name = "image_name";
    
    
    public function insertImageAccessLog($json)
    {
        $CI = CI_Controller::get_instance();
        $db_params = $CI->config->item('db_params');
        
        if(is_null($json)  || !isset($json->Base_url)
                || !isset($json->Image_id) || !isset($json->Ip_address))
        {
            $array = array();
            $array[$this->success] = false;
            $array[$this->error_type] = "Input";
            $array[$this->error_message] = "Invalid inputs!";
            return $array;
        }
     
        $conn = pg_connect($db_params);
        if (!$conn) 
        {
            $array = array();
            $array[$this->success] = false;
            $array[$this->error_type] = "DB";
            $array[$this->error_message] = "Cannot establish connection!";
            return $array;
        }
        
        $userAgent = "Unknown";
        if(isset($json->User_agent))
        {
            $userAgent = $json->User_agent;
        }
        
        $input = array();
        array_push($input,$json->Base_url);
        array_push($input,$json->Image_id);
        array_push($input,$json->Ip_address);
        array_push($input,$userAgent);
        
        
        $sql = "insert into cil_image_access_log(id, base_url, image_id, ip_address, access_time, user_agent) ".
               " values(nextval('cil_w_log_seq'), $1, $2, $3, now(), $4)";
        
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

