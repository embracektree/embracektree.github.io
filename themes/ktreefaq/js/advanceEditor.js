function getSelectionText() {
    var text = "";
    if (window.getSelection) {
        text = window.getSelection().toString();
    } else if (document.selection && document.selection.type != "Control") {
        text = document.selection.createRange().text;
    }
    return text;
}

(function ($) {
    $.fn.pageEditor = function (options, callback) {

        $this = this;
        this.colloutType = {'danger': 'icon fa fa-ban', 'info': 'icon fa fa-info', 'warning': 'icon fa fa-warning', 'success': 'icon fa fa-check'};
        $this.settingsHandler = '<div class="settings-handles"><a href="javascript:void(0)" class="options-handler cursor drag-handler"><i class="fa fa-arrows"></i></a><a href="javascript:void(0)" class="options-handler delete-handler"><i class="fa fa-trash"></i></a><a href="javascript:void(0)" class="options-handler settings-handler"><i class="fa fa-cog"></i></a></div>';
        // adding drop area..
        var dropArea = $('<div/>').addClass("droppable drop-area");
        $this.wrapInner(dropArea);

        var editattributes = {
            "text": {
                "id": 'textwidget',
                "label": '<i class="fa fa-text-width fa-1"></i>',
                "display": 'HTML'
            },
            "heading": {
                "id": 'headingwget',
                "label": '<i class="fa fa-header fa-1"></i>',
                "display": "Heading"
            },
            'notifycollouts': {
                "id": 'notcolwidget',
                "label": '<i class="fa fa-info-circle fa-1"></i>',
                "display": "Collout"
            }, 'table': {
                "id": 'tabledataWid',
                "label": '<i class="fa fa-table fa-1"></i>',
                "display": "Table"
            },
            'media': {
                "id": 'mediaWidget',
                "label": '<i class="fa fa-picture-o"></i>',
                "display": "Image"
            },
            'embed': {
                "id": 'embedWidget',
                "label": '<i class="fa fa-download"></i>',
                "display": "Embed"
            }
        };
        /* Drag area Start */
        var evts = {
            stop: function (evt, ui) {
            },

            connectToSortable: '.drop-area',
            helper: function (evt, ui) {
                return $("<div style='width: 50px; height: 50px; vertical-align: middle; text-align: center;'></div>").append($(this).clone());

            },
            revert: true
        };
        var toolKit = $('<ul/>').addClass("toolKit");
        $this.append(toolKit);

        $.each(editattributes, function (k, v) {
            var ele = $('<li/>').addClass("hand-" + v['id']).attr('rel-id', v['id']).html(v["label"] + "<h5>" + v['display'] + "</h5>");
            $('.toolKit', $this).append(ele);

            ele.draggable(evts);
            ele.disableSelection();
        });

        /* Drop Area Start */
        var sortOptions = {
            placeholder: "ui-state-highlight",
            handle: '.drag-handler',
            toArray: {attribute: "data-pos"},
            receive: function (event, ui) {
                $(this).data('id', ui.item[0].id);
            }, update: function (event, ui) {
                var currentItem = ui.item;
                if (currentItem.is('.ui-draggable')) {
                    ui.item.replaceWith('<div class="wid-block wid-' + currentItem.attr('rel-id') + '">' + $this.settingsHandler + $this.createWidget(currentItem.attr('rel-id')) + '</div>');
                    ui.item.find('.settings-handler').trigger('click');
                }
            }
        };
        $('.droppable', $this).sortable(sortOptions);
        toolKit.disableSelection();
        /* Generic Methods */
        $this.th = function (data) {
            return $('<th/>').attr({'style': 'width:200px'}).append($('<div/>').attr({"contenteditable": true}).text(data));
        };
        $this.td = function (data) {
            return $('<td/>').attr({"contenteditable": true});
        };
        $this.callBackTrigger = function () {
            // make sure the callback is a function
            // brings the scope to the callback
            if (typeof callback == 'function') {
                callback.call(this);
            }
        };
        /* Generic methods End */
        $this.createWidget = function (type, additionInfo) {

            $widget = '';
            $ele = $('<div/>').addClass("widget");
            additionInformation = (typeof additionInfo == "undefined") ? {type: 'new', data: ""} : additionInfo;
            switch (type) {
                case "textwidget":

                    additionInfo = (typeof additionInfo == "undefined") ? {type: 'new', data: "Start typing here..."} : additionInfo;
                    $text = $('<div/>').addClass("contentEditor no-text elements").attr('contenteditable', true).html(additionInfo.data).attr('title', "Start typing here...");
                    $widget = $('<div/>').addClass('wid-contain').append($text).prop('outerHTML');
                    break;
                case "headingwget":
                    additionInfo = (typeof additionInfo == "undefined") ? {tag: 'h1', data: "Heading here..."} : additionInfo;

                    $h = $this.createHeadElement(additionInfo.tag, additionInfo.data).prop('outerHTML');

                    $widget = $this.headOptions(additionInfo.tag).prop('outerHTML') + ($('<div/>').addClass('wid-contain').append($h).prop('outerHTML'));
                    break;
                case "notcolwidget":
                    additionInfo = (typeof additionInfo == "undefined") ? {type: 'info', data: {'head': 'Heading!', 'text': "Collout data here..."}} : additionInfo;
                    $h = $this.createColloutElement(additionInfo.type, additionInfo.data).prop('outerHTML');

                    $widget = $this.colloutOptions(additionInfo.type).prop('outerHTML') + ($('<div/>').addClass('wid-contain').append($h).prop('outerHTML'));
                    break;
                case "tabledataWid":

                    additionInfo = additionInformation;
                    $h = $this.createTableElement(additionInfo.type, additionInfo.data).prop('outerHTML');
                    $widget = $this.tableOptions('info').prop('outerHTML') + ($('<div/>').addClass('wid-contain').append($h).prop('outerHTML'));
                    break;
                case "mediaWidget":

                    additionInfo = additionInformation;
                    $img = $this.createImageElement(additionInfo.type, additionInfo.data).prop('outerHTML');
                    $widget = $this.imageOptions('info').prop('outerHTML') + ($('<div/>').addClass('wid-contain').append($img).prop('outerHTML'));
                    break;
                case "embedWidget":

                    additionInfo = additionInformation;
                    $embed = $this.createEmbedElement(additionInfo.type, additionInfo.data).prop('outerHTML');
                    $widget = $('<div/>').addClass('wid-contain').append($embed).prop('outerHTML');
                    break;
                case "lisWidget":

                    additionInfo = additionInformation;
                    $h = $this.createTableElement(additionInfo.type, additionInfo.data).prop('outerHTML');
                    $widget = $this.listOptions('info').prop('outerHTML') + ($('<div/>').addClass('wid-contain').append($h).prop('outerHTML'));
                    break;
                case "default":

                    break;
            }
            $this.callBackTrigger();
            return $widget;
        };
        $this.createHeadElement = function (type, data) {
            var classes = (data == "Heading here...") ? 'heading-edit no-text elements' : 'heading-edit elements';

            $hE = $(document.createElement(type)).addClass(classes).attr('contenteditable', true).text(data).attr('title', "Heading here...");
            return $hE;
        };
        $this.createEmbedElement = function (type, data) {
            var classes = (type == "new") ? 'embed-wid elements' : 'embed-wid elements';
            $embed = '';

            if (type == 'new') {
                $embed = $('<div/>').addClass(classes);
                $form = $('<button/>').attr({class: "btn btn-block btn-primary btn-lg embed-upload"}).text('Add Emebed Url');
                $form.appendTo($embed);
            } else {
                $embed = data;
            }
            return $embed;
        };
        $this.createImageElement = function (type, data) {
            var classes = (type == "new") ? 'image-wid elements' : 'image-wid elements';
            $img = '';

            if (type == 'new') {
                $img = $('<div/>').addClass(classes);
                $form = $('<button/>').attr({class: "btn btn-block btn-primary btn-lg media-upload"}).text('Upload');
                $form.appendTo($img);
            } else {
                $img = data;
            }
            return $img;
        };
        $this.mediaForm = function () {
            addLoader();
            var imageWidget = '';
            $.ajax({
                url: ajaxUrl + 'questions/render-image-widget',
                type: 'post',
                async: false,
                success: function (data) {
                    removeLoader();
                    imageWidget = data;
                }
            });


            return imageWidget;
        };
        $this.createColloutElement = function (typeInfo, data) {
            var types = $this.colloutType;
            var type = (typeof typeInfo == "undefined") ? 'info' : typeInfo;
            var classes = 'alert elements alert-' + type;
            var icon = $("<i/>").addClass(types[type]);
            var span = $("<span/>").attr({'contenteditable': 'true', "class": 'heading-col', 'title': "Collout heading.."}).text(data.head);
            var body = $("<span/>").attr({'contenteditable': 'true', "class": 'heading-body', 'title': "Collout content.."}).text(data.text);
            $cH = $("<h4/>").append(icon).append(span);
            $cO = $("<div/>").addClass(classes).append($cH).append(body);
            return $cO;
        };

        $this.createTableElement = function (type, data) {
            var timstap = Date.now();
            if (type != "existing") {
                var trh = $('<tr/>').append($this.th("Column Title")).append($this.th("Column Title"));
                var trd = $('<tr/>').append($this.td(1)).append($this.td(2));
                var thead = $('<thead/>').append(trh);
                var tbody = $('<tbody/>').append(trd);
                var table = $('<table/>').attr({'id': timstap}).addClass('table elements table-bordered table-hover table-striped').append(thead).append(tbody);
                $tE = table;
            } else {
                $("td,th", data).attr("contenteditable", true);
                $tE = data;
            }
            return $tE;
        };

        $this.headOptions = function (option) {
            option = option.toLowerCase();
            $options = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
            $opt = $('<div/>');
            $('<h5/>').text('Heading Options').append($('<a/>').attr({'href': 'javascript:void(0)', 'class': 'close-pane'}).text('X')).appendTo($opt);
            $.each($options, function (k, v) {
                $item = $('<a/>').attr({'href': 'javascript:void(0)', 'data-rel': v, 'class': 'headac'}).text(v);
                if (option == v) {
                    $item.addClass('active');
                }
                $opt.append($item);

            });

            $widgetopt = $('<div/>').addClass('head-options options-panel').append($opt);
            return $widgetopt;
        };
        $this.imageOptions = function (option) {
            option = option.toLowerCase();
            $options = ['center', 'left', 'right'];
            $opt = $('<div/>');
            $('<h5/>').text('Align Option').append($('<a/>').attr({'href': 'javascript:void(0)', 'class': 'close-pane'}).text('X')).appendTo($opt);
            $.each($options, function (k, v) {
                $item = $('<a/>').attr({'href': 'javascript:void(0)', 'data-rel': v, 'class': 'imgac'}).text(v);
                if (option == v) {
                    $item.addClass('active');
                }
                $opt.append($item);

            });
            $widgetopt = $('<div/>').addClass('image-options options-panel').append($opt);

            $('<h5/>').text('Size Option').appendTo($widgetopt);
            $woptions = ['Size'];
            $wopt = $('<div/>').addClass('input-group').css({width: '200px'});
            $.each($woptions, function (k, v) {

                $span = $('<input/>').attr({'type': 'text', 'value': '100', 'class': 'form-control imgsize'});
                $wopt.append($span);
                $item = $('<span/>').addClass('input-group-addon').text('%');
                $wopt.append($item);
            });
            $widgetopt.append($wopt);
            return $widgetopt;
        };
        $this.colloutOptions = function (option) {
            option = option.toLowerCase();
            $options = $this.colloutType;
            $opt = $('<div/>');
            $.each($options, function (k, v) {
                var icon = $("<i/>").addClass(v);
                $item = $('<a/>').attr({'href': 'javascript:void(0)', 'data-rel': k}).html(icon);
                if (option == k) {
                    $item.addClass('active');
                }
                $opt.append($item);

            });

            $widgetopt = $('<div/>').addClass('collout-options options-panel').append($opt);
            return $widgetopt;
        };
        $this.tableOptions = function (option) {
            $widgetopt = $('<div/>').addClass('table-options options-panel');
            $('<h5/>').text('Table Options').append($('<a/>').attr({'href': 'javascript:void(0)', 'class': 'close-pane'}).text('X')).appendTo($widgetopt);
            $("<button/>").attr({"class": "btn btn-sm btn-default addRow"}).text("Add Row").appendTo($widgetopt);
            $("<button/>").attr({"class": "btn btn-sm btn-default addCol"}).text("Add Column").appendTo($widgetopt);
            return $widgetopt;
        };

        $this.listOptions = function (option) {
            $widgetopt = $('<div/>').addClass('table-options options-panel');
            $("<button/>").attr({"class": "btn btn-sm btn-default addRow"}).text("Add Row").appendTo($widgetopt);
            $("<button/>").attr({"class": "btn btn-sm btn-default addCol"}).text("Add Column").appendTo($widgetopt);
            return $widgetopt;
        };
        $this.getData = function () {
            $html = '';
            $('.wid-block', $this).each(function () {
                var savedHtml = $(this).clone();
                var imageStatusVariable = false;
                var embededStatus = false;
                $('*', savedHtml).removeAttr('contenteditable');
                $('*', savedHtml).removeClass('ui-resizable');
                $('.ui-resizable-handle', savedHtml).remove();
                imageStatusVariable = $this.find('.wid-contain').find('.image-wid').find('button').hasClass('media-upload');
                embededStatus = $this.find('.wid-contain').find('.embed-wid').find('button').hasClass('embed-upload');
                if (imageStatusVariable || embededStatus) {
                    $this.find('.wid-contain').find('.image-wid').remove();
                    $this.find('.wid-contain').find('.embed-wid').remove();
                }

                $html += $('.wid-contain', savedHtml).html();
            });
            return $html;
        };
        /* Binding Events to elements Where ever required like options */
        $this.on('focus', '.no-text', function () {
            if ($(this).text() == $(this).attr('title')) {
                $(this).removeClass('no-text').html('');
            }
        });
        $this.on('blur', '*[contenteditable]', function () {
            $this.callBackTrigger();
            if ($(this).text() == " " || $(this).text() == "") {
                $(this).addClass('no-text').html($(this).attr('title'));
            } else {
                $(this).removeClass('no-text');
            }
            if ($(this).is('.heading-edit')) {
                $(this).html($(this).text());
            }
        });
        $this.on('hover', '.elements.table tr', function () {
        });
        $this.on('click', '.embed-upload', function () {
            $url = prompt("Please enter 'embed' URL:");
            if ($url != null) {
                $frame = $('<iframe/>').attr({'src': $url, 'width': '100%', frameborder: '0'});
                $(this).replaceWith($frame);
            }
        });
        $this.on('click', '.media-upload', function () {
            button = $(this);
            if ($('#media-editor-modal').length == 0) {
                $('body').append($this.mediaForm());
            }

            $('#mediainput-btn').trigger('click');

            $('#media-editor-modal #mediainput').bind('change', function (event) {
                var input = document.getElementById("mediainput");
                file = input.files[0];
                if (file != undefined) {
                    $formUrl = $(this).closest('form').attr('action');
                    formData = new FormData();
                    if (!!file.type.match(/image.*/)) {
                        formData.append('file', file);

                        $.ajax({
                            url: $formUrl,
                            type: 'post',
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            success: function (data) {
                                button.trigger("mediaReceieved", [data.imagePath]);
                            }
                        });
                    } else {
                        dialogBox('Not a valid image!');
                    }
                } else {
                    dialogBox('Invalid input data ..');

                }
            });
        });
        $this.on('mediaReceieved', '.media-upload', function (e, data, $formUrl) {
            var imageUrl = data;
            var thisVariable = $(this);
            $.ajax({
                url: $formUrl,
                type: 'post',
                data: {'url': imageUrl},
                success: function (datainfo) {

                    var ajaxResponse = $.parseJSON(datainfo);
                    $image = $('<img/>').attr({'src': ajaxResponse.imagePath, 'align': 'center'}).css({'width': '100%'});
                    thisVariable.replaceWith($image);
                    $('#media-editor-modal').modal('hide');
                }
            });
        });
        $this.on('click', '.head-options a.headac', function () {
            $this.callBackTrigger();
            $target = $('.elements', $(this).closest('.wid-block'));
            $targetHtml = $target.html();
            $replace = $this.createHeadElement($(this).attr('data-rel'), $target.text());
            $target.replaceWith($replace);
            $(this).closest('.head-options').find('a').removeClass('active');
            $(this).addClass('active');
        });

        $this.on('blur', '.image-options .imgsize', function () {
            $this.callBackTrigger();
            $target = $('.elements img', $(this).closest('.wid-block'));
            if ($(this).val() != '' && $(this).val() != null) {
                $width = $(this).val() + '%';
                $target.css({'width': $width});
            } else {
                $target.css({'width': 'auto'});
            }
        });
        $this.on('click', '.image-options a.imgac', function () {
            $this.callBackTrigger();
            $target = $('.elements', $(this).closest('.wid-block'));
            $target.css({'text-align': $(this).attr('data-rel')});
            $(this).closest('.image-options').find('a').removeClass('active');
            $(this).addClass('active');
        });

        $this.on('click', '.settings-handles .delete-handler', function () {
            if (confirm("Are you sure want to delete?")) {
                $ele = $(this).closest('.wid-block');
                $ele.remove();
            }
        });
        $this.on('click', '.settings-handles .settings-handler', function () {
            $wid = $(this).closest('.wid-block');
            $ele = $(this).closest('.wid-block').find('.options-panel');
            if ($wid.hasClass('wid-textwidget') && !$('.contentEditor', $wid).hasClass('cke_editable_inline')) {
                $('.contentEditor', $wid).ckeditor();

                $('.cke_editable', $wid).addClass('text-options options-panel');

            } else if ($wid.hasClass('wid-tabledataWid') && !$('th', $wid).hasClass('ui-resizable')) {
                $('th', $wid).resizable({
                    handles: "e",
                    start: function (event, ui) {
                    }
                });
            } else {
                if ($ele.is(':visible')) {
                    $ele.hide();
                    $(this).closest('.settings-handles').removeAttr('style');
                } else {
                    $ele.show();
                    $(this).closest('.settings-handles').show();
                }
            }
        });
        $this.on('click', '.options-panel a.close-pane', function () {
            $ele = $(this).closest('.wid-block').find('.options-panel');
            $ele.hide();
            $(this).closest('wid-block').find('.settings-handles').removeAttr('style');
        });
        $this.on('click', '.collout-options a', function () {
            $this.callBackTrigger();
            $target = $('.wid-contain', $(this).closest('.wid-block'));
            $targetHtml = {
                'head': $('h4 span', $target).text(),
                'text': $('span.heading-body', $target).text()
            };
            $replace = $this.createColloutElement($(this).attr('data-rel'), $targetHtml);
            $target.html($replace);
            $(this).closest('.collout-options').find('a').removeClass('active');
            $(this).addClass('active');
        });
        $this.on('click', '.table-options .btn', function () {
            $this.callBackTrigger();
            var currentTable = $('table', $(this).closest('.wid-block'));
            if ($(this).is('.addRow')) {
                $row = currentTable.find('tr:last');
                currentTable.find('tbody').append($row.prop('outerHTML'));
                currentTable.find('tr:last').find('td').html('');
            } else {
                currentTable.find('thead').find('tr').append($this.th("Column Title"));
                currentTable.find('tbody').find('tr').append($this.td());
                $('th:not(.ui-resizable)', $wid).resizable({
                    handles: "e",
                    start: function (event, ui) {
                    }
                });
            }

        });
        $this.on('mouseover', 'table.elements tbody tr', function () {
            $handler = $(this).closest('.wid-block').find('.table-options .rowhandler');
            $handler.css({'top': $(this).position(), 'display': 'block'});
        });
        $('.elements', $this).each(function () {
            $this.callBackTrigger();
            var type = '';
            var additionalInfo = [];
            if ($(this).is('.alert')) {
                $notifyType = 'info';
                if ($(this).is('.alert-danger')) {
                    $notifyType = 'danger';
                }

                if ($(this).is('.alert-warning')) {
                    $notifyType = 'warning';
                }

                if ($(this).is('.alert-success')) {
                    $notifyType = 'success';
                }

                type = 'notcolwidget';
                additionalInfo = {type: $notifyType, data: {'head': $('.heading-col', $(this)).text(), 'text': $('.heading-body', $(this)).text()}};

            } else if ($(this).is('.heading-edit')) {
                type = 'headingwget';
                additionalInfo = {tag: $(this).prop('tagName'), data: $(this).text()};
            } else if ($(this).is('.table')) {
                type = 'tabledataWid';
                additionalInfo = {type: 'existing', data: $(this)};
            } else if ($(this).is('.contentEditor')) {
                type = 'textwidget';
                additionalInfo = {type: 'existing', data: $(this).html()};
            } else if ($(this).is('.image-wid')) {
                type = 'mediaWidget';
                additionalInfo = {type: 'existing', data: $(this)};
            } else if ($(this).is('.embed-wid')) {
                type = 'embedWidget';
                additionalInfo = {type: 'existing', data: $(this)};
            }
            $widget = '<div class="wid-block wid-' + type + '">' + $this.settingsHandler + $this.createWidget(type, additionalInfo) + '</div>';

            $elethis = $(this).replaceWith(function () {
                return $widget;
            });
        });

        return $this;
    };


}(jQuery));

