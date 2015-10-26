<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8" />
	<title>w2html PHP版</title>
</head>
<body>
	<?php
	// ## Define.
	//  -> Filepath define.
	$configPath = "data/config.json";
	//  -> Data Table define.
	$dataTablePath = "data/products-data.csv";

	// ## Ready.
	//  -> Open Data table.
	$csvFp = fopen($dataTablePath,"r");
	//  -> Get Field Names.
	$fieldArray = fgetcsv($csvFp);
	mb_convert_variables("UTF-8", "SJIS", $fieldArray);
	//  -> Get Config Map
	$configMap = readConfig($configPath);
	//  -> Set Paramater Map
	$configMapParamater = $configMap['paramater'];

	// ## Main. (Read template , Replace & Output.)
	while($ret_csv = fgetcsv($csvFp)){

		// 1. Read Template file.
		$defaultTemplate = getTemplate("../template/test.html");
		$kataban_replace = str_replace('/', '', $ret_csv[0]);
		$customTemplate = "../template/" . $kataban_replace . ".html";

		//  Template Set.
		if(file_exists($customTemplate)){
			$tmp = $customTemplate;
		}else{
			$tmp = $defaultTemplate;
		}
		$src = $tmp;
		
		// 2. Replace.
		for ($i=0,$len=count($fieldArray);$i<$len;$i++){
			$paramater = @$configMapParamater[$fieldArray[$i]];
			$rep_data = $ret_csv[$i];
			switch ($paramater) {
				case 'xxxxx':
					
					break;
				default:
					$src = str_replace($paramater, $rep_data, $src);	
					break;
			}
		}

		// 3. Output.
		print_r($src . "<br />");
	};

	// Files　Close.
	fclose($csvFp);

// Function: Read Config file.
function readConfig($path){
	$jsonStr = file_get_contents($path);
	$jsonStr = mb_convert_encoding($jsonStr, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
	$map = json_decode($jsonStr,true);
	return $map;
}
// Function: Get Template file.
function getTemplate($path){
	$tmpFp = fopen($path,"r");
	while($line = fgets($tmpFp)){
		$tmp = "";
		$tmp = $tmp . $line;
	};
	fclose($tmpFp);
	return $tmp;
}
// Function: Check Genetate Flag.
function genCtrl($array){
	$genFlagFieldName = "生成フラグ";
	$flagCol = array_search($genFlagFieldName,$array) - 1;
	return $flag;
}

	?>
</body>
</html>
