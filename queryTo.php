<?php
  function queryToXml($query, $parentName, $childName){
		$xml = new SimpleXMLElement('<'.$parentName.'/>');
		$arrayP=queryToArray($query);
		foreach($arrayP as $array) {
			$reg=$xml->addChild($childName);
			foreach($array as $key => $value) {
				$reg->addChild(strtolower($key), $value);
			}
		}
		//Header('Content-type: text/xml');     	
		return $xml;

	}

	function queryToArray($query){
		$array=array();
		while($row=mysqli_fetch_array($query, MYSQL_ASSOC)){
			$array[]=$row;
		}
		return $array;
	}

	function queryToJSON($query){
		$array=queryToArray($query);
		//header("Content-type:application/json");
		return json_encode($array);   
	}
?>
