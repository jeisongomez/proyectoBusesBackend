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


//verificar si el cliente existe
$app->post('/clienteExist', function() use($db, $app){
	$json = $app->request->post('json');
    $data = json_decode($json, true);
    
    $sql = "SELECT * FROM cliente WHERE	Documento = '{$data["Documento"]}'";

	$query = $db->query($sql);

    $result = array(
			'status' 	=> 'success',
			'code'		=> 200,
			'message' 	=> 'el cliente no existe'
		);
    
    //var_dump($sql);
    //var_dump($query);

	if($query->num_rows == 1){
		$usuario = $query->fetch_assoc();

        $result = array(
			'status' 	=> 'error',
			'code'		=> 400,
			'message'  => 'el cliente ya existe',
			'data' 	=> $usuario
		);
	}

	echo json_encode($result);

});

//mostrar todos los clientes 
$app->get('/clientes', function() use($db, $app){
	$sql = 'SELECT * FROM cliente;';
	$query = $db->query($sql);

	$clientes = array();
	while ($cliente = $query->fetch_assoc()) {
		$clientes[] = $cliente;
	}

	$result = array(
			'status' => 'success',
			'code'	 => 200,
			'data' => $clientes
		);

	echo json_encode($result);
});

//mostrar un cliente
$app->get('/clientes/:id', function($id) use($db, $app){
	$sql = 'SELECT * FROM cliente WHERE idCliente = '.$id;
	$query = $db->query($sql);

	$result = array(
		'status' 	=> 'error',
		'code'		=> 404,
		'message' 	=> 'cliente no disponible'
	);

	if($query->num_rows == 1){
		$cliente = $query->fetch_assoc();

		$result = array(
			'status' 	=> 'success',
			'code'		=> 200,
			'data' 	=> $cliente
		);
	}

	echo json_encode($result);
});

//eliminar un cliente
$app->get('/delete-cliente/:id', function($id) use($db, $app){
	$sql = 'DELETE FROM cliente WHERE idCliente = '.$id;
	$query = $db->query($sql);

	if($query){
		$result = array(
			'status' 	=> 'success',
			'code'		=> 200,
			'message' 	=> 'El cliente se ha eliminado correctamente!!'
		);
	}else{
		$result = array(
			'status' 	=> 'error',
			'code'		=> 404,
			'message' 	=> 'El cliente no se ha eliminado!!'
		);
	}

	echo json_encode($result);
});

//actualizar un cliente
$app->post('/update-cliente/:id', function($id) use($db, $app){
	$json = $app->request->post('json');
	$data = json_decode($json, true);

    $sql = "UPDATE cliente SET ".
		   "Nombre_cliente = '{$data["Nombre_cliente"]}', ".
           "Apellido_cliente = '{$data["Apellido_cliente"]}', ".
           "Documento_cliente = '{$data["Documento_cliente"]}' ".
           "WHERE idCliente = {$id}";
           
	$query = $db->query($sql);

	if($query){
		$result = array(
			'status' 	=> 'success',
			'code'		=> 200,
			'message' 	=> 'El cliente se ha actualizado correctamente!!'
		);
	}else{
		$result = array(
			'status' 	=> 'error',
			'code'		=> 404,
			'message' 	=> 'El cliente no se ha actualizado!!'
		);
    }
    
    //echo var_dump($json);
    //echo var_dump($data);
    //echo var_dump($sql);
    //echo var_dump($query);
	echo json_encode($result);

});

//guardar un cliente
$app->post('/add-cliente', function() use($app, $db){
	$json = $app->request->post('json');
	$data = json_decode($json, true);

	$query = " INSERT INTO `cliente`(`Nombre`, `Apellido`, `Documento`, `Email`) 
			   VALUES ('{$data["Nombre"]}','{$data["Apellido"]}','{$data["Documento"]}','{$data["Email"]}') ";
	
	//var_dump($query);

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
