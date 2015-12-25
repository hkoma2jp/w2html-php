<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8" />
	<title>w2html PHP版</title>
</head>
<body>
	<?php
	// ## Define.
	// - Config Filepath define.
	$configPath = 'data/config.json';

	// - Data Table define.
	$dataTablePath = 'data/products-data.csv';

	// - Data Table 'generation flag' column.
	$p_genflag = 0;
	// - Data Table 'kataban' column.
	$p_kataban = 1;

	// - Template Folderpath define.
	$templateFolderPath = '../products/Template/';
	$defaultTemplateFilename = 'DEFAULT_TEMPLATE.html';
	// - Output Folder define.
	$outputFolderPath = '../products/';
	// - Backup Folder define.
	$backupFolderPath = '../products/Backup/';

	// - Log Counter define.
	$count_suc = 0;
	$count_del = 0;

	// ## Ready.
	// - Open Data table.
	$csvFp = fopen($dataTablePath,'r');
	// - Get Field Names.
	$fieldArray = fgetcsv($csvFp);
	//mb_convert_variables('UTF-8', 'SJIS', $fieldArray);  <- CSV Shift_JIS Formatted.

	// - Get Config Map
	$configMap = readConfig($configPath);
	// - Set Paramater Map
	$configMapParamater = $configMap['replace_rules'];

	// ## Main. (Read template,Replace and Output or Delete.)
	while($ret_csv = fgetcsv($csvFp)){

		// 1. Read Template file
			// Default Template
			$defaultTemplateSrc = file_get_contents($templateFolderPath . $defaultTemplateFilename);

			// Custom Template
			$kataban_replace = str_replace('/', '', $ret_csv[$p_kataban]);
			$customTemplate = $templateFolderPath . $kataban_replace . '.html';
			if(file_exists($customTemplate)){
				$customTemplateSrc = '';
				$customTemplateSrc = file_get_contents($customTemplate);
			}

			// Select Template and Read.
			if(file_exists($customTemplate)){
				$tmp = $customTemplateSrc;
			}else{
				$tmp = $defaultTemplateSrc;
			}
			$src = $tmp;

		// 2. Target Filepath set
			$targetFilepath = $outputFolderPath . $kataban_replace . '.html';
			$targetFilepath_backup = $backupFolderPath . $kataban_replace . '.' . date('Ymd') . '.backup.html';

		// 3. Replace and Output or Delete.
			switch ($ret_csv[$p_genflag]){
				case '0': // Generate flag = '0' -> Backup and Delete HTML File.
					// Backup and Delete.
					backupAndDelete($targetFilepath, $targetFilepath_backup);
					$count_del += 1;
				break;
				case '1': // Generate flag = '1' -> Backup and Genarate HTML File.
					// Backup.
					backupAndDelete($targetFilepath, $targetFilepath_backup);
					// Replace.
					$src = getReplacedSource($src,$ret_csv,$fieldArray,$configMapParamater);
					// Output.
					//print_r($src . '<br />');
					file_put_contents($targetFilepath, $src, LOCK_EX);
					$count_suc += 1;
				break;
				default: // Other -> Skip.
				break;
			}

	};

	// Files　Close.
	fclose($csvFp);

	// Log Print
	print_r('Genarate Success：' . $count_suc . '<br />Delete Success：' . $count_del);

// ## Ready Functions
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

// ## Oparation Functions
// Function: Replace paramaters in Template.
function getReplacedSource($src,$csv_data,$field_names,$config_data){
	for ($i=0,$len=count($field_names);$i<$len;$i++){
		$paramater = @$config_data[$field_names[$i]];
		$buf = $csv_data[$i];
		//mb_convert_variables("UTF-8", "SJIS", $buf); <- CSV Shift_JIS Formatted.
		switch ($paramater) {
			case '<%= price %>':
				$buf = number_format($buf);
			break;
			case 'calFunctionName': // Calicurated.
				// $buf = calFunction($buf);
			break;
			default: // Default.
			break;
		}
		$rep_data = $buf;
		$src = str_replace($paramater, $rep_data, $src);
	}
	return $src;
}
// Function: Backup and Delete.
function backupAndDelete($fromPath,$toPath){
	if (file_exists($fromPath)){
		if(!file_exists($toPath)){
			rename($fromPath, $toPath);
		}else{
			unlink($fromPath);
		}
	}
}

// ## Calculate Functions:

	?>
</body>
</html>
