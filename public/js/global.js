$(function() {

    relHistory = new Array(document.location.pathname);

    $(document).on('click', '.content-load', function(event) {
        $(this).parents('.dropdown.open').toggleClass('open');
        if ($(this).parents('.nav').length) {
            $('.nav').find('li').removeClass('active');
        }
        $(this).parents('li').addClass('active');
        var url = $(this).attr('href');
        var target = $(this).attr('data-target') || '#content';
        ajaxLoad(url, target);
        event.preventDefault();
        event.stopPropagation();
        return false;
    });

    $(document).on('click', '.check-all', function(event) {
        var checked = $(this).is(':checked');
        if (ids = $(this).attr('data-elements-id')) {
            elements = $(this).parents('.table-responsive,.table-striped,.table').find('tbody').find('[type=checkbox][name=' + $(this).attr('data-elements-id') + ']');
        } else {
            elements = $(this).parents('.table-responsive,.table-striped,.table').find('tbody').find('[type=checkbox]');
        }
        elements.each(function(index, value) {
            $(value).prop('checked', checked);
        });
    });

    $(document).on('submit', '.file-upload', function(event) {
        $('#loading-indicator').show();
        document.getElementById("file-upload").onload = function() {
            $('#loading-indicator').hide();
            var content = $('#file-upload').contents().find('body').find('#content').html();
            if (!content) {
                content = $('#file-upload').contents().find('body').html();
            }
            $('#content').html(content);
        }
    });

    $(document).on('dblclick', '.dbl-click', function(event) {
        var url = $(this).attr('href');
        var target = $(this).attr('data-target') || '#content';
        ajaxLoad(url, target);
        event.preventDefault();
        event.stopPropagation();
        return false;
    });

    $(document).on('change', '#count', function(event) {
        var url = $(this).attr('href');
        var target = $(this).attr('data-target') || '#content';
        var params = url.split('/');
        if (params.indexOf('count') !== -1) {
            params.splice(params.indexOf('count'), 2);
            url = params.join('/');
        }
        url += '/count/' + $(this).val();
        ajaxLoad(url, target);
        event.preventDefault();
        event.stopPropagation();
        return false;
    })

    $(document).on('click', '.popup-load', function(event) {
        var url = $(this).attr('href');
        var multi = $(this).hasClass('multi');
        if (multi) {
            var elements = $(this).parents('.table-responsive,.table-striped,.table').find('[type=checkbox]:checked');
            if (!elements.length) {
                return false;
            }
            for (var i = 0; i < elements.length; i++) {
                if (!$(elements[i]).val() || !$(elements[i]).attr('name')) {
                    continue;
                }
                url += '/' + $(elements[i]).attr('name') + '/' + encodeURI($(elements[i]).val());
            }
        }
        $('#myModal').modal({
            keyboard: false
        });
        var target = $(this).attr('data-target') || '.modal-body';
        ajaxLoad(url, target);
        event.preventDefault();
        event.stopPropagation();
        return false;
    });

    $(document).on('submit', '.form-load', function(event) {

        var target = $(this).attr('placeholder') ? $('#' + $(this).attr('placeholder')) : $(this).parents('.modal-body').length ? $(this).parents('.modal-body') : $(this).parents('#content');
        var url = $(this).attr('action');
        var data = $(this).serialize(true);
        if (url.indexOf('format') == -1) {
            url += '/format/html';
        }
        $(target).html('<img src="/css/images/ajax-loader.gif">');
        $.ajax({
            type: "POST",
            url: url,
            enctype: 'multipart/form-data',
            data: data,
            success: function(data, status, xhr) {
                $(target).html(data);
            },
            dataType: "html",
        });
        event.preventDefault();
        event.stopPropagation();
        return false;
    });

    $(document).on('click', '.btn.search', function(event) {
        var url = $(this).parents('form').attr('action');
        var params = $(this).parents('form').serializeArray();
        for (i = 0; i < params.length; i++) {
            value = $.trim(params[i].value).replace(/\u000a/g,',');//.replace("\n/g", ",");
            if (value.length > 1900) {
                //continue;
            }
            url += '/' + params[i].name.replace('[]', '') + '/' + encodeURI(value);//;
        }
        ;
        var params = $(this).parents('form').serialize();//console.log(url);
        //url += '/format/html?' + params;
        relHistory.push(url);
        $(this).parents('form').trigger('submit');
        event.preventDefault();
        event.stopPropagation();
        return false;
    });

    $(document).on('click', '.btn.xls', function(event) {
        var url = $(this).parents('form').attr('action');
        var target = $(this).attr('data-target') || '#content';
        $(target).html('<img src="/css/images/ajax-loader.gif">');
        url += '/format/xls';
        $.ajax({
            type: "POST",
            url: url,
            enctype: 'multipart/form-data',
            data: $(this).parents('form').serialize(true),
            success: function(data, status, xhr) {
                var header = xhr.getResponseHeader('Content-Disposition');
                if (header) {
                    var file = header.substr(header.indexOf('filename=') + 'filename='.length, header.length);
                    document.location = '/files/download/file/' + Base64.encode(file);
                }
                //relHistory.pop();
                //if (relHistory.length >= 1) {
                //    var url = relHistory[relHistory.length - 1];
                //    ajaxLoad(url, '#content');
                //}
                historyBack();
                //$(target).html(data);
            },
            dataType: "html",
        });
        event.preventDefault();
        event.stopPropagation();
        return false;
    });

    $(document).on('click', '.navbar-form .btn-success', function(event) {
        $('.navbar-form').trigger('submit');
    });

    $(document).on('keyup', 'input:not([autocomplete=off])', function(event) {
        //console.log(event.target);
        if (event.which == 13) {
            $(event.target).parents('form').trigger('submit');
            return false;    //<---- Add this line
        }
    });

    $(document).on('click', '.btn-success', function(event) {
        //relHistory = jQuery.unique(relHistory);
        //console.log(relHistory);
        //relHistory.pop();
        if ($(this).parents('.modal-body').length) {
            $(this).parents('.modal').modal('hide');
        }
        //if (relHistory.length >= 1) {
        //    var url = relHistory[relHistory.length - 1];
        //    ajaxLoad(url, '#content');
        //}
        historyBack();
        event.preventDefault();
        event.stopPropagation();
        return false;
    });

    $(document).on('click', '.btn#cancel', function(event) {
        //relHistory = jQuery.unique(relHistory);
        //console.log(relHistory);
        //relHistory.pop();
        if ($(this).parents('.modal-body').length) {
            $(this).parents('.modal').modal('hide');
            return false;
        }
        //if (relHistory.length >= 1) {
        //    var url = relHistory[relHistory.length - 1];
        //    ajaxLoad(url, '#content');
        //}
        historyBack();
        event.preventDefault();
        event.stopPropagation();
        return false;
    });

    $(document).on('click', '.remove-from-basket', function(event) {
        var url = $(this).attr('href');
        //var element = $(this).parent('li');
        var target = $('.fa-shopping-cart').parents('.dropdown-toggle').next('.dropdown-menu');
        $(target).html('<img src="/css/images/ajax-loader.gif">');
        $.ajax({
            type: "POST",
            url: url,
            enctype: 'multipart/form-data',
            data: $(this).serialize(true),
            success: function(data, status, xhr) {
                //element.next('.divider').remove();
                //element.remove();
                checkBasket();
            },
            dataType: "html",
        });
        event.preventDefault();
        event.stopPropagation();
        return false;
    });

    $(document).on('click', '.product-return', function(event) {
        var url = $(this).attr('href');
        //var element = $(this).parent('li');
        var target = $('#content');
        var multi = $(this).hasClass('multi');
        if (multi) {
            var elements = $(this).parents('.table').find('[type=checkbox]:checked');
            if (!elements.length) {
                return false;
            }
            for (var i = 0; i < elements.length; i++) {
                if (!$(elements[i]).val() || !$(elements[i]).attr('name')) {
                    continue;
                }
                url += '/' + $(elements[i]).attr('name') + '/' + encodeURI($(elements[i]).val());
            }
        }
        $(target).html('<img src="/css/images/ajax-loader.gif">');
        $.ajax({
            type: "POST",
            url: url,
            enctype: 'multipart/form-data',
            data: $(this).serialize(true),
            success: function(data, status, xhr) {
                //element.next('.divider').remove();
                //element.remove();
                //checkBasket();
                //var url = relHistory[relHistory.length - 1];
                //ajaxLoad(url, '#content');
                historyBack();
            },
            dataType: "html",
        });
        event.preventDefault();
        event.stopPropagation();
        return false;
    });

    $(document).ajaxSuccess(function(event, request, settings) {
        //console.log(request,request.getAllResponseHeaders());
        if (request.getResponseHeader('Requires-Auth') === '0') {
            window.location = '/';
        }
        //getBasket();
        //$('.chosen-select').chosen({allow_single_deselect: true});
    });

    $('.dropdown').removeClass('active');
    $('.dropdown').find('.active').removeClass('active');
    checkBasket();

    /*$(document).ajaxSend(function(event, request, settings) {
     $('#content').html('');
     $('#loading-indicator').show();
     });*/

    /*$(document).ajaxComplete(function(event, request, settings) {
     
     
     });*/

});

