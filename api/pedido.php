<?php

require_once './../Database/Database.php';
require_once './../Services/PedidoService.php';
require_once './../Services/Detalles_pedidoService.php';
require_once './../Services/EntradaService.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // 1. Recibir JSON desde el cuerpo de la solicitud
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    // 2. Validar estructura JSON
    if (!isset($data["usuario"]) || !isset($data["carrito"]) || !is_array($data["carrito"])) {
        throw new Exception("JSON inválido. Se requiere 'usuario' y 'carrito' como array.");
    }

    $email = $data["usuario"];
    $carrito = $data["carrito"];

    // 3. Instanciar los servicios
    $pedidoService = new PedidoService();
    $detalleService = new Detalles_pedidoService();
    $entradaService = new EntradaService();

    // 4. Crear el pedido en la base de datos
    $pedidoService->CreatePedido($email);
    $id_pedido = $pedidoService->FindPedido($email);

    if (!$id_pedido) {
        throw new Exception("Error al crear el pedido.");
    }

    // 5. Registrar los detalles del pedido
    $detalleService->CreateDetalle($id_pedido, $carrito);

    // 6. Calcular el total del pedido
    $total = $pedidoService->getTotalByPedido($id_pedido);

    foreach ($data["carrito"] as $producto) {
        $entradaService->restaStock($producto["id_producto"], $producto["cantidad"]);
    }

    // 7. Responder con éxito y el total calculado
    echo json_encode([
        "success" => true,
        "message" => "Pedido registrado correctamente.",
        "id_pedido" => $id_pedido,
        "total" => $total
    ]);

} catch (Exception $e) {
    // Capturar errores y enviar respuesta en JSON
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
