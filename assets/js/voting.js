jQuery(document).ready(function($) {
    $("ul.stars .voto a").click(function(e)
    {
        e.preventDefault(); /* Evitamos el # en la barra de direcciones */
        var puntaje=$(this).data("value"); /* Obtenemos el resultado del voto: 1 a 5 */
        var objeto=$(this).closest("li").find(".count"); /* Obtenemos el elemento para cambiar el valor una vez realizado el voto */ 
        if (objeto && puntaje) votar(puntaje,objeto); /* Votamos: en este tipo, el valor siempre será la estrella elegida */
    });
});

function votar(puntaje,objeto)
{
    $.ajax({
        method: "POST",
        url: context.ajaxurl,
        dataType: 'json',
        data: { action:"svs_vote" , "post_id": context.post_id, "vote_score": puntaje }
    })
    .done(function(msg) 
    {                 
            var maximoposible=msg.votos*5;
            var porcentaje=msg.puntaje*100/maximoposible;
            $(".stars .stat").css("width",porcentaje+"%");
        
        alerta("Gracias por tu voto :)","ok");
    })
    .fail(function(msg) {
        alerta(msg,"ko");
    });
}



function alerta(mensaje,modo)
{
    $("body").append("<div class='mensaje "+modo+"'>"+mensaje+"</div>");
    $('.mensaje').fadeOut(4000, function(){ $(this).remove(); });
}