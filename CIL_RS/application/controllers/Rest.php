<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once './application/libraries/REST_Controller.php';
require_once 'CILServiceUtil.php';
require_once 'JSONUtil.php';
require_once 'DBUtil.php';

class Rest extends REST_Controller
{
    private $success = "success";
    private $error_type = "error_type";
    private $error_message = "error_message";
    
    public function backend_version_get($id=0)
    {
        $array = array();
        $array['elasticsearch'] = $this->config->item('elasticsearchPrefix'); 
        
        $this->response($array);
    }
    
    public function documents_put($id="0")
    {

        $sutil = new CILServiceUtil();
        $jutil = new JSONUtil();
        $input = file_get_contents('php://input', 'r');
        
        
        if(strcmp($id,"0")==0)
        {
             $array = array();
            $array['Error'] = 'Empty ID!';
            $this->response($array);
        }
        
        
        if(is_null($input))
        {
            $mainA = array();
            $mainA['error_message'] ="No input parameter";
            $this->response($mainA);

        }
        $owner = $this->input->get('owner', TRUE);
        if(is_null($owner))
            $owner = "unknown";
        $params = json_decode($input);
        
        if(is_null($params))
        {
            $array = array();
            $array['Error'] = 'Empty document!';
            $this->response($array);
        }
        
        $jutil->setStatus($params,$owner);
        $doc = json_encode($params);
        if(is_null($params))
        {
            $mainA = array();
            $mainA['error_message'] ="Invalid input parameter:".$input;
            $this->response($mainA);

        }
        
        $result = $sutil->updateDocument($id,$doc);
        $this->response($result);
    }

    public function documents_post()
    {
        $sutil = new CILServiceUtil();
        $jutil = new JSONUtil();
        $input = file_get_contents('php://input', 'r');
      
        if(is_null($input))
        {
            $mainA = array();
            $mainA['error_message'] ="No input parameter";
            $this->response($mainA);


        }
        $owner = $this->input->get('owner', TRUE);
        if(is_null($owner))
            $owner = "unknown";
        $params = json_decode($input);
        $jutil->setStatus($params,$owner);
        $doc = json_encode($params);
        if(is_null($params))
        {
            $mainA = array();
            $mainA['error_message'] ="Invalid input parameter:".$input;
            $this->response($mainA);

        }
        $result = $sutil->addDocument($doc);
                
        $this->response($result);
        
    }
    
    public function nextsequence_get($id = "0")
    {
        $sutil = new CILServiceUtil();
        $result = $sutil->nextSequence();

        $this->response($result);
    }
    
    
    public function documents_delete($id)
    {
        $sutil = new CILServiceUtil();
        $result = $sutil->deleteDocument($id);
        $this->response($result); 
    }
    
    
    public function documents_get($id = "0")
    {
      $sutil = new CILServiceUtil();
      $from = 0;
      $size = 10;
      $operator="OR";
      
      $temp = $this->input->get('from', TRUE);
      if(!is_null($temp))
      {
          $from = intval($temp);
      }
      $temp = $this->input->get('size', TRUE);
      if(!is_null($temp))
      {
          $size = intval($temp);
      }
      $search =  $this->input->get('search', TRUE);
      
      $temp = $this->input->get('default_operator', TRUE);
      if(!is_null($temp))
      {
          $operator = $temp;
      }
      
      
      $result = null;
      if(is_null($search))
        $result = $sutil->getDocument($id,$from,$size);
      else 
      {
        $result = $sutil->searchDocuments($search,$from,$size,$operator);
      }
      $this->response($result);
    }
    
    public function adv_data_search_get()
    {
        $sutil = new CILServiceUtil();
        $from = 0;
        $size = 10;

        $temp = $this->input->get('from', TRUE);
        if(!is_null($temp))
        {
            $from = intval($temp);
        }
        $temp = $this->input->get('size', TRUE);
        if(!is_null($temp))
        {
            $size = intval($temp);
        }
        $data = file_get_contents('php://input', 'r');
        
        $json = $sutil->adv_data_search($data, $from, $size);
        $this->response($json);
    }
    
