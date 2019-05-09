<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers Email
 */
final class TestService extends TestCase
{
    
    public $index = "ccdbv8";
    
    //public static $elasticsearchHost = "http://localhost:8080"; //Development server
    public static $elasticsearchHost = "https://cilia.crbs.ucsd.edu"; //Staging server
    //public static $elasticsearchHost = "https://cil-api.crbs.ucsd.edu"; //Production server
    
    //Setting the configuration file location
    private $cil_config_file = "C:/data/cil_service_config.json";
    
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
    /*
     * This is a helpping method to get a Project in String type.
     * 
     */
    private function getProject($id)
    {
        ///$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/projects/".$id;
        $url = TestService::$elasticsearchHost . $this->context. "/projects/".$id;
        
        echo "\ngetProject URL:".$url;
        $response = $this->curl_get($url);
        //echo "\n-------getProject Response:".$response;
        //$result = json_decode($response);
        
        return $response;
    }
    
    
    /*
     * This is a helpping method to get a Project in String type.
     * 
     */
    private function getExperiment($id)
    {
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/experiments/".$id;
        $url = TestService::$elasticsearchHost . $this->context. "/experiments/".$id;
        
        $response = $this->curl_get($url);
        //echo "\n-------getProject Response:".$response;
        //$result = json_decode($response);
        
        return $response;
    }
    
    /*
     * This is a helpping method to get a Project in String type.
     * 
     */
    private function getUser($id)
    {
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/users/".$id;
        $url = TestService::$elasticsearchHost.$this->context."/users/".$id;
        
        $response = $this->curl_get($url);
        //echo "\n-------getProject Response:".$response;
        //$result = json_decode($response);
        
        return $response;
    }
    
    
    
    /*
     * This is a helpping method to get a subject in String type.
     * 
     */
    private function getSubject($id)
    {
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/subjects/".$id;
        $url = TestService::$elasticsearchHost . $this->context."/subjects/".$id;
        
        $response = $this->curl_get($url);
        //echo "\n-------getProject Response:".$response;
        //$result = json_decode($response);
        
        return $response;
    }
    
    
    /*
     * This is a helpping method to get a document in String type.
     * 
     */
    private function getDocument($id)
    {
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/documents/".$id;
        $url = TestService::$elasticsearchHost . $this->context."/documents/".$id;
        
        $response = $this->curl_get($url);
        //echo "\n-------getProject Response:".$response;
        //$result = json_decode($response);
        
        return $response;
    }
    
    /*
     * This is a helpping method to get a document in String type.
     * 
     */
    private function listDocumentsFromTo($from,$size)
    {
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/documents";
        $url = TestService::$elasticsearchHost . $this->context. "/documents";
        
        $url = $url."?from=".$from."&size=".$size;
            
        
        echo "\n".$url;
        $response = $this->curl_get($url);
        //echo "\n-------getProject Response:".$response;
        //$result = json_decode($response);
        
        return $response;
    }
    
    /**
     * This is a helpping method to create a project using the local prj.json file.
     * @return type
     */
     private function createProject()
    {
        $input = file_get_contents('prj.json');
        //echo $input;
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/projects?owner=wawong";
        $url = TestService::$elasticsearchHost .$this->context."/projects?owner=wawong";
        
        $params = json_decode($input);
        $data = json_encode($params);   
        $response = $this->curl_post($url,$data);
        //echo "\nCreate project:".$response."\n";
        
        $result  = json_decode($response);
        $id = $result->_id;
        
        return $id;
    }
    
    /**
     * This is a helpping method to create a project using the local prj.json file.
     * @return type
     */
     private function createExperiment()
    {
        $input = file_get_contents('exp.json');
        //echo $input;
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/experiments?owner=wawong";
        $url = TestService::$elasticsearchHost .$this->context."/experiments?owner=wawong";
        
        $params = json_decode($input);
        $data = json_encode($params);   
        $response = $this->curl_post($url,$data);
        
        
        $result  = json_decode($response);
        $id = $result->_id;
        
        return $id;
    }
    
    
    /**
     * This is a helpping method to create a project using the local prj.json file.
     * @return type
     */
     private function createSubject()
    {
        $input = file_get_contents('subject.json');
        //echo $input;
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/subjects?owner=wawong";
        $url = TestService::$elasticsearchHost .$this->context."/subjects?owner=wawong";
        
        $params = json_decode($input);
        $data = json_encode($params);   
        $response = $this->curl_post($url,$data);
        
        
        $result  = json_decode($response);
        $id = $result->_id;
        
        return $id;
    }
    
