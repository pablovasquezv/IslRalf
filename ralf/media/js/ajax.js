function select_prestador(reg_id){    
    $.post('http://'+location.host+site+'/ajax/select_prestador/'+reg_id,
        function(data) {
            $('#select_prestador').html(data);
            $('#select_comuna select').val('-1');
            $('#select_direccion select').val('-1');
            $('#select_hr_atencion input').val('');
        });
}

function select_comuna(prtd_id,reg_id){    
    $.post('http://'+location.host+site+'/ajax/select_comuna/'+prtd_id+'/'+reg_id,
        function(data) {
            $('#select_comuna').html(data);
            $('#select_direccion select').val('-1');
            $('#select_hr_atencion input').val('');
        });
}

function select_direccion(com_id,prtd_id){    
    $.post('http://'+location.host+site+'/ajax/select_direccion/'+com_id+'/'+prtd_id,
        function(data) {            
            $('#select_direccion').html(data);
            $('#select_hr_atencion input').val('');
        }
    );
}

function select_hr_atencion(prtd_id){    
    $.post('http://'+location.host+site+'/ajax/select_hr_atencion/'+prtd_id,
        function(data) {            
            $('#select_hr_atencion').html(data);
        }
    );
}

function select_insumo(ose_insumo_id){    
    $.post('http://'+location.host+site+'/ajax/select_insumo_descrip/'+ose_insumo_id,
        function(data) {   
            $('#select_insumo_descrip').html(data);
        }
    );
}


function autocompletar(tipo){    
    var ids=getIds(tipo);
    var persona = $(ids["texfield"]).val();
    
    if(persona.length <= 0){
        $(ids["caja"]).hide();
        return;
    }            
    
    $.ajax({
        type: "POST",
        url: site+"/ajax/autocompletar",
        data: "persona="+persona+"&tipo="+tipo,
        error: function(){
            $(ids["caja"]).show();
            $(ids["loader"]).addClass('hide');
        },
        success: function(msg){
            $(ids["caja"]).show();
            $(ids["loader"]).addClass('hide');
            if (msg.length > 0) {
                $(ids["lista"]).html(msg);
            }
            $(ids["lista"]).removeClass('hide');
        },
        beforeSend: function(){
            $(ids["lista"]).addClass('hide');
            $(ids["caja"]).show();
            $(ids["loader"]).removeClass('hide');
        }
    });
}

function autocompletar_ubica_lesion(tipo){
    var ids=getIds(tipo);
    var lesion = $(ids["texfield"]).val();
    
    $(ids["hidden"]).val('');

    if(lesion.length <= 0){
        $(ids["caja"]).hide();
        return;
    }            
    
    if(lesion.length <= 2 ) {
        return;
    }

    $.ajax({
        type: "POST",
        url: site+"/ajax/autocompletar_ubica_lesion",
        data: "lesion="+lesion+"&tipo="+tipo,
        error: function(){
            $(ids["caja"]).show();
            $(ids["loader"]).addClass('hide');
        },
        success: function(msg){
            $(ids["caja"]).show();
            $(ids["loader"]).addClass('hide');
            if (msg.length > 0) {
                $(ids["lista"]).html(msg);
            }
            $(ids["lista"]).removeClass('hide');
        },
        beforeSend: function(){
            $(ids["lista"]).addClass('hide');
            $(ids["caja"]).show();
            $(ids["loader"]).removeClass('hide');
        }
    });
}

function add(persona, id, tipo) {
    
    var ids=getIds(tipo);
    
    var texfield= $(ids["texfield"]);
    texfield.val(persona);
    texfield.attr("disabled", "disabled");
    $(ids["hidden"]).val(id);
    $(ids["caja"]).hide();
    $('.delete-diagnostico').css('visibility','visible');

}

function limpiar(tipo) {

    var ids=getIds(tipo);

    var texfield= $(ids["texfield"]);
    texfield.val("");
    texfield.removeAttr("disabled");
    $(ids["hidden"]).val("");
    $(ids["caja"]).hide();
    $('.delete-diagnostico').css('visibility','hidden')
   
}

function getIds(tipo){
    var ids=[];
    if(tipo == "evaluado"){
        ids["hidden"] = "#evaluado_id";
        ids["texfield"] = "#evaluado";
        ids["caja"] = "#evaluados";
        ids["lista"] = "#autoListaEvaluados";
        ids["loader"] = '#evaluados_loader';
        ids["boton_agregar"] = "";
    }
    if(tipo == "lesion"){
        ids["hidden"] = "#ubica_lesion_id";
        ids["texfield"] = "#lesion";
        ids["caja"] = "#lesiones";
        ids["lista"] = "#autoListaLesiones";
        ids["loader"] = '#lesiones_loader';
        ids["boton_agregar"] = "";
    }
    return ids;
}

function hide_caja(tipo){
    var ids=getIds(tipo);
    $(ids["caja"]).hide();
}

function init_autocompletado_lesion() {
    var ids=getIds('lesion');
    var textfield = $(ids['texfield']);
    if(textfield.val()) {
        textfield.attr("disabled", "disabled");
        $('.delete-diagnostico').css('visibility','visible');
    }
}

