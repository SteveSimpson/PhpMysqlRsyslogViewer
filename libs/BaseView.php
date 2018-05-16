<?php


namespace libs;


class BaseView
{
  
    
    /**
     * Path to logo image
     * @var string
     */
    public $logo;
    
    public $srcLink;
    
    public $srcName;
    
    public function header($title) {
        
        if (is_readable(dirname(dirname(__FILE__)) . "/vendor/css/bootstrap.min.css" )) {
            
            $head = <<<ENDHEAD
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
ENDHEAD;
            
        } else {
            
            $head = <<<ENDHEAD
<link rel="stylesheet" href="vendor/css/bootstrap.min.css" >
<link rel="stylesheet" href="vendor/css/bootstrap-grid.min.css" >
<link rel="stylesheet" href="vendor/css/bootstrap-reboot.min.css" >

<script src="vendor/js/bootstrap.min.js"></script>
<script src="vendor/js/jquery-slim.min.js"></script>
<script src="vendor/js/popper.min.js"></script>
ENDHEAD;
            
        }
        
        // Begin the HTML side of things.
        
        echo "<html>\n";
        echo "<head>\n";
        
        echo "<title>".$title."</title>\n";
        
        echo $head;
        
        echo "</head>\n";
        echo "<body>\n";
        echo "<div class='container' style='margin-top:10px;margin-bottom:30px'>";
        
        echo "<div class='container'><img src='". $this->logo . "' class='float-right'></div>";
        echo "<div class='row' style='margin-top:10px'><div class='col col-md-12'><h2>". $title . "</h2></div></div>";
    }
    
    
    public function footer() {
        echo "<hr>\n";
        
        if ($this->srcLink) {
            echo "<a href='".$this->srcLink."'>".$this->srcName."</a>";
        } elseif ($this->srcName) {
            echo $this->srcName;
        }
        
        
        echo "\n</div>\n";
        echo "</body>\n";
        echo "</html>\n";
    }
    
}