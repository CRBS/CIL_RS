<?php

$cil_config_file = "C:/data/cil_service_config.json";
//$cil_config_file = "/var/www/cil_service_config.json";

$json_str = file_get_contents($cil_config_file);
$configJson = json_decode($json_str);

$db_params = $configJson->cil_pgsql_db;
$conn = pg_pconnect($db_params);
if (!$conn) 
{
    exit("\nUnable to connect!");
}

$input = array();
array_push($input,"::1");
array_push($input,"CCDB_1");
array_push($input,"http://cildata/test1.txt");
array_push($input,"123");
        
$sql = "insert into cil_download_statistics(id, ip_address ,image_id, ".
               " url,size, download_time) ".
               " values(nextval('general_sequence'),$1,$2".
               " ,$3,$4,now())";
$result = pg_query_params($conn,$sql,$input);
if (!$result) 
{
    exit("\nError happens during the sql execution!");
}

echo "\nSuccess!";