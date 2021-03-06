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
	echo "Hola mundo desde Slim PHP para empleados";
});

//mostrar todos los empleados 
$app->get('/empleados', function() use($db, $app){
	$sql = " SELECT * FROM `empleados` ";
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

//añadir login
$app->post('/add-login', function() use($app, $db){
	$json = $app->request->post('json');
	$data = json_decode($json, true);

	$query = " INSERT INTO `login`(`NombreUsuario`, `Contrasena`) 
			   VALUES ('{$data['NombreUsuario']}','{$data['Contrasena']}') ";

	$insert = $db->query($query);

	$result = array(
		'status' => 'error',
		'code'	 => 400,
		'message' => 'El login NO se ha creado'
	);

	if($insert){
		$result = array(
			'status' => 'success',
			'code'	 => 200,
			'message' => 'login creado correctamente'
		);
	}

	echo json_encode($result);
});

$app->get('/get-id-login', function() use($db, $app){

	$sql = " SELECT `idLogin` FROM login WHERE `idLogin` = ( SELECT MAX( `idLogin` ) FROM login) ";
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

//mostrar un empleado
$app->get('/empleados/:id', function($id) use($db, $app){
	$sql = 'SELECT * FROM empleados WHERE idEmpleados = '.$id;
	$query = $db->query($sql);

	$result = array(
		'status' 	=> 'error',
		'code'		=> 404,
		'message' 	=> 'empleado no disponible'
	);

	if($query->num_rows == 1){
		$empleado = $query->fetch_assoc();

		$result = array(
			'status' 	=> 'success',
			'code'		=> 200,
			'data' 	=> $empleado
		);
	}

	echo json_encode($result);
});

//eliminar un empleado
$app->get('/delete-empleado/:id', function($id) use($db, $app){
	$sql = 'DELETE FROM empleados WHERE idEmpleados = '.$id;
	$query = $db->query($sql);

	if($query){
		$result = array(
			'status' 	=> 'success',
			'code'		=> 200,
			'message' 	=> 'El empleado se ha eliminado correctamente!!'
		);
	}else{
		$result = array(
			'status' 	=> 'error',
			'code'		=> 404,
			'message' 	=> 'El empleado no se ha eliminado!!'
		);
	}

	echo json_encode($result);
});

//actualizar un empleado
$app->post('/update-empleado/:id', function($id) use($db, $app){
	$json = $app->request->post('json');
	$data = json_decode($json, true);

    $sql = "UPDATE empleados SET ".
		   "Nombre_empleado = '{$data["Nombre_empleado"]}', ".
           "Apellido_empleado = '{$data["Apellido_empleado"]}', ".
           "Telefono = '{$data["Telefono"]}', ".
           "Direccion = '{$data["Direccion"]}', ".
           "Cargo = '{$data["Cargo"]}' ".
           "WHERE idEmpleados = {$id}";
           
	$query = $db->query($sql);

	if($query){
		$result = array(
			'status' 	=> 'success',
			'code'		=> 200,
			'message' 	=> 'El empleado se ha actualizado correctamente!!'
		);
	}else{
		$result = array(
			'status' 	=> 'error',
			'code'		=> 404,
			'message' 	=> 'El empleado no se ha actualizado!!'
		);
    }
    
    //echo var_dump($json);
    //echo var_dump($data);
    //echo var_dump($sql);
    //echo var_dump($query);
	echo json_encode($result);

});

//guardar un empleado
$app->post('/add-empleado', function() use($app, $db){
	$json = $app->request->post('json');
	$data = json_decode($json, true);

	$query = " INSERT INTO `empleados`(`Login_idLogin`, `Roles_idRoles`, `ubicacion`, 
			   `Nombre`, `Apellido`, `Telefono`, `Direccion`, `Cargo`, `Email`) 
			   VALUES ({$data['Login_idLogin']},{$data['Roles_idRoles']},'{$data['ubicacion']}',
			   '{$data['Nombre']}','{$data['Apellido']}','{$data['Telefono']}',
			   '{$data['Direccion']}','{$data['Cargo']}','{$data['Email']}') ";

	$insert = $db->query($query);

	$result = array(
		'status' => 'error',
		'code'	 => 400,
		'message' => 'El empleado NO se ha creado'
	);

	if($insert){
		$result = array(
			'status' => 'success',
			'code'	 => 200,
			'message' => 'Empleado creado correctamente'
		);
	}

	echo json_encode($result);
});

$app->run();