    /**
     * This is a helpping method to create a document using the local prj.json file.
     * @return type
     */
     private function createDocument()
    {
        $input = file_get_contents('doc.json');
        //echo $input;
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/documents?owner=wawong";
        $url = TestService::$elasticsearchHost .$this->context."/documents?owner=wawong";
        
        $params = json_decode($input);
        $data = json_encode($params);   
        $response = $this->curl_post($url,$data);
        
        
        $result  = json_decode($response);
        $id = $result->_id;
        
        return $id;
    }
    
    /**
     * Testing the project creation with the prj.json file
     */
    public function testCreateProject()
    {
        echo "\nTesting project creation...";
        $input = file_get_contents('prj.json');
        //echo $input;
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/projects?owner=wawong";
        $url = TestService::$elasticsearchHost.$this->context."/projects?owner=wawong";
        
        
        $params = json_decode($input);
        $data = json_encode($params);   
        $response = $this->curl_post($url,$data);
       
        //echo "\nType:".$response."-----Create Response:".$response;
        
        $result  = json_decode($response);
        $id = $result->_id;
        
        //Assert index version
        $this->assertTrue(strcmp($result->_index,$this->index) == 0);
        $this->assertTrue($result->created);
        
    }
    
   
    
    /**
     * Testing the project listing method
     * 
     */
    public function testListProjects()
    {
        echo "\nTesting project listing...";
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/projects";
        $url = TestService::$elasticsearchHost . $this->context."/projects";
        
        $response = $this->curl_get($url);
        //echo "\n-------testListProjects Response:".$response."\n";
        $result = json_decode($response);
        $total = $result->total;
        
        $this->assertTrue(($total > 0));
        
    }
    
    
    /**
     * Testing the project retreival by ID
     * @return type
     * 
     */
    public function testGetProjectByID()
    {
        echo "\nTesting the project retrieval by ID...";
        $response = $this->getProject("P1");
        
        //echo "\ntestGetProjectByID------".$response;
        
        
        $result = json_decode($response);
        $prj = $result->Project;
        $this->assertTrue((!is_null($prj)));
        return $result;
    }
    
    
    /**
     * Testing the project search
     */
    public function testSearchProject()
    {
        echo "\nTesting project search...";
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/projects?search=test";
        $url = TestService::$elasticsearchHost . $this->context."/projects?search=test";
        
        $response = $this->curl_get($url);
        //echo "\n-------Response:".$response;
        $result = json_decode($response);
        $total = $result->hits->total;
        
        $this->assertTrue(($total > 0));
    }
    
    /**
     * Testing the prject deletion. Note that this is logical deletion. The document will remain
     * in the Elasticsearch
     * 
     */
    public function testDeleteProject()
    {
        echo "\nTesting project deletion...";
        $id = $this->createProject();
        //sleep(1);
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/projects/".$id;
        $url = TestService::$elasticsearchHost .$this->context."/projects/".$id;
        
        //echo "\nDelete URL:".$url;
       // $url = "http://localhost/CIL_RS/index.php/rest/projects/P5334222";
        //$url = "http://localhost/CIL_RS/index.php/rest/projects/".$id;
        echo "Delete project:".$url."\n";
        
        $response = $this->curl_delete($url);
        //echo "Delete response:".$response;
        
        $response = $this->getProject($id);
        //echo $response;
        $json = json_decode($response);
        //var_dump($json);
        
        $this->assertTrue(!$json->Found);
    }
    
