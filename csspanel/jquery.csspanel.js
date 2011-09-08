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
      profiles: '',
      onFoo: function() {}
    }

    var plugin = this;
    var selected_profile = '';

    plugin.settings = {}

    var $element = $(element),
    element = element;

    plugin.init = function() {
      plugin.settings = $.extend({}, defaults, options);

      // remove old panel if we rebuild it, or only append it
      if ($element.find('.panel-content').length > 0) $element.find('.panel-content').remove();
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

    var profiles = function() {
      var ele = $(element).find('.content');

      var str = '<label for="profil">Profiles</label><select name="profil">';
      $.each(plugin.settings.profiles, function(key, profile) {
        str += '<option value="' + key +'">' + key +'</option>';
      })

      str += '</select>'

      if(typeof plugin.settings.profiles == 'string') {
      //TODO: load it on json
      }

      ele.append('<div class="clearfix profiles"/>').find('> :last-child').append(str).find('select').change(function(){
        selected_profile = $(this).val();

        $.each(plugin.settings.profiles[selected_profile], function(key, value) {

          // convert string splitted by | to object
          if (typeof value == 'string') {
            var substr = value.split('|');
            value = {
              'selector': substr[0],
              'type': substr[1],
              'value': substr[2]
            };
          }

          // merge values from profile to our global object
          $.each(tt, function(temp, cssopt) {
            if(value.selector == cssopt.selector && value.type == cssopt.type) {
              tt[temp].value = value.value;
            }
          })


        })

        // set and reload panel on new color
        inlineCSS();
        plugin.init();

      });

      // if any profil is selected add attrib to selectbox
      if (selected_profile != '') {
        ele.find('.profiles select option[value="' + selected_profile + '"]').attr('selected', 'selected');
      }

    }

    var build = function() {

      $.each(tt,function(key, array) {
        tt[key]['obj'] = $(element).find(".content").append('<div class="clearfix ' +  array.type  + '"><h2 class="title">' +  array.text + '</h2></div>').find('div:last');

        var RunFunction = array.type.replace('-', '_') + '_worker';

        try {
          func = eval(RunFunction);
        } catch (exception) {
          console.log(exception.toString())
        }

        if (typeof func === 'function') {
          func(key, array);
        } else {
          console.log('unknown function: ' + RunFunction)
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

      if (plugin.settings.profiles != '') {
        profiles();
      }

      $(element).find(".content").append('<div class="logger"><div class="icon"></div><span class="text"></span></div>');

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
        $(sel).html('<style>' + ret + '</style>');
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

        // IE7 has no stringify
        var string = (typeof JSON === 'undefined') ? $.toJSON(values) : string = JSON.stringify(values);

        $.post(plugin.settings.jsonpath, {
          save: string
        },
        
        function(data) {
          logger('saved');
        });


      }
    }

    var font_family_worker = function(key, array) {

      var fonts = {};
      fonts['times'] = {
        'name': "Times, 'Times New Roman'"
      };
      fonts['georgia'] = {
        'name': "Georgia, 'Times New Roman', 'DejaVu Serif', serif"
      };
      fonts['verdana'] = {
        'name': "Verdana, Tahoma, 'DejaVu Sans', sans-serif"
      };
      fonts['driod'] = {
        'name': "'Droid Sans',sans-serif",
        'link': 'http://fonts.googleapis.com/css?family=Droid+Sans'
      };
      fonts['helvetica'] = {
        'name': "'HelveticaNeue-Light', 'Helvetica Neue Light',Helvetica,sans-serif"
      };

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

    var font_size_worker = function(key, array) {
      var obj = $(tt[key]['obj']);

      var objs = ['12px', '14px', '16px']
      var create = '<select id="fontsize">';

      $.each(objs, function(skey, svalue) {
        create += '<option value="'+svalue+'">'+svalue+'</option>';
      })

      create += '</select>';
      $(obj).append(create);

      $(obj).find('select').change(function() {
        tt[key].value = $(this).val();
        inlineCSS();
      })

      // if any profil is selected add attrib to selectbox
      if (typeof tt[key].value !== 'undefined') {
        obj.find('select option[value="' + tt[key].value + '"]').attr('selected', 'selected');
      }

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
      
      if(!jQuery().ColorPicker) {
        selc.parent().append('<div class="error">ColorPicker missing</div>');
        return false;
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

    // some one use it for callback
    var background_color_worker = color_worker

    var background_image_worker = function(key, array) {
      var obj = $(tt[key]['obj']).append('<div class="ext images clearfix"/>').find('div:last');

      $.each(array.files ,function(image_id, img_array) {
        var selc = $(tt[key]['obj']).find('> div').append('<div class="img"><img src="' + img_array.thumbnail + '"></div>').find(".img:last");

        $(selc).click(function(){
          $(tt[key]['obj']).find('.selected').removeClass('selected');
          $(selc).addClass('selected');
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
        $(tt[key]['obj']).find('.selected').removeClass('selected');
        $(this).addClass('selected');
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