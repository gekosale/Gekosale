<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="utf-8">
    <link rel="stylesheet" href="bootstrap.css" type="text/css"/>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
    <script src="prettify.js"></script>
    <script>
    	addEventListener('load', prettyPrint, false);
        $(document).ready(function(){
        	$('pre').addClass('prettyprint');           
		}); 
 	</script>
</head>
<body>
	<div class="container">
		<div class="hero-unit">
         <h1>WellCommerce WebAPI</h1>
         <p>wersja API 1.0 z dnia 2013-07-04</p>
      	</div>
	
<?php include 'getProduct.php' ;?>
    
</body>
</html>