    /**
     * Testing the project update.
     * 
     */
    public function testUpdateProject()
    {
        echo "\nTesting project update...";
        //$id = "P5334183";
        $id = $this->createProject();
        //echo "\n-----Is string:".gettype ($id);
        $input = file_get_contents('prj_update.json');
        //echo $input;
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/projects/".$id."?owner=wawong";
        $url = TestService::$elasticsearchHost .$this->context."/projects/".$id."?owner=wawong";

        //echo "\nURL:".$url;
        $params = json_decode($input);
        $data = json_encode($params);   
        $response = $this->curl_put($url,$data);
        
         
         $response = $this->getProject($id);
         //echo "\nResponse:".$response;
         
         $json = json_decode($response);
         $deleted = false;
         if(strcmp($json->Project->Name,'Updated')==0)
         {
                 $deleted = true;
                 
         }
         
         $this->assertTrue($deleted);
        
    }
    
    
    /**
     * Testing the experiment creation.
     * 
     */
    public function testCreateExperiment()
    {
        echo "\nTesting Experiment creation...";
        $input = file_get_contents('exp.json');
        //echo $input;
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/experiments?owner=wawong";
        $url = TestService::$elasticsearchHost . $this->context."/experiments?owner=wawong";
        
        $params = json_decode($input);
        $data = json_encode($params);   
        $response = $this->curl_post($url,$data);
        //echo "-----Create Response:".$response;
        
        $result  = json_decode($response);
        //$id = $result->_id;
        //echo "-----ID:".$id;
        
        
        $this->assertTrue($result->created);
        
        
    }
    
    /**
     * Testing the experiment listing 
     * 
     */
    public function testListExperiments()
    {
        echo "\nTesting experiment listing...";
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/experiments";
        $url = TestService::$elasticsearchHost .$this->context."/experiments";
        
        $response = $this->curl_get($url);
        //echo "-------Response:".$response;
        $result = json_decode($response);
        $total = $result->total;
        
        $this->assertTrue(($total > 0));
        
    }
    
    /**
     * Testing the experiment retreival by ID
     * @return type
     * 
     */
    public function testGetExperimentByID()
    {
        echo "\nTesting the experiment retrieval by ID...";
        $response = $this->getExperiment("1");
        $result = json_decode($response);
        $exp = $result->Experiment;
        $this->assertTrue((!is_null($exp)));
        return $result;
    }
    
    
    /**
     * Testing the project search
     */
    public function testSearchExperiment()
    {
        echo "\nTesting experiment search...";
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/experiments?search=test";
        $url = TestService::$elasticsearchHost .$this->context."/experiments?search=test";
        
        $response = $this->curl_get($url);
        //echo "\n-------Response:".$response;
        $result = json_decode($response);
        $total = $result->hits->total;
        
        $this->assertTrue(($total > 0));
    }
    
    
    
