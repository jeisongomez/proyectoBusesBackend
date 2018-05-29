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
	echo "Hola mundo desde Slim PHP para destinos";
});

//obtener rutas
$app->post('/getrutas', function() use($app, $db){
	$json = $app->request->post('json');
	$data = json_decode($json, true);

	$sql = "SELECT * FROM rutas WHERE Origen = '{$data["ubicacion"]}' ";

	$query = $db->query($sql);

	$result = array(
		'status' 	=> 'error',
		'code'		=> 400,
		'message' 	=> 'ruta no disponible'
	);

	//var_dump($sql);
	//var_dump($query);

	if($query->num_rows >= 1){
		$rutas = array();
		while ($ruta = $query->fetch_assoc()) {
			$rutas[] = $ruta;
		}

		$result = array(
				'status' => 'success',
				'code'	 => 200,
				'data' => $rutas
			);
	}

	echo json_encode($result);
});

//guardar destinos
$app->post('/add-destinos', function() use($app, $db){
	$json = $app->request->post('json');
	$data = json_decode($json, true);

	if(!isset($data['Nombre_cliente'])){
		$data['Nombre_cliente']=null;
	}

	if(!isset($data['Apellido_cliente'])){
		$data['Apellido_cliente']=null;
    }
    
    if(!isset($data['Documento_cliente'])){
		$data['Documento_cliente']=null;
    }

	$query = "INSERT INTO cliente VALUES(NULL,".
			 "'{$data['Nombre_cliente']}', ".
             "'{$data['Apellido_cliente']}', ".
             "'{$data['Documento_cliente']}'".
			 ")";

	$insert = $db->query($query);

	$result = array(
		'status' => 'error',
		'code'	 => 404,
		'message' => 'El cliente NO se ha creado'
	);

	if($insert){
		$result = array(
			'status' => 'success',
			'code'	 => 200,
			'message' => 'Cliente creado correctamente'
		);
	}

	echo json_encode($result);
});

$app->run();