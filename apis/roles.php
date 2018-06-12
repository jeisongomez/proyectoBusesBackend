<?php

require_once '../vendor/autoload.php';

$app = new \Slim\Slim();

$db = new mysqli('localhost', 'root', '', 'uvexpress');

// Configuración de cabeceras
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}

//prueba para el API
$app->get("/pruebas", function() use($app, $db){
	echo "Hola mundo desde Slim PHP para clientes";
});

$app->post('/add-roles', function() use($app, $db){
	$json = $app->request->post('json');
    $data = json_decode($json, true);
    
    $query = " INSERT INTO `roles`(`CrearFactura`, `ConsultarFactura`, `CrearEmpleado`,
               `EditarEmpleado`, `BorrarEmpleado`, `ConsultarEmpleado`, `CrearRutas`, 
               `EditarRutas`, `ConsultarRutas`, `CrearCliente`, `EditarCliente`, 
               `ConsultarCliente`, `CrearBuses`, `EditarBuses`, `ConsultarBuses`)
               VALUES ('{$data["CrearFactura"]}','{$data["ConsultarFactura"]}','{$data["CrearEmpleado"]}',
               '{$data["EditarEmpleado"]}','{$data["BorrarEmpleado"]}','{$data["ConsultarEmpleado"]}',
               '{$data["CrearRutas"]}','{$data["EditarRutas"]}','{$data["ConsultarRutas"]}',
               '{$data["CrearCliente"]}','{$data["EditarCliente"]}','{$data["ConsultarCliente"]}',
               '{$data["CrearBuses"]}','{$data["EditarBuses"]}','{$data["ConsultarBuses"]}') ";
	
	//var_dump($query);

	$insert = $db->query($query);

	$result = array(
		'status' => 'error',
		'code'	 => 404,
		'message' => 'El Rol NO se ha creado'
	);

	if($insert){
		$result = array(
			'status' => 'success',
			'code'	 => 200,
			'message' => 'roles creado correctamente'
		);
	}

	echo json_encode($result);
});

$app->get('/get-id-rol', function() use($db, $app){
	$sql = " SELECT `idRoles` FROM roles WHERE `idRoles` = ( SELECT MAX( `idRoles` ) FROM roles) ";
	$query = $db->query($sql);

	$empleados = array();
	while ($empleado = $query->fetch_assoc()) {
		$empleados[] = $empleado;
	}

	$result = array(
			'status' => 'success',
			'code'	 => 200,
			'data' => $empleados
		);

	echo json_encode($result);
});

$app->run();
