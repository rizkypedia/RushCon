<?php namespace Onmeda\Controller;

use Onmeda\Controller\HomeAppController as OnmedaAppController;
use RushCon\Core\Console;
use RushCon\Core\Register;
use Onmeda\Model\Config\MongoSource as Mongo;

class HomeController extends OnmedaAppController {
    
    private  $__tables = array();
    private $__articles;
    private $__mongoSource;
    public function __construct() {
        parent::__construct();        
        $this->__articles = Register::table("Onmeda.Articles", $this->credentials);
        $this->__mongoSourceClient = new Mongo();
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
    
    public function mongoAction() {
        $src = $this->__mongoSourceClient->getSource();
        $db = $src->selectDB('myDatabase');
        $col = new \MongoCollection($db, 'ebooks');
        //var_dump($col);
        $query = array('name' => 'JavaScript');

        $ebooks = $col->find($query);
        foreach ($ebooks as $book) {
            var_dump($book);
        }
                
    }
    
}
