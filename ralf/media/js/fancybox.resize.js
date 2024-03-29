jQuery(document).ready(function()
{
  //Número Mágicos que pueden variar
  var $big_start    = 130;
  var $big_view     = 55;
  var $small_view   = 10;
  var $margin_top   = -150;
  //var $margin_left  = -190;
  //Nombre del contenedor principal
  var $main_wrap    = $('#contenedor');
  var $view_height  = $main_wrap.height();
  
  var w = document.defaultView || document.parentWindow;
  var d = w.parent.document;

  var $fancybox_wrap    = $(d.getElementById('fancybox-wrap'));
  var $fancybox_inner   = $(d.getElementById('fancybox-inner'));
  var $fancybox_frame   = $(d.getElementById('fancybox-frame'));
    
  $view_height = ($view_height > $big_start) ? ($view_height+$big_view) : ($view_height+$small_view);
  //alert($view_height-$margin_top);
  
  /**
   * Controlamos el resize según el alto de la pantalla
   * ToDo Obtener dinamicamente el alto máximo según tamaño pantalla
   */
  if($view_height >= 600)
  {
    $view_height = 600;
    $margin_top = -250;
  }
  
  //Agregamos efecto al resize y la nueva posición
  $fancybox_wrap.animate({
    //width: ($main_wrap.width()+50)+'px',
    height: $view_height+'px'
    //marginTop: $margin_top+'px'
    //marginLeft: $margin_left+'px'
  });

  $fancybox_inner.height($view_height);
  //$fancybox_inner.width($main_wrap.width()+50);

  $fancybox_frame.height($view_height);
  //$fancybox_frame.width($main_wrap.width()+50);
  
});