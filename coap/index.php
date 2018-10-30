<?php
include 'db.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$uri = $_SERVER['REQUEST_URI'];
if (!(substr($uri, -1) === '/')){
    $uri = $uri . '/';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    foreach($data['values'] as $row) {
        foreach($row as $key => $val) {
            if($key === 'key'){
                $key_name = $val;
            } else if($key === 'value'){
                $value_val = $val;
            }
        }
        $sql_insert = "INSERT INTO `coap` (`uri`, `key`, `value`)
        VALUES ('$uri', '$key_name', '$value_val')";
        if (!mysqli_query($conn, $sql_insert)) {
            echo "Error: " . $sql_insert . "<br>" . mysqli_error($conn);
        }
    }
}
$conn->close();
?>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<script>
    $(function() {
        var traces = [];
        var layout = {};
        function plot_data(data){
            //console.log(data);
            for (var key in data) {
                console.log(key);
                if (data.hasOwnProperty(key)) {
                    x = [];
                    y = [];
                    for (var i in data[key]){
                        x.push(data[key][i][0]);
                        y.push(data[key][i][1]);
                    }
                    //x = Array.apply(null, {length: y.length}).map(Number.call, Number);
                    console.log(x);
                    console.log(y);
                    var trace = {
                        x: x,
                        y: y,
                        mode: 'line',
                        name: key,
                        showlegend: true,
                    };
                    traces.push(trace);
                }
            }
        }
        $.get("/coap/get_data.php", "uri=<?php echo $uri ?>", function(data) {
            plot_data(JSON.parse(data));
            Plotly.newPlot('myDiv', traces, layout);
        });
    });


</script>

<body>
  <div id="myDiv"></div>
</body>
