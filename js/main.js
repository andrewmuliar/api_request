 function crm_req(data)
 {
  $.ajax({
        type:     "GET",
		cache:    false,
		async:	  true,
		url:      "api_crm.php?apiModule="+data,
		dataType: "json",
		error: function (request, error) 
		{
		 console.log(arguments);
		 alert("Cannot get request from DATA: " + error);
		},
		success: function (data) 
		{
		 $("#crm_req").text(data)
		 console.log(data)
		}
	   }); 
}

 function input_data()
{
 
 object = 
 [
  { "username": "data_user_name", "option":{'sex':'male', 'home':{'street':'carrol street'}} },
  { "lastname": "LastName", "markerPosition": "3333" },
  { "phone": "4657812", "markerPosition": "-3" }
 ]

 object = JSON.stringify(object)
  $.ajax({
        type:     "POST",
		cache:    false,
		async:	  true,
		data:	  object,
		url:      "http://5.149.254.139/testing.php",
	    contentType: "application/json",
		error: function (request, error) 
		{
		 console.log(arguments);
		// alert("Cannot get request from DATA: " + error);
		},
		success: function (data) 
		{
		 $("#crm_req").text(data)
		 console.log(data)
		}
	   }); 
}

$(document).ready(function()
{
 //crm_req('lead')
 //crm_req('Customer')
// getData()
 input_data();
})