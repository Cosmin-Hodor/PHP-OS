$(document).ready(function()
{
	$('#logo_PHP-OS').click(function()
	{
		window.location.href = '/';
	})

	$("#deschide_meniu").on('click', function()
	{
		let isMobile = window.matchMedia('only screen and (max-width: 760px)').matches;

		if (isMobile)
		{
			$('#m_l').css('width', '100%');
			$('#pagina').css('marginLeft', '100%');
			$('#inchide_meniu').css('left', '49%');			
		} else
		{
			$('#m_l').css('width', '250px');
			$('#pagina').css('marginLeft', '250px');
			$('#inchide_meniu').css('left', '210px');	
		}
	})

	$('#inchide_meniu').on('click', function()
	{
		$('#m_l').css('width', '0');
		$('#pagina').css('marginLeft', '0');
	})

});