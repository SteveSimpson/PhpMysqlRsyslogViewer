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
*/


require_once(dirname(__FILE__, 2) . "/libs/app.php");

$app = new libs\App();

// Form the SQL query.
$sql = "select substring_index(substring_index(SysLogTag, '[', 1), ':', 1) as service from SystemEvents group by service order by service";

$result = $app->dbQuery($sql);

// Begin the HTML side of things. 
$app->startPage('Log Service List');

$counter = 0;

// Loop over the results. 
if ($app->dbRows() > 0) {
    // output data of each row
    
    echo "<ul class='list-group'>\n";
    
    while($row = $app->dbFetchAssoc()) {
        echo "  <li class='list-group-item'>" . $row['service']. "</li>\n";
        $counter++;
    }
    
    echo "</ul>\n";
    
    echo "<br /><br />\nServices: " . $counter ."\n";
} else {
    echo "0 results";
}

// Show the number of rows returned.


$app->endPage();
?>
