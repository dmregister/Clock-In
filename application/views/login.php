<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="" />  
<meta name="keywords" content="" />
<link href="../../960_css/reset.css" rel="stylesheet" type="text/css" />
<link href="../../960_css/960.css" rel="stylesheet" type="text/css" />
<link href="../../css/jquery.datepick.css" rel="stylesheet" type="text/css" />
<link href="../../css/style.css" rel="stylesheet" type="text/css" />
<!-- Google Webfonts -->
<link href="http://fonts.googleapis.com/css?family=Lobster|Droid+Serif:r,b,i,bi" rel="stylesheet" type="text/css" />
	<title>Clock In for Work</title>
	<style type="text/css">
	#inDiv{
		margin-bottom: 70px;
	}
	#ajaxDiv{
		margin-top: 20px;
	}
	img.trigger { margin: 0.25em; vertical-align: top; }
	#to{
		margin-left: 15px;
	}
	#description{
		margin-top: 25px;
		margin-bottom: 5px;
	}
	#logo p{font:normal 6em Lobster;color: #555;text-shadow: 1px 1px 1px white; }
	</style>

	
</head>

	
	<body>

	
	<div class="container_12">
	<div id="header" class="col-full">
 		       
		<div id="logo">	
			<p>Time App</p>      	
		</div><!-- /#logo -->
	              
	</div><!-- /#header -->  
	
	<div id="content">
	<div id="main">
		<div id="intro" class="block">
	    		<h3><span>Web Based App to Track Time</span></h3>
	    		<p>Web based service to keep track of how long you spend on a project. It is an easy and very lightweight app that is very portable and can be accessed with just an internet connection.</p>
	    	</div><!-- #intro -->
	
	<div class="grid_4 alpha">
		
	</div>
	<div class="grid_4">
	<?php if($error){
		echo $error;
	};?>
		<?php 
		echo validation_errors();
		echo form_open("clocking/login");?>
			<p><label for="username">Username</label></p>
			<p><input type="text" name="username" value="demo"/></p>
			<p><label for="password">Password</label></p>
			<p><input type="password" name="password" value="demo"/></p>
			<input type="submit" name="submit" value="Submit"/>
		<?php form_close();?>
	</div>
	<div class="grid_4 omega">
	
	</div>
	<div class="clear"></div>
	
	</div>
</div>	
</div>
</body>

</html>