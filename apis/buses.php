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

//prueba para el API
$app->get("/pruebas", function() use($app, $db){
	echo "Hola mundo desde Slim PHP para buses";
});

//mostrar todos los buses
$app->get('/buses', function() use($db, $app){
	$sql = 'SELECT * FROM buses;';
	$query = $db->query($sql);

	$buses = array();
	while ($bus = $query->fetch_assoc()) {
		$buses[] = $bus;
	}

	$result = array(
			'status' => 'success',
			'code'	 => 200,
			'data' => $buses
		);

	echo json_encode($result);
});

//obtener un bus
$app->post('/bus-actual', function() use($db, $app){
	$json = $app->request->post('json');
	$data = json_decode($json, true);
	
	$sql = "SELECT * FROM `buses` WHERE `idBuses` = {$data["id"]} ";

	$query = $db->query($sql);

    $result = array(
			'status' 	=> 'error',
			'code'		=> 400,
			'message' 	=> 'bus no disponible'
		);
    
    //var_dump($sql);
    //var_dump($query);

	if($query->num_rows == 1){
		$usuario = $query->fetch_assoc();

        $result = array(
			'status' 	=> 'success',
			'code'		=> 200,
			'data' 	=> $usuario
		);
	}

	echo json_encode($result);

});

//guardar un bus
$app->post('/add-bus', function() use($app, $db){
	$json = $app->request->post('json');
	$data = json_decode($json, true);

	if(!isset($data['Placa'])){
		$data['Placa']=null;
	}

	if(!isset($data['CapacidadPasajeros'])){
		$data['CapacidadPasajeros']=null;
	}

	$query = "INSERT INTO buses VALUES(NULL,".
			 "'{$data['Placa']}',".
			 "'{$data['CapacidadPasajeros']}'".
			 ")";

	$insert = $db->query($query);

	$result = array(
		'status' => 'error',
		'code'	 => 404,
		'message' => 'Bus NO se ha creado'
	);

	if($insert){
		$result = array(
			'status' => 'success',
			'code'	 => 200,
			'message' => 'Bus creado correctamente'
		);
	}

	echo json_encode($result);
});

$app->run();
