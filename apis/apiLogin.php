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

$app->post('/login', function() use($db, $app){
	$json = $app->request->post('json');
    $data = json_decode($json, true);
    
    $sql = "SELECT * FROM login WHERE 
            ( NombreUsuario = '{$data["nombreUsuario"]}' ) AND
            ( Contrasena = '{$data["contrasena"]}' ) ";

	$query = $db->query($sql);

    $result = array(
			'status' 	=> 'error',
			'code'		=> 400,
			'message' 	=> 'usuario no disponible'
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

$app->post('/empleados', function() use($db, $app){
	$json = $app->request->post('json');
    $data = json_decode($json, true);
    
    $sql = "SELECT * FROM empleados WHERE Login_idLogin = '{$data["id"]}'";

	$query = $db->query($sql);

    $result = array(
			'status' 	=> 'error',
			'code'		=> 400,
			'message' 	=> 'usuario no disponible'
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

$app->post('/roles', function() use($db, $app){
	$json = $app->request->post('json');
    $data = json_decode($json, true);
    
    $sql = "SELECT * FROM roles WHERE idRoles = '{$data["Roles_idRoles"]}'";

	$query = $db->query($sql);

    $result = array(
			'status' 	=> 'error',
			'code'		=> 400,
			'message' 	=> 'usuario no disponible'
		);
    
    //var_dump($sql);
    //var_dump($query);

	if($query->num_rows == 1){
		$roles = $query->fetch_assoc();

        $result = array(
			'status' 	=> 'success',
			'code'		=> 200,
			'data' 	=> $roles
		);
	}

	echo json_encode($result);

});

$app->run();

