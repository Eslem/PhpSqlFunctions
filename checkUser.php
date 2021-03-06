<?php
   session_start();
	ini_set( 'default_charset', 'UTF-8' );

	require "checkIp.php";

	function conexion(){
		$con=mysqli_connect('localhost', 'root', '', 'practicas');
		return $con;
	}


	//Cifrado md5
	function cifrar($pass){
		$cifrado=md5("$pass");
		return $cifrado;
	}


	//scape string
	function limpiar($data){
		$con=conexion();
		/*$data=trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);   */
		$data=mysqli_real_escape_string($con, $data);
		return $data;
		mysqli_close($con);
	}	


	//Compruebo si ha pasado el tiempo
	function checkTime($date){
		$time=$date;
		$check=$time+(60*10);//60 seg y 30 min
		$dia=$time+(60*60*24); //dia
		if(time()>$check || time()>$dia){
			return true;  //true si ya se han cumplido los 30min
		}
		else{
			return false;
		}
	}


	//Compruebo si esta bloqueado
	function checkState($state){
		if($state==1){
			return true;
		}
		else{         //Bloqueado
			return false;
		}
	}


	//Hago las comprobaciones antes del login por si el usuario esta bloqueado o ya paso el tiempo 
	function comprobarLog($user){
		$con=conexion();
		$sql="SELECT * FROM log_login WHERE usuario='$user'";
		$registro=mysqli_query($con, $sql) or die (mysqli_error($con));
		if(mysqli_num_rows($registro)==0){
			return true;
		}
		else{
			while($reg=mysqli_fetch_array($registro)){
				if(checkTime($reg['fecha'])){
					borrarLog($reg['id']);
					return true;
				}
				if(checkState($reg['estado'])){ //Compruebo si no esta bloqueado
					$intentos=$reg['intentos'];
					if($intentos<3){
						return true;
					}
					if($intentos>=3){
						bloquear($user);
						return false;
					}
				}
				else{
					//si lo esta bloqueado
					//header("location:http://es.wikipedia.org/wiki/Plantilla:Aviso_bloqueado");
					avisoErrorLogin("Ha sobrepasado el numero de intentos con ese usuario");

				}
			}
		}
		mysqli_close($con);
	}


	//Si el login esta mal
	function loginFail($user){ 
		$con=conexion();
		$sql="SELECT * FROM log_login WHERE usuario='$user'";
		$registro=mysqli_query($con, $sql) or die (mysqli_error($con));
		if(mysqli_num_rows($registro)==0){
			$time=time();
			$sql="INSERT INTO log_login (fecha, usuario) VALUES ('$time', '$user')";
			$insert=mysqli_query($con, $sql) or die (mysqli_error($con));
		}
		else{
			$sql="SELECT * FROM log_login WHERE usuario='$user'";
			$registro=mysqli_query($con, $sql) or die (mysqli_error($con));
			while($reg=mysqli_fetch_array($registro)){
				$id=$reg['id'];
				$intentos=$reg['intentos'];
				if($intentos<2){
					if(checkTime($reg['fecha'])){
						borrarLog($id);
						loginFail($user);
					}
					else{   
						$mas= $intentos+1; 
						$sql="UPDATE log_login SET intentos='$mas' WHERE id='$id'";
						$set=mysqli_query($con, $sql) or die (mysqli_error($con));
					}
				}
				else{
					bloquear($user);
				}
			}
		}
		mysqli_close($con);
	}


	//Bloqueo al usuario
	function bloquear($user){
		$con=conexion();
		$sql="UPDATE log_login SET estado='0' WHERE usuario='$user'";
		$set=mysqli_query($con, $sql) or die (mysqli_error($con)); 
		mysqli_close($con);
		//header("location:http://es.wikipedia.org/wiki/Plantilla:Aviso_bloqueado");
		avisoErrorLogin("Ha sobrepasado el numero de intentos");
		$_SESSION['user']=$user;
	}


	//Borro el log
	function borrarLog($id){
		borrarError();
		$con=conexion();
		$borrar=mysqli_query($con, "DELETE from log_login WHERE id='$id'") or die (mysqli_error($con)); 
		mysqli_close($con);
	}

	function borrarLogUser($user){
		borrarError();
		$con=conexion();
		$sql=mysqli_query($con, "SELECT * from log_login WHERE usuario='$user'") or die (mysqli_error($con)); 
		while($reg=mysqli_fetch_array($sql)){
			borrarLog($reg['id']);
		}
		mysqli_close($con);
	}

	function comprobar($user){
		if(comprobarLog($user) && comprobarIp()){
			return true;
		}
	}

	function avisoErrorLogin($error){
		
		$_SESSION['error']=$error;
	} 

	function borrarError(){
		if( isset($_SESSION['error']) && $_SESION != "ipBlock"){
			unset($_SESSION['error']);
		}
		if( isset($_SESSION['user'])){
			unset($_SESSION['user']);
		}
	}

	function mostrarErrorJS(){
		if( isset($_SESSION['error'])  && isset($_POST['submit'])){
			echo $_SESSION['error'];
		}	
	} 
	function mostrarUserJS(){
		if( isset($_SESSION['user']) && isset($_POST['submit'])){
			echo $_SESSION['user'];
		}	
	}     

	function login($user, $pass){
		$con=conexion();
		$user=limpiar($user);
		$pass=cifrar($pass);

		if(comprobar($user)){
			$sql="SELECT * FROM users WHERE nombre='$user' AND pass='$pass'";
			$registro=mysqli_query($con, $sql) or die (mysqli_error($con));
			if(mysqli_num_rows($registro)>0){
				borrarLogIp();
				borrarLogUser($user);
				borrarError();
				//header("location: http://www.google.com");
				loginCorrect($registro);
			}
			else{
				avisoErrorLogin("Usuario o contraseņa incorrectos");
				loginfail($user);
				loginFailIp();
			}

		}
		mysqli_close($con);
	}
	
	function loginCorrect($registro){
	 	while($reg=mysqli_fetch_array($registro)){
	 	 	$_SESSION['id']=$reg['id'];	 	
	 	 	$_SESSION['login']=$reg['login'];	 	
	 	 	$_SESSION['admin']=$reg['admin'];	 	
		}
		
		if($_SESSION['admin']==0){
		   header("location:panel.php");
		}
		else{
		 	header("location:admin.php");
		}
	}
	
	
?>
