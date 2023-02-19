/**
* 2020 C. Hodor - Open Source Community Platform
*/

$(document).ready(function()
{

	$('#arata_formular_postare').click(function()
	{
		$('#modal_postare').css('display', 'block');
		$('#modal_postare').css('height', '100%');
	})

	$('#inchide_modal').click(function()
	{
		$('#modal_postare').css('display', 'none');
	})

	window.onclick = function (event)
	{
	    if (event.target == modal_postare)
	    {
	        $('#modal_postare').css('display','none');
	    }    
	}

})