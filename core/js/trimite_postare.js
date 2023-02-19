/**
* 2020 C. Hodor - PHP OS
*/

$(document).ready(function()
{
    $("#trimite_postare").click(function () {
        editor
            .save()
            .then((e) => {
                $.ajax({
                    url: "/nodes/RequestProtocol.php",
                    type: "POST",
                    data: { post_request: JSON.stringify(e), post_title: $("#titlu_postare").val(), token: token_general, expeditor: expeditor_id },
                    success: function () {
                        location.reload();
                    },
                });
            })
            .catch((e) => {
                console.log("Eroare: " + e);
            });
    });
});