var site = '/';
var url  = 'http://'+location.host+site;

function ajax_send(form, custom_pt)
{
  $('.animacion-cargando').css('visibility','visible');
  var url   = form.action;
  var elems = form.elements;
  var array  = {};
  for(var i = 0; i < elems.length; i++)
  {
    var elm = elems[i];
    var val = elm.value;
    //if(val)
    //{

      if(elm.type == 'checkbox'  || elm.type == 'radio')
      {
        if(elm.checked == true)
          array[elm.name] = val;
        else if(!array[elm.name])
          array[elm.name] = '';
      }
      else if(elm.type == 'select-multiple') 
      {
        array[elm.name] = [];
        for(var x = 0; x < elm.length; x++)
        {
          if(elm[x].getAttribute('selected'))
          {
            array[elm.name].push(elm[x].value);
          }
        }
      }
      else if(elm.type == 'file')
      {
      }
      else if(elm.type != 'radio')
      {
        array[elm.name] = val;
      }
    //}
    //alert(elm.type)
  }
  /*$.each(array, function(k,v){
    //alert(k+"=>"+v);
  });*/
  array['ajax'] = true;
  //alert(url);
  jQuery.post(url, array, function(response) {
    //alert(response);
   $('.animacion-cargando').css('visibility','hidden');
    var res=jQuery.parseJSON(response)  
    if(res)
    {
      if(res.set_fields)
      {
        $.each(res.set_fields, function(key,value)
        {
          $("[name='"+key+"']").val(value);
        });
      }
      if(res.show_fields)/** <<< REFACTORIZAR TODO ESTE BLOQUE*/
      {
        
        $.each(res.show_fields, function(key,value)
        {
          //alert(value)
          var element = $("[name='"+key+"']")[0];
          var type = element.nodeName.toLowerCase();
          //alert($(element).attr('type'));
          if($(element).attr('type') == 'radio')
            $("."+key).css('color','#F18F8F').css('font-weight','bold');
          else if($(element).attr('type') == 'file')
          {
            //$(element).css('border', '1px solid #F18F8F');//ToDo parametrizar 
            $(element).css('color', '#F18F8F');//ToDo parametrizar
          }
          else if(type == 'input')
            $(element).css(res.css_rule, res.css_value);
          else if(type == 'select')
          {
            $(element).css('border', '1px solid #F18F8F');//ToDo parametrizar 
            $(element).css('color', '#F18F8F');//ToDo parametrizar
          }
          $(element).val('');
        });

        if(res.prompt)
          $.prompt(res.prompt+"<br/>"+res.message);
        else
          $.prompt(res.message);
      }
      else if(res.errors)
      {
        if(!custom_pt)
        {
          $.prompt(res.response,{buttons: {Aceptar:true}});
        }
        else
        {
          custom_prompt(res.response, 250);
        }
      }
      else
      {
        if(!custom_pt)
        {
          $.prompt(res.response, {buttons: {Aceptar:true}, submit:function(){
              if(parent.$.fancybox) parent.$.fancybox.close()
            }
          });
        }
        else {
          custom_prompt(res.response, 350, function() {
            if(parent.$.fancybox) parent.$.fancybox.close();
            if(res.redirect) window.location.href=res.redirect;
          });
        }
      }/** <<< REFACTORIZAR TODO ESTE BLOQUE*/
    }
  });
  return false;
}

/**
 * Esta funcion vuelve a cargar url del sitio
 */
function ajax_load(container, url)
{
  $(container).load(url);
}

function set_url_to_ajax_load(url, pos,load)
{
  var slash_count = 6 + pos - 1;//la cantidad de elementos entre slash en kohana:http://dominio/aplicacion/controlador/accion
  var url_split = url.split('?');
  var paginador = '';
  if(url_split.length == 2)//esto significa (en este sitio) que esta usando un paginador
  {
    url  = url_split[0];
    paginador = '?'+url_split[1];
  }
  var lchar = url.substring(url.length-1);//ultimo character
  url       = (lchar == '/') ? url.substring(0, url.length-1) : url;//url sin el ultimo slash
  url_split = url.split('/');//ahora dividimos por slash
  if(url_split.length < slash_count)
  {
    var restantes = slash_count - url_split.length
    for(var i = 0; i < restantes;i++)
    {
      url += '/0';//llenamos con 0 los parametros anteriores:0 es igual NULL o FALSE en php
    }
  }
  return url+'/'+load+'/'+paginador;
}