$(document).ready(function () {

    var advanceEditor = $('.editor').pageEditor([], function () {
        if ($('.editable_div').text()) {
            $('.save_topic_content').prop("disabled", false);
        }

    });
    $('button.save_topic_content').click(function (e) {

        var questionId = $('.topic_id_hidden').val();
        var topicLanguage = $('.topic_language_hidden').val();
        var content = advanceEditor.getData();
        var topicStatus = $('.topic_status_change').val();
        var questionTitle = $('.editable_div').text();
        var topicTitle = $('.course-title').text();
        $(".contentSaveResponse").html('');

        $.ajax({
            type: "POST",
            url: ajaxUrl + "questions/update-question-content",
            data: {'questionId': questionId, 'content': content, 'questionTitle': questionTitle, 'topicLanguage': topicLanguage, 'topicStatus': topicStatus, 'topicTitle': topicTitle},
            beforeSend: function () {
                addLoader();
            },
            success: function (data) {
                removeLoader();
                var ajaxResponse = $.parseJSON(data);

                $('.contentSaveResponse').show();
                $('.save_topic_content').prop('disabled', 'true');

                if (ajaxResponse.status) {

                    $('div.editable_div').removeClass('editable').prop('contenteditable', false);

                    $(".contentSaveResponse").append("<div class='flash-success alert-success'> Successfully updated </div>");
                    $(".flash-success ").delay(2000).slideUp("slow", function () {
                        $(this).remove();
                    });
                    if (ajaxResponse.selectedLanguage == 'EN') {
                        window.location.href = '/topics' + '/' + ajaxResponse.topicName + '/' + ajaxResponse.message.question_id + '/' + ajaxResponse.message.slug;
                    } else {
                        $('.editable_div').text();
                        var node = $("#fancyree_w0").fancytree("getTree").getNodeByKey(topicId);
                        $(node.span.lastChild).text(topicTitle);
                    }
                }
                else {
                    $(".contentSaveResponse").append("<div class='flash-error alert-danger'> " + ajaxResponse.message + "</div>");
                    $(".flash-error ").delay(2000).slideUp("slow", function () {
                        $(this).remove();
                    });

                }
            }
        });
    });
    $('.topic_status_change').change(function () {
        $('.save_topic_content').prop("disabled", false);
    });
});
