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
	</div>
	<div class="grid_5 alpha">
	<?php if($clockInTime != ''){?>	
			<h1>Clock Out!</h1>
			<?php 
				echo form_open('clocking/clock_time');
			?>
			<form action="http://timeapp.davidmregister.com/index.php/clocking/clock_time" method="post" >
			<p><input type="hidden" value="" name="timeClock" id="timeClock"/></p>
			<p><input type="hidden" value="1" name="timeClockOut" id="timeClockOut"/></p>
			<input type="submit" value="Punch Card"/>
			</form>

		<?php } else { ?>
			<h1 id="cOut">Clock Out!</h1>
			<h1 id="cIn">Clock In!</h1>
			<?php 
				$attr = array('id' => 'clockingTime');
				echo form_open('clocking/clock_time', $attr);
			?>
			<p>Current Client: 
				<?php
			    $attr = 'id="client" name = "client"';
				echo form_dropdown('client', $clients, set_value('client'), $attr);?>
			</p>
			<input type="hidden" value="" name="timeClock" id="timeClock"/>
			<p>Comments:<br />
			<textarea id="comments" rows="5" cols="30"></textarea></p>
			<input type="button" value="Punch Card" onClick="processDetails()"/>
			</form>
			
		<?php };?>	
				
			
		</div>
		
		<div class="grid_5 omega" id="msg">
			<?php if($clockInTime != ''){?>
				<p><?php echo $clockInUser;?>, You have already clocked In at <?php echo $clockInTime;?></p>
			<?php }?>
		</div>
		

<div class="clear"></div>	

<p id="description">Please select the dates to see the time worked.</p>


<?php $attr = array('id' => 'getTimeForm');
	echo form_open('clocking/get_time', $attr);
?>


    <h3>Search</h3> 
    Client: 
    <?php
    $attr = 'id="client" name = "client"';
	echo form_dropdown('client', $clients, set_value('client'), $attr)."<br /><br /><br /><br />";?>
    <div class="grid_3 alpha">
<label for="toDate">Start Date</label><br />
<input type="text" id="toDate" name="toDate"/><img src="../../img/calendar.gif" alt="Popup" class="trigger datepick-trigger"><span id="to">to</span>
</div>
<div class="grid_3 omega">
<label for="fromDate">End Date</label><br />
<input type="text" id="fromDate" name="fromDate"/><img src="../../img/calendar.gif" alt="Popup" class="trigger datepick-trigger">
<input type="hidden" id="dateHidden" name="dateHidden" value="1"/>

<input type="button" value="Get Time" id="getTimeBtn" onclick="getTime()"/>
</form>
</div>

<div class="clear"></div>	
<div id="ajaxDiv"></div>
	</div>
</div>	
	
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="../../js/jquery.datepick.js"></script>
<script type="text/javascript" src="../../js/jquery.datepick.ext.js"></script>
<script type="text/javascript" src="../../js/functions.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	
	$('#cOut').hide();
 
 	$('#toDate,#fromDate').datepick({ 
   		 onSelect: customRange, showTrigger: '#calImg', dateFormat: 'yyyy-mm-dd'});
     
	function customRange(dates) { 
	    if (this.id == 'toDate') { 
	        $('#fromDate').datepick('option', 'minDate', dates[0] || null); 
	    } 
	    else { 
	        $('#toDate').datepick('option', 'maxDate', dates[0] || null); 
	    } 
	}
 
$('#fullWeek').datepick({ 
    renderer: $.datepick.weekOfYearRenderer, 
    firstDay: 1, showOtherMonths: true, rangeSelect: true, 
    onShow: $.datepick.selectWeek, showTrigger: '#calImg'});

});


//-->
</script>
</body>

</html>