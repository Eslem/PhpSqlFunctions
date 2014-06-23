<?php
	session_start();

	function logOut(){
		session_destroy();
		header("location:index.php");
	}
	function logOut_href(){
		session_destroy();
		header("location:../index.php");
	}

	function checkSession(){
		if(!isset($_SESSION['id'])){
			logOut();
			return false;
		}
		return true;
	}

	function checkAdmin(){
		if(checkSession()){
			if($_SESSION['admin']==0){
				//echo "<script language='javascript'>alert('No tienes permisos para acceder a esta pagina!');</script>";
				//sleep(5);
				header("location:panel.php");
				return false;
			}
			else{
				return true;
			}

		}
		else{
			return false;
		}

	}

	if(isset($_GET['logout'])){
		logOut_href();
	}
?>