    /**
     * Testing the experiment update.
     * 
     */
    public function testUpdateExperiment()
    {
        echo "\nTesting experiment update...";
        //$id = "P5334183";
        $id = $this->createExperiment();
        //echo "\n-----Is string:".gettype ($id);
        $input = file_get_contents('exp_update.json');
        //echo $input;
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/experiments/".$id."?owner=wawong";
        $url = TestService::$elasticsearchHost . $this->context."/experiments/".$id."?owner=wawong";
        
        //echo "\nURL:".$url;
        $params = json_decode($input);
        $data = json_encode($params);   
        $response = $this->curl_put($url,$data);
        
         
         $response = $this->getExperiment($id);
         //echo "\nResponse:".$response;
         
         $json = json_decode($response);
         $deleted = false;
         if(strcmp($json->Experiment->Purpose,'Updated')==0)
         {
                 $deleted = true;
                 
         }
         
         $this->assertTrue($deleted);
        
    }
    
    
    /**
     * Testing the prject deletion. Note that this is logical deletion. The document will remain
     * in the Elasticsearch
     * 
     */
    public function testDeleteExperiment()
    {
        echo "\nTesting experiment deletion...";
        $id = $this->createExperiment();
        //sleep(1);
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/experiments/".$id;
       
        //$url =  TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/experiments/".$id;
        $url =  TestService::$elasticsearchHost . $this->context."/experiments/".$id;
        
        $response = $this->curl_delete($url);
        //echo "Delete response:".$response;
        //echo "----ID:".$id;
        $response = $this->getExperiment($id);
        //echo $response;
        $json = json_decode($response);
        //var_dump($json);
        
        $this->assertTrue(!$json->Found);
    }
    
    
    /**
     * Testing the experiment creation.
     * 
     */
    public function testCreateSubject()
    {
        echo "\nTesting Subject creation...";
        $input = file_get_contents('subject.json');
        //echo $input;
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/subjects?owner=wawong";
        $url = TestService::$elasticsearchHost .$this->context."/subjects?owner=wawong";
        
        $params = json_decode($input);
        $data = json_encode($params);   
        $response = $this->curl_post($url,$data);
        //echo "-----Create Response:".$response;
        
        $result  = json_decode($response);
        //$id = $result->_id;
        //echo "-----ID:".$id;
        
        
        $this->assertTrue($result->created);
        
        
    }
    
    
    /**
     * Testing the subject listing 
     * 
     */
    public function testListSubjects()
    {
        echo "\nTesting subject listing...";
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/subjects";
        $url = TestService::$elasticsearchHost .$this->context."/subjects";
        
        $response = $this->curl_get($url);
        //echo "-------Response:".$response;
        $result = json_decode($response);
        $total = $result->total;
        
        $this->assertTrue(($total > 0));
        
    }
    
    
    /**
     * Testing the experiment retreival by ID
     * @return type
     * 
     */
    public function testGetSubjectByID()
    {
        echo "\nTesting the subject retrieval by ID...";
        $response = $this->getSubject("20");
        //echo $response;
        $result = json_decode($response);
        $sub = $result->Subject;
        $this->assertTrue((!is_null($sub)));
        
    }
    
    
    /**
     * Testing the subject update.
     * 
     */
    public function testUpdateSubject()
    {
        echo "\nTesting subject update...";
        //$id = "P5334183";
        $id = $this->createSubject();
        //echo "\n-----Is string:".gettype ($id);
        $input = file_get_contents('subject_update.json');
        //echo $input;
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/subjects/".$id."?owner=wawong";
        $url = TestService::$elasticsearchHost .$this->context."/subjects/".$id."?owner=wawong";

        //echo "\nURL:".$url;
        $params = json_decode($input);
        $data = json_encode($params);   
        $response = $this->curl_put($url,$data);
        
         
         $response = $this->getSubject($id);
         //echo "\nResponse:".$response;
         
         $json = json_decode($response);
         $deleted = false;
         if(strcmp($json->Subject->Scientific_name,'Updated')==0)
         {
                 $deleted = true;
                 
         }
         
         $this->assertTrue($deleted);
        
    }
    
    /**
     * Testing the project search
     */
    public function testSearchSubject()
    {
        echo "\nTesting subject search...";
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/subjects?search=mouse";
        $url = TestService::$elasticsearchHost . $this->context."/subjects?search=mouse";
        
        $response = $this->curl_get($url);
        //echo "\n-------Response:".$response;
        $result = json_decode($response);
        $total = $result->hits->total;
        
        $this->assertTrue(($total > 0));
    }
    
    
    /**
     * Testing the subject deletion. Note that this is logical deletion. The subject will remain
     * in the Elasticsearch
     * 
     */
    public function testDeleteSubject()
    {
        echo "\nTesting subject deletion...";
        $id = $this->createSubject();
        
        //$url =  TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/subjects/".$id;
        $url =  TestService::$elasticsearchHost . $this->context."/subjects/".$id;
        
        $response = $this->curl_delete($url);
        //echo "Delete response:".$response;
        //echo "----ID:".$id;
        $response = $this->getSubject($id);
       // echo $response;
        $json = json_decode($response);
        //var_dump($json);
        
        $this->assertTrue(!$json->Found);
    }
    
   
    
