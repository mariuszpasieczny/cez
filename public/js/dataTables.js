$(function() {
    /*var $table = $('.scrollTable');
     $table.floatThead({
     scrollingTop: '70px',
     useAbsolutePositioning: false,
     zIndex: 1099,
     debug: true,
     //scrollContainer: function($table) {
     //    var $wrapper = $table.closest('.tableContainer');
     //    $wrapper.css({'overflow': 'auto', 'height': '600px', 'margin-top': '150px'});
     //    return $wrapper;
     //}
     });*/

    orderBy = params.orderBy ? params.orderBy.split(" ") : null;
    oTable = $('.table').dataTable({
        //"iDisplayLength": paginator.itemCountPerPage != -1 ? paginator.itemCountPerPage : null,
        //"iDisplayStart": paginator.itemCountPerPage != -1 ? (paginator.current * paginator.itemCountPerPage + 1) : null,
        "paging": false,
        "info": false,
        //"bServerSide": true,
        //"sAjaxSource": url,
        //"iDeferLoading": paginator.totalItemCount,
        "order": orderBy && orderBy[0] != 6 ? [[orderBy[0], orderBy[1].toLowerCase()]] : [],
        //"order": [[0, "asc"]],
        "scrollY": "500px",
        "scrollCollapse": true,
        //"deferRender": true,
        //"processing": true,
        //"search": {
        //    "search": typeof params != 'undefined' ? params.search : ""
        //},
        "searching": false,
        //searchDelay: 1000,
        "oLanguage": {
            "sInfo": "Pobrano _START_ do _END_ z _TOTAL_ wierszy"
        },
        "aoColumnDefs" : columnsDef
    });

    $('.table').on('order.dt', function(e, settings, len) {
        col = len[0].col;
        if (params.orderBy) {
            var ordering = params.orderBy.split(' ');
            dir = ordering[1] == 'asc' ? 'desc' : 'asc';
        } else {
            dir = len[0].dir
        }
        
        var url = relHistory[relHistory.length - 1];
        if (url.indexOf('format') == -1) {
            url += '/format/html';
        }
        if (url.indexOf('orderBy') != -1) {
            var urlParams = url.split('/');
            urlParams.splice(urlParams.indexOf('orderBy'), 2);
            url = urlParams.join('/');
        }
        url += '/orderBy/' + encodeURI(col + ' ' + dir);
        relHistory.push(url);
        $('#content').html('');
        $('#loading-indicator').show();
        $('#content').load(url, function(response, status, xhr) {
            //alert( "Load was performed." );
            $('#loading-indicator').hide();
            $('.dataTable').find('thead').find('th').removeClass('sorting_asc').removeClass('sorting_desc').addClass('sorting');
            var ordering = params.orderBy.split(' ');
            $($('.dataTable').find('thead').find('th')[ordering[0]]).removeClass('sorting').addClass('sorting_' + ordering[1]);
        });
        e.preventDefault();
        e.stopPropagation();
        return false;
    });

    jQuery.fn.dataTableExt.oApi.fnSetFilteringDelay = function(oSettings, iDelay) {
        var _that = this;

        if (iDelay === undefined) {
            iDelay = 500;
        }

        this.each(function(i) {
            $.fn.dataTableExt.iApiIndex = i;
            var
                    $this = this,
                    oTimerId = null,
                    sPreviousSearch = null,
                    anControl = $('input', _that.fnSettings().aanFeatures.f);

            anControl.unbind('keyup search input').bind('keyup', function() {
                var $$this = $this;

                if (sPreviousSearch === null || sPreviousSearch != anControl.val()) {
                    window.clearTimeout(oTimerId);
                    sPreviousSearch = anControl.val();
                    oTimerId = window.setTimeout(function() {
                        $.fn.dataTableExt.iApiIndex = i;
                        //_that.fnFilter(anControl.val());
                        //console.trace();
                        var url = relHistory[relHistory.length - 1];
                        if (url.indexOf('format') == -1) {
                            url += '/format/html';
                        }
                        if (url.indexOf('search') != -1) {
                            var urlParams = url.split('/');
                            urlParams.splice(urlParams.indexOf('search'), 2);
                            url = urlParams.join('/');
                        }
                        var data = $('.dataTables_filter').find('input').val();
                        url += '/search/' + encodeURI(data);
                        relHistory.push(url);
                        $('#content').html('');
                        $('#loading-indicator').show();
                        $('#content').load(url, function(response, status, xhr) {
                            //alert( "Load was performed." );
                            $('#loading-indicator').hide();
                        });
                    }, iDelay);
                }
            });

            return this;
        });
        return this;
    };

    /*if (oTable.length) {
        $('.dataTables_info').html(oTable.fnSettings().oLanguage.sInfo.
                replace(/_START_/g, (paginator.current - 1) * paginator.itemCountPerPage + 1).
                replace(/_END_/g, paginator.currentItemCount).
                replace(/_TOTAL_/g, paginator.totalItemCount));

        oTable.dataTable().fnSetFilteringDelay(500);
    }

    $('.table').on('length.dt', function(e, settings, len) {
        e.preventDefault();
        e.stopPropagation();
        var url = relHistory[relHistory.length - 1];
        if (url.indexOf('format') == -1) {
            url += '/format/html';
        }
        if (url.indexOf('count') != -1) {
            var urlParams = url.split('/');
            urlParams.splice(urlParams.indexOf('count'), 2);
            url = urlParams.join('/');
        }
        url += '/count/' + len;
        relHistory.push(url);
        $('#content').html('');
        $('#loading-indicator').show();
        $('#content').load(url, function(response, status, xhr) {
            //alert( "Load was performed." );
            $('#loading-indicator').hide();
        });
        return false;
    });*/

    /*$('.table').on('search.dt', function(e, settings) {
     console.log(e, settings);console.trace();
     e.preventDefault();
     e.stopPropagation();
     var url = relHistory[relHistory.length - 1];
     if (url.indexOf('format') == -1) {
     url += '/format/html';
     }
     if (url.indexOf('search') != -1) {
     var urlParams = url.split('/');
     urlParams.splice(urlParams.indexOf('search'), 2);
     url = urlParams.join('/');
     }
     var data = $('.dataTables_filter').find('input').val();console.log(data);
     url += '/search/' + encodeURI(data);
     relHistory.push(url);
     $('#content').load(url, function(response, status, xhr) {
     //alert( "Load was performed." );
     });
     return false;
     });*/

});