(function worker() {
    checkBasket();
})();

function checkBasket() {
    if (!$('.fa-shopping-cart').length) {
        return;
    }
    $.get('/warehouse/orders/products-check/format/json', function(data) {
        // Now that we've completed the request schedule the next one.
        if (data.itemCount && data.itemCount != 0) {
            $('.fa-shopping-cart').parents('.dropdown-toggle').addClass('active');
            if ($('.fa-shopping-cart').html() != data.itemCount) {
                getBasket();
            }
            if (!$('#basket').is(':visible')) {
                $('.fa-shopping-cart').trigger('click');
            }
        } else {
            $('.fa-shopping-cart').parents('.dropdown-toggle').removeClass('active');
            $('.fa-shopping-cart').parents('.dropdown-toggle').next('.dropdown-menu').html('<li><a href="#"><div><i class="fa fa-fw"></i> Brak produktów w koszyku</div></a></li>');
        }
        $('.fa-shopping-cart').html(data.itemCount);
        //setTimeout(worker, 5000);
    });
}

function getBasket() {
    var url = '/warehouse/orders/basket/format/html';
    var target = $('.fa-shopping-cart').parents('.dropdown-toggle').next('.dropdown-menu');
    $(target).html('<img src="/css/images/ajax-loader.gif">');
    target.load(url, function(response, status, xhr) {
        var width = window.innerWidth * 0.5;
        $('#basket').css("max-width", width + 'px').css("width", width + 'px');
    });
}

