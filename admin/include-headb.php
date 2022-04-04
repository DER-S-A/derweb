<!-- INICIO include-headb.php -->

<?php
$start_time = microtime_float();
$showFooter = true;
include("sc-cachebuster.php");
?>

<meta charset="UTF-8">

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">


<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<meta name="description" content="Sistemas de gestion, consorcios, inmobiliarias, constructoras">
<meta name="author" content="SC3 - Soluciones IT">



<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/ajax.js")); ?>" charset="iso-8859-1"></script>

<!-- http://fontawesome.io/ -->
<link rel="stylesheet" href="terceros/font-awesome-4.7.0/css/font-awesome.min.css">



<style>
	body {
		font-family: Verdana, Geneva, Tahoma, sans-serif !important;
	}

	.navbar-nav>li>a,
	.navbar-brand {
		height: 40px;
		font-size: 13px;
		font-style: normal;
		font-variant: small-caps;
		line-height: normal;

		color: #ffffff !important;
		text-decoration: none;
		padding: 5px;
		margin-bottom: 3px;
	}
</style>

<!-- FIN include-headb.php -->