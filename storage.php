<?php

class Storage {

    static private function path(){
      return dirname($_SERVER['SCRIPT_FILENAME']) . "/storage/";
    }

    static private function init(){
      $htaccess_file = static::path() . ".htaccess" ;
      if (!file_exists($htaccess_file)){
        file_put_contents($htaccess_file, "order deny,allow \n deny from all \n allow from 127.0.0.1");
      }
    }

    private $prefix;

    function __construct($name){
        static::init();
        $this->prefix = "\$\$_" . $name . "_\$\$_";
    }

    private function file($name){
        return static::path() . $this->prefix . $name;
    }

    public function put($name, $data){
        $file = $this->file($name);
        $json = json_encode($data);
        file_put_contents($file, $json);
    }

    public function get($name){
        $file = $this->file($name);

        if (file_exists($file)){
          $json = file_get_contents($file);
          return json_decode($json);
        }

        return NULL;
    }

}

?>
