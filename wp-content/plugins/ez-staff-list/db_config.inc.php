<?php
//*************************************************************************************
// Data Sources
//*************************************************************************************
// 1. source name
// 2. type		(ldap, mysql, pgsql)
// 3. server
// 4. port		(389, 3306, 5432)
// 5. source (table or directory)
// 6. user
// 7. password

$server_name = $_SERVER["SERVER_NAME"];
$split_point = strpos($server_name, ".");
$sub_domain = substr($server_name, $split_point+1, strlen($server_name));

switch ($sub_domain) {

	default:
		$i = "main";
		$data_arr[$i]["type"] = "mysql";
		$data_arr[$i]["port"] = 3306;
		$data_arr[$i]["source"] = DB_NAME;
		$data_arr[$i]["server"] = DB_HOST;
		$data_arr[$i]["user"] = DB_USER;
		$data_arr[$i]["pass"] = DB_PASSWORD;
        
		break;
}
?>
