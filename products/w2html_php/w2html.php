<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8" />
	<title>w2html PHP版</title>
</head>
<body>
	<?php
	// Read Master table.
	$csvFp = fopen("data/products-data.csv","r");

	// Read Template file.
	$tmpFp = fopen("../template/test.html","r");

	while($line = fgets($tmpFp)){
		$tmp = "";
		$tmp = $tmp . $line;
	};

	// Replace & Output.
	while($ret_csv = fgetcsv($csvFp)){
		mb_convert_variables("UTF-8", "SJIS", $ret_csv);
		// for ($i=0,$len=count($ret_csv);$i<$len;$i++){

		// };

		// Set initial template.
		$src = $tmp;

		// Replace.
		$src = str_replace("<%= kataban %>", $ret_csv[0], $src);

		$src = str_replace("<%= name %>", $ret_csv[8], $src);

		$src = str_replace("<%= price %>", $ret_csv[9], $src);

		// Output.
		print_r($src . "<br />");
	};

	// Files　Close.
	fclose($csvFp);
	fclose($tmpFp);
	?>
</body>
</html>
