<?php
    function just_curl_get_data($url,$data)
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
$config_file = "C:/data/cil_service_config.json";
$json_str = file_get_contents($config_file);
$json = json_decode($json_str);
$from = 0;
$size = 1;
$url = $json->elasticsearch_host_stage."/ccdbv8/data/_search?pretty=true&from=0&size=1";
$query = "{\"query\":{\"query_string\":{\"query\":\"(CIL_CCDB.Status.Is_public:true AND CIL_CCDB.Status.Deleted:false) AND !(CIL_CCDB.CIL.CORE.TERMSANDCONDITIONS.free_text:copyright*)\"}},\"stored_fields\": []}";
//echo $query;
$response = just_curl_get_data($url, $query);
echo $response;