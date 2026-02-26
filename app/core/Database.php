<?php

class Database
{

    private ?\PDO $connection = null;

    public function getConnection(): \PDO
    {
       if($this->connection){
        return $this->connection;
       }
    
        $host = getenv('DB_HOST') ?: 'localhost';
        $dbname = getenv('DB_NAME') ?: 'sistemaoperacionaldasbrasil';
        $username = getenv('DB_USERNAME') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';
        $charset = getenv('DB_CHARSET') ?: 'utf8mb4';

        $dns = "mysql:host={$host};dbname={$dbname};charset={$charset}";

         $opcoes = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
         ];

         try{
            $this->connection = new \PDO($dns, $username, $password, $opcoes);
            return $this->connection;
         }catch (\PDOException $e){
            throw new \Exception("ERRO DE CONEXÃO COM BANCO DE DADOS " . $e->getMessage());    
         };     
    }
   
}


?>