function custom_prompt(mensaje, width, callback)
{
  width = (width) ? width : 250;
  
  $.prompt(mensaje,{buttons:{Aceptar:true}, submit:callback});
  $('.jqimessage').css('font-size','12px');
  $('div.jqi').css('width',width+'px');
  margin_left = -((width/2)+8)+'px';
  margin_top  = ($(window).height()/4)-200+'px';
  $('div.jqi').css({'margin-left' : margin_left, 'margin-top' : margin_top,'text-align' : 'center'});
  $('.jqibuttons').css({'text-align' : 'center'});
}

jQuery(document).ready(function(){
  var short_bg = "url('"+url+"media/images/short-input-bg.png"+"')";
  var medium_bg = "url('"+url+"media/images/medium-input-bg.png"+"')";
      
  $('div.crear-cuenta-wrap input').click(function(){
    _click_focus(this, 'input', medium_bg, null)}
  ).focus(function(){
    _click_focus(this, 'input', medium_bg, null)}
  );
    
  $('div.crear-usuario-wrap input').click(function(){
    _click_focus(this, 'input', medium_bg, null)}
  ).focus(function(){
    _click_focus(this, 'input', medium_bg, null)}
  );
    
  $('div.crear-usuario-wrap select').click(function(){
    _click_focus(this, 'select', null, null)}
  ).focus(function(){
    _click_focus(this, 'select', null, null)}
  );
  
  $('div.crear-cuenta-wrap select').click(function(){
    _click_focus(this, 'select', null, null)}
  ).focus(function(){
    _click_focus(this, 'select', null, null)}
  );
    
  $('div.crear-usuario-wrap input[name="persona_sexo"]').click(function(){
    _click_focus(this, 'radio', null, 'span.persona_sexo')}
  ).focus(function(){
    _click_focus(this, 'radio', null, 'span.persona_sexo')}
  );
    
  $( ".datepicker" ).datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "1945:2030"
  }
        
    );
  
  
  $('.aha_search').keyup(function()
  {
    var value = $(this).val();
    var model = $(this).attr('id');
    var field = $(this).attr('name');
    var array = {'field': field, 'value': value}
    var url   = site+'/ajax/search/'+model;
    var search = false;
    //alert(validar_rut(value))
    if(field.match(new RegExp(/rut/)) && validar_rut(value))
      search = true;
    //else if(false)//validar string antes de buscar
    //  search = false;
    
    if(search)
    {
      //alert(field)
      jQuery.post(url, array, function(data) 
      {//alert(data);
        var response = jQuery.parseJSON(data)
        if(response.fields)
        {
          $.each(response.fields, function(key,value)
          {
            var element = $("[name='"+key+"']")[0];
            if($(element).attr('type') == 'radio' && $(element).val() == value)
              $(element).attr('checked','checked');
            else
              $(element).val(value);
            
            $(element).removeAttr('readonly').removeAttr('disabled');
          });
        }
      });
    }
    
  });
  
  
});

function _click_focus(elemento, type, bg, _class) 
{
  if(type == 'select')
    $(elemento).css('color', '#787878').css('border','1px solid #CCC');
  else if(type == 'radio')
    $(_class).css('color', '#787878').css('font-weight','normal');
  else if(type == 'input')
    $(elemento).css('background-image', bg);
}


function send_page(page)
{
  window.location = page;
}

function validar_rut(rut)
{
  var rexp = new RegExp(/^([0-9])+\-([kK0-9])+$/);
  if(rut.match(rexp))
  {
    var RUT     = rut.split("-");
    //var elRut   = RUT[0].toArray();no funca con jQuery
    //reemplazamos la linea anterior por la dos siguientes:
    var elRut = new Array();
    for(k=0; k < RUT[0].length; k++) elRut[k]=RUT[0][k];
    
    var factor  = 2;
    var suma    = 0;
    var dv;
    
    for(i=(elRut.length-1); i>=0; i--)
    {
      factor = factor > 7 ? 2 : factor;
      suma += parseInt(elRut[i])*parseInt(factor++);
    }
    
    dv = 11 -(suma % 11);
    
    if(dv == 11)
      dv = 0;
    else if (dv == 10)
      dv = "k";

    if(dv == RUT[1].toLowerCase())
      return true;
    else
      return false;
  }
  else
    return false;
}