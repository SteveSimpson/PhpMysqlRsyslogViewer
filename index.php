<?php
/*

Copyright (c) 2018 AGIX

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

Andrew Galdes (AGIX), andrew.galdes@agix.com.au
http://www.agix.com.au

With contribution by Steve Simpson (software@lcsas.us).

A script to allow simplified log filtering. 

Refer to the following article for more information:
    http://www.agix.com.au/rsyslog-and-mysql-on-centos7-and-redhat-7/

You'll want to protect this page to prevent unauthorised access to your
system logs. 

*/


// START OF OPTIONS
// moved to external file that is filtered by Apache
require(".ht_config.php");
// END OF OPTIONS

// Get variables and prevent SQL injection. 
preg_match('/[A-Za-z0-9\-\. :]*/', $_REQUEST['filter_timestamp'], $match1);
preg_match('/[A-Za-z0-9\-\. :]*/', $_REQUEST['filter_log'],       $match2);
preg_match('/[A-Za-z0-9\-\. :]*/', $_REQUEST['filter_server'],    $match3);
$filter_timestamp = $match1[0];
$filter_log       = $match2[0];
$filter_server    = $match3[0];

// Create DB connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Form the SQL query.
$sql = "SELECT Priority, ReceivedAt, DeviceReportedTime, FromHost, Message, SysLogTag FROM SystemEvents WHERE 
(ReceivedAt LIKE '%" . $filter_timestamp . "%' OR DeviceReportedTime LIKE '%" . $filter_timestamp . "%') AND 
FromHost LIKE '%" . $filter_server . "%' AND
Message LIKE '%" . $filter_log . "%'
ORDER BY ID DESC LIMIT " . $max_results;
$result = mysqli_query($conn, $sql);

// Begin the HTML side of things. 
?>
<html>
<head>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>

<body>
<div class="container" style="margin-top:10px">

<img src="<?php echo $logo; ?>">
<h2><?php echo $title; ?></h2>

<div class="well">
<form method="POST" action="#">
Search <input class="form-control" type="search" name="filter_log" autofocus value="<?php echo $filter_log;?>">
Date <input class="form-control" type="search" name="filter_timestamp" value="<?php echo $filter_timestamp;?>">
Server <input class="form-control" type="search" name="filter_server" value="<?php echo $filter_server;?>">
<P></P>
<input class="btn btn-lg btn-primary btn-block" type="submit" value="Filter" style="width: 200px;"> 
</form>
</div>

<table class="table table-striped">
<thead>
<tr><th>Log time</th>
    <th>Event time</th>
    <th>Host</th>
    <th>Message</th>
    <th>Priority</th>
    <th>Service</th></tr>
</thead>

<?php

// Loop over the results. 
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>" . $row['ReceivedAt']. "</td>";
        echo "    <td>" . $row['DeviceReportedTime']. "</td>";
        echo "    <td>" . $row['FromHost']. "</td>";
        echo "    <td>" . $row['Message']. "</td>";
        echo "    <td>" . $row['Priority']. "</td>";
        echo "    <td>" . $row['SysLogTag']. "</td></tr>\n";
        $counter++;
    }
} else {
    echo "0 results";
}

?>
</div>
</table>

<?php
// Show the number of rows returned. 
echo "Results: " . $counter . " (max " . $max_results . ")";
?>

<hr>
<?php
    if ($srcLink) {
        echo "<a href='$srcLink'>$srcName</a>";
    } elseif ($srcName) {
        echo $srcName;
    } else {
	echo '<a href="http://www.agix.com.au">AGIX</a>';
    }
?>
</body>
</html>
<?php

mysqli_close($conn);
?>
