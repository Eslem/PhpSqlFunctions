<?php
	require "connect.php";

	function insertUser($nombre, $apellidos, $email, $login, $pass){
		$nombre=clean($nombre);
		$apellidos=clean($apellidos);
		$email=clean($email);
		$login=clean($login);
		$pass=md5($pass);
		$sql="INSERT INTO Users(login, nombre, apellidos, email, pass) values('$login', '$nombre', '$apellidos', '$email', '$pass')";
		ejec($sql);
	}

	function removeUser($id){
		$sql="DELETE FROM users WHERE id='$id'";
		ejec($sql);		
	}

	function editUser($id, $nombre, $apellidos, $email, $login){
		$nombre=clean($nombre);
		$apellidos=clean($apellidos);
		$email=clean($email);
		$login=clean($login);
		$pass=md5($pass);
		$sql="UPDATE users SET login='$login', nombre='$nombre', apellidos='$apellidos', email='$email' WHERE id='$id'";
		ejec($sql);
	}

	function insertPc($mac, $ip, $nombre, $centro){
		$nombre=clean($nombre);
		$mac=clean($mac);
		$ip=clean($ip);
		$centro=clean($centro);
		$sql="INSERT INTO pcs(mac, ip, nombre, centro) values('$mac', '$ip', '$nombre', '$centro')";
		ejec($sql);
	}

	function removePc($id){
		$sql="DELETE FROM pcs WHERE id='$id'";
		ejec($sql);	
	}

	function editPc($id, $mac, $ip, $nombre, $centro){
		$nombre=clean($nombre);
		$mac=clean($mac);
		$ip=clean($ip);
		$centro=clean($centro);
		$sql="UPDATE pcs SET mac='$mac', ip='$ip', nombre='$nombre', centro='$centro' WHERE id='$id'";
		ejec($sql);
	}
	
	
	function newRelation($user, $pc){
		$sql="INSERT into user_pc(user, pc) values('$user', '$pc')";
		ejec($sql);
	}
	
	function removeRelation($id){
	 	$sql="DELETE FROM user_pc WHERE id='$id'";
	 	ejec($sql);
	}
	
?>
