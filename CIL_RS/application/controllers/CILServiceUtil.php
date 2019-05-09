<?php
/**
 * This class contains the helper functions for accessing the Elasticsearch
 * data.
 * 
 * PHP version 5.6+
 * 
 * @author Willy Wong
 * @license https://github.com/slash-segmentation/CIL_RS/blob/master/LICENSE.txt
 * @version 1.0
 * 
 */
class CILServiceUtil 
{
    private $found = "found";
    /**
     * This function retrieves data through the doGet method. This function sends extra
     * in CURLOPT_POSTFIELDS. 
     * 
     * @param type $url
     * @param type $data
     * @return string
     */
    public function just_curl_get_data($url,$data)
    {
 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;

    }
    
    /**
     * This function retrieves data from the URL through the doGet method 
     * 
     * @param type $url
     * @param type $data
     * @return string
     */
    public function curl_get($url)
    {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    /**
     * This function sends data to the server through the doPost method. The additional
     * data is included in CURLOPT_POSTFIELDS. This method is usually
     * used for creating a new document in the Elasticsearch.
     * 
     * @param type $url
     * @param type $data
     * @return string
     */
    public function curl_post($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    /**
     * This function executes a CURL XDELETE call. The document in
     * the url is supposed to be deleted.
     * 
     * @param type $url
     * @deprecated This function should not be used in the current version.
     * @return string
     */
    private function curl_delete($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_USERPWD, "cil:32C7D1D31D817734B421CC346EE65");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    /**
     * This function execute a CURL XPUT call. The additional data is
     * included in CURLOPT_POSTFIELDS. This function is usually used
     * to update a document in Elasticsearch.
     * 
     * @param type $url
     * @param type $data
     * @return string
     */
    private function curl_put($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    /**
     * This function is a workaround for generating a new ID in Elasticsearch
     * using the version number.
     * 
     * @return integer
     */
    public function nextSequence()
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix');
        //$url = Config::$elasticsearchHost . "/".Config::$data_index."/sequence/nextid";
        $url = $esPrefix."/sequence/nextid";
        
        $input= "{ \"Sequence\": 1 }";
        $params = json_decode($input);
        $doc = json_encode($params);

        /* $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$doc);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch); */
        $response = $this->curl_put($url,$doc);
        
        //$data = $this->getURL($url);
        //return $data;
        
        $temp = json_decode($response);
        //$temp = json_encode($temp);
        $newid = $temp->_version +5000;
        return $newid."";
    }
    
    /**
     * This function generates a new project ID.
     * 
     * @return interger
     */
    private function getNextProjectID()
    {
       $id = $this->nextSequence()+20409;
       return $id;
    }
    
    
    /**
     * This function generates a new experiment ID.
     * 
     * @return integer
     */
    private function getNextExpID()
    {
       $id = $this->nextSequence()+5329025;
       return $id;
    }
    
    /**
     * This function retrieves the content from a URL.
     * 
     * @param type $url
     * @return string
     */
    public function getURL($url)
    {
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        
        
        $json = null;
        if(!is_null($resp))
            $json = json_decode($resp);
        
        return $json;
    }

    /**
     * This function retrieve all documents that belong to the same group.
     * The input parameter can be any ID in this group.
     * 
     * @param type $id
     * @return JSON object
     */
    public function getGroupInfo($id)
    {
        
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix'); 
        //$url = $esPrefix."/groups/_search?q=_id:".$id;
        $url = $esPrefix."/groups/_search?q=Group.Group_members:".$id;
        $response = $this->curl_get($url);
        if(is_null($response))
        {
            $json = array();
            $json['Error'] = "Cannot connect to ".$esPrefix;
        }
        $json = json_decode($response);
        return $json;
    }
    
    
    public function searchPublicDocument($keywords,$from,$size)
    {
        $keywords = str_replace("/", "+", $keywords);
        $cutil = new CILServiceUtil();
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix'); 
        
        $keywords = urlencode($keywords); 
        /*if(strpos($keywords, '.') != true)
            $keywords = "\"".$keywords."\"";*/
        
        $url = $esPrefix."/data/_search?q=".$keywords."+CIL_CCDB.Status.Is_public:true+CIL_CCDB.Status.Deleted:false&from=".$from."&size=".$size."&default_operator=AND";
        $response = $cutil->curl_get($url);
        $response = $this->handleResponse($response);
        
        
        
        $array = array();
        $array['error'] = true;
        $array['error_message'] = "Unable to parse the query";
        //$array['url'] = $url;
        
        if(is_null($response))
            return $array;
        
        $json = json_decode($response);
        if(is_null($json))
            return $array;
        
        if(isset($json->error))
            return $array;
        
        return $json;
        
        
    }
    
    
    /**
     * This function retrieves a public CIL document only.
     * 
     * @param type $id
     */
    public function getPublicDocument($id)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix'); 
        
        if(strcmp($id,"0")==0)
        {
            $array = array();
            $array[$this->found] = false;
            return $array;
        } 
        
        $url = $esPrefix."/data/".$id;
        $response = $this->curl_get($url);
        $response = $this->handleResponse($response);
        if(is_null($response))
        {
            $array = array();
            $array[$this->found] = false;
            return $array;
        }
        
        $json = json_decode($response);
        if(is_null($response))
        {
            $array = array();
            $array[$this->found] = false;
            return $array;
        }
        
        if(isset($json->found) && $json->found && 
                isset($json->_source->CIL_CCDB->Status->Is_public) &&
                !$json->_source->CIL_CCDB->Status->Is_public)
        {
            $array = array();
            $array[$this->found] = false;
            return $array;
        }
        
        return $json;
    }
    
    /**
     * This function retrieves a document if $id is set. Otherwise,
     * it will retrieve all documents based on the cursor parameter, $from
     * and the request size, $size.
     * 
     * @param type $id
     * @param type $from
     * @param type $size
     * @return JSON object
     */
    public function getDocument($id,$from,$size)
    {
       $CI = CI_Controller::get_instance();
       $esPrefix = $CI->config->item('elasticsearchPrefix'); 
       
       if(strcmp($id,"0")==0)
       {
          
           //$url = Config::$elasticsearchHost . "/".Config::$data_index."/data/_search?q=";
           $url = $esPrefix."/data/_search?q=";
           $url = $url."+CIL_CCDB.Status.Deleted:false&default_operator=AND"."&from=".$from."&size=".$size;
       }
       else
       {
           //$url = Config::$elasticsearchHost . "/".Config::$data_index."/data/".$id;
           $url = $esPrefix."/data/".$id;
       }
       $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        
        
        $json = null;
        if(!is_null($resp))
            $json = json_decode($resp);
        if(!is_null($json) & strcmp($id, "0") !=0)
        {
            /*if(isset($json->hits) && $json->hits->total > 0)
                $json = $json->hits->hits[0]->_source;
            else {
                $json = array();
                $json['Error'] = $id." does not exist!";
            }*/
            if($json->found == true && $json->_source->CIL_CCDB->Status->Deleted ==false)
            {
                return $json->_source;
            }
            else 
                {
                
                
                $json = array();
                $json['Found'] =false;
                
            }
        }
        else if(!is_null($json) & strcmp($id, "0") ==0 )
        {
           if(isset($json->hits))
            $json = $json->hits;
        }
        
        
        return $json;
    }
    
    
    /**
     * This function performs the advanced search query. The JSON query
     * is attached in the parameter, $data. 
     * 
     * @param type $data
     * @param type $from
     * @param type $size
     * @return JSON object
     */
    public function adv_data_search($data,$from,$size)
    {
       $CI = CI_Controller::get_instance();
       $esPrefix = $CI->config->item('elasticsearchPrefix');
       $url = $esPrefix."/data/_search?from=".$from."&size=".$size;
       
       $response = $this->just_curl_get_data($url, $data);
       $json = array();
       $json['Found'] =false;
       
       if(!is_null($response))
         $json = json_decode($response);
       
       return $json;
       
       
    }
    
    public function getDataMapping()
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix');
        
        $url = $esPrefix."/data/_mapping";
        $response = $this->curl_get($url);
        $response = $this->handleResponse($response);
        if(is_null($response))
        {
            return $this->getErrorArray("Unable to get response from this query");
        }
        
        $json = json_decode($response);
        return $json;
    }
    
    
    public function getAllPublicIds($from, $size, $lastModified)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix');
        
