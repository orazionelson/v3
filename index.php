<?php
require "inc/vcube.class.php";
$vcube = new Vcube();
$messages=$vcube->vcube_run();
?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>A triple validation contact form in jQuery+PHP for Bootstrap</title>
        <meta name="description" content="vCube is a contact form with three validation levels in jQuery+PHP for Bootstrap">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <style>
            body {
                padding-top: 50px;
                padding-bottom: 20px;
            }
        </style>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">

		<link rel="icon" href="img/favicon.png" type="image/png" />
        <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
    </head>
    <body id="page-top" class="index">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header page-scroll">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">v<sup>3</sup> form validator</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <!-- Header-->


    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
		<div class="col-md-12">
			<h1>v<sup>3</sup></h1>	 
		</div>
		<div class="col-md-12">	
				<div id="contact-form">
				<?php 
				//here build the form
				$vcube->build_form($messages); 
				?> 
			</div>			
		</div>
	</div>
	
      <hr>

    </div> <!-- /container -->        
	<footer class="text-center">
		<div class="footer-below">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
			MIT License &copy; Alfredo Cosco alfredo.cosco@gmail.com 2015 | <a href="https://github.com/orazionelson/v3">v<sup>3</sup> on GitHub</a>
					</div>
				</div>
			</div>
		</div>
	</footer>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>

        <script src="js/vendor/bootstrap.min.js"></script>
        <script src="js/vendor/validator.js"></script>

        <script src="js/plugins.js"></script>
    </body>
</html>
