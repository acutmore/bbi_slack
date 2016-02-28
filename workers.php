<?php

class Workers {

    static private function getPort(){
      return $_SERVER['SERVER_PORT'];
    }

    static private function getAddress(){
      return empty($_SERVER['SERVER_ADDR']) ?
        static::getHTTPHost() : $_SERVER['SERVER_ADDR'];
    }

    static private function getHTTPHost(){
      return explode(':', $_SERVER['HTTP_HOST'])[0];
    }

    static private function getWorker($worker){
      return '/' . dirname($_SERVER['REQUEST_URI']) . '/' . $worker . '.php';
    }

    /**
    Send a HTTP GET to another php script to run then close connection immediately
    */
    static public function sendMessage($worker, $data){

      $data = http_build_query($data);
      $fp = fsockopen(static::getAddress(), static::getPort(), $errno, $errstr, 10);

      if (!$fp) {
          echo "$errstr ($errno)<br />\n";
      } else {
          $resource_uri = static::getWorker($worker) . '?' . $data;
          $out = "GET " . $resource_uri . " HTTP/1.1\r\n";
          $out .= "Host: " . static::getHTTPHost() . "\r\n";
          $out .= "Connection: Close\r\n\r\n";
          fwrite($fp, $out);
          fclose($fp);
      }

    }

}



?>
