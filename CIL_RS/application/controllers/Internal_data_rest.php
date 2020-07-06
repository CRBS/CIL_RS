<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once './application/libraries/REST_Controller.php';
require_once 'CILServiceUtil.php';
require_once 'JSONUtil.php';
require_once 'DBUtil.php';


class Internal_data_rest extends REST_Controller
{
    private $success = "success";
    private $error_type = "error_type";
    private $error_message = "error_message";
    
    
    public function is_correct_token_get($username,$token)
    {
        $dbutil = new DBUtil();
        $isCorrectToken = $dbutil->isTokenCorrect($username, $token);
        $array = array();
        $array['is_correct_token'] = false;
        if($isCorrectToken)
            $array['is_correct_token'] = true;
        
        $this->response($array);
    }
    
    public function metadata_get($image_id)
    {
        $dbutil = new DBUtil();
        $mjson = $dbutil->getMetadata($image_id);
        if(is_null($mjson))
        {
            $array = $this->getErrorArray2("Data_error", "Cannot retrieve the data from the database");
            $this->response($array);
            return;
        }
        
        $metadata = $mjson->metadata;
        $json = json_decode($metadata);
        if(is_null($json))
        {
            $array = $this->getErrorArray2("Data_error", "Cannot retrieve the data from the database");
            $this->response($array);
            return;
        }
        
        $this->response($json);
    }
    
    /////////////Helper functions///////////////
    private function getErrorArray2($type, $message)
    {
        $array = array();
        $array['success'] = false;
        $array['error_type'] = $type;
        $array['error_message'] = $message;
        
        
        
        return $array;
    }
    
    ////////////End Helper functions////////////
}

