/**
* 2020 C. Hodor - Open Source Community Platform
*/

$(document).ready(function()
{
	$('#arata_parola').click(function()
	{
		let parola = document.getElementById('parola');

		(parola.type === "password") ? parola.type = "text" : parola.type = "password"
	})
});