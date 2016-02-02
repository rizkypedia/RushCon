<?php namespace Onmeda\Controller;

use RushCon\Controller\Controller as AppController;
use Onmeda\Model\Config\database as database;

class HomeAppController extends AppController {
    
    protected $credentials;


    public function __construct() {
          parent::__construct();
          $this->credentials = new database();
          
    }
}

