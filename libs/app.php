<?php
/**
 * @author Steve Simpson
 *
 */
namespace libs;


require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';
require_once dirname(__FILE__).'/BaseView.php';

class App
{
    /**
     * 
     * @var BaseView
     */
    public $view;
    
    /**
     * 
     * @var mixed array
     */
    public $config;
    
    public $db;
    
    private $_db_result;
    
    public function __construct(array $arguments = []) {
        if (!empty($arguments)) {
            foreach ($arguments as $property => $argument) {
                $this->{$property} = $argument;
            }
        } 
        
        if (!isset($this->config)) {
            if (!is_readable( dirname(dirname(__FILE__))."/config/config.php")) {
                die('Pleaase add and configure ' . dirname(dirname(__FILE__))."/config/config.php");
            }
            
            $this->config = require(dirname(dirname(__FILE__))."/config/config.php");
            
            //print_r($this->config); die();
        }
        
        if (!isset($this->view)) {
            $this->view = new BaseView();
            
            $this->view->logo = $this->config['logo'];
        }
        
        if (!isset($this->db)) {
            // Create DB connection
            $this->db = mysqli_connect($this->config['db_host'], $this->config['db_user'], $this->config['db_pass'], $this->config['db_name']);
            // Check connection
            if (!$this->db) {
                die("Connection failed: " . mysqli_connect_error());
            }
        }
    }
    
    public function startPage($title)
    {
        $this->view->header($title);
    }
    
    public function endPage()
    {
        $this->view->footer();
        
        mysqli_close($this->db);
    }
    
    public function dbQuery($sql) 
    {
        $this->_db_result = mysqli_query($this->db, $sql);
        
        return $this->_db_result;
    }
    
    public function dbRows($result = false)
    {
        if (!$result) {
            $result = $this->_db_result;
        }
        
        return mysqli_num_rows($result);
    }
    
    public function dbFetchAssoc($result = false)
    {
        if (!$result) {
            $result = $this->_db_result;
        }
        
        return mysqli_fetch_assoc($result);
    }
}