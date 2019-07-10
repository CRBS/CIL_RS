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
class Cdeep3m_prp_rest extends REST_Controller
{
    private $success = "success";
    private $error_type = "error_type";
    private $error_message = "error_message";
    
    public function yaml_get()
    {
        $filename="default.yaml";
        
        $error = false;
        ob_clean ();
        $header = $this->input->get_request_header('Authorization');
      
        $header = str_replace("Basic ", "", $header);
        
        $decoded = base64_decode($header);
        
        //echo $decoded;
        $prp_user = $this->config->item('prp_user');
        $user = $this->input->get('user',TRUE);
        $pass = $this->input->get('pass',TRUE);
        $augspeed = $this->input->get('augspeed',TRUE);
        $models = $this->input->get('models',TRUE);
        $enhance = $this->input->get('enhance',TRUE);
        $overlay = $this->input->get('overlay',TRUE);
        $crop_id = $this->input->get('crop_id',TRUE);
        $model_doi = $this->input->get('model_doi',TRUE);
        
        //$content = $prp_user;
        if(strcmp($decoded, $prp_user) != 0)
        {
            $error= true;
            $content = "Error: Invalid authentication key";
            
        }
        $lines = "";
        $templatePath = getcwd()."/templates/predict-prp-template1.yml";
        if(!$error && !file_exists($templatePath))
            $content = "Error: Template file does not exist: ".$templatePath;
        else if(!$error)
        {
            $content = file_get_contents($templatePath);
            if(!is_null($crop_id))
                $content = str_replace("\$crop_id", $crop_id, $content);
            $content = str_replace("\$model_doi", $model_doi, $content);
            $content = str_replace("\$user", $user, $content);
            $content = str_replace("\$pass", $pass, $content);
            $content = str_replace("\$augspeed", $augspeed, $content);
            $content = str_replace("\$models", $models, $content);
            if(!is_null($enhance) && strcmp($enhance, "true")==0)
                $content = str_replace("\$enhance", "--enhance", $content);
            else
                $content = str_replace("\$enhance", " ", $content);
            
            if(!is_null($overlay) && strcmp($overlay, "true")==0)
            {
                $content = str_replace("\$overlay", "--overlay", $content);
                //echo "1";
            }
            else
            {
                $content = str_replace("\$overlay", " ", $content);
                //echo "2";
            }
            
        }
        header('Content-Length: '.strlen($content)); //<-- sends filesize header
        header("Content-Type: text/yaml"); //<-- send mime-type header
        header('Content-Disposition: inline; filename="'.$filename.'";'); //<-- sends filename header
        
        echo  $content;
        return; 
    }
}
