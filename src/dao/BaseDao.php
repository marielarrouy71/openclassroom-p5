<?php

namespace App\Dao;

use PDO;
use PDOException;

require_once('property.ini.php');

class BaseDao{

    protected $db;

    public function __construct(){
      /*  $env = parse_ini_file($_SERVER["DOCUMENT_ROOT"].'/src/env');*/



        try {
            $this->db = new PDO("mysql:dbname=".db_name.";host=".db_host, db_user, db_password);



            /*$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);*/
        } catch (PDOException $e) {
            echo 'Connexion échouée : ' . $e->getMessage();
        }
    }





}
