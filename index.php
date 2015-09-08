<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<link rel="stylesheet" type="text/css" href="nice_r.css?version=<?php echo filemtime('nice_r.css'); ?>"/>
		<script type="text/javascript" src="nice_r.js?version=<?php echo filemtime('nice_r.js'); ?>"></script>
	</head><body><?php

        require('Nicer.php');

        abstract class a {
            /**
             * Summary of baseMethod. Copyright (C) www.christian.sciberras.me
             * @param integer $throw @test gff
             * @see http://google.com/
             * @author Christian Sciberras christian@sciberras.me
             */
            public function baseMethod($throw = 90) {}
            abstract function abs1();
            public function abs2() { echo "base"; }
        }
        class c extends a {
            public function __construct($b = true) {}
            public function __toString() { return "hi"; }
            public function __destruct() { /* cleanup */ }
            final public function noOverride() { echo "ich bin perma"; }
            public function abs1() { echo "declared"; }
            public function abs2() { echo "override"; }
        }
        $c = new c();
        
        $n = new Nicer(new Nicer('This is so meta'), true);
        $n->render();

    ?></body>
</html>