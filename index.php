<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<title>nice_r() demo</title>
		<meta charset="UTF-8"/>
		<link rel="stylesheet" type="text/css" href="src/nice_r/nice_r.css?version=<?php echo filemtime('src/nice_r/nice_r.css'); ?>"/>
		<script type="text/javascript" src="src/nice_r/nice_r.js?version=<?php echo filemtime('src/nice_r/nice_r.js'); ?>"></script>
	</head><body><?php
		
		require_once('src/nice_r/nice_r.php');
		
		nice_r($_SERVER);
		
		nice_r(array(
			'a' => '1',
			'b' => 352,
			'c' => 7/3,
			'd' => fopen('php://temp', 'w+'),
			'e' => new ArrayObject(),
		));
	
	?></body>
</html>