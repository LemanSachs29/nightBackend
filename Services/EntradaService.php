<?php

require_once './../Database/Database.php';

class EntradaService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function restaStock(int $id_entrada, int $cantidad): bool
    {

        $result = false;
        try{

            $query = "UPDATE entrada 
            SET stock_actual = stock_actual - :cantidad 
            WHERE id_entrada = :id_entrada";

            $statement = $this->db->prepare($query);

            $result = $statement->execute([":cantidad" => $cantidad, ":id_entrada" => $id_entrada]);


        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $result;
    }

}


