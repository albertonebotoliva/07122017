<?php
/*
  Generic class for the conexion to the database and general query's

  NOTE: Please review your connection credentials

  Methods:
    - connect
    - close
    - checkTable - Checks if the table exists in the DB.
    - getPopular
    - delete
    - insert    
    - filter

  author: Alberto Nebot Oliva
  email: albertonebotoliva@gmail.com
*/
class Database{
    private $host, $usr, $pwd, $db;
    private $conn;

    public function __construct()
    {
        $this->host = "localhost";
        $this->usr  = "root";
        $this->pwd  = "";
        $this->db   = "nps";
    }

    public function connect()
    {
        $this->conn = new mysqli('p:'.$this->host, $this->usr, $this->pwd, $this->db);
    }
    private function close(){
        mysqli_close($this->conn);
    }
    private function checkTable($tb):bool{
      $this->connect();
      $result = $this->conn->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '$this->db' AND table_name = '$tb'");
      $data = $result->fetch_all(MYSQLI_ASSOC);
      $result->free();
      $this->close();

      if(isset($data[0]['table_name'])){
        return true;
      }
      return false;
    }
    //To get the most popular entities
    public function getPopular($tb = 'entities')
    {
      $existResource = $this->checkTable($tb);
      if($existResource){
        $this->connect();
        $result = $this->conn->query("SELECT name, count(name) as count FROM entities GROUP BY name ORDER BY count desc limit 25");
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $this->close();      
        return $data;
      }

      return false;
    }
    public function mostSentiment($tb = 'entities')
    {
      $existResource = $this->checkTable($tb);
      if($existResource){
        $this->connect();
        $result = $this->conn->query("SELECT comment, name, score FROM entities GROUP BY comment ORDER BY score desc limit 25");
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $this->close();      
        return $data;
      }

      return false;
    }
    public function delete($tb,$where){
      $existResource = $this->checkTable($tb);
      if($existResource){
        $this->connect();

        $result = $this->conn->query(sprintf("DELETE FROM $tb WHERE %s",strip_tags($where)));

        $this->close();

        return true;
      }
      return false;
    }
    public function insert($tb,$data)
    {
      $existResource = $this->checkTable($tb);
      if($existResource){
        $this->connect();

        $keys = implode(', ',array_keys($data));
        $values = "'".implode("','", $data)."'";

        $result = $this->conn->query(sprintf("INSERT INTO $tb ($keys) VALUES (%s)",strip_tags($values)));

        $this->close();

        return true;
      }
      return false;
    }

    public function filter($tb, $where, $select)
    {
      $existResource = $this->checkTable($tb);
      if($existResource){
        $this->connect();

        $result = $this->conn->query(sprintf("SELECT $select FROM $tb WHERE %s",strip_tags($where)));
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $this->close();

        return $data;
      }
      return array();
    }



}

?>
