<?php
namespace Go\Controller;

use Go\Controller\MainAppController as GoAppController;
use RushCon\Core\Console;

class HomeController extends GoAppController {
    public function __construct() {
        parent::__construct();        
        //$this->__articles = Register::table("Onmeda.Articles", $this->credentials);
        //$this->__mongoSourceClient = new Mongo();
    }
    
    public function indexAction() {
        Console::pprintln("test");
    }
}
