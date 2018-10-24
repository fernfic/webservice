<?php
// Pull in the NuSOAP code
require_once('lib/nusoap.php');
// Create the server instance
$server = new soap_server();
// Initialize WSDL support
$server->configureWSDL('airdata', 'urn:airdata');
// Register the data structures used by the service
$server->wsdl->addComplexType(
    'AirData',
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
    'GetAir',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'GetAir' => array('name' => 'GetAir','minOccurs'=> '0', 'maxOccurs' =>'unbounded','nillable' => 'true', type=>'tns:AirData')
    )
);

function set_data($data) {
    $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b2efec9f22e714', '2d88bcce', 'heroku_c9738a7c9866d40') or die('not connect database'.mysqli_connect_error());
	mysqli_set_charset($dbcon, 'utf8');
	$room = $data['room'];
	$time = $data['time'];
	$temp = $data['temp'];
	$humidity = $data['humidity'];
    $query = "INSERT INTO data_table (room, time, temp, humidity) VALUES('$room','$time','$temp','$humidity')";
    // $query = "INSERT INTO data_table(room, time, temp, humidity) VALUES('01', '12-09-2016 05:00', '22.5', '10.2')";
    $result = mysqli_query($dbcon, $query);
    mysqli_close($dbcon);
    $send = "add data complete!";
    return $send;
}

// Register the method to expose
$server->register('set_data',                    // method name
    array('data' => 'tns:AirData'),          // input parameters
    array('return' => 'xsd:string'),    // output parameters
    'urn:airdata'                  // soapaction
);


function get_data($room) {
    $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b2efec9f22e714', '2d88bcce', 'heroku_c9738a7c9866d40') or die('not connect database'.mysqli_connect_error());
    mysqli_set_charset($dbcon, 'utf8');
	// >= '2010-01-31 12:01:01'
	$query = "SELECT * FROM data_table";
    $result = mysqli_query($dbcon, $query);
    
    if($result){
        $data = array();
	    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	        $data[] = array('room'=>$row['room'], 'time'=>$row['time'], 'temp'=>$row['temp'], 'humidity'=>$row['humidity']);
	    }
    }
    
    mysqli_close($dbcon);
    return array('GetAir' => $data);
}

$server->register('get_data',                    // method name
    array('room' => 'xsd:string'),
    array('return' => 'tns:GetAir'),    // output parameters
    'urn:airdata');                // soapaction

@$server->service(file_get_contents("php://input"));
?>