    /**
     * Testing the experiment creation.
     * 
     */
    public function testCreateDocument()
    {
        echo "\nTesting document creation...";
        $input = file_get_contents('doc.json');
        //echo $input;
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/documents?owner=wawong";
        $url = TestService::$elasticsearchHost . $this->context."/documents?owner=wawong";
        
        $params = json_decode($input);
        $data = json_encode($params);   
        $response = $this->curl_post($url,$data);
        //echo "-----Create Response:".$response;
        
        $result  = json_decode($response);
        //$id = $result->_id;
        //echo "-----ID:".$id;
        
        
        $this->assertTrue($result->created);
        
        
    }
    
    
    /**
     * Testing the experiment listing 
     * 
     */
    public function testListDocuments()
    {
        echo "\nTesting document listing...";
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/documents";
        $url = TestService::$elasticsearchHost .$this->context."/documents";
        
        $response = $this->curl_get($url);
        //echo "-------Response:".$response;
        $result = json_decode($response);
        $total = $result->total;
        
        $this->assertTrue(($total > 0));
        
    }
    
    
    /**
     * Testing the experiment retreival by ID
     * @return type
     * 
     */
    public function testGetDocumentByID()
    {
        echo "\nTesting the document retrieval by ID...";
        $response = $this->getDocument("CCDB_2");
        //echo $response;
        $result = json_decode($response);
        $exp = $result->CIL_CCDB;
        $this->assertTrue((!is_null($exp)));
        return $result;
    }
    
    /**
     * Testing the document search
     */
    public function testSearchDocument()
    {
        echo "\nTesting document search...";
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/documents?search=mouse";
        $url = TestService::$elasticsearchHost . $this->context."/documents?search=mouse";
        
        $response = $this->curl_get($url);
        //echo "\n-------Response:".$response;
        $result = json_decode($response);
        $total = $result->hits->total;
        
        $this->assertTrue(($total > 0));
    }
    
    
    /**
     * Testing the document update.
     * 
     */
    public function testUpdateDocument()
    {
        echo "\nTesting dcoument update...";
        //$id = "P5334183";
        $id = $this->createDocument();
        //echo "\n-----Is string:".gettype ($id);
        $input = file_get_contents('doc_update.json');
        //echo $input;
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/documents/".$id."?owner=wawong";
        $url = TestService::$elasticsearchHost .$this->context."/documents/".$id."?owner=wawong";

        //echo "\nURL:".$url;
        $params = json_decode($input);
        $data = json_encode($params);   
        $response = $this->curl_put($url,$data);
        
         
         $response = $this->getDocument($id);
         //echo "\nResponse:".$response;
         
         $json = json_decode($response);
         $deleted = false;
         if(strcmp($json->CIL_CCDB->CCDB->Microscopy_product->Image_basename,'Updated')==0)
         {
                 $deleted = true;
                 
         }
         
         $this->assertTrue($deleted);
        
    }
    
    
    /**
     * Testing the document deletion. Note that this is logical deletion. The document will remain
     * in the Elasticsearch
     * 
     */
    public function testDeleteDocument()
    {
        echo "\nTesting subject deletion...";
        $id = $this->createDocument();
        
        //$url =  TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/documents/".$id;
        $url =  TestService::$elasticsearchHost .$this->context."/documents/".$id;
        
        $response = $this->curl_delete($url);
        //echo "Delete response:".$response;
        //echo "----ID:".$id;
        $response = $this->getDocument($id);
       // echo $response;
        $json = json_decode($response);
        //var_dump($json);
        
        $this->assertTrue(!$json->Found);
    }
    
    /**
     * Testing document listing with the parameters, "from" and "size"
     * @return type
     * 
     */
    public function testListDocumentsFromTo()
    {
        echo "\nTesting the document retrieval by ID...";
        $response = $this->listDocumentsFromTo(0,26);
       //echo $response;
        $result = json_decode($response);
        
        $hits = $result->hits;
        //echo "\nCOUNT:".count($hits);
        $this->assertTrue(count($hits)==26);
    }
    
    
    /**
     * Testing the user listing 
     * 
     */
    public function testListUsers()
    {
        echo "\nTesting user listing...";
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/users";
        $url = TestService::$elasticsearchHost .$this->context."/users";
        
        $response = $this->curl_get($url);
        //echo "-------Response:".$response;
        $result = json_decode($response);
        $total = $result->total;
        
        $this->assertTrue(($total > 0));
        
    }
    
    
    /**
     * Testing the user retreival by ID
     * @return type
     * 
     */
    public function testGetUsertByID()
    {
        echo "\nTesting the user retrieval by ID...";
        $response = $this->getUser("44225");
        //echo $response;
        $result = json_decode($response);
        $user = $result->User;
        $this->assertTrue((!is_null($user)));
        
    }
    
