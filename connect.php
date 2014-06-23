hce<?php

	$link = '';

	function connect(){
		$con=mysqli_connect('localhost', 'username', '', 'bd');
		return $con;
	}

	function clean($input){
		global $link;

		if ( ($link == '') ) $link = connect();
		$link=connect();
		$input = trim($input);
		$input = stripslashes($input);
		$input = htmlspecialchars($input);
		//	$input = addslashes($input);
		$input = mysqli_real_escape_string($link, $input);
		mysqli_close($link);
		return $input;
	}

	function encyptMd5($pass){
		$cifrado=md5("$pass");
		return $cifrado;
	}

	function saveError($txt){
		$fecha=date("Y-m-d_h:m");
		$content = "Log: $fecha"."\r\n".$txt;
		$fechaU=date("U");

		if(!file_exists("log"))	mkdir("log");

		$fp = fopen("log/Log_$fechaU.txt","wb");
		fwrite($fp,$content);
		fclose($fp);
	}

	function checkInput($array){
		foreach($array as $key => $value){
			if(empty($value)){
				$array[$key]="null";
			}
			else{
				$value=clean($value);
				$array[$key]="'$value'";
			}
		}
	}

	function ejec($sql){
		global $link;

		if ( ($link == '') ) $link = connect();
		$txt="";
		if (mysqli_connect_errno()) {
			$txt="Connect failed: %s\n". mysqli_connect_error();
			exit();
		}
		$reg=mysqli_query($link, $sql);
		if (!$reg) {
			$txt .="Errormessage (query): %s\n". mysqli_error($link);
		}

		if($txt!=""){
			saveError($txt);
		}
		mysqli_close($link); 
		return $reg;
	}
?>