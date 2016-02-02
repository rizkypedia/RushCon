<?php namespace Onmeda\Controller;

use Onmeda\Controller\HomeAppController as OnmedaAppController;
use RushCon\Core\Console;
use RushCon\Network\SocketChatServer as SocketChat;
use RushCon\Network\SocketServer as SockerServer;

class ChatController extends OnmedaAppController {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function indexAction() {
        $s = new SockerServer();
        $s->start();
        /*$s = new SocketChat();
        $s->start();*/
    }
    
}


