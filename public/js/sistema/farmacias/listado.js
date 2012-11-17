function actualizar_listado(comuna,opcion){
    var link = (opcion) ? opcion+'/':'';
    location.href = '/farmacias/'+comuna+'/'+link;
}

function call_comuna(region,contenedor){
    $.ajax({
        url: '/index/comunas/',
        type: 'POST',
        data: 'region='+region,
        beforeSend: function(){$('#'+contenedor).html('<option>Espere....</option>')},
        dataType: 'json',
        success: function(result){
            $('#'+contenedor).html(result.result);
            $('#'+contenedor).attr('disabled',!result.operation);
        }
    })
}

$(document).ready(function(){
    ($("#content-list").find(".media").size())?null:$("#maps").attr("disabled",true);
    $("#maps").live("click",function(){
        if($("#content-list").find(".media").size()){
            if($("#content-mapa").css("display")=="none"){
                $("#content-mapa").show();
                $("#content-list").hide();
                if(typeof $("#cambiar-ubicacion").val() == 'undefined'){
                    GMaps.geolocate({
                        success: function(position) {
                            map.setCenter(position.coords.latitude, position.coords.longitude);
                            map.addMarker({
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            });
                        }
                    });
                }
                map.refresh();
                
            }
            $(this).addClass("active");
            $("#list").removeClass("active");
        }
    });
    $("#list").live("click",function(){
        if($("#content-list").css("display")=="none"){
            $("#content-list").show();
            $("#content-mapa").hide();
        }
        $(this).addClass("active");
        $("#maps").removeClass("active");
    });
});
