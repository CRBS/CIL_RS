<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once './application/libraries/REST_Controller.php';
require_once 'CILServiceUtil.php';
require_once 'JSONUtil.php';
require_once 'DBUtil.php';
require_once 'StatisticsDBUtil.php';


class Statistics_rest extends REST_Controller
{
    private $success = "success";
    private $error_type = "error_type";
    private $error_message = "error_message";
    
    private function canWrite()
    {
       $header = $this->input->get_request_header('Authorization');
       $headerCode = str_replace("Basic ", "", $header);
       $headerCode = base64_decode($headerCode); 
        
       $cil_config_file = $this->config->item('cil_config_file'); 
       $config_str = file_get_contents($cil_config_file);
       $json = json_decode($config_str);
       
       if(!is_null($json) && isset($json->cil_service_users) 
            && count($json->cil_service_users) > 0)
       {     
           foreach($json->cil_service_users as $user)
           {
               if(isset($user->username) && isset($user->key)
                  && isset($user->can_write))
               {
                    $mainKey = $user->username.":".$user->key;
                    if(strcmp($headerCode, $mainKey)==0)
                    {
                        if($user->can_write)
                        {
                            return true;
                        }
                    }
               }
           }
       }
       
       return false;
    }
    
    public function web_image_access_post()
    {
        if(!$this->canWrite())
        {
            $array = $this->getErrorArray2("permission", "This user does not have the write permission");
            $this->response($array);
            return;
        }
        
        $input = file_get_contents('php://input', 'r');
        $sdbutil = new StatisticsDBUtil();
        $json = json_decode($input);
        if(is_null($json))
        {
            $array = array();
            $array[$this->success] = false;
            $array[$this->error_type] = "input";
            $array[$this->error_message] = "Invalid input";
            $this->response($array);
        }
        $array = $sdbutil->insertImageAccessLog($json);
        $this->response($array);
    }
    
    
    public function test_get()
    {
        $array = array(); 
        $array['success'] = true;
        $this->response($array);
    }
}

