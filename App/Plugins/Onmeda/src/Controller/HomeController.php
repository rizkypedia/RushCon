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
        //$this->__articles = Register::table("Onmeda.Articles", $this->credentials);
        //$this->__mongoSourceClient = new Mongo();
    }
    
    public function indexAction($articelId = null) {
        $conditions = array();
        if (!empty($articelId)) {
            $conditions = array("Articles.id" => $articelId);
        }
        Console::pprintln($this->encrypt_password("aufeminin100%"));
        /*$arts = $this->__articles->find(array(
            'conditions' => $conditions
        ));
        
        
        foreach ($arts as $articles => $article) {
            Console::pprintln($article['id']);
            Console::pprintln($article['title']);
        }*/
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
    
    public function encrypt_password($plain) {
        $password="";
    	for ($i=0; $i<10; $i++) {
      		$password .= $this->mk_rand();
    	}

    	$salt = substr(md5($password), 0, 2);

    	$password = md5($salt . $plain) . ':' . $salt;

   	return $password;
  }
  
  public function mk_rand($min = null, $max = null) {
    static $seeded;

    if (!isset($seeded)) {
      mt_srand((double)microtime()*1000000);
      $seeded = true;
    }

    if (isset($min) && isset($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
  }
    
}
