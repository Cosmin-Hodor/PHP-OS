/**
* 2020 C. Hodor - Open Source Community Platform
*/

$(document).ready(function()
{
	let camp_recuperare = $('#camp_recuperare');
	let camp_parola = $('#camp_parola');

	$('#recuperare_cont').click(function()
	{
		if (camp_recuperare.type == "hidden")
		{
	    	camp_recuperare.type = "text";
	    	camp_parola.type = "hidden";
		} else 
		{
			camp_recuperare.type = "hidden";    		
			camp_parola.type = "password";
		} 
	})
})
