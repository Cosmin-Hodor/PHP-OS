/**
* 2020 C. Hodor - Open Source Community Platform
*/

$(document).ready(function()
{

	function vote(id, expeditor, type, token)
	{
		$.ajax({
            url: "/nodes/RequestProtocol.php",
            type: "POST",
            data: 
            {
            	id: id,
            	expeditor: expeditor,
            	tip: type,
                token: token
            }
        });
	}

	$(document).on('click', '.vot', function()
	{
		var id = $(this).attr('data-postare');

		var token = $('#token_general').val();
		var element_vot = $(this).closest('.reactii_postare').children('#numar_voturi');
		var voturi = $(this).closest('.reactii_postare').children('#numar_voturi').text();
		var contra_activ = $(this).closest('.reactii_postare').children('.contra');
		var pro_activ = $(this).closest('.reactii_postare').children('.pro');

		if ($(this).hasClass('pro'))
		{
			if ($(this).hasClass('verde'))
			{
				$(this).removeClass('verde');
				element_vot.text(Number(voturi) - 1);
			} else
			{
				$(this).addClass('verde');

				if (contra_activ.hasClass('rosu')) 
				{
					contra_activ.removeClass('rosu');
					element_vot.text(Number(voturi) + 2);
				} else
				{
					element_vot.text(Number(voturi) + 1);
				}
			}

			vote(id, expeditor_id, 1, token);
		}
		else if ($(this).hasClass('contra'))
		{
			if ($(this).hasClass('rosu'))
			{
				$(this).removeClass('rosu');
				element_vot.text(Number(voturi) + 1);
			} else
			{
				$(this).addClass('rosu');

				if (pro_activ.hasClass('verde')) 
				{
					pro_activ.removeClass('verde');
					element_vot.text(Number(voturi) - 2);
				} else
				{
					element_vot.text(Number(voturi) - 1);
				}
			}
			
			vote(id, expeditor_id, 0, token);
		}
	})
})