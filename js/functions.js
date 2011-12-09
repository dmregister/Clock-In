function processDetails(){
	var errors = '';
	
	var currentTime = new Date()
	var hours = currentTime.getHours()
	var minutes = currentTime.getMinutes()
	var timeClockIn = hours + ":" + minutes
	
	if (minutes < 10){
		minutes = "0" + minutes
		var timeClockIn = hours + ":" + minutes
	}
	if(hours > 11){
		var timeClockIn = hours + ":" + minutes + " PM"
	} else {
		var timeClockIn = hours + ":" + minutes + " AM"
	}
	
	if(hours > 13){
		pmHours = hours - 12;
		var timeClockIn = pmHours + ":" + minutes + " PM"
	}
	
	$('#timeClock').val(timeClockIn);
	
	//Validate TimeIn
	var time = $("#clockingTime [name='timeClock']").val();
	var clientVal = $("#client").val();
	var newClientVal = $("#clientNew").val();
	var timeClockOut = $("#timeClockOut").val();
	//alert(newClientVal);
	
	if(!time){
		errors += ' -Please enter a time';
	}
	if(!clientVal && !newClientVal && timeClockOut != '1'){
		errors += 'Please enter a name';
	}
	
	if (errors){
		//errors = 'The following errors occurred:n' + errors;
		alert(errors);
		return false;
	} else {
		//submit our form via Ajax
		var dataString = $('#clockingTime').serialize();
		//console.log(dataString);
	    $.ajax({
		      type:"POST",
		      url:"http://timeapp.davidmregister.com/index.php/clocking/clock_time",
		      data: dataString,
		      success:function(response){
		      	clockIn();
				$('#msg').html(response);       
		      }
	
	    })
		return false;
	}
	
};

function showResult(data){
	
	if(data == 'save_failed'){
		alert('Form saved failed, Please contact your administrator');
		return false;
	} else{
		clockIn();
		return false;
	}
}

function clockIn(){
		
	$('#cIn').hide('fast');
	$('#clockingTime p').hide('fast');
	$('#cOut').show('fast');
	

};


function  timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}

function getTime(){
	if( $("#getTimeForm [name='client']").val() == ''){
		alert("Please select a client");
		return;
	}
	if( $("#getTimeForm [name='toDate']").val() == ''){
		alert("Please select a start date");
		return;
	}
	
	if( $("#getTimeForm [name='fromDate']").val() == ''){
		alert("Please select an end date");
		return;
	}

	var dataString = $('#getTimeForm').serialize();
	//console.log(dataString);
    $.ajax({
	      type:"POST",
	      url:"http://timeapp.davidmregister.com/index.php/clocking/get_time",
	      data: dataString,
	      success:function(response){
			$('#ajaxDiv').html(response);       
	      }

    })

}
