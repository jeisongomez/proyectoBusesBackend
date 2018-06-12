<?php

require_once '../vendor/autoload.php';

$app = new \Slim\Slim();

$db = new mysqli("localhost", "root", "", "uvexpress");

// Configuración de cabeceras
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}

$app->post('/add-factura', function() use($app, $db){
	$json = $app->request->post('json');
	$data = json_decode($json, true);

    $query = " INSERT INTO `facturacion`(`Rutas_idRutas`, `Cliente_idCliente`, `Empleados_idEmpleados`, `precioV`) 
               VALUES ({$data["Rutas_idRutas"]},{$data["Cliente_idCliente"]},{$data["idEmpleado"]},{$data["precio"]}) ";
	
	//var_dump($query);

	$insert = $db->query($query);

	$result = array(
		'status' => 'error',
		'code'	 => 404,
		'message' => 'la factura NO se ha creado'
	);

	if($insert){
		$result = array(
			'status' => 'success',
			'code'	 => 200,
			'message' => 'Factura creada correctamente'
		);
	}

	echo json_encode($result);
});

$app->get('/get-facturas', function() use($db, $app){

	$sql = " SELECT `idFacturacion`, `idEmpleados`, `Documento`, `Orgien`, `Destino`, `Precio`, `Fecha_Hora` 
			 FROM `facturacion` INNER JOIN cliente ON cliente.idCliente=facturacion.Cliente_idCliente 
			 INNER JOIN rutas ON rutas.idRutas=facturacion.Rutas_idRutas INNER JOIN empleados 
			 ON empleados.idEmpleados=facturacion.Empleados_idEmpleados ";

	$query = $db->query($sql);

	//var_dump($sql);
	//var_dump($query);

	$productos = array();
	while ($producto = $query->fetch_assoc()) {
		$productos[] = $producto;
	}

	//var_dump($productos);

	$result = array(
			'status' => 'success',
			'code'	 => 200,
			'data' => $productos
		);

	echo json_encode($result);
});

$app->run();