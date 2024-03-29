$(document).ready(function(){
    console.log("listo!");

    $("input#txt_cod_causa").live("keyup", function( event ){
        if(this.value.length == 4 || this.value.length == 5) {
                buscaGlosaCausaPorCodigo(this.value);
            }
    });


});