    public function advanced_document_search_get($id="0")
    {
        $sutil = new CILServiceUtil();
        $from = 0;
        $size = 10;

        $temp = $this->input->get('from', TRUE);
        if(!is_null($temp))
        {
            $from = intval($temp);
        }
        $temp = $this->input->get('size', TRUE);
        if(!is_null($temp))
        {
            $size = intval($temp);
        }
        $data = file_get_contents('php://input', 'r');
       
         //$url = $this->config->item('elasticsearchPrefix')."/data/_search"."?q=CIL_CCDB.Status.Deleted:false&from=".$from."&size=".$size;
         $url = $this->config->item('elasticsearchPrefix')."/data/_search?from=".$from."&size=".$size;
         $response = $sutil->just_curl_get_data($url,$data);
         $json = json_decode($response);
        
        //$this->response($data);
        $this->response($json);
    }
    
    
    public function simple_ontology_expansion_get($type="all",$field="Name",$search_value)
    {
        $sutil = new CILServiceUtil();
        $field = "Expansion.".$field;
        $data = file_get_contents('php://input', 'r');
        $url =  $this->config->item('esOntologyExpansionPrefix')."/".$type."/_search";
        $search_value = str_replace("%20", " ", $search_value);
        $main = array();
        $termOuter = array();
        $term = array();
        $term[$field] = $search_value;
        $termOuter['term'] = $term;
        $main['query'] = $termOuter;
        $json_str = json_encode($main);
        $json1 = json_decode($json_str);
        
        $response = $sutil->curl_post($url,$json_str);
        $json = json_decode($response);
        $this->response($json);
    }
    
    public function category_search_get($category_name="cell_process")
    {
        $sutil = new CILServiceUtil();
        $query = file_get_contents('php://input', 'r');
        $json = $sutil->searchCategory($category_name, $query);
        $this->response($json);
    }
    
    public function category_get($category_name="None",$sort_by="Name",
            $order="asc",$from="0",$size="10000")
    {
        
        $sutil = new CILServiceUtil();
        $input_str = file_get_contents('php://input', 'r');

        
        $query = "{\"size\" : ".$size.","."\"from\" : ".$from.",".
                " \"sort\" : [{ \"".$sort_by."\" : \"".$order."\" }],".
                "\"query\":{\"query_string\":{\"query\":\"*\"}}}";

        $json = $sutil->getCategory($category_name, $query);
        $this->response($json);
    }
    
    public function ontology_expansion_get($type="all",$field="Name")
    {
        $sutil = new CILServiceUtil();
        $field = "Expansion.".$field;
        $input_str = file_get_contents('php://input', 'r');
        
        
        if(is_null($input_str) || strlen(trim($input_str))==0)
        {
            $array = $this->getErrorArray2("input", "The input is empty.");
            $this->response($array);
            return;
        }
        $input_json = json_decode($input_str);
        if(is_null($input_json))
        {
            $array = $this->getErrorArray2("input", "The input is in a valid JSON format.");
            $this->response($array);
            return;
        }
        
        $url =  $this->config->item('esOntologyExpansionPrefix')."/".$type."/_search";
        $search_value = $input_json->Search_value;
        $main = array();
        $termOuter = array();
        $term = array();
        $term[$field] = $search_value;
        $termOuter['term'] = $term;
        $main['query'] = $termOuter;
        $json_str = json_encode($main);
        $json1 = json_decode($json_str);
        
        $response = $sutil->curl_post($url,$json_str);
        $json = json_decode($response);
        
        $this->response($json);
    }
    
    
    
    
    public function simple_ontology_search_get($type="all",$field="Name",$search_value)
    {
        $sutil = new CILServiceUtil();
        
        $data = file_get_contents('php://input', 'r');
        $url =  $this->config->item('esOntologyPrefix')."/".$type."/_search";
        $search_value = str_replace("%20", " ", $search_value);
        $main = array();
        $termOuter = array();
        $term = array();
        $term[$field] = $search_value;
        $termOuter['term'] = $term;
        $main['query'] = $termOuter;
        $json_str = json_encode($main);
        $json1 = json_decode($json_str);
        
        $response = $sutil->curl_post($url,$json_str);
        $json = json_decode($response);
        
        $this->response($json);
    }

    
    public function website_settings_get($type="homepage")
    {
        $json = array();
        $sutil = new CILServiceUtil();
        if(strcmp($type, "homepage") == 0)
        {
            $url = $this->config->item('homepage_settings');
            $data = NULL;
            $response = $sutil->just_curl_get_data($url,$data);
            $json = json_decode($response);
        }
        else 
        {
            $json['Found'] =false;
            $json['Error'] = "Invalid type:".$type;
        }
        
        $this->response($json);
    }
    
    
    public function experiments_get($id = "0")
    {
      $sutil = new CILServiceUtil();
      $from = 0;
      $size = 10;
      
      $temp = $this->input->get('from', TRUE);
      if(!is_null($temp))
      {
          $from = intval($temp);
      }
      $temp = $this->input->get('size', TRUE);
      if(!is_null($temp))
      {
          $size = intval($temp);
      }
      $search = $this->input->get('search', TRUE);
      $result = null;
      if(is_null($search))
          $result = $sutil->getExperiment($id,$from,$size);
      else 
      {
         $result = $sutil->searchExperiments($search,$from,$size);
      }
      $this->response($result);
    }
    
