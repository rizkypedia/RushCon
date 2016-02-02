<?php namespace Onmeda\Controller;

use Onmeda\Controller\HomeAppController as OnmedaAppController;
use RushCon\Core\Console;
use RushCon\Core\Register;

class HomeController extends OnmedaAppController {
    
    private  $__tables = array();
    private $__articles;
    public function __construct() {
        parent::__construct();        
        $this->__articles = Register::table("Onmeda.Articles", $this->credentials);
    }
    
    public function indexAction($articelId = null) {
        $conditions = array();
        if (!empty($articelId)) {
            $conditions = array("Articles.id" => $articelId);
        }
        $arts = $this->__articles->find(array(
            'conditions' => $conditions
        ));
        
        
        foreach ($arts as $articles => $article) {
            Console::pprintln($article['id']);
            Console::pprintln($article['title']);
        }
    }
    
}
