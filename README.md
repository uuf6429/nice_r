nice_r()
========

A nicer replacement for PHP's print_r().

Usage
-----

The following example loads `nice_r.php` and prints out PHP's `$_SERVER` variable.

```php
<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<link rel="stylesheet" type="text/css" href="nice_r.css?version=<?php echo filemtime('src/nice_r/nice_r.css'); ?>"/>
		<script type="text/javascript" src="nice_r.js?version=<?php echo filemtime('src/nice_r/nice_r.js'); ?>"></script>
	</head><body><?php

		require_once('nice_r.php');

		nice_r($_SERVER);

	?></body>
</html>
```

Screenshot
----------

![Screenshot](http://i.stack.imgur.com/VnUuV.png)

Report Issues/Bugs
------------------
[Bugs](https://github.com/uuf6429/nice_r/issues)