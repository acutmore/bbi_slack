<?php

class Workers {

    static private function getPort(){
      return $_SERVER['SERVER_PORT'];
    }

    static private function getHost(){
      return explode(':', $_SERVER['HTTP_HOST'])[0];
    }

    static private function getWorker($worker){
      return '/' . dirname($_SERVER['REQUEST_URI']) . '/' . $worker . '.php';
    }

    static public function sendMessage($worker, $data){
      $data = http_build_query($data);

      $fp = fsockopen(static::getHost(), static::getPort(), $errno, $errstr, 30);
      if (!$fp) {
          echo "$errstr ($errno)<br />\n";
      } else {
          $resource_uri = static::getWorker($worker) . '?' . $data;
          $out = "GET " . $resource_uri . " HTTP/1.1\r\n";
          $out .= "Host: " . static::getHost() . "\r\n";
          $out .= "Connection: Close\r\n\r\n";
          fwrite($fp, $out);
          /*
          while (!feof($fp)) {
              echo fgets($fp, 128);
          }
          */
          fclose($fp);
      }

    }

}



?>
