/**
* 2020 C. Hodor - Open Source Community Platform
*/

$(document).ready(function()
{
    var singleJS = function (url, locatie)
    {
        var scriptTag = document.createElement('script');
        
        scriptTag.src = url;

        locatie.appendChild(scriptTag);
    }

    singleJS('core/plugins/editorjs/addon/header.js', document.body);
    singleJS('core/plugins/editorjs/addon/list.js', document.body);
    singleJS('core/plugins/editorjs/addon/link-embed.js', document.body);
    singleJS('core/plugins/editorjs/addon/image.js', document.body);
    singleJS('core/plugins/editorjs/addon/embed.js', document.body);
    singleJS('core/plugins/editorjs/addon/code.js', document.body);
    singleJS('core/plugins/editorjs/editor.js',  document.body);

    console.log("%cVrei sa participi la dezvoltarea platformei? >> %c<MAILUL TAU>","font-weight: bold;font-size:1rem;","color:red;font-weight: bold;")
    console.log("%cSTOP! >>","color:red;font-weight: bold;font-size:2rem;")
    console.log("Executarea unui cod in aceasta consola poate rezulta in %cpierderea contului.", "color: red")
});