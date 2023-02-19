$(document).ready(function()
{
	$pivot = 0;
	$pachet_nou = 5;

	$(window).scroll(function()
	{
		if ($(window).scrollTop() >= $(document).height() - $(window).height())
		{
			$pivot += $pachet_nou;

			$.ajax({
	            url: "/nodes/RequestProtocol.php",
	            type: "POST",
	            data: 
	            {
	            	baza: $pivot,
	            	interval: $pachet_nou,
	            	expeditor_id: expeditor_id,
	                token: token_general
	            },

	            success: function(data)
	            {
	                if (data != '')
	                {
	                	$('.perete').append(data).show('slow');
	                	$('.incarca_mai_mult').hide();

	                } else
	                {
	                	$('footer').addClass('subsol');
	                }
	            }
	        });
		}
	});
});