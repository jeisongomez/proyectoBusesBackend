<?php

require_once '../vendor/autoload.php';

$app = new \Slim\Slim();

$db = new mysqli('localhost', 'root', '', 'buses');

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

//mostrar un bus
$app->get('/buses/:id', function($id) use($db, $app){
	$sql = 'SELECT * FROM buses WHERE idBuses = '.$id;
	$query = $db->query($sql);

	$result = array(
		'status' 	=> 'error',
		'code'		=> 404,
		'message' 	=> 'bus no disponible'
	);

	if($query->num_rows == 1){
		$bus = $query->fetch_assoc();

		$result = array(
			'status' 	=> 'success',
			'code'		=> 200,
			'data' 	=> $bus
		);
	}

	echo json_encode($result);
});

//eliminar un bus
$app->get('/delete-bus/:id', function($id) use($db, $app){
	$sql = 'DELETE FROM buses WHERE idBuses = '.$id;
	$query = $db->query($sql);

	if($query){
		$result = array(
			'status' 	=> 'success',
			'code'		=> 200,
			'message' 	=> 'El bus se ha eliminado correctamente!!'
		);
	}else{
		$result = array(
			'status' 	=> 'error',
			'code'		=> 404,
			'message' 	=> 'El bus no se ha eliminado!!'
		);
	}

	echo json_encode($result);
});

//actualizar un bus
$app->post('/update-bus/:id', function($id) use($db, $app){
	$json = $app->request->post('json');
	$data = json_decode($json, true);

    $sql = "UPDATE buses SET ".
		   "Placa = '{$data["Placa"]}', ".
           "CapacidadPasajeros = '{$data["CapacidadPasajeros"]}' ".
           "WHERE idBuses = {$id}";
           
	$query = $db->query($sql);

	if($query){
		$result = array(
			'status' 	=> 'success',
			'code'		=> 200,
			'message' 	=> 'El bus se ha actualizado correctamente!!'
		);
	}else{
		$result = array(
			'status' 	=> 'error',
			'code'		=> 404,
			'message' 	=> 'El bus no se ha actualizado!!'
		);
    }
    
    //echo var_dump($json);
    //echo var_dump($data);
    //echo var_dump($sql);
    //echo var_dump($query);
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
