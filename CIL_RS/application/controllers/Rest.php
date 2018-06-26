<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once './application/libraries/REST_Controller.php';
require_once 'CILServiceUtil.php';
require_once 'JSONUtil.php';
require_once 'DBUtil.php';

/**
 * This class is the REST controller provides the REST services for the
 * CIL website to access the metadata in the JSON format.
 * 
 * PHP version 5.6+
 * 
 * @author Willy Wong
 * @license https://github.com/slash-segmentation/CIL_RS/blob/master/LICENSE.txt
 * @version 1.0
 * 
 */
class Rest extends REST_Controller
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
    
    public function auth_checking_get()
    {
       $array = array();
       $array['can_write'] = $this->canWrite();
       $this->response($array);
    }
    
    /**
     * This doGET function shows the data type URL in the 
     * Elasticsearch server.
     * 
     */
    public function backend_version_get($id=0)
    {
        $array = array();
        $array['elasticsearch'] = $this->config->item('elasticsearchPrefix'); 
        
        $this->response($array);
    }
    
    /**
     * Get all public IDs
     */
    public function public_ids_get()
    {
        $from = "0";
        $size = "10";
        
        $temp = $this->input->get('from', TRUE);
        if(!is_null($temp) & is_numeric($temp))
           $from = $temp;
        
        $temp = $this->input->get('size', TRUE);
        if(!is_null($temp) & is_numeric($temp))
           $size = $temp;
        
        $cutil = new CILServiceUtil();
        $result = $cutil->getAllPublicIds($from, $size);
        $this->response($result);
          
    }
    
    
    /**
     * This doPUT function updates the CIL document if the ID is defined.
     * 
     * @param type $id string
     * 
     */
    public function documents_put($id="0")
    {
        if(!$this->canWrite())
        {
            $array = $this->getErrorArray2("permission", "This user does not have the write permission");
            $this->response($array);
            return;
        }

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

    /**
     * This doPost function creates a new CIL document based on the data
     * input. The new ID is auto-generated.
     */
    public function documents_post()
    {
        if(!$this->canWrite())
        {
            $array = $this->getErrorArray2("permission", "This user does not have the write permission");
            $this->response($array);
            return;
        }
        
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
    
    /**
     * This functions generates a new ID.
     * 
     * @param type $id string
     */
    public function nextsequence_get($id = "0")
    {
        $sutil = new CILServiceUtil();
        $result = $sutil->nextSequence();

        $this->response($result);
    }
    
    /**
     * This doDelete function deletes a CIL document simply by marking as deleted.
     * 
     * @param type $id string
     */
    public function documents_delete($id)
    {
        if(!$this->canWrite())
        {
            $array = $this->getErrorArray2("permission", "This user does not have the write permission");
            $this->response($array);
            return;
        }
        $sutil = new CILServiceUtil();
        $result = $sutil->deleteDocument($id);
        $this->response($result); 
    }
    
    /**
     * This doGet function retrieves a CIL document if the ID is set. Otherwise,
     * it will returns all CIL documents up to 1000 records.
     * 
     * @param type $id string
     */
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
    
    /**
     * This doGet function peforms the advanced ontology search based on the
     * query in the input data.
     * 
     */
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
    
    /**
     * This doGet function peforms the advanced ontology search based on the
     * query in the input data.
     * 
     */
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
    
    
    /**
     * This doGet function peforms the advanced ontology search based on the
     * parameters in the URL. $type is the index type in Elastic search.
     * $field is the data field in JSON. $search_value is the value that
     * you want to search for in the data field.
     * 
     */
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
    
    /**
     * This doGET function performs an advanced search based on the query
     * in the data field. $category_name limits which category or type
     * in Elasticsearch to search for.
     * 
     * @param type $category_name string
     */
    public function category_search_get($category_name="cell_process")
    {
        $sutil = new CILServiceUtil();
        $query = file_get_contents('php://input', 'r');
        $json = $sutil->searchCategory($category_name, $query);
        $this->response($json);
    }
    
    /**
     * This doGet function retrieves all documents in a particular category
     * or Elasticsearch type based on the URL parameters.
     * 
     * @param type $category_name string
     * @param type $sort_by string 
     * @param type $order string (asc or desc)
     * @param type $from string (where the cursor should to start)
     * @param type $size string (The maximum returned results in this query)
     */
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
    
    /*
    * This doGet function performs an ontology expansion based on the
    * URL parameters and the input data field in JSON. $type is the category
    * or type in Elasticsearch. 
    * 
    * @param type $type
    * @param string $field
    * @return type 
    */
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
    
    
    
    /**
     * This doGet function performs an ontology search based on the URL
     * parameters. $type is the category or the type in Elasticsearch.
     * $field is the key name in the JSON object. $search_value is
     * the search value to match with.
     * 
     * @param type $type
     * @param type $field
     * @param type $search_value
     */
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

    /**
     * This doGet function returns the website homepage settings.
     * 
     * @param type $type
     */
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
    
    /**
     * This doGet function retrieves an experiment object if the ID is
     * set. Otherwise, it will return all experiment objects up to
     * 1000 results.
     * 
     * @param type $id
     */
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
    
    /**
     * This doPost function creates a new experiment object based on
     * the data input in JSON. 
     * 
     */
    public function experiments_post()
    {
        if(!$this->canWrite())
        {
            $array = $this->getErrorArray2("permission", "This user does not have the write permission");
            $this->response($array);
            return;
        }
        
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
    
    
    /**
     * This doPut function updates an experiment object based on
     * the input data in JSON.
     * 
     * @param type $id
     */
    public function experiments_put($id="0")
    {
        if(!$this->canWrite())
        {
            $array = $this->getErrorArray2("permission", "This user does not have the write permission");
            $this->response($array);
            return;
        }
        
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
    
    /**
     * This doDelete function deletes an experiment object by simply
     * marking it as deleted.
     * 
     * @param type $id
     */
    public function experiments_delete($id)
    {
        if(!$this->canWrite())
        {
            $array = $this->getErrorArray2("permission", "This user does not have the write permission");
            $this->response($array);
            return;
        }
        $sutil = new CILServiceUtil();
        $result = $sutil->deleteExperiment($id);
        $this->response($result); 
    }
    
    /**
     * This doGet function retrieves a project object if the ID is set.
     * Otherwise, it will return all project objects up to 1000 records.
     * 
     * @param type $id
     * @param type $type
     */
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
    
    /**
     * This doPost function creates a new project object based on the input
     * data in JSON.
     * 
     */
    public function projects_post()
    {
        if(!$this->canWrite())
        {
            $array = $this->getErrorArray2("permission", "This user does not have the write permission");
            $this->response($array);
            return;
        }
        
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
    
    /**
     * This doPut function update the project object based on the input
     * data in JSON and the ID in the URL.
     * 
     * @param type $id string
     */
    public function projects_put($id="0")
    {
        
        if(!$this->canWrite())
        {
            $array = $this->getErrorArray2("permission", "This user does not have the write permission");
            $this->response($array);
            return;
        }
        
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
    
    /**
     * This doDelete function deletes a project record simply by marking
     * it as deleted.
     * 
     * @param type $id
     */
    public function projects_delete($id)
    {
        if(!$this->canWrite())
        {
            $array = $this->getErrorArray2("permission", "This user does not have the write permission");
            $this->response($array);
            return;
        }
        $sutil = new CILServiceUtil();
        $result = $sutil->deleteProject($id);
        $this->response($result); 
    }
    
    /**
     * This doGet function retrieves a subject record if the ID is set.
     * Otherwise, it will return all subject records up to 1000 results.
     * 
     * @param type $id string
     */
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
    
    /**
     * This doPost function creates a new subject record based on
     * the input data in JSON.
     */
    public function subjects_post()
    {
        if(!$this->canWrite())
        {
            $array = $this->getErrorArray2("permission", "This user does not have the write permission");
            $this->response($array);
            return;
        }
        
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
    
    /**
     * This doPut function updates a subject record based on the input 
     * data in JSON and the ID in the URL parameter.
     * 
     * @param type $id string
     */
    public function subjects_put($id="0")
    {
        if(!$this->canWrite())
        {
            $array = $this->getErrorArray2("permission", "This user does not have the write permission");
            $this->response($array);
            return;
        }
        
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
    
    /**
     * This doDelete function deletes a subject record simply by
     * marking it as deleted.
     * 
     * @param type $id String
     */
    public function subjects_delete($id)
    {
        if(!$this->canWrite())
        {
            $array = $this->getErrorArray2("permission", "This user does not have the write permission");
            $this->response($array);
            return;
        }
        $sutil = new CILServiceUtil();
        $result = $sutil->deleteSubject($id);
        $this->response($result); 
    }
    

    /**
     * This doGet function retrieves a list of CIL contributors.
     * 
     * @param type $id string
     */
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
    
    /**
     * This doGet function retrieves a group record containing a list of 
     * image IDs if the ID is specified. Otherwise, it will return all 
     * group records up to 1000 results.
     * 
     * @param type $id string
     */
    public function groups_get($id=0)
    {
        $sutil = new CILServiceUtil();
        $result = $sutil->getGroupInfo($id);
        $this->response($result);

    }
    
    /**
     * This doPost function creates a new record in PostgreSQL database
     * to track the download statistics.
     */
    public function download_statistics_post()
    {
        if(!$this->canWrite())
        {
            $array = $this->getErrorArray2("permission", "This user does not have the write permission");
            $this->response($array);
            return;
        }
        
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