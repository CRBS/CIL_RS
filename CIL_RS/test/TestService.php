<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers Email
 */
final class TestService extends TestCase
{
    
    public $index = "ccdbv8";
    
    public static $elasticsearchHost = "http://localhost:8080"; //Development server
    //public static $elasticsearchHost = "https://cilia.crbs.ucsd.edu"; //Staging server
    //public static $elasticsearchHost = "https://tendril.crbs.ucsd.edu"; //Production server
    
    //Setting the configuration file location
    private $cil_config_file = "C:/data/microbial_service_config.json";
    
    //public $context = "/CIL_RS/index.php/rest";
    public $context = "/rest";
    
    
    /**
     * This is a helpping method to call CURL PUT request with the username and key
     * 
     * @param type $url
     * @param type $data
     * @return type
     */
    private function curl_put($url, $data)
    {
        $json_str = file_get_contents($this->cil_config_file);
        $json = json_decode($json_str);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $json->cil_unit_tester);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    
    /**
     * This is a helpping method to calll CURL Delete request with user-name and password
     * 
     * @param type $url
     * @return type
     */
    private function curl_delete($url)
    {
        $json_str = file_get_contents($this->cil_config_file);
        $json = json_decode($json_str);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $json->cil_unit_tester);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    /**
     * This is a helpping method to call CURL POST request with the user name and password.
     * @param type $url
     * @param type $data
     * @return type
     * 
     */
    private function curl_post($url, $data)
    {
        $json_str = file_get_contents($this->cil_config_file);
        $json = json_decode($json_str);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $json->cil_unit_tester);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    /**
     * This is a helpping method to call CURL GET request with the user name and key
     * 
     * @param type $url
     * @return type
     * 
     */
    private function curl_get($url)
    {
        $json_str = file_get_contents($this->cil_config_file);
        $json = json_decode($json_str);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $json->cil_unit_tester);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    /**
     * This is the helping function 
     * 
     * @param type $url
     * @param type $data
     * @return type
     * 
     */
    private function just_curl_get_data($url,$data)
    {
        
        $json_str = file_get_contents($this->cil_config_file);
        $json = json_decode($json_str);
 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($doc)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $json->cil_unit_tester);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;

    }
   
    

    
    public function testAdvSearchOntology()
    {
        echo "\nTesting Eukaryotic_cell query";
        $input = file_get_contents('Eukaryotic_cell.json');
        //echo $input;
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/advanced_document_search";
        $url = TestService::$elasticsearchHost . $this->context."/advanced_document_search";
        
        //echo "\n".$url."\n";
        
        $params = json_decode($input);
        $data = json_encode($params);   
        
        
        $response = $this->just_curl_get_data($url,$data);
        $response = trim($response);
        
        //echo "\n------------Response------------\n";
        //echo $response;
        //echo "\n------------Response------------";
        
        $json = json_decode($response);
        $total = $json->hits->total;
        
        //echo "\n------------total:".$total;*/
        
         $this->assertTrue(($total > 0));
       
    }
    
    public function testWebsite_homepage_settings()
    {
        echo "\nTesting website_homepage_settings";
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/website_settings/homepage";
        $url = TestService::$elasticsearchHost . $this->context."/website_settings/homepage";
        
        $data = NULL;
        $response = $this->just_curl_get_data($url,$data);
        $response = trim($response); 
        
        //echo "\n------------Response------------\n";
        //echo $response;
        //echo "\n------------Response------------";
        $json = json_decode($response);
        
        $this->assertTrue($json->found);
        
    }
    
   
    public function testSimple_ontology_search_by_name()
    {
        echo "\nTesting simple_ontology_search by name...";
        //$url = TestService::$elasticsearchHost."/CIL_RS/index.php/rest/simple_ontology_search/biological_processes/Name/cell%20death";
        $url = TestService::$elasticsearchHost. $this->context."/simple_ontology_search/biological_processes/Name/cell%20death";
        
        $response = $this->curl_get($url);
        //echo $response;
        $result = json_decode($response);
        if(isset($result->hits->total) && $result->hits->total > 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    }
    
    public function testSimple_ontology_search_by_synonym()
    {
        echo "\nTesting simple_ontology_search by synonym...";
        //$url = TestService::$elasticsearchHost."/CIL_RS/index.php/rest/simple_ontology_search/biological_processes/Synonyms/necrosis";
        $url = TestService::$elasticsearchHost.$this->context."/simple_ontology_search/biological_processes/Synonyms/necrosis";
        
        $response = $this->curl_get($url);
        //echo $response;
        $result = json_decode($response);
        if(isset($result->hits->total) && $result->hits->total > 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    }
    
    public function testSimple_ontology_search_by_wrong_name()
    {
        echo "\nTesting simple_ontology_search by wrong name...";
        //$url = TestService::$elasticsearchHost."/CIL_RS/index.php/rest/simple_ontology_search/biological_processes/Name/tesetsgsgrert";
        $url = TestService::$elasticsearchHost.$this->context."/simple_ontology_search/biological_processes/Name/tesetsgsgrert";
        
        
        $response = $this->curl_get($url);
        //echo $response;
        $result = json_decode($response);
        if(isset($result->hits->total) && $result->hits->total == 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    }
    
    
    public function testSimple_ontology_search_by_wrong_synonym()
    {
        echo "\nTesting simple_ontology_search by wrong synonym...";
        //$url = TestService::$elasticsearchHost."/CIL_RS/index.php/rest/simple_ontology_search/biological_processes/Synonyms/tsetafrwer34";
        $url = TestService::$elasticsearchHost.$this->context."/simple_ontology_search/biological_processes/Synonyms/tsetafrwer34";
        
        
        $response = $this->curl_get($url);
        //echo $response;
        $result = json_decode($response);
        if(isset($result->hits->total) && $result->hits->total == 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    }
    
    
    public function testSimple_ontology_expansion_by_name()
    {
        echo "\nTesting Simple_ontology_expansion by name...";
        //$url = TestService::$elasticsearchHost."/CIL_RS/index.php/rest/simple_ontology_expansion/biological_processes/Name/cell%20death";
        $url = TestService::$elasticsearchHost.$this->context."/simple_ontology_expansion/biological_processes/Name/cell%20death";
        
        $response = $this->curl_get($url);
        //echo $response;
        $result = json_decode($response);
        if(isset($result->hits->total) && $result->hits->total > 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    }
    
    public function testSimple_ontology_expansion_by_synonym()
    {
        echo "\nTesting simple_ontology_expansion by synonym...";
        //$url = TestService::$elasticsearchHost."/CIL_RS/index.php/rest/simple_ontology_expansion/biological_processes/Synonyms/necrosis";
        $url = TestService::$elasticsearchHost.$this->context."/simple_ontology_expansion/biological_processes/Synonyms/necrosis";
        
        
        $response = $this->curl_get($url);
        //echo $response;
        $result = json_decode($response);
        if(isset($result->hits->total) && $result->hits->total > 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    }
    
    
    public function testSimple_ontology_expansion_by_wrong_name()
    {
        echo "\nTesting Simple_ontology_expansion_by_wrong_name...";
        //$url = TestService::$elasticsearchHost."/CIL_RS/index.php/rest/simple_ontology_expansion/biological_processes/Name/ataetawesdfgf";
        $url = TestService::$elasticsearchHost.$this->context."/simple_ontology_expansion/biological_processes/Name/ataetawesdfgf";
        
        
        $response = $this->curl_get($url);
        //echo $response;
        $result = json_decode($response);
        if(isset($result->hits->total) && $result->hits->total == 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    }
    
    public function testSimple_ontology_expansion_by_wrong_synonym()
    {
        echo "\nTesting Simple_ontology_expansion_by_wrong_synonym...";
        //$url = TestService::$elasticsearchHost."/CIL_RS/index.php/rest/simple_ontology_expansion/biological_processes/Synonyms/teatesagdfgdfg";
        $url = TestService::$elasticsearchHost.$this->context."/simple_ontology_expansion/biological_processes/Synonyms/teatesagdfgdfg";
        
        $response = $this->curl_get($url);
        //echo $response;
        $result = json_decode($response);
        if(isset($result->hits->total) && $result->hits->total == 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    }
    
    public function testAdvDataSearch()
    {
        echo "\nTesting the advanced data search...";
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/subjects?search=mouse";
        $url = TestService::$elasticsearchHost . $this->context."/adv_data_search";
        $data = "{\"query\":{\"terms\":{\"_id\":[\"CIL_12414\",\"CIL_12415\",\"CIL_12416\",\"CIL_12417\",\"CIL_13523\",\"CIL_13524\"]}}}";
        $response = $this->just_curl_get_data($url,$data);
        //echo "\n-------Response:".$response;
        $result = json_decode($response);
        $total = $result->hits->total;
        
        if($total > 0 && $total < 10)
           $this->assertTrue(true);
        else
           $this->assertTrue(false);
    }
    
    public function testGroupInfo()
    {
        echo "\nTesting testGroupInfo";
        $url = TestService::$elasticsearchHost . $this->context."/groups/36613";
        echo "\nGroupURL:".$url;
        $response = $this->curl_get($url);
        if(is_null($response))
        {
            echo "Cannot connection ".$url;
            $this->assertTrue(false);
        }
        
        $result = json_decode($response);
        $total = $result->hits->total;
        if($total > 0 && $total < 10)
           $this->assertTrue(true);
        else
           $this->assertTrue(false);
    }
    
    
    
    ///////////Testing ontology expansion///////////////////
    public function testOntology_expansion_by_name()
    {
        echo "\n testOntology_expansion_by_name...";
        //$url = TestService::$elasticsearchHost."/CIL_RS/index.php/rest/simple_ontology_expansion/biological_processes/Name/cell%20death";
        $url = TestService::$elasticsearchHost.$this->context."/ontology_expansion/biological_processes/Name";
        $array = array();
        $array['Search_value'] = "cell death";
        $json_str = json_encode($array);
        $response = $this->just_curl_get_data($url, $json_str);
        //echo $response;
        $result = json_decode($response);
        if(isset($result->hits->total) && $result->hits->total > 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    }
    
    
    public function testOntology_expansion_by_name_with_comma()
    {
        echo "\n testOntology_expansion_by_name_with_comma...";
        //$url = TestService::$elasticsearchHost."/CIL_RS/index.php/rest/simple_ontology_expansion/biological_processes/Name/cell%20death";
        $url = TestService::$elasticsearchHost.$this->context."/ontology_expansion/mouse_gross_anatomies/Name";
        $array = array();
        $array['Search_value'] = "TS20,extraembryonic component";
        $json_str = json_encode($array);
        $response = $this->just_curl_get_data($url, $json_str);
        //echo $response;
        $result = json_decode($response);
        if(isset($result->hits->total) && $result->hits->total > 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    }
    
    
    public function testOntology_expansion_by_synonym()
    {
        echo "\n testOntology_expansion_by_synonym...";
        //$url = TestService::$elasticsearchHost."/CIL_RS/index.php/rest/simple_ontology_expansion/biological_processes/Synonyms/necrosis";
        $url = TestService::$elasticsearchHost.$this->context."/ontology_expansion/biological_processes/Synonyms";
        $array = array();
        $array['Search_value'] = "necrosis";
        $json_str = json_encode($array);
        $response = $this->just_curl_get_data($url, $json_str);
        //echo $response;
        $result = json_decode($response);
        if(isset($result->hits->total) && $result->hits->total > 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    }
    
    public function testOntology_expansion_by_wrong_name()
    {
        echo "\n testOntology_expansion_by_wrong_name...";
        $url = TestService::$elasticsearchHost.$this->context."/ontology_expansion/biological_processes/Name";
        $array = array();
        $array['Search_value'] = "77777777";
        $json_str = json_encode($array);
        $response = $this->just_curl_get_data($url, $json_str);
        //echo "\nRESPONSE:".$response."----";
        $result = json_decode($response);
        if(isset($result->hits->total) && $result->hits->total == 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    }
    
    public function testOntology_expansion_by_wrong_synonym()
    {
        echo "\n testOntology_expansion_by_wrong_synonym...";
        $url = TestService::$elasticsearchHost.$this->context."/ontology_expansion/biological_processes/Synonyms";
        $array = array();
        $array['Search_value'] = "77777777";
        $json_str = json_encode($array);
        $response = $this->just_curl_get_data($url, $json_str);
        //echo $response;
        $result = json_decode($response);
        if(isset($result->hits->total) && $result->hits->total == 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    }
    ///////////End testing ontology expansion///////////////////
    
    
    //////////Testing the category name sorting////////////////
    public function testCategoryNameSorting()
    {
        echo "\n testCategoryNameSorting...";
        $url = TestService::$elasticsearchHost.$this->context."/category/cell_process/Name/asc/0/10000";
        $response = $this->curl_get($url);
        //echo $response;
        $result = json_decode($response);
        if(isset($result->error))
        {
            echo "\nError in testCategoryNameSorting";
            $this->assertTrue(false);
        }
        
        if(isset($result->hits->total) && $result->hits->total > 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    }
    
    public function testCategoryTotalSorting()
    {
        echo "\n testCategoryTotalSorting...";
        $url = TestService::$elasticsearchHost.$this->context."/category/cell_process/Total/desc/0/10000";
        $response = $this->curl_get($url);
        //echo $response;
        $result = json_decode($response);
        if(isset($result->error))
        {
            echo "\nError in testCategoryTotalSorting";
            $this->assertTrue(false);
        }
        
        if(isset($result->hits->total) && $result->hits->total > 0)
            $this->assertTrue(true);
        else 
        {
            $this->assertTrue(false);
        }
    } 
    
    /////////End testing the category name sorting////////////////
    
    /////////Testing the category search//////////////////////////
    public function testCategorySearchByName()
    {
        echo "\n testCategorySearchByName...";
        $url = TestService::$elasticsearchHost.$this->context."/category_search";
        $query = "{\"query\": { ".
                "\"term\" : { \"Name\" : \"Cell Death\" } ". 
                "}}";
        $response = $this->just_curl_get_data($url, $query);
        $response = $this->handleResponse($response);
        //echo "\n testCategorySearchByName response:".$response."---";
        if(is_null($response))
        {
            echo "\n testCategorySearchByName response is empty";
            $this->assertTrue(false);
        }
        
        $result = json_decode($response);
        if(is_null($result))
        {
            echo "\n testCategorySearchByName json is invalid";
            $this->assertTrue(false);
        }
        
        if(isset($result->hits->total) && $result->hits->total > 0)
            $this->assertTrue(true);
        else 
            $this->assertTrue(false);
        
    }
    
    /////////End testing the category search//////////////////////////
    public function testTrackDownloads()
    {
        echo "\ntestTrackDownloads";
        $json_str = "{\"Ip_address\":\"::1\",\"ID\":\"13007\",\"URL\":\"https://cildata.crbs.ucsd.edu/media/images/13007/13007.tif\",\"Size\":\"4400000\"}";
        $url = TestService::$elasticsearchHost."/rest/download_statistics";
        $response = $this->curl_post($url, $json_str);
        //echo "\n-------testTrackDownloads Response:".trim($response)."------";
        $json = json_decode($response);
        $this->assertTrue($json->success);
    }
    
    
    public function testFailedTrackDownload()
    {
        echo "\ntestFailedTrackDownload";
        $json_str = "testing";
        $url = TestService::$elasticsearchHost."/rest/download_statistics";
        $response = $this->curl_post($url, $json_str);
        //echo "\n-------testFailedTrackDownload Response:".trim($response)."------";
        $json = json_decode($response);
        if(!$json->success)
        {
            echo "\ntestFailedTrackDownload:".$json->error_message;
        }
        $this->assertTrue(!$json->success);
    }
   
    /////////Testing download statistics////////////////////////////
    
    
    /////////End testing download statistics////////////////////////////
    
    ////////Helper functions/////////////////////////////////////
    private function handleResponse($response)
    {
        if(is_null($response))
            return null;
        
        $response = trim($response);
        if(strlen($response) == 0)
            return null;
        
        return $response;
    }
    ////////End helper functions///////////////////////////////////
}