        /*$url = $esPrefix."/data/_search?pretty=true";
        $query = "{  ".
                 "\n\"from\" : ".$from.", \"size\" : ".$size.",".
                 "\n\"query\": {".
                 "\n\"match\": {".
                 "\n\"CIL_CCDB.Status.Is_public\": true".
                 "\n}".
                 "\n},".
                 "\n\"stored_fields\": []".
                 "\n}"; */
        $url = $esPrefix."/data/_search?pretty=true&from=".$from."&size=".$size;
        $query = "";
        if(is_null($lastModified))
            $query = "{\"query\":{\"query_string\":{\"query\":\"(CIL_CCDB.Status.Is_public:true AND CIL_CCDB.Status.Deleted:false) AND !(CIL_CCDB.CIL.CORE.TERMSANDCONDITIONS.free_text:copyright*)\"}},\"stored_fields\": []}";
        else
            $query = "{ ".
                     "\"query\": {".
                     "\"bool\": {".
                     "\"must\": {".
                     "\"match\": {".
                     "\"CIL_CCDB.Status.Is_public\": true".
                     "}".
                     "},".
                     "\"must\": {".
                     "\"match\": {".
                     "\"CIL_CCDB.Status.Deleted\": false".
                     "}".
                     "},".
                     "\"must_not\": {".
                     "\"match\": {".
                     "\"CIL_CCDB.CIL.CORE.TERMSANDCONDITIONS.free_text\":\"copyright*\"".
                     "}".
                     "},".
                     "\"filter\": {".
                     "\"range\": {\"CIL_CCDB.Status.Publish_time\": {\"lte\": ".$lastModified."}}".
                     "}".
                     "}".
                     "}".
                     "}";
            
            
        //    $query = "{\"query\":{ \"filtered\": {\"query_string\":{\"query\":\"(CIL_CCDB.Status.Is_public:true AND CIL_CCDB.Status.Deleted:false) AND !(CIL_CCDB.CIL.CORE.TERMSANDCONDITIONS.free_text:copyright*)\"}} ".
        //             " ,\"filter\":{\"range\": {\"CIL_CCDB.Status.Publish_time\": {\"lte\": ".$lastModified."}}}} ".
        //             " ,\"stored_fields\": []}";

