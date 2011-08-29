// $ Plugin Boilerplate
// A boilerplate for jumpstarting $ plugins development
// version 1.1, May 14th, 2011
// by Stefan Gabos

(function($) {

  $.CSSPanel = function(element, options) {

    var defaults = {
      foo: 'bar',
      csselements: '',
      jsonpath: '',
      onFoo: function() {}
    }

    var plugin = this;

    plugin.settings = {}

    var $element = $(element),
    element = element;

    plugin.init = function() {
      plugin.settings = $.extend({}, defaults, options);
	  
	  $element.append('<div class="panel-content" class="clearfix"><div id="panel-content-inner" class="clearfix"><div class="content"></div></div><a href="" class="trigger">info</a></div>');

      if (plugin.settings.csselements != '') {
        tt = plugin.settings.csselements;
        build();
      }

      if (plugin.settings.csselements == '' && plugin.settings.jsonpath != '') {
        $.getJSON(plugin.settings.jsonpath, function(data) {
          tt = data;
          build();
          console.log(data);
        });
      }

      $(element).find(".trigger").click(function(){
        $(this).parent().find('.content').toggle({
          width: "toggle"
        });
        return false;
      });

    }

    plugin.foo_public_method = function() {
    // code goes here
    }

    var build = function() {
      $.each(tt,function(key, array) {
        tt[key]['obj'] = $(element).find(".content").append('<div class="clearfix ' +  array.type  + '"><h2 class="title">' +  array.text + '</h2></div>').find('div:last');

        if (array.type == 'background-color' || array.type == 'color') {
          color_worker(key, array);
        }

        if (array.type == 'background-image') {
          image_worker(key, array);
        }

        if (array.type == 'font-family') {
          fontfamily_worker(key, array);
        }		
		
        if (typeof array.description !== 'undefined') {
          $(tt[key]['obj']).append('<div class="help"/>').append('<div class="description">'+ array.description +'</div>');

          $(tt[key]['obj']).find(".help").click(function(){
            $(this).toggleClass('open').parent().find('.description').toggle();
          });
        }


      })

      $(element).find(".content").append('<input value="Save" type="submit" />');
      $(element).find(".content").find('input').click(save);
	  
	  $(element).find(".content").append('<div class="logger"><div class="icon"></div><span class="text"></span></div>');
	  
	  

    //$('body').append('<style>body { background-color: red; }</style>');
    }

	var logger = function(str, type) {
		var log = $(element).find('.logger');
		$(log).find('.text').text(str);
		
	}
	
    var inlineCSS = function() {
      var ret = '';
      $.each(tt, function(key, array) {

        if (typeof array.value !== 'undefined') {
          ret += array.selector + ' {' + array.type + ':' + array.value  + ';} ';
        }

      })

      // output all css values in inline style
      // wrap style tag in div to get it working and IE
      // style attribute can not really have id/rel attributes
      if((sel = $('#panel-styles')).length == 0) {
        $('body').append('<div id="panel-styles"><style>' + ret  + '</div>');
      } else {
        $(sel).html('<style>' + ret + '<style>');
      }
    }

    var save = function() {
      var values = {};
      var i = 0;

      $.each(tt, function(key, array) {

        if (typeof array.value !== 'undefined') {
          values[i] = {
            "selector": array.selector,
            "type": array.type,
            "value": array.value
          }
		  
		if (typeof array.ext !== 'undefined') {
			  values[i].ext = array.ext
		}

		i++;
		  
        }

      })

      if (i > 0) {
        //alert(i);

        var string = (typeof JSON === 'undefined') ? $.toJSON(values) : string = JSON.stringify(values);

        $.post(plugin.settings.jsonpath, {
          save: string
        },
        function(data) {
          //alert("Data Loaded: " + data);
		  logger('saved');
        });
        console.log(values);
      //saveit
      }
    }

    var fontfamily_worker = function(key, array) {
	  //var fonts = ['Times, "Times New Roman", Georgia, "DejaVu Serif", serif', 'Georgia, "Times New Roman", "DejaVu Serif", serif'];
	  var fonts = {};
	  fonts['times'] = {'name': "Times, 'Times New Roman'"};
	  fonts['georgia'] = {'name': "Georgia, 'Times New Roman', 'DejaVu Serif', serif"};
	  fonts['verdana'] = {'name': "Verdana, Tahoma, 'DejaVu Sans', sans-serif"};
	  fonts['driod'] = {'name': "'Droid Sans',sans-serif", 'link': 'http://fonts.googleapis.com/css?family=Droid+Sans'};
	  fonts['helvetica'] = {'name': "'HelveticaNeue-Light', 'Helvetica Neue Light',Helvetica,sans-serif"};
	  
	  

	  var opt = '<select name="fontfamiliy" size="1"><option value="clear1" class="clear1">clear1</option>'

	  for (var font in fonts) {
		opt += '<option value="' + font +'" class="' + font +'" style="font-family: ' + fonts[font].name  + '">' + font +'</option>';
		if (typeof fonts[font].link !== 'undefined') {
			$('body').append("<link href='" +  fonts[font].link + "' rel='stylesheet' type='text/css'>");
		}
	  }
	  opt += '</select>'
	  
      var obj = $(tt[key]['obj']).append(opt).find('select');
	  $(obj).change(function() {

		if(typeof tt[key].ext !== 'undefined') delete tt[key].ext
		if ($(this).val() == 'clear1') {
			if(typeof tt[key].value !== 'undefined') delete tt[key].value
		} else {
			tt[key].value = fonts[$(this).val()].name;		
			if (typeof fonts[$(this).val()].link !== 'undefined') tt[key].ext = fonts[$(this).val()].link;
		}

        inlineCSS();
      });	  
	  
	}
	
    var color_worker = function(key, array) {
      var selc = $(tt[key]['obj']).append('<div class="colorselector"/>').find('.colorselector');


      // set current color values
      try {
        if (typeof (color = $(array.selector).css(array.type)) !== 'undefined') {
          if (color != 'transparent') $(selc).css('background-color', color);
        }
      } catch(e) {
      // IE7 + $ cant handle :hover effects
      }



      selc.ColorPicker({
        onChange: function (hsb, hex, rgb) {
          //$(array.selector).css(array.type, '#' + hex);

          $(selc).css('background-color', '#' + hex);
          tt[key].value = '#' + hex;
          inlineCSS();
        }

      });
    }

    var image_worker = function(key, array) {
      var obj = $(tt[key]['obj']).append('<div class="ext images clearfix"/>').find('div:last');
      
      $.each(array.files ,function(image_id, img_array) {
        var selc = $(tt[key]['obj']).find('> div').append('<div class="img"><img src="' + img_array.thumbnail + '"></div>').find(".img:last");

        $(selc).click(function(){
          $(tt[key]['obj']).find('.selected').removeClass('selected'); $(selc).addClass('selected');
          tt[key].value = 'url(' + img_array.image + '); ';
		  if (img_array.image.indexOf("repeat-y") != -1 ) {
			tt[key].value += 'background-position: center top ; background-repeat:repeat-y'
		  } else if (img_array.image.indexOf("repeat-x") != -1 ) {
			tt[key].value += 'background-position: center top ; background-repeat:repeat-x'
		  } else {
			tt[key].value += 'background-position:0% 0% ; background-repeat:repeat'		  
		  }
		  
		  
          inlineCSS();
        });

      })
      
      var none = obj.prepend('<div class="img none"/>').find('.none');
      $(none).click(function(){
        $(tt[key]['obj']).find('.selected').removeClass('selected'); $(this).addClass('selected');
		tt[key].value = 'none';		
	    inlineCSS();
      });

      
    }

    var tt = {};

    plugin.init();

  }

  $.fn.CSSPanel = function(options) {

    return this.each(function() {
      if (undefined == $(this).data('CSSPanel')) {
        var plugin = new $.CSSPanel(this, options);
        $(this).data('CSSPanel', plugin);
      }
    });

  }

})(jQuery);