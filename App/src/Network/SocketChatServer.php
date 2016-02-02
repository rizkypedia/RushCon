<?php namespace RushCon\Network;

class SocketChatServer {
    private $__address = '127.0.0.1';   // 0.0.0.0 means all available interfaces
    private $__port = 25003;          // the TCP port that should be used
    private $__maxClients = 10;
 
    private $__clients;
    private $__socket;
    
    public function __construct() {
        // Set time limit to indefinite execution
        set_time_limit(0);
        // error_reporting(E_ALL ^ E_NOTICE);
    }
    
    public function start() {
        // Create a TCP Stream socket
        $this->__socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        // Bind the socket to an address/port
        socket_set_option($this->__socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($this->__socket, $this->__address, $this->__port);
        // Start listening for connections
        socket_listen($this->__socket, $this->__maxClients);
 
 
        $this->__clients = array('0' => array('socket' => $this->__socket));
        
        while (true) {
            // Setup clients listen socket for reading
            $read[0] = $this->__socket;
            for($i=1; $i<count($this->__clients)+1; ++$i) {
                if(isset($this->__clients[$i])) {
                    $read[$i+1] = $this->__clients[$i]['socket'];
                }
            }
 
            // Set up a blocking call to socket_select()
            $ready = socket_select($read, $write = NULL, $except = NULL, $tv_sec = NULL);
 
            /* if a new connection is being made add it to the client array */
            if(in_array($this->__socket, $read)) {
                for($i=1; $i < $this->__maxClients+1; ++$i) {
                    if(!isset($this->__clients[$i])) {
                        $this->clients[$i]['socket'] = socket_accept($this->socket);
                        socket_getpeername($this->__clients[$i]['socket'], $ip);
                        $this->__clients[$i]['ipaddy'] = $ip;
 
                        socket_write($this->__clients[$i]['socket'], 'Welcome to my Custom Socket Server'."\r\n");
                        socket_write($this->__clients[$i]['socket'], 'There are '.(count($this->__clients) - 1).' client(s) connected to this server.'."\r\n");
 
                        $this->log("New client #$i connected: " . $this->__clients[$i]['ipaddy']);
                        break;
                    } elseif($i == $this->__maxClients - 1) {
                        $this->log('Too many Clients connected!');
                    }
 
                    if($ready < 1) {
                        continue;
                    }
                }
            }
 
            // If a client is trying to write - handle it now
            for($i=1; $i<$this->__maxClients+1; ++$i) {
                if(in_array($this->__clients[$i]['socket'], $read)) {
                    $data = @socket_read($this->__clients[$i]['socket'], 1024, PHP_NORMAL_READ);
 
                    if($data === FALSE) {
                        unset($this->__clients[$i]);
                        $this->log('Client disconnected!');
                        continue;
                    }
 
                    $data = trim($data);
 
                    if(!empty($data)) {
                        switch ($data) {
                            case 'exit':
                            case 'quit':
                                socket_write($this->__clients[$i]['socket'], "Thanks for trying my Custom Socket Server, Goodbye.\r\n");
                                $this->log("Client #$i is exiting");
                                unset($this->__clients[$i]);
                                continue;
                            case 'term':
                                // first write a message to all connected clients
                                for($j=1; $j < $this->__maxClients+1; ++$j) {
                                    if(isset($this->__clients[$j]['socket'])) {
                                        if($this->__clients[$j]['socket'] != $this->socket) {
                                            socket_write($this->__clients[$j]['socket'], "Server will be shut down now...\r\n");
                                        }
                                    }
                                }
                                // Close the master sockets, server termination requested
                                socket_close($this->__socket);
                                $this->log("Terminated server (requested by client #$i)");
                                exit;
                            default:
                                for($j=1; $j < $this->__maxClients+1; ++$j) {
                                    if(isset($this->__clients[$j]['socket'])) {
                                        if(($this->__clients[$j]['socket'] != $this->__clients[$i]['socket']) && ($this->__clients[$j]['socket'] != $this->__socket)) {
                                            $this->log($this->__clients[$i]['ipaddy'] . ' is sending a message to ' . $this->__clients[$j]['ipaddy'] . '!');
                                            socket_write($this->__clients[$j]['socket'], '[' . $this->__clients[$i]['ipaddy'] . '] says: ' . $data . "\r\n");
                                        }
                                    }
                                }
                                break(2);
                        }
                    }
                }
            }
        } // end while
    }
 
    private function log($msg) {
        // instead of echoing to console we could write this to a database or a textfile
        echo "[".date('Y-m-d H:i:s')."] " . $msg . "\r\n";
    }
    
}

