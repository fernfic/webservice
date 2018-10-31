<?php
// Pull in the NuSOAP code
require_once('lib/nusoap.php');
// Create the server instance
$server = new soap_server();
// Initialize WSDL support
$server->configureWSDL('testdata', 'urn:testdata');

// --------------------pre test Air Data---------------------------//
$server->wsdl->addComplexType(
    'AirData',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'room' => array('name' => 'room', 'type' => 'xsd:integer'),
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
    $result = mysqli_query($dbcon, $query);
    mysqli_close($dbcon);
    $send = "add data complete!";
    return $send;
}
$server->register('set_data',                    // method name
    array('data' => 'tns:AirData'),          // input parameters
    array('return' => 'xsd:string'),    // output parameters
    'urn:testdata');

function get_data($room) {
    $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b2efec9f22e714', '2d88bcce', 'heroku_c9738a7c9866d40') or die('not connect database'.mysqli_connect_error());
    mysqli_set_charset($dbcon, 'utf8');
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
                      'urn:testdata');                  // soapaction
                        
                        
//--------------------------test1 User Data-------------------------------//
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

function get_user($name) {
    $hobby = array(0=>'read book', 1=>'play game');
    $sport = array(0=>'ball',1=>'tennis');
    $data = array('name'=> 'Supitcha', 'id'=>'5801012620097', 'hobby'=>$hobby,'sport'=>$sport);
    return $data;
}
$server->register('get_user',                    // method name
    array('name' => 'xsd:string'),
    array('return' => 'tns:UserData'),
    'urn:testdata'); 


//----------------------------test2 Kerry Data-----------------------------//  
$server->wsdl->addComplexType(
    'KerryData',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'id' => array('name' => 'id', 'type' => 'xsd:integer'),
        'name' => array('name' => 'name', 'type' => 'xsd:string'),
    )
);
$server->wsdl->addComplexType(
    'GetKerryData',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'id' => array('name' => 'id', 'type' => 'xsd:integer'),
        'name' => array('name' => 'name', 'type' => 'xsd:string'),
        'addr' => array('name' => 'addr', 'type' => 'xsd:string'),
        'weight' => array('name' => 'weight', 'type' => 'xsd:float'),
        'status' => array('name' => 'status', 'type' => 'xsd:string'),
    )
);

$server->wsdl->addComplexType(
    'GetKerry',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'GetKerry' => array('name' => 'GetKerry','minOccurs'=> '0', 'maxOccurs' =>'unbounded','nillable' => 'true', type=>'tns:GetKerryData')
    )
);

function send_kerry($data) {
    $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b2efec9f22e714', '2d88bcce', 'heroku_c9738a7c9866d40') or die('not connect database'.mysqli_connect_error());
    mysqli_set_charset($dbcon, 'utf8');
    $name = $data['name'];
    $addr = $data['addr'];
    $weight = $data['weight'];
    $status = "Not Done";
    $query = "INSERT INTO kerry ( name, addr, weight, status) VALUES( '$name','$addr','$weight','$status')";
    $result = mysqli_query($dbcon, $query);
    mysqli_close($dbcon);
    $send = "add data complete!";
    return $send;
}
$server->register('send_kerry',                    // method name
    array('data' => 'tns:GetKerryData'),
    array('return' => 'xsd:string'),    // output parameters
    'urn:testdata'); 
    
function update_kerry($data) {
    $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b2efec9f22e714', '2d88bcce', 'heroku_c9738a7c9866d40') or die('not connect database'.mysqli_connect_error());
    mysqli_set_charset($dbcon, 'utf8');
    $name = $data['name'];
    $id = $data['id'];
    $query = "SELECT * FROM kerry WHERE name='$name' and id = '$id'";
    $result = mysqli_query($dbcon, $query);
    if($result){
        $query = "UPDATE kerry SET status='Done' WHERE name='$name' and id = '$id'";
        $result = mysqli_query($dbcon, $query);
        $text = "Update Complete";
    }else{
        $text = "No kerry";
    }
    mysqli_close($dbcon);
    return $text;
}
$server->register('update_kerry',                    // method name
    array('data' => 'tns:KerryData'),
    array('return' => 'xsd:string'),    // output parameters
    'urn:testdata');
    
function get_kerry($name) {
    $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b2efec9f22e714', '2d88bcce', 'heroku_c9738a7c9866d40') or die('not connect database'.mysqli_connect_error());
    mysqli_set_charset($dbcon, 'utf8');
    $query = "SELECT * FROM kerry";
    $result = mysqli_query($dbcon, $query);
    if($result){
        $data = array();
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $data[] = array('id'=>$row['id'],'name'=>$row['name'], 'addr'=>$row['addr'], 'weight'=>$row['weight'], 'status'=>$row['status']);
        }
    }
    mysqli_close($dbcon);
    return array('GetKerry' => $data);
}
$server->register('get_kerry',                    // method name
    array('name' => 'xsd:string'),
    array('return' => 'tns:GetKerry'),    // output parameters
    'urn:testdata'); 


@$server->service(file_get_contents("php://input"));
?>