function antecedentes_calificacion(calidad){    
    $.post('http://'+location.host+site+'/ajax/antecedentes_calificacion/'+calidad,
        function(data) {
            $('#antecedentes_calificacion').html(data);
            $('#indicaciones').empty();
        });    
}

function ante_insuf(calidad,evento){        
    $.post('http://'+location.host+site+'/ajax/antecedentes_insuficientes/'+calidad+'/'+evento,
        function(data) {
          if(calidad == 'insuficiente')
          {
            $('.div-float-left').addClass('cal-admin');
            $('.div-float-right').addClass('ant-calif');
            $('.conclusiones').removeAttr('class');
          }
          else
          {
            $('.div-float-left').removeClass('cal-admin');
            $('.div-float-right').removeClass('ant-calif');
          }
          
            $('#antecedentes_insuficientes').html(data);
            $('#indicaciones').empty();
        });
}

function indicaciones(riot_cls) {
    //val = $('.clrg-dsc-indic').val();    
    $.post('http://'+location.host+site+'/ajax/indicaciones/'+riot_cls,
        function(data) {
            $('#indicaciones').html(data);
        });
}

function otros_antecedentes(checked) {
    $.post('http://'+location.host+site+'/ajax/otros_antecedentes/'+checked,
        function(data) {
            $('#otros_antecedentes').html(data);
        });
}

function otro_especialista(checked) {
    $.post('http://'+location.host+site+'/ajax/otro_especialista/'+checked,
        function(data) {
            $('#otro_especialista').html(data);
        });
}


jQuery(document).ready(function()
{
  $('.otros-antecedentes').live('click', function() {otros_antecedentes(this.checked);});
  $('select[multiple=multiple] option').hover (
    function(){$(this).css('background','#CCC').css('color','#FFF')},
    function() {$(this).css('background','#FFF').css('color','#787878');}).css('padding','2px 0');

  $('#listado').dataTable( {"sScrollY": "300px","bPaginate": false, "bInfo": false, "bFilter": true});


  $('div.dataTables_scrollBody table.listado tr').hover(
    function() {$(this).css('background','#EEE');}, 
    function() {if(!$(this).hasClass('selected'))$(this).css('background','#FFF');}
  );

  $('.listado tr td').click(function() {
    window.location = $("input[name='url_edit']").val()+"/"+$(this).parent().attr('id');
  });


  $('.otro-especialista').live('click', function() {
    otro_especialista(this.checked);
  });
  
  //$('.fancybox').fancybox();
  /*$('.fancybox').fancybox({
        'width' : 690,
       'height' : 600,
       'padding': 0,
       'centerOnScroll': true,
       'type' : 'iframe',
       'onClosed': function() { }
  });*/
  
  $('.fancybox').live('click', function() {
    $this = $(this);
    $.fancybox({
       'href': $this.attr('href'),
       'width' : 690,
       'height' : 600,
       'padding': 0,
       'centerOnScroll': true,
       'type' : 'iframe'
    });
    return false;
  });

  $('.fancybox-narrow').live('click', function() {
    $this = $(this);
    $.fancybox({
       'href': $this.attr('href'),
       'width' : 600,
       'height' : 300,
       'padding': 0,
       'centerOnScroll': true,
       'type' : 'iframe'
    });
    return false;
  });

  $('.fancybox-medium').live('click', function() {
    $this = $(this);
    $.fancybox({
       'href': $this.attr('href'),
       'width' : 600,
       'height' : 450,
       'padding': 0,
       'centerOnScroll': true,
       'type' : 'iframe'
    });
    return false;
  });

  $('.fancybox-small').live('click', function() {
    $this = $(this);
    $.fancybox({
       'href': $this.attr('href'),
       'width' : 600,
       'height' : 180,
       'padding': 0,
       'centerOnScroll': true,
       'type' : 'iframe'
    });
    return false;
  });

  $('.fancybox-big').live('click', function() {
    $this = $(this);
    $.fancybox({
       'href': $this.attr('href'),
       'width' : 1024,
       'height' : 700,
       'padding': 0,
       'centerOnScroll': false,
       'overlayShow': true,
       'type' : 'iframe'
    });
    return false;
  });

  $('.back-page').click(function(){
        if(document.referrer.indexOf(window.location.hostname) != -1){
            parent.history.back();
            return false;
        }
    });
  
  init_autocompletado_lesion();
  
});

/*
function buscar_trabajador(form)
{
  $('.animacion-cargando').css('visibility','visible');
  //formToArray(form);
  //return false;
  $.post(form.action, formToArray(form), function(data) {
    $('.animacion-cargando').css('visibility','hidden');
    $('#buscar-trabajador-view').html(data); 
  });
  return false;
}
*/

/**
 * ToDo: faltan más validaciones
 * entre otras cosas.

function formToArray(form)
{
  var array = {};
  $.each(form,function(k,v){array[v.name]=v.value;});
  return array;
}
*/

function get_empleador(element)
{
  if(element.value)
  {
    element.form.submit();
  }
}

function volver_a_buscar(url)
{
  var r=confirm("¿Volver a buscar?, esto borrará los datos ingresados");
  if (r== true)
  {
    //window.location.href='http://www.google.cl';
    window.location.href = window.location.pathname;
  }
  return false;
}