    public function experiments_post()
    {
        $sutil = new CILServiceUtil();
        $jutil = new JSONUtil();
        $input = file_get_contents('php://input', 'r');
      
        if(is_null($input))
        {
            $mainA = array();
            $mainA['error_message'] ="No input parameter";
            $this->response($mainA);


        }
        $owner = $this->input->get('owner', TRUE);
        if(is_null($owner))
            $owner = "unknown";
        $params = json_decode($input);
        if(is_null($params))
        {
            $mainA = array();
            $mainA['error_message'] ="Invalid input parameter:".$input;
            $this->response($mainA);
        }
        
        $jutil->setExpStatus($params,$owner);
        
        if(is_null($params))
        {
            $mainA = array();
            $mainA['error_message'] ="Invalid input parameter:".$input;
            $this->response($mainA);

        }
        $result = $sutil->addExperiment($params);
                
        $this->response($result);
    }
    
    public function experiments_put($id="0")
    {
        $sutil = new CILServiceUtil();
        $jutil = new JSONUtil();
        $input = file_get_contents('php://input', 'r');
        
        
        if(strcmp($id,"0")==0)
        {
             $array = array();
            $array['Error'] = 'Empty ID!';
            $this->response($array);
        }
        
        
        if(is_null($input))
        {
            $mainA = array();
            $mainA['error_message'] ="No input parameter";
            $this->response($mainA);

        }
        $owner = $this->input->get('owner', TRUE);
        if(is_null($owner))
            $owner = "unknown";
        $params = json_decode($input);
        
        if(is_null($params))
        {
            $array = array();
            $array['Error'] = 'Empty document!';
            $this->response($array);
        }
        
        $jutil->setExpStatus($params,$owner);
        $doc = json_encode($params);
        if(is_null($params))
        {
            $mainA = array();
            $mainA['error_message'] ="Invalid input parameter:".$input;
            $this->response($mainA);

        }
        
        $result = $sutil->updateExperiment($id,$doc);
        $this->response($result);
    }
    
    public function experiments_delete($id)
    {
        $sutil = new CILServiceUtil();
        $result = $sutil->deleteExperiment($id);
        $this->response($result); 
    }
    
    public function projects_get($id = "0",$type="0")
    {
      $sutil = new CILServiceUtil();
      $from = 0;
      $size = 10;
      
      $temp = $this->input->get('from', TRUE);
      if(!is_null($temp))
      {
          $from = intval($temp);
      }
      $temp = $this->input->get('size', TRUE);
      if(!is_null($temp))
      {
          $size = intval($temp);
      }
      
      $search = $this->input->get('search',TRUE);
      
      
      
      if(strcmp($type, "0")==0 && is_null($search))
        $result = $sutil->getProject($id,$from,$size);
      else if(strcmp($type, "Documents")==0)
      {
          $result = $sutil->getDocumentByProjectID($id,$from,$size);
      }
      else if(!is_null($search))
      {
          $result = $sutil->searchProject($search,$from,$size);
      }
      else
      {
          $result = array();
          $result['Error'] = "Invalid input:".$type;
          
      }
      
       $this->response($result);
      
    }
    
    public function projects_post()
    {
        $sutil = new CILServiceUtil();
        $jutil = new JSONUtil();
        $input = file_get_contents('php://input', 'r');
      
        if(is_null($input))
        {
            $mainA = array();
            $mainA['error_message'] ="No input parameter";
            $this->response($mainA);


        }
        $owner = $this->input->get('owner', TRUE);
        if(is_null($owner))
            $owner = "unknown";
        $params = json_decode($input);
        if(is_null($params))
        {
            $mainA = array();
            $mainA['error_message'] ="Invalid input parameter:".$input;
            $this->response($mainA);
        }
        
        $jutil->setExpStatus($params,$owner);
        
        if(is_null($params))
        {
            $mainA = array();
            $mainA['error_message'] ="Invalid input parameter:".$input;
            $this->response($mainA);

        }
        $result = $sutil->addProject($params);
                
        $this->response($result);
    }
    
    public function projects_put($id="0")
    {
        $sutil = new CILServiceUtil();
        $jutil = new JSONUtil();
        $input = file_get_contents('php://input', 'r');
        
        
        if(strcmp($id,"0")==0)
        {
             $array = array();
            $array['Error'] = 'Empty ID!';
            $this->response($array);
        }
        
        
        if(is_null($input))
        {
            $mainA = array();
            $mainA['error_message'] ="No input parameter";
            $this->response($mainA);

        }
        $owner = $this->input->get('owner', TRUE);
        if(is_null($owner))
            $owner = "unknown";
        $params = json_decode($input);
        
        if(is_null($params))
        {
            $array = array();
            $array['Error'] = 'Empty document!';
            $this->response($array);
        }
        
        $jutil->setExpStatus($params,$owner);
        $doc = json_encode($params);
        if(is_null($params))
        {
            $mainA = array();
            $mainA['error_message'] ="Invalid input parameter:".$input;
            $this->response($mainA);

        }
        
        $result = $sutil->updateProject($id,$doc);
        $this->response($result);
    }
    
