<?php

require_once './../Database/Database.php';

class Detalles_pedidoService
{
    private PDO $db;
    private string $table = "pedido";

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function CreateDetalle(int $id_pedido, array $carrito): bool
    {

        $result = false;
        try {
            $query = "INSERT 
            INTO detalles_pedido (id_pedido, id_entrada, cantidad) 
            VALUES (:id_pedido, :id_entrada, :cantidad)";

            $statement = $this->db->prepare($query);

            foreach ($carrito as $producto) {
                $id_entrada = $producto["id_producto"];
                $cantidad = $producto["cantidad"];
            
                $result = $statement->execute([
                    ":id_pedido" => $id_pedido,
                    ":id_entrada" => $id_entrada,
                    ":cantidad" => $cantidad
                ]);
            }


        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $result;
    }

}