    /**
     * Testing the user search
     */
    public function testSearchUser()
    {
        echo "\nTesting user search...";
        //$url = TestService::$elasticsearchHost . "/CIL_RS/index.php/rest/users?search=Vicky";
        $url = TestService::$elasticsearchHost . $this->context."/users?search=Vicky";
        
        $response = $this->curl_get($url);
        //echo "\n-------Response:".$response;
        $result = json_decode($response);
        $total = $result->hits->total;
        
        $this->assertTrue(($total > 0));
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
        $data = "{\"query\":{\"terms\":{\"_id\":[\"CIL_7756\",\"CIL_12607\",\"CIL_10276\",\"CIL_12329\",\"CIL_12623\",\"CIL_12655\"]}}}";
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
    
    public function testTrackImageViewerStats()
    {
        $ip_address = "0.0.0.0";
        $id = "CIL_2";
        echo "\ntestTrackImageViewerStats";
        $json_str = "{\"Ip_address\":\"".$ip_address."\",\"ID\":\"".$id."\"}";
        
        $url = TestService::$elasticsearchHost."/rest/track_image_viewer";
        $response = $this->curl_post($url, $json_str);
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
   
    
    public function testGetAllPublicIds()
    {
        echo "\ntestGetAllPublicIds";
        $url = TestService::$elasticsearchHost."/rest/public_ids?from=0&size=10";
        $response = $this->curl_get($url);
        $json = json_decode($response);
        if(!is_null($json) && isset($json->hits->total)
                && $json->hits->total > 0)
        {
            $this->assertTrue(true);
        }
        else
            $this->assertTrue(false);
        
    }
    
    public function testGetDataMapping()
    {
        echo "\ntestGetDataMapping";
        $url = TestService::$elasticsearchHost."/rest/data_mapping";
        echo "\n".$url;
        $response = $this->curl_get($url);
        $json = json_decode($response);
        if(!is_null($json) && isset($json->ccdbv8->mappings))
        {
            $this->assertTrue(true);
        }
        else
            $this->assertTrue(false);
    }
    
    
    public function testGetPublicDocument()
    {
        echo "\ntestGetDataMapping";
        $url = TestService::$elasticsearchHost."/rest/public_documents/CIL_2";
        $response = $this->curl_get($url);
        $json = json_decode($response);
        if(!is_null($json) && isset($json->found) && $json->found)
        {
            $this->assertTrue(true);
        }
        else
            $this->assertTrue(false);
    }

    public function testPublicKeywordSearch()
    {
        echo "\ntestGetDataMapping";
        $url = TestService::$elasticsearchHost."/rest/public_documents?search=purkinje+cell&from=0&size=36";
        $response = $this->curl_get($url);
        $json = json_decode($response);
        if(!is_null($json) && isset($json->hits->total) && $json->hits->total > 0)
        {
            $this->assertTrue(true);
        }
        else
            $this->assertTrue(false);
    }
    
    public function testGetMicrobialData()
    {
        echo "\ntestGetMicrobialData";
        $url = TestService::$elasticsearchHost."/rest/microbial/virus?from=0&size=1&z_stack=true";
        $response = $this->curl_get($url);
        $json = json_decode($response);
        if(!is_null($json) && isset($json->hits->total) && $json->hits->total > 0)
        {
            $this->assertTrue(true);
        }
        else
            $this->assertTrue(false);
    }
    
    public function testDataPermission()
    {
        echo "\ntestDataPermission";
        $url = TestService::$elasticsearchHost."/rest/data_permission/d5tmE4wuRs";
        $response = $this->curl_get($url);
        $json = json_decode($response);
        if(!is_null($json) && isset($json->found) && $json->found)
            $this->assertTrue(true);
        else
            $this->assertTrue(false);
    }
    
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