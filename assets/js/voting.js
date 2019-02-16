
jQuery(document).ready(function($) {
    var cookieHandler = new CookieHandler();
    $("ul.stars .voto a").click(function(e)
    {
        e.preventDefault(); /* Evitamos el # en la barra de direcciones */
        var puntaje=$(this).data("value"); /* Obtenemos el resultado del voto: 1 a 5 */
        var objeto=$(this).closest("li").find(".count"); /* Obtenemos el elemento para cambiar el valor una vez realizado el voto */ 
        if (objeto && puntaje) votar(puntaje,objeto); /* Votamos: en este tipo, el valor siempre será la estrella elegida */
    });

    function votar(puntaje,objeto)
    {
        if ( cookieHandler.getCookie("voto") === false ) {
            
            // asignamos una cookie
            cookieHandler.setCookie("voto", puntaje, 1, 'months');

            jQuery.ajax({
                method: "POST",
                url: context.ajaxurl,
                dataType: 'json',
                data: { action:"svs_vote" , "post_id": context.post_id, "vote_score": puntaje }
            })
            .done(function(msg) 
            {                 
                var maximoposible=msg.votos*5;
                var porcentaje=msg.puntaje*100/maximoposible;
                    jQuery(".stars .stat").css("width",porcentaje+"%");

                alerta("Gracias por tu voto :)","ok");

            })
            .fail(function(msg) {
                alerta(msg,"ko");
            });
        }
        else 
            alerta("Lo sentimos, solo puede votar una vez", "ko");
    }



    function alerta(mensaje,modo)
    {
        jQuery("body").append("<div class='mensaje "+modo+"'>"+mensaje+"</div>");
        jQuery('.mensaje').fadeOut(4000, function(){ jQuery(this).remove(); });
    }

});

