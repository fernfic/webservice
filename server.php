<?php
// Pull in the NuSOAP code
require_once('lib/nusoap.php');
// Create the server instance
$server = new soap_server();
// Initialize WSDL support
$server->configureWSDL('air_data', 'urn:air_data');
// Register the data structures used by the service
$server->wsdl->addComplexType(
    'Air_Data',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'room' => array('name' => 'room', 'type' => 'xsd:string'),
        'time' => array('name' => 'time', 'type' => 'xsd:string'),
        'temp' => array('name' => 'temp', 'type' => 'xsd:float'),
        'humidity' => array('name' => 'humidity', 'type' => 'xsd:float'),
    )
);
$server->wsdl->addComplexType(
    'Get_Air',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'Get_Air' => array('name' => 'Get_Air','minOccurs'=> '0', 'maxOccurs' =>'unbounded','nillable' => 'true', type=>'tns:Air_Data')
    )
);

// Register the method to expose
$server->register('set_data',                    // method name
    array('data' => 'tns:Air_Data'),          // input parameters
    array('return' => 'xsd:string'),    // output parameters
    'urn:air_data',                         // namespace
    'urn:air_data#set_data'                   // soapaction
);
$server->register('get_data',                    // method name
    array('room' => 'xsd:string'),
    array('return' => 'tns:Get_Air'),    // output parameters
    'urn:air_data',                         // namespace
    'urn:air_data#get_data'                   // soapaction
);

// Define the method as a PHP function
function set_data($data) {
    $dbcon =  mysqli_connect('localhost', 'wolfbit', '', 'air_data') or die('not connect database'.mysqli_connect_error());
	mysqli_set_charset($dbcon, 'utf8');
	$room = $data['room'];
	$time = $data['time'];
	$temp = $data['temp'];
	$humidity = $data['humidity'];
    $query = "INSERT INTO data_table(room, time, temp, humidity) VALUES('$room','$time','$temp','$humidity')";
    // $query = "INSERT INTO data_table(room, time, temp, humidity) VALUES('01', '12-09-2016 05:00', '22.5', '10.2')";
    $result = mysqli_query($dbcon, $query);
    mysqli_close($dbcon);
    $send = "add data complete!";
    return $send;
}
function get_data($room) {
    $dbcon =  mysqli_connect('localhost', 'wolfbit', '', 'air_data') or die('not connect database'.mysqli_connect_error());
	mysqli_set_charset($dbcon, 'utf8');
// 	$query = "SELECT * FROM data_table WHERE room='$room'";
	$query = "SELECT * FROM data_table";
    $result = mysqli_query($dbcon, $query);
    
    if($result){
        $data = array();
	    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	        $data[] = array('room'=>$row['room'], 'time'=>$row['time'], 'temp'=>$row['temp'], 'humidity'=>$row['humidity']);
	    }
    }
    
    mysqli_close($dbcon);
    return array(
        'Get_Air' => $data
        );
}

// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>