        //$json= json_decode($query);
        $response = $this->just_curl_get_data($url, $query);
        $response = $this->handleResponse($response);
        if(is_null($response))
        {
            return $this->getErrorArray("Unable to get response from this query");
        }
        
        $json = json_decode($response);
        return $json;

    }
    
    
    /**
     * This function searches for documents based on the keywords. The 
     * operator (AND or OR) determines whether this is a AND condition query
     * or an OR condition query.
     * 
     * @param type $keywords
     * @param type $from
     * @param type $size
     * @param type $operator
     * @return JSON object
     */
    public function searchDocuments($keywords,$from, $size,$operator)
    {
        
       $CI = CI_Controller::get_instance();
       $esPrefix = $CI->config->item('elasticsearchPrefix'); 
        
        $keywords = urlencode($keywords); 
        if(strpos($keywords, '.') != true)
            $keywords = "\"".$keywords."\"";
        
        //$url = Config::$elasticsearchHost .
        //        "/".Config::$data_index."/data/_search?q=".$keywords."&from=".$from."&size=".$size;
        
        $url = $esPrefix."/data/_search?q=".$keywords."&from=".$from."&size=".$size."&default_operator=".$operator;
        
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        
        
        $json = array();
        $json['Found'] =false;
        
        
        if(!is_null($resp))
            $json = json_decode($resp);
   
        /*     if(isset($json->hits) && $json->hits->total > 0)
             { // $json = $json->hits->hits[0]->_source;
                 $json = $json->hits;
             }
            else {
                //$json = array();
                //$json['Error'] = "Empty result!";
                
                $json = array();
                $json['Found'] =false;
            }  */
      
        
        
        return $json;
       //return $url;
    }
    
    
    /**
     * This function retrieve one experiment document if the id is set.
     * Otherwise, it will return all experiment documents.
     * 
     * @param type $id
     * @param type $from
     * @param type $size
     * @return JSON object
     */
    public function getExperiment($id,$from,$size)
    {
       $CI = CI_Controller::get_instance();
       $esPrefix = $CI->config->item('elasticsearchPrefix'); 
       
       if(strcmp($id,"0")==0)
       {
          
           //$url = Config::$elasticsearchHost . "/".Config::$data_index."/experiments/_search?q=";
           $url = $esPrefix."/experiments/_search?q=";
           $url = $url."+Status.Deleted:false&default_operator=AND"."&from=".$from."&size=".$size;
       }
       else
       {
          
           
           //$url = Config::$elasticsearchHost . "/".Config::$data_index."/experiments/".$id;
           $url = $esPrefix."/experiments/".$id;
       }
       $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        
        
        $json = null;
        if(!is_null($resp))
            $json = json_decode($resp);
        if(!is_null($json) & strcmp($id, "0") !=0)
        {
            //if(isset($json->hits) && $json->hits->total > 0)
            //    $json = $json->hits->hits[0]->_source;
            if($json->found == true && $json->_source->Status->Deleted ==false)
            {
                return $json->_source;
            }
            else 
            {
                $json = array();
                $json['Found'] =false;
            }
        }
        else if(!is_null($json) & strcmp($id, "0") ==0 )
        {
           if(isset($json->hits))
            $json = $json->hits;
        }
        
        
        return $json;
    }
    
    /**
     * This function searches for experiments based on the keywords.
     * 
     * @param type $keywords
     * @param type $from
     * @param type $size
     * @return JSON object
     */
    public function searchExperiments($keywords,$from, $size)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix'); 
        
        
        $keywords = urlencode($keywords); 
        //$url = Config::$elasticsearchHost .
        //        "/".Config::$data_index."/experiments/_search?q=\"".$keywords."\"&from=".$from."&size=".$size;
        $url = $esPrefix."/experiments/_search?q=\"".$keywords."\"&from=".$from."&size=".$size;
        
        
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        
        
        $json = array();
        $json['Found'] =false;
        
        if(!is_null($resp))
            $json = json_decode($resp);
   
        
        
        /* if(isset($json->hits) && $json->hits->total > 0)
             { // $json = $json->hits->hits[0]->_source;
                 $json = $json->hits;
             }
            else {
                //$json = array();
                //$json['Error'] = "Empty result!";
                
                $json = array();
                $json['Found'] =false;
            } 
        */
        
        
        return $json;
       //return $url;
    }
    
   /* public function addDocument($doc)
    {
        $elasticaClient = new \Elastica\Client([
    'connections' => [
        ['transport' => 'Http', 'host' => 'search-elastic-cil-tetapevux3gwwhdcbbrx4zjzhm.us-west-2.es.amazonaws.com', 'port' => 80],
        
        ],
        ]);
        $id = "Test1";
        
        $elasticaIndex = $elasticaClient->getIndex('ccdbv6');
        $elasticaType = $elasticaIndex->getType('data');
        $tweetDocument = new \Elastica\Document($id,$doc);
        $response = $elasticaType->addDocument($tweetDocument);
        
        // Refresh Index
        $elasticaType->getIndex()->refresh();
        
        return $response;
        
    } */
    
    
    /**
     * This function updates the content of a document based on its ID.
     * 
     * @param type $id
     * @param type $doc
     * @return JSON object
     */
    public function updateDocument($id, $doc)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix'); 
        
        //$url = Config::$elasticsearchHost . "/".Config::$data_index."/data/".$id;
        $url = $esPrefix."/data/".$id;

        /*$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$doc);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);*/
        $response = $this->curl_put($url,$doc);
        
        $json = array();
        if(!is_null($response))
            $json = json_decode ($response);
        else
        {
            $json['Error'] = 'Unable to create the document!';
            
        }
        
        return $json;
        
    }
    
    /**
     * This function add a new document and the document ID is automatically
     * generated with the CIL_ prefix.
     * 
     * @param type $doc
     * @return JSON object
     */
     public function addDocument($doc)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix');  
        
        $newid = "CIL_".$this->nextSequence();
        
        //$url = Config::$elasticsearchHost . "/".Config::$data_index."/data/".$newid;
        $url = $esPrefix."/data/".$newid;
        
        
        /*$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$doc);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch); */
        $response = $this->curl_put($url,$doc);   //Using PUT instead POST because we use our CIL_ ID.
        
        $json = array();
        if(!is_null($response))
            $json = json_decode ($response);
        else
        {
            $json['Error'] = 'Unable to create the document!';
            
        }
        
        return $json;
        
    } 
    
    /**
     * This function adds a new experiment record. The experiment ID is
     * automatically generated.
     * 
     * @param type $params
     * @return JSON object
     */
    public function addExperiment($params)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix');  
        
        $newid = "".$this->getNextExpID();
        //$url = Config::$elasticsearchHost . "/".Config::$data_index."/experiments/".$newid;
        $url = $esPrefix."/experiments/".$newid;
        
        if(isset($params->Experiment))
            $params->Experiment->ID = $newid;
        else
        {
            $params->Experiment = array();
            $params->Experiment->ID = $newid;
        }
        
         $exp = json_encode($params);   


        /*$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$exp);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch); */
        $response = $this->curl_put($url,$exp);
         
        
        $json = array();
        if(!is_null($response))
            $json = json_decode ($response);
        else
        {
            $json['Error'] = 'Unable to create the experiment!';
            
        }
        
        return $json;
        
    }  
    
    
    
    /*public function getCIL_user_key($key)
    {
        $elasticaClient = new \Elastica\Client([
    'connections' => [
        ['transport' => 'Http', 'host' => 'search-elastic-cil-tetapevux3gwwhdcbbrx4zjzhm.us-west-2.es.amazonaws.com', 'port' => 80],
        
        ],
        ]);
        
        
        $elasticaIndex = $elasticaClient->getIndex('cil');
        $elasticaType = $elasticaIndex->getType('user_key');
        $query = "key:".$key;
        $result = $elasticaType->search($query);
        return $result;
        //var_dump($result);
        
    }*/
    
    
    
    
    /*public function deleteDocument($id)
    {
        $url = "http://search-elastic-cil-tetapevux3gwwhdcbbrx4zjzhm.us-west-2.es.amazonaws.com/ccdbv6/data/".$id;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $result;
        
    }*/
    
    
    /**
     * This function deletes a document by simply marking this
     * document as deleted.
     * 
     * @param type $id
     * @return JSON object
     */
    public function deleteDocument($id)
    {
       
       $array = $this->getDocument($id,0,1);
       
       
       
       if(array_key_exists("Error", $array))
               return $array;
          
       if(!isset($array->CIL_CCDB->Status))
            $array->CIL_CCDB->Status = array();
       
      // return $array;
       if(isset($array->CIL_CCDB->Status->Deleted))
           $array->CIL_CCDB->Status->Deleted = true;
       else
            $array->CIL_CCDB->Status['Deleted'] = true;
      $doc = json_encode($array);
      return $this->updateDocument($id,$doc);
    } 
    
    
   
    /**
     * This function update the content of the experiment.
     * 
     * @param type $id
     * @param type $exp
     * @return JSON Object
     */
    public function updateExperiment($id, $exp)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix');  
       
        //$url = Config::$elasticsearchHost . "/".Config::$data_index."/experiments/".$id;
        $url = $esPrefix."/experiments/".$id;

        /*$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$exp);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch); */
        $response = $this->curl_put($url,$exp);
        
        $json = array();
        if(!is_null($response))
            $json = json_decode ($response);
        else
        {
            $json['Error'] = 'Unable to create the document!';
            
        }
        
        return $json;
    }
    
    
    /**
     * This function deletes an experiment simply by marking it as
     * deleted.
     * 
     * @param type $id
     * @return JSON object
     */
    public function deleteExperiment($id)
    {
       
       $array = $this->getExperiment($id,0,1);
       
       
       
       if(array_key_exists("Error", $array))
               return $array;
          
       if(!isset($array->Status))
            $array->Status = array();
       
      // return $array;
       if(isset($array->Status->Deleted))
           $array->Status->Deleted = true;
       else
            $array->Status['Deleted'] = true;
      $doc = json_encode($array);
      return $this->updateExperiment($id,$doc);
    } 
    

    /**
     * This function retrieves all documents under the same project ID.
     * 
     * @param type $pid
     * @param type $from
     * @param type $size
     * @return JSON object
     */
    public function getDocumentByProjectID($pid,$from,$size)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix');
        
        //$url = Config::$elasticsearchHost."/".Config::$data_index."/data/_search?q=+CIL_CCDB.CCDB.Project.ID:".
        //        $pid."+CIL_CCDB.Status.Deleted:false&&default_operator=AND&from=".$from."&size=".$size;
        $url = $esPrefix."/data/_search?q=+CIL_CCDB.CCDB.Project.ID:".
                        $pid."+CIL_CCDB.Status.Deleted:false&&default_operator=AND&from=".$from."&size=".$size;
        
        
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        
        
        $json = null;
        if(!is_null($resp))
            $json = json_decode($resp);
       /* if(!is_null($json) & strcmp($pid, "0") !=0)
        {
            if(isset($json->hits) && $json->hits->total > 0)
                $json = $json->hits->hits[0]->_source;
            else {
                $json = array();
                $json['Error'] = $id." does not exist!";
            }
        }
        else if(!is_null($json) & strcmp($id, "0") ==0 )
        {
           if(isset($json->hits))
            $json = $json->hits;
        } */
        
        
        return $json;
    }
    
    
    /**
     * This function searches for projects based on the keywords.
     * 
     * @param type $keywords
     * @param type $from
     * @param type $size
     * @return JSON object
     */
    public function searchProject($keywords,$from, $size)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix');
        
        $keywords = urlencode($keywords); 
        //$url = Config::$elasticsearchHost .
        //        "/".Config::$data_index."/projects/_search?q=\"".$keywords."\"&from=".$from."&size=".$size;
        $url = $esPrefix."/projects/_search?q=\"".$keywords."\"&from=".$from."&size=".$size;
        
        
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        
        
        $json = array();
        $json['Found'] =false;
        
        if(!is_null($resp))
            $json = json_decode($resp);
   
        /*
            if(isset($json->hits) && $json->hits->total > 0)
             { // $json = $json->hits->hits[0]->_source;
                 $json = $json->hits;
             }
            else {
                //$json = array();
                //$json['Error'] = "Empty result!";
                
                $json = array();
                $json['Found'] =false;
            } 
        */
        
        
        return $json;
       //return $url;
    }
    
    
    /**
     * This function retrieves one project if the id is set. Otherwise,
     * it will return all projects.
     * 
     * @param type $id
     * @param type $from
     * @param type $size
     * @return JSON object
     * 
     */
    public function getProject($id,$from,$size)
    {
       $CI = CI_Controller::get_instance();
       $esPrefix = $CI->config->item('elasticsearchPrefix'); 
       
       if(strcmp($id,"0")==0)
       {
           //$url = Config::$elasticsearchHost . "/".Config::$data_index."/projects/_search?q=";
           $url = $esPrefix."/projects/_search?q=";
           $url = $url."+Status.Deleted:false&default_operator=AND"."&from=".$from."&size=".$size;
       }
       else
       {
           //$url = Config::$elasticsearchHost . "/".Config::$data_index."/projects/".$id;
           $url = $esPrefix."/projects/".$id;
           
       }
       //echo $url."\n";
       $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        
        
        $json = null;
        if(!is_null($resp))
            $json = json_decode($resp);
        if(!is_null($json) & strcmp($id, "0") !=0)
        {
           /* if(isset($json->hits) && $json->hits->total > 0)
            {
                $json = $json->hits->hits[0]->_source;
            }
            else {
                
                
                $json = array();
                $json['Error'] = "P5334222 does not exist!";
                
            } */
            
            if(isset($json->found) && $json->found == true && isset($json->_source) && isset($json->_source->Status) && $json->_source->Status->Deleted ==false)
            {
                return $json->_source;
            }
            else 
            {
                
                
                $json = array();
                $json['Found'] =false;
                
            }
                
        }
        else if(!is_null($json) & strcmp($id, "0") ==0 )
        {
           if(isset($json->hits))
            $json = $json->hits;
        }
        
        
        return $json;
    }
    
    /**
     * This function creates a new project. The project ID is automatically
     * created with the "P" prefix.
     * 
     * @param type $params
     * @return JSON object
     */
    public function addProject($params)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix'); 
        
        $newid = "".$this->getNextExpID();
        //$url = Config::$elasticsearchHost . "/".Config::$data_index."/projects/P".$newid;
        $url = $esPrefix."/projects/P".$newid;

        //echo "Add Project URL:".$url."\n";
        if(isset($params->Project))
            $params->Project->ID = 'P'.$newid;
        else
        {
            $params->Project = array();
            $params->Project->ID = 'P'.$newid;
        }
        
         $exp = json_encode($params);   


        /*$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$exp);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);*/
        $response = $this->curl_put($url,$exp);
        
        $json = array();
        if(!is_null($response))
            $json = json_decode ($response);
        else
        {
            $json['Error'] = 'Unable to create the project!';
            
        }
        
        return $json;
        
    }  
    
    /**
     * This function update the content of a project based on its ID.
     * 
     * @param type $id
     * @param type $prj
     * @return JSON object
     */
    public function updateProject($id, $prj)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix'); 
        
        //$url = Config::$elasticsearchHost . "/".Config::$data_index."/projects/".$id;
        $url = $esPrefix."/projects/".$id;
        
        /*$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$prj);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);*/
        $response  = $this->curl_put($url,$prj);
        
        
        
        $json = array();
        if(!is_null($response))
            $json = json_decode ($response);
        else
        {
            $json['Error'] = 'Unable to create the document!';
            
        }
        
        return $json;
    }
    
    /**
     * This function deletes a project by simply marking it as deleted.
     * 
     * @param type $id
     * @return JSON object
     */
    public function deleteProject($id)
    {
       
       $array = $this->getProject($id,0,1);
       
       
       
       if(array_key_exists("Error", $array))
               return $array;
          
       if(!isset($array->Status))
            $array->Status = array();
       
      // return $array;
       if(isset($array->Status->Deleted))
           $array->Status->Deleted = true;
       else
            $array->Status['Deleted'] = true;
      $doc = json_encode($array);
      return $this->updateProject($id,$doc);
    } 
    
    /**
     * This function searches for the ontology items in a category type.
     * $category_name is the type name in Elasticsearch. The real query
     * is attached in the $query parameter.
     * 
     * @param type $category_name
     * @param type $query
     * @return JSON object
     */
    public function searchCategory($category_name,$query)
    {
       $CI = CI_Controller::get_instance();
       $esPrefix = $CI->config->item('esPrefix');  
       $url = $esPrefix."/category/".$category_name."/_search";
       
       $response = $this->just_curl_get_data($url, $query);
       $response = $this->handleResponse($response);
       if(is_null($response))
       {
           $array = $this->getErrorArray("Empty response.");
           $json_str = json_encode($array);
           $json = json_decode($json_str);
           return $json;
       }
       
       $json = json_decode($response);
       if(is_null($json))
       {
           $array = $this->getErrorArray("Empty JSON.");
           $json_str = json_encode($array);
           $json = json_decode($json_str);
           return $json;
       }
       
       return $json;
    }
    
    public function getMicrobial($name,$from, $size,
            $time_series, $still_image, $z_stack, $video)
    {
       $cutil = new CILServiceUtil();
       $CI = CI_Controller::get_instance();
       $esPrefix = $CI->config->item('esPrefix');  
       $url = $esPrefix."/ccdbv8/data/_search?q=CIL_CCDB.Microbial_type:".$name;
       if($time_series)
           $url = $url."+CIL_CCDB.Data_type.Time_series:true";
       if($still_image)
           $url = $url."+CIL_CCDB.Data_type.Still_image:true";
       if($z_stack)
           $url = $url."+CIL_CCDB.Data_type.Z_stack:true";
       if($video)
           $url = $url."+CIL_CCDB.Data_type.Video:true";
       
       $url = $url."&from=".$from."&size=".$size."&default_operator=AND";
       
       $response = $cutil->curl_get($url);
       $response = $this->handleResponse($response);
       
       if(is_null($response))
       {
           $array = $this->getErrorArray("Cannot retrieve any microbial data from the server");
           return $array;
       }
       
       $json = json_decode($response);
       if(is_null($json))
       {
           $array = $this->getErrorArray("Cannot retrieve any microbial data from the server");
           return $array;
       }
       return $json;
    }
    
    
    /**
     * This function searches for the ontology items in a category type.
     * $category_name is the type name in Elasticsearch. The real query
     * is attached in the $query parameter.
     * 
     * @param type $category_name
     * @param type $query
     * @return JSON object
     */
    public function getCategory($category_name, $query)
    {
       $CI = CI_Controller::get_instance();
       $esPrefix = $CI->config->item('esPrefix');  
       $url = $esPrefix."/category/".$category_name."/_search";
       
       $response = $this->just_curl_get_data($url, $query);
       $response = $this->handleResponse($response);
       if(is_null($response))
       {
           $array = $this->getErrorArray("Empty response.");
           $json_str = json_encode($array);
           $json = json_decode($json_str);
           return $json;
       }
       
       $json = json_decode($response);
       if(is_null($json))
       {
           $array = $this->getErrorArray("Empty JSON.");
           $json_str = json_encode($array);
           $json = json_decode($json_str);
           return $json;
       }
       
       /* $array = array();
       $array['url'] = $url;
       $json_str = json_encode($array);
       $json = json_decode($json_str);*/
       
       
       return $json;
    }
    
    /**
     * This function retrieves one subject if the id is set. Otherwise,
     * it will returns all subjects.
     * 
     * @param type $id
     * @param type $from
     * @param type $size
     * @return JSON object
     */
    public function getSubject($id,$from,$size)
    {
       $CI = CI_Controller::get_instance();
       $esPrefix = $CI->config->item('elasticsearchPrefix');  
       
       if(strcmp($id,"0")==0)
       {
           //$url = Config::$elasticsearchHost . "/".Config::$data_index."/subjects/_search?q=";
           $url = $esPrefix."/subjects/_search?q=";
           $url = $url."+Status.Deleted:false&default_operator=AND"."&from=".$from."&size=".$size;
       }
       else
       {
           //$url = Config::$elasticsearchHost . "/".Config::$data_index."/subjects/".$id;
           $url = $esPrefix."/subjects/".$id;
       }
       $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        
        
        $json = null;
        if(!is_null($resp))
            $json = json_decode($resp);
        if(!is_null($json) & strcmp($id, "0") !=0)
        {
            //if(isset($json->hits) && $json->hits->total > 0)
            if($json->found == true && $json->_source->Status->Deleted ==false)
                return $json->_source;
                //$json = $json->hits->hits[0]->_source;
            else {
               // $json = array();
               // $json['Error'] = $id." does not exist!";
                $json = array();
                $json['Found'] =false;
            }
        }
        else if(!is_null($json) & strcmp($id, "0") ==0 )
        {
           if(isset($json->hits))
            $json = $json->hits;
        }
        
        
        return $json;
    }
    
    /**
     * This function searches for subjects based on the keywords.
     * 
     * @param type $keywords
     * @param type $from
     * @param type $size
     * @return JSON object
     */
    public function searchSubjects($keywords,$from, $size)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix');
        
        $keywords = urlencode($keywords); 
        //$url = Config::$elasticsearchHost .
        //        "/".Config::$data_index."/subjects/_search?q=\"".$keywords."\"&from=".$from."&size=".$size;
         $url = $esPrefix."/subjects/_search?q=\"".$keywords."\"&from=".$from."&size=".$size;
        
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        
        
        $json = array();
        $json['Found'] =false;
        
        if(!is_null($resp))
            $json = json_decode($resp);
   
        /*
             if(isset($json->hits) && $json->hits->total > 0)
             { // $json = $json->hits->hits[0]->_source;
                 $json = $json->hits;
             }
            else {
                //$json = array();
                //$json['Error'] = "Empty result!";
                
                $json = array();
                $json['Found'] =false;
            } 
        */
        
        
        return $json;
       //return $url;
    }
    
    /**
     * This function adds a new subject. The subject ID is automatically generated.
     * 
     * @param type $params
     * @return JSON object
     * 
     */
    public function addSubject($params)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix'); 
        
        $newid = "".$this->getNextExpID();
        //$url = Config::$elasticsearchHost . "/".Config::$data_index."/subjects/".$newid;
        $url = $esPrefix."/subjects/".$newid;
        
        if(isset($params->Subject))
            $params->Subject->ID = $newid;
        else
        {
            $params->Subject = array();
            $params->Subject->ID = $newid;
        }
        
         $sub = json_encode($params);   

        /*$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$sub);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);*/
        $response = $this->curl_put($url,$sub);
        
        $json = array();
        if(!is_null($response))
            $json = json_decode ($response);
        else
        {
            $json['Error'] = 'Unable to create the experiment!';
            
        }
        
        return $json;
        
    }  
    
    /**
     * This function updates the content of a subject based on the ID.
     * 
     * @param type $id
     * @param type $exp
     * @return JSON object
     */
    public function updateSubject($id, $exp)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix'); 
        
        //$url = Config::$elasticsearchHost . "/".Config::$data_index."/subjects/".$id;
        $url = $esPrefix."/subjects/".$id;
        
        /*$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$exp);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch); */
        $response = $this->curl_put($url,$exp);
        
        $json = array();
        if(!is_null($response))
            $json = json_decode ($response);
        else
        {
            $json['Error'] = 'Unable to create the document!';
            
        }
        
        return $json;
    }
    
    /**
     * This function deletes a subject simply by marking it as deleted.
     * 
     * @param type $id
     * @return JSON object
     */
    public function deleteSubject($id)
    {
       
       $array = $this->getSubject($id,0,1);
       
       
       
       if(array_key_exists("Error", $array))
               return $array;
          
       if(!isset($array->Status))
            $array->Status = array();
       
      // return $array;
       if(isset($array->Status->Deleted))
           $array->Status->Deleted = true;
       else
            $array->Status['Deleted'] = true;
      $doc = json_encode($array);
      return $this->updateSubject($id,$doc);
    } 
    
    
    /**
     * This function retrieves one user if the id is set. Otherwise,
     * it will return all users.
     * 
     * @param type $id
     * @param type $from
     * @param type $size
     * @return JSON object
     */
    public function getUser($id,$from,$size)
    {
       $CI = CI_Controller::get_instance();
       $esPrefix = $CI->config->item('elasticsearchPrefix'); 
       
       if(strcmp($id,"0")==0)
       {
           //$url = Config::$elasticsearchHost . "/".Config::$data_index."/users/_search?q=*";
           $url = $esPrefix."/users/_search?q=*";
           $url = $url."&from=".$from."&size=".$size;
       }
       else
       {
           //$url = Config::$elasticsearchHost . "/".Config::$data_index."/users/".$id;
           $url = $esPrefix."/users/".$id;
       }
       $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        
        
        $json = null;
        if(!is_null($resp))
            $json = json_decode($resp);
        if(!is_null($json) & strcmp($id, "0") !=0)
        {
            //if(isset($json->hits) && $json->hits->total > 0)
            if($json->found == true)
                return $json->_source;
                //$json = $json->hits->hits[0]->_source;
            else {
               // $json = array();
               // $json['Error'] = $id." does not exist!";
                $json = array();
                $json['Found'] =false;
            }
        }
        else if(!is_null($json) & strcmp($id, "0") ==0 )
        {
           if(isset($json->hits))
            $json = $json->hits;
        }
        
        
        return $json;
    }
    
    /**
     * This function searches for users based the keywords.
     * 
     * @param type $keywords
     * @param type $from
     * @param type $size
     * @return JSON object
     */
    public function searchUsers($keywords,$from, $size)
    {
        $CI = CI_Controller::get_instance();
        $esPrefix = $CI->config->item('elasticsearchPrefix'); 
        
        $keywords = urlencode($keywords); 
        //$url = Config::$elasticsearchHost .
        //        "/".Config::$data_index."/users/_search?q=\"".$keywords."\"&from=".$from."&size=".$size;
        $url = $esPrefix."/users/_search?q=\"".$keywords."\"&from=".$from."&size=".$size;
        
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        
        
        $json = array();
        $json['Found'] =false;
        
        if(!is_null($resp))
            $json = json_decode($resp);
   
        /*
             if(isset($json->hits) && $json->hits->total > 0)
             { // $json = $json->hits->hits[0]->_source;
                 $json = $json->hits;
             }
            else {
                //$json = array();
                //$json['Error'] = "Empty result!";
                
                $json = array();
                $json['Found'] =false;
            } 
        */
        
        
        return $json;
       //return $url;
    }
    
    ////////////Helper functions/////////////
    private function getErrorArray($reason)
    {
        $array = array();
        $error = array();
        $array['error'] = $error;
        $error['reason'] = $reason;
        return $array;
    }
    
    
    private function handleResponse($response)
    {
        if(is_null($response))
            return null;
        
        $response = trim($response);
        if(strlen($response) == 0)
            return null;
        
        return $response;
    }
    
    ///////////End helper functions/////////
}
