<?php
/*
Copyright (c) 2018 Steve Simpson

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

Original Script by:
Andrew Galdes (AGIX), andrew.galdes@agix.com.au
http://www.agix.com.au

A script to allow simplified log filtering. 

Refer to the following article for more information:
    http://www.agix.com.au/rsyslog-and-mysql-on-centos7-and-redhat-7/

You'll want to protect this page to prevent unauthorised access to your
system logs. 

*/

require_once(dirname(__FILE__, 2) . "/libs/app.php");

$app = new libs\App();

$resultCount = 0;

// END OF OPTIONS

// Get variables and prevent SQL injection. 
if (isset($_REQUEST['filter_log'])) {
    preg_match('/[A-Za-z0-9\-\. :]*/', $_REQUEST['filter_timestamp'],    $match1);
    preg_match('/[A-Za-z0-9\-\. :]*/', $_REQUEST['filter_log'],          $match2);
    preg_match('/[A-Za-z0-9\-\. :]*/', $_REQUEST['filter_server'],       $match3);
    preg_match('/[A-Za-z0-9\-\. :]*/', $_REQUEST['filter_tag'],          $match4);
    preg_match('/[A-Za-z0-9\-\. :]*/', $_REQUEST['filter_timestamp_to'], $match5);
    $resultCount = intval($_REQUEST['resultCount']);
    $filter_timestamp    = $match1[0];
    $filter_log          = $match2[0];
    $filter_server       = $match3[0];
    $filter_tag          = $match4[0];
    $filter_timestamp_to = trim($match5[0]);
} else {
    $filter_timestamp    = '';
    $filter_log          = '';
    $filter_server       = '';
    $filter_tag          = '';
    $filter_timestamp_to = '';
}

if ($resultCount == 0) {
    $resultCount = $app->config['default_results'];
} 

if ($resultCount == $app->config['default_results']) {
    $resultCountText = '';
} else {
    $resultCountText = $resultCount;
}

// Form the SQL query.
$sql = "SELECT Priority, ReceivedAt, DeviceReportedTime, FromHost, Message, SysLogTag FROM SystemEvents WHERE ";

// no _to timestamp, work the orignal way
if ($filter_timestamp_to == "") {
    $sql .= "(ReceivedAt LIKE '%" . $filter_timestamp . "%' OR DeviceReportedTime LIKE '%" . $filter_timestamp . "%') AND ";
} else {
    $sql .= "(ReceivedAt >= '" . $filter_timestamp    . "' OR DeviceReportedTime >= '" . $filter_timestamp    . "') AND ";
    $sql .= "(ReceivedAt <= '" . $filter_timestamp_to . "' OR DeviceReportedTime <= '" . $filter_timestamp_to . "') AND ";
}

$sql .= "FromHost LIKE '%" . $filter_server . "%' AND
Message LIKE '%" . $filter_log . "%' AND
SysLogTag LIKE '%" . $filter_tag . "%'
ORDER BY ID DESC LIMIT " . $resultCount;

$result = $app->dbQuery($sql);

$app->startPage('Log Filter');
?>



<div class="well">
<form method="POST" action="#">
<div class="row" style="margin-top:10px"><div class="col col-md-12"><input class="form-control"    type="text" placeholder="Search Message" name="filter_log" autofocus value="<?php echo $filter_log;?>"></div></div>
<div class="row" style="margin-top:10px"><div class="col col-md-6"><input class="form-control"     type="text" placeholder="From Date" name="filter_timestamp" value="<?php echo $filter_timestamp;?>"></div>
                 <div class="col col-md-6"><input class="form-control"     type="text" placeholder="To Date" name="filter_timestamp_to" value="<?php echo $filter_timestamp_to;?>"></div></div>
<div class="row" style="margin-top:10px"><div class="col col-md-4"><input class="form-control"     type="text" placeholder="Server" name="filter_server" value="<?php echo $filter_server;?>"></div>
				 <div class="col col-md-4"><input class="form-control"     type="text" placeholder="Result Count" name="resultCount" value="<?php echo $resultCountText;?>"></div>
                 <div class="col col-md-4"><input class="form-control"     type="text" placeholder="Service" name="filter_tag" value="<?php echo $filter_tag;?>"></div></div>
<div class="row" style="margin-top:10px"><div class="col col-md-12"><input class="btn btn-primary" type="submit" value="Filter" style="width: 200px;"> </div></div>
</form>
</div>

<table class="table table-striped">
<thead>
<tr><th>Log time</th>
    <th>Event time</th>
    <th>Host</th>
    <th>Message</th>
    <th>Priority</th>
    <th>Service <a href='services.php' target='_blank'>^</a></th></tr>
</thead>

<?php
$counter = 0;

// Loop over the results. 
if ($app->dbRows() > 0) {
    // output data of each row
    while($row = $app->dbFetchAssoc()) {
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

</table>

<?php
// Show the number of rows returned. 
echo "Results: " . $counter . " (max " . $resultCount . ")";

$app->endPage();
?>
