<?php
$dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b2efec9f22e714', '2d88bcce', 'heroku_c9738a7c9866d40') or die('not connect database'.mysqli_connect_error());
    mysqli_set_charset($dbcon, 'utf8');
// >= '2010-01-31 12:01:01'
$query = "SELECT * FROM data_table";

$result = mysqli_query($dbcon, $query);

if($result){
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo("id ".$row['room']."<br>");
        echo("time ".$row['time']."<br>");
        echo("temperature ".$row['temp']."<br>");
        echo("humidity ".$row['humidity']."<br>");
        // echo "fan ".$row['fan']."<br>";
        echo "<br>";
    }
    mysqli_free_result($result);
}else{
    echo "error";
}

mysqli_close($dbcon);
?>
    