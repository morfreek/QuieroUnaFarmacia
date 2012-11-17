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

function actualizar_listado(){
    var link = ($("#opcion_listado").val()) ? $("#opcion_listado").val()+'/':'';
    location.href = '/farmacias/'+$("#comuna").val()+'/'+link;
}