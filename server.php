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
        'roomid' => array('name' => 'roomid', 'type' => 'xsd:integer'),
        'time' => array('name' => 'time', 'type' => 'xsd:string'),
        'temperature' => array('name' => 'temperature', 'type' => 'xsd:float'),
        'humidity' => array('name' => 'humidity', 'type' => 'xsd:float'),
    )
);
$server->wsdl->addComplexType(
    'UserData',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'name' => array('name' => 'name', 'type' => 'xsd:string'),
        'id' => array('name' => 'id', 'type' => 'xsd:string'),
        'hobby' => array('name' => 'hobby', 'minOccurs'=> '0', 'maxOccurs' =>'unbounded','nillable' => 'true','type' => 'xsd:string'),
        'sport' => array('name' => 'sport', 'minOccurs'=> '0', 'maxOccurs' =>'unbounded','nillable' => 'true','type' => 'xsd:string'),
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
$server->wsdl->addComplexType(
    'GetUser',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'GetUser' => array('name' => 'GetUser','minOccurs'=> '0', 'maxOccurs' =>'unbounded','nillable' => 'true', type=>'tns:UserData')
    )
);

// Define the method as a PHP function
function set_data($data) {
    $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b527b3315d2375', '50a5650c', 'heroku_412cbb6c0f293a3') or die('not connect database'.mysqli_connect_error());
    mysqli_set_charset($dbcon, 'utf8');
    $roomid = $data['roomid'];
    $time = $data['time'];
    $temperature = $data['temperature'];
    $humidity = $data['humidity'];
    $query = "INSERT INTO data (roomid, time, temperature, humidity) VALUES('$roomid','$time','$temperature','$humidity')";
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
    'urn:airdata');

function get_data($room) {
    $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b527b3315d2375', '50a5650c', 'heroku_412cbb6c0f293a3') or die('not connect database'.mysqli_connect_error());
    mysqli_set_charset($dbcon, 'utf8');
    $query = "SELECT * FROM data";
    $result = mysqli_query($dbcon, $query);
    if($result){
        $data = array();
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $data[] = array('roomid'=>$row['roomid'], 'time'=>$row['time'], 'temperature'=>$row['temperature'], 'humidity'=>$row['humidity']);
        }
    }
    
    mysqli_close($dbcon);
    return array('GetAir' => $data);
}

// Register the method to expose
$server->register('get_data',                    // method name
    array('room' => 'xsd:string'),
    array('return' => 'tns:GetAir'),    // output parameters
                        'urn:airdata');                  // soapaction

function get_user($name) {
//     $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b2efec9f22e714', '2d88bcce', 'heroku_c9738a7c9866d40') or die('not connect database'.mysqli_connect_error());
//     mysqli_set_charset($dbcon, 'utf8');
// 	// >= '2010-01-31 12:01:01'
// 	$query = "SELECT * FROM data_table";
//     $result = mysqli_query($dbcon, $query);
    
//     if($result){
//         $data = array();
// 	    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
// 	        $data[] = array('room'=>$row['room'], 'time'=>$row['time'], 'temp'=>$row['temp'], 'humidity'=>$row['humidity']);
// 	    }
//     }
    // mysqli_close($dbcon);
    $data = array('name'=> 'Supitcha', 'id'=>'5801012620097', 'hobby'=>array('0'=>'read book', '1'=>'play game'),'sport'=>array('0'=>ball,'1'=>'tennis'));
    return array('GetUser' => $data);
}

$server->register('get_user',                    // method name
    array('name' => 'xsd:string'),
    array('return' => 'tns:GetUser'),    // output parameters
    'urn:airdata');   

@$server->service(file_get_contents("php://input"));
?>