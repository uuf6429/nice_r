nice_r()
========

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/uuf6429/nice_r/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/uuf6429/nice_r/?branch=master)

A nicer replacement for PHP's print_r(). Works with (at least) PHP 5.2 (tested).

Usage
-----

Following a recent rewrite, this library has been converted into a class.
However, an adapter is available for calling the library the old way.

The following example shows how one prints out PHP's `$GLOBALS` variable.

```php
?><!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<link rel="stylesheet" type="text/css" href="nice_r.css?version=<?php echo filemtime('src/nice_r/nice_r.css'); ?>"/>
		<script type="text/javascript" src="nice_r.js?version=<?php echo filemtime('src/nice_r/nice_r.js'); ?>"></script>
	</head><body><?php

		require('Nicer.php');

		// oop way (reccomended)
		$n = new Nicer($GLOBALS);
		$n->render();
		
		// procedural way (requires nice_r.php)
		require('nice_r.php');
		nice_r($GLOBALS);

	?></body>
</html>
```

Screenshot
----------

![Screenshot](http://i.imgur.com/zOTQT9W.png)

Report Issues/Bugs
------------------
[Bugs](https://github.com/uuf6429/nice_r/issues)