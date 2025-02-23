<?php

require_once './../Database/Database.php';

class PedidoService
{
    private PDO $db;
    private string $table = "pedido";

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function CreatePedido(string $email): bool
    {

        $result = false;
        try {
            $query = "INSERT INTO pedido (id_usuario) 
	                  VALUES (
                                (SELECT id 
			                    FROM directus_users 
			                    WHERE email = :email)
                            )";

            $statement = $this->db->prepare($query);

            $result = $statement->execute([":email" => $email]);


        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $result;
    }

    public function FindPedido($email): int|false
{
    $id_pedido = false; 

    try {
        $query = "SELECT p.id_pedido
                  FROM pedido p
                  WHERE p.id_usuario = (SELECT id FROM directus_users WHERE email = :email)
                  AND p.id_pedido NOT IN (SELECT id_pedido FROM detalles_pedido)
                  ORDER BY p.fecha_pedido DESC
                  LIMIT 1";

        $statement = $this->db->prepare($query);
        $statement->execute([":email" => $email]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        
        if ($result && isset($result["id_pedido"])) {
            $id_pedido = (int) $result["id_pedido"];
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    return $id_pedido;
}

    public function getTotalByPedido(int $id_pedido): float
    {
        $result = 0.0;
        try {
            $query = "SELECT SUM(e.precio * dp.cantidad) AS total
                        FROM detalles_pedido dp
                        JOIN entrada e ON dp.id_entrada = e.id_entrada
                        WHERE dp.id_pedido = :id_pedido";

            $statement = $this->db->prepare($query);

            $statement->execute([":id_pedido" => $id_pedido]);

            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result["total"])) {
                $total = (float) $result["total"]; // Convertir a float para evitar errores con null
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        return $total;
    }

}