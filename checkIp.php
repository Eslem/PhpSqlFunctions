<?php
	session_start();
	function con(){
		$con=mysqli_connect('localhost', 'root', '', 'practicas');
		return $con;
	}



	function getDatosIp(){
		$con=con();
		$response=null;
		$ip=$_SERVER['REMOTE_ADDR'];	
		$sql="SELECT * FROM log_ips WHERE ip='$ip'";
		$registro=mysqli_query($con, $sql) or die (mysqli_error($con));
		while ($reg=mysqli_fetch_array($registro)){
			$response=$reg;
		}
		return $response;
	}

	function checkStateIp($reg){
		if($reg['estado']==1){
			return true;
		}
		else{
			return false;
		}
	}

	function checkExistIp($reg){
		if($reg != null){ //si no existe en el log
			return true;
		}
		else{
			return false;
		}
	}

	function checkAttempIp($reg){
		$inten=$reg['intentos'];

		if($intent<10){
			return true;
		}
		else{
			return false;
		}
	}

	function checkTimeIp($reg){
		$time=$reg['fecha'];
		$check=$time+(60*10);//60 seg y 10 min
		$dia=$time+(60*60*24);
		if(time()>$check || time()>$dia){
			return true;  //true si ya se han cumplido los 30min
		}
		else{
			return false;
		}
	}

	function bloquearIp($id){
		$con=con();
		$sql="UPDATE log_ips SET estado='0' WHERE id='$id'";
		$set=mysqli_query($con, $sql) or die (mysqli_error($con)); 
		mysqli_close($con);
		avisoErrorLoginIp("ipBlock");
		//header("location:https://lh4.googleusercontent.com/-U-zjcHXZ-Mc/UxN9SvBgnaI/AAAAAAAAEmg/cSAF-D0atus/block-IP-address.png");
	}

	function avisoErrorLoginIp($error){
		session_start();
		$_SESSION['error']=$error;		 	
	}
	
	


	//Borro el log
	function borrarLogIp($id){
		$con=con();
		$borrar=mysqli_query($con, "DELETE from log_ips WHERE id='$id'") or die (mysqli_error($con)); 
		mysqli_close($con);
		borrarErrorIp();
	}

	function __borrarLogIp(){
		$con=con();
		$ip=$_SERVER['REMOTE_ADDR'];	
		$borrar=mysqli_query($con, "DELETE from log_ips WHERE ip='$ip'") or die (mysqli_error($con)); 
		mysqli_close($con);
		borrarErrorIp();
	}

	function borrarErrorIp(){
		if( isset($_SESSION['error']) && $_SESION == "ipBlock"){
			unset($_SESSION['error']);
		}
	}

	function comprobarIp(){
		$reg=getDatosIp();
		if(checkTimeIp($reg)){//Ha pasado el tiempo
			borrarLogIp($reg['id']);
			return true;
		}
		if(checkExistIp($reg)){	//No hay logs
			if(checkStateIp($reg)){ //Si esta activo
				if(checkAttempIp($reg)){ //Si no ha sobrepasado los intentos
					return true;                
				}	
				else{
					return false;
				}
			}
			else{
				//header("location:https://lh4.googleusercontent.com/-U-zjcHXZ-Mc/UxN9SvBgnaI/AAAAAAAAEmg/cSAF-D0atus/block-IP-address.png");
				avisoErrorLoginIp("ipBlock");
				return false;
			}
		}
		else{
			return true;
		}
	}

	function loginFailIp(){ 
		$con=con();
		$ip=$_SERVER['REMOTE_ADDR'];	

		$sql="SELECT * FROM log_ips WHERE ip='$ip'";
		$registro=mysqli_query($con, $sql) or die (mysqli_error($con));
		if(mysqli_num_rows($registro)==0){
			$time=time();
			$sql="INSERT INTO log_ips (ip, fecha) VALUES ('$ip', '$time')";
			$insert=mysqli_query($con, $sql) or die (mysqli_error($con));
		}
		else{
			$sql="SELECT * FROM log_ips WHERE ip='$ip'";
			$registro=mysqli_query($con, $sql) or die (mysqli_error($con));
			while($reg=mysqli_fetch_array($registro)){
				$id=$reg['id'];
				$intentos=$reg['intentos'];
				if($intentos<9){
					if(checkTime($reg['fecha'])){
						borrarLog($id);
						loginFailIp();
					}
					else{   
						$mas= $intentos+1; 
						$sql="UPDATE log_ips SET intentos='$mas' WHERE ip='$ip'";
						$set=mysqli_query($con, $sql) or die (mysqli_error($con));
					}
				}
				else{
					bloquearIp($id);
				}
			}
		}
		mysqli_close($con);
	}

	//if(comprobarIp()) echo "ok";


?>