    public function projects_delete($id)
    {
        $sutil = new CILServiceUtil();
        $result = $sutil->deleteProject($id);
        $this->response($result); 
    }
    
    
    public function subjects_get($id = "0")
    {
      $sutil = new CILServiceUtil();
      $from = 0;
      $size = 10;
      
      $temp = $this->input->get('from', TRUE);
      if(!is_null($temp))
      {
          $from = intval($temp);
      }
      $temp = $this->input->get('size', TRUE);
      if(!is_null($temp))
      {
          $size = intval($temp);
      }
      $search = $this->input->get('search', TRUE);
      if(is_null($search))
        $result = $sutil->getSubject($id,$from,$size);
      else 
      {
        $result = $sutil->searchSubjects($search,$from,$size);
      }
      $this->response($result);
    }
    
    public function subjects_post()
    {
        $sutil = new CILServiceUtil();
        $jutil = new JSONUtil();
        $input = file_get_contents('php://input', 'r');
      
        if(is_null($input))
        {
            $mainA = array();
            $mainA['error_message'] ="No input parameter";
            $this->response($mainA);


        }
        $owner = $this->input->get('owner', TRUE);
        if(is_null($owner))
            $owner = "unknown";
        $params = json_decode($input);
        if(is_null($params))
        {
            $mainA = array();
            $mainA['error_message'] ="Invalid input parameter:".$input;
            $this->response($mainA);
        }
        
        $jutil->setExpStatus($params,$owner);
        
        if(is_null($params))
        {
            $mainA = array();
            $mainA['error_message'] ="Invalid input parameter:".$input;
            $this->response($mainA);

        }
        $result = $sutil->addSubject($params);
                
        $this->response($result);
    }
    public function subjects_put($id="0")
    {
        $sutil = new CILServiceUtil();
        $jutil = new JSONUtil();
        $input = file_get_contents('php://input', 'r');
        
        
        if(strcmp($id,"0")==0)
        {
             $array = array();
            $array['Error'] = 'Empty ID!';
            $this->response($array);
        }
        
        
        if(is_null($input))
        {
            $mainA = array();
            $mainA['error_message'] ="No input parameter";
            $this->response($mainA);

        }
        $owner = $this->input->get('owner', TRUE);
        if(is_null($owner))
            $owner = "unknown";
        $params = json_decode($input);
        
        if(is_null($params))
        {
            $array = array();
            $array['Error'] = 'Empty document!';
            $this->response($array);
        }
        
        $jutil->setExpStatus($params,$owner);
        $doc = json_encode($params);
        if(is_null($params))
        {
            $mainA = array();
            $mainA['error_message'] ="Invalid input parameter:".$input;
            $this->response($mainA);

        }
        
        $result = $sutil->updateSubject($id,$doc);
        $this->response($result);
    }
    
    public function subjects_delete($id)
    {
        $sutil = new CILServiceUtil();
        $result = $sutil->deleteSubject($id);
        $this->response($result); 
    }
    

     
    public function users_get($id = "0")
    {
      $sutil = new CILServiceUtil();
      $from = 0;
      $size = 10;
      
      $temp = $this->input->get('from', TRUE);
      if(!is_null($temp))
      {
          $from = intval($temp);
      }
      $temp = $this->input->get('size', TRUE);
      if(!is_null($temp))
      {
          $size = intval($temp);
      }
      $search = $this->input->get('search', TRUE);
      if(is_null($search))
        $result = $sutil->getUser($id,$from,$size);
      else 
      {
        $result = $sutil->searchUsers($search,$from,$size);
      }
      $this->response($result);
    }
    
    public function groups_get($id=0)
    {
        $sutil = new CILServiceUtil();
        $result = $sutil->getGroupInfo($id);
        $this->response($result);

    }
    
    public function download_statistics_post()
    {
        $input = file_get_contents('php://input', 'r');
        $dbutil = new DBUtil();
        $json = json_decode($input);
        if(is_null($json))
        {
            $array = array();
            $array[$this->success] = false;
            $array[$this->error_type] = "input";
            $array[$this->error_message] = "Invalid input";
            $this->response($array);
        }
        $array = $dbutil->insertDownloadStatistics($json);
        $this->response($array);
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




?>