function historyBack() {
    var link = $('.nav.navbar-nav.navbar-left').find('li.active:not(.dropdown)').find('a');
    do {
        url = relHistory.pop();
        if (!link.length)
            continue;
        params = link.attr('href').split('/').filter(Boolean).slice(0, 3);
        refUrl = params.join('/');
        if (url.indexOf(refUrl) != -1) {
            break;
        }
    } while (relHistory.length >= 1);
    ajaxLoad(url, '#content');
}

$.ajaxSetup({
    //timeout: 5000,
    error: function(event, request, settings) {
        //alert(event.statusText);
    }
});

function ajaxLoad(url, target) {
    $(target).html('<img src="/css/images/ajax-loader.gif">');
    if (url == '/') {
        url += 'index/index';
    }
    if (url.indexOf('format') == -1) {
        url += '/format/html';
    }
    relHistory.push(url);
    $(target).load(url, function(response, status, xhr) {
        checkBasket();
    });
    //event.preventDefault();
    //event.stopPropagation();
    return false;
}

$(function() {
    var keyStop = {
        8: ":not(input:text, textarea, input:file, input:password)", // stop backspace = back
        13: "input:text, input:password", // stop enter = submit 

        end: null
    };
    $(document).bind("keydown", function(event) {
        var selector = keyStop[event.which];

        if (selector !== undefined && $(event.target).is(selector)) {
            event.cancelBubble = true;
            event.returnValue = false;
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
        return true;
    });
});

function stopSubmitOnEnter(e) {
    var eve = e || window.event;
    var keycode = eve.keyCode || eve.which || eve.charCode;

    if (keycode == 13) {
        eve.cancelBubble = true;
        eve.returnValue = false;

        if (eve.stopPropagation) {
            eve.stopPropagation();
            eve.preventDefault();
        }

        return false;
    }
}

var Base64 = {
// private property
    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
// public method for encoding
    encode: function(input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length) {

            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output +
                    Base64._keyStr.charAt(enc1) + Base64._keyStr.charAt(enc2) +
                    Base64._keyStr.charAt(enc3) + Base64._keyStr.charAt(enc4);

        }

        return output;
    },
// public method for decoding
    decode: function(input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = Base64._keyStr.indexOf(input.charAt(i++));
            enc2 = Base64._keyStr.indexOf(input.charAt(i++));
            enc3 = Base64._keyStr.indexOf(input.charAt(i++));
            enc4 = Base64._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

        }

        output = Base64._utf8_decode(output);

        return output;

    },
// private method for UTF-8 encoding
    _utf8_encode: function(string) {
        string = string.replace(/\r\n/g, "\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if ((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },
// private method for UTF-8 decoding
    _utf8_decode: function(utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while (i < utftext.length) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if ((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i + 1);
                c3 = utftext.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }
        return string;
    }
}

/* Polish initialisation for the jQuery UI date picker plugin. */
/* Written by Jacek Wysocki (jacek.wysocki@gmail.com). */
jQuery(function($){
        $.datepicker.regional['pl'] = {
                closeText: 'Zamknij',
                prevText: '&#x3c;Poprzedni',
                nextText: 'Następny&#x3e;',
                currentText: 'Dziś',
                monthNames: ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec',
                'Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
                monthNamesShort: ['Sty','Lu','Mar','Kw','Maj','Cze',
                'Lip','Sie','Wrz','Pa','Lis','Gru'],
                dayNames: ['Niedziela','Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota'],
                dayNamesShort: ['Nie','Pn','Wt','Śr','Czw','Pt','So'],
                dayNamesMin: ['N','Pn','Wt','Śr','Cz','Pt','So'],
                weekHeader: 'Tydz',
                dateFormat: 'dd.mm.yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['pl']);
});