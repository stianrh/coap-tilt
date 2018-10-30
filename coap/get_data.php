<?php
include 'db.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//$uri = $_SERVER['REQUEST_URI'];
$uri = $_GET['uri'];
if (!(substr($uri, -1) === '/')){
    $uri = $uri . '/';
}

$sql = "SELECT `key`, `value`, `time` FROM coap WHERE uri='$uri'";
$result = $conn->query($sql);
$points = array();
foreach($result as $row){
    $p_pair = array($row["time"], $row["value"]);
    if (array_key_exists($row["key"], $points)){
        array_push($points[$row["key"]], $p_pair);
    } else {
        $points[$row["key"]] = array($p_pair);
    }
}
echo json_encode($points);
?>
