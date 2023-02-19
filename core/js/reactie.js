/**
* 2020 C. Hodor - PHP OS
*/

window.global_data = undefined;

$(document).ready(function()
{
	var token = $('#token_general').val();
	var id = $('.reactii_postare').children('.pro').attr('data-postare');
	var audio = new Audio('/themes/main/assets/pop.mp3');

	function set_data(data)
	{
		window.global_data = data;
	};

	$.ajax({
	    url: "/nodes/RequestProtocol.php",
	    type: "POST",
	    data: 
	    {
	    	id: id,
	    	expeditor: expeditor_id,
	    	react_check: 'internal',
	    	token: token
	    },

	    success:function(data)
	    {	
	    	set_data(data);
	    }
    });

	function react(id, expeditor, type, token)
	{
		$.ajax({
            url: "/nodes/RequestProtocol.php",
            type: "POST",
            data: 
            {
            	id: id,
            	expeditor: expeditor,
            	react: type,
                token: token
            },

            success:function()
            {
            	audio.play();
            }
        });
	};

	$('.reactie, .reactie_hug').hover(function()
	{
		$('.reactie_hug').show();
	}, function()
	{
		$('.reactie_hug').hide();
	});

	$(document).on('click', '.confirm', function()
	{

		var type = $(this).attr('class');
		var list = type.split(/\s+/);
		
		var activ = $('.activ');
		var smile = $('#smile');
		var lmao = $('#lmao');
		var love = $('#love');
		var msef = $('#msef');

		$.each(list, function(index, value)
		{
			switch (value)
			{
				case 'smile':

					if (smile.hasClass('activ'))
					{
						(Number(smile.text()) == 1) ? smile.text('') : smile.text(Number(smile.text()) - 1); 
						smile.removeClass('activ');
					} else if (!smile.hasClass('activ'))
					{
						(Number(activ.text()) == 1) ? activ.text('') : activ.text(Number(activ.text() - 1));
						activ.removeClass('activ');

						smile.addClass('activ');
						smile.text(Number(smile.text()) + 1);
					}

					react(id, expeditor_id, 0, token);
				break;

				case 'lmao':

					if (lmao.hasClass('activ'))
					{
						(Number(lmao.text()) == 1) ? lmao.text('') : lmao.text(Number(lmao.text()) - 1); 
						lmao.removeClass('activ');
					} else if (!lmao.hasClass('activ'))
					{
						(Number(activ.text()) == 1) ? activ.text('') : activ.text(Number(activ.text() - 1));
						activ.removeClass('activ');

						lmao.addClass('activ');
						lmao.text(Number(lmao.text()) + 1);
					}

					react(id, expeditor_id, 1, token);
				break;

				case 'love':

					if (love.hasClass('activ'))
					{
						(Number(love.text()) == 1) ? love.text('') : love.text(Number(love.text()) - 1); 
						love.removeClass('activ');
					} else if (!love.hasClass('activ'))
					{
						(Number(activ.text()) == 1) ? activ.text('') : activ.text(Number(activ.text() - 1));
						activ.removeClass('activ');

						love.addClass('activ');
						love.text(Number(love.text()) + 1);
					}

					react(id, expeditor_id, 2, token);
				break;

				case 'msef':

					if (msef.hasClass('activ'))
					{
						(Number(msef.text()) == 1) ? msef.text('') : msef.text(Number(msef.text()) - 1); 
						msef.removeClass('activ');
					} else if (!msef.hasClass('activ'))
					{
						(Number(activ.text()) == 1) ? activ.text('') : activ.text(Number(activ.text() - 1));
						activ.removeClass('activ');

						msef.addClass('activ');
						msef.text(Number(msef.text()) + 1);
					}

					react(id, expeditor_id, 3, token);
				break;
			}
		});
	});
});