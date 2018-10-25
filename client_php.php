<?php
require_once('lib/nusoap.php');

$client = new nusoap_client("https://testselect.herokuapp.com/server.php?wsdl");

// $client = new nusoap_client("https://testselect.herokuapp.com/server.php?wsdl");

$data = array('room' => '01', 'time' => '12-09-2016 05:00', 'temp' => 22.5, 'humidity' => 10.12);
$result = $client->call("get_data", array("room" => "01"));
print_r($result);
?>