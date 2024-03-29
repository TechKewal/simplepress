/*global tinymce, jQuery */

;(function (tinymce, $) {
    'use strict';

    var AutoComplete = function (ed, options) {
        this.editor = ed;

        this.options = $.extend({}, {
            source: [],
            delay: 500,
            queryBy: 'name',
            items: 10
        }, options);

        this.matcher = this.options.matcher || this.matcher;
        this.renderDropdown = this.options.renderDropdown || this.renderDropdown;
        this.render = this.options.render || this.render;
        this.insert = this.options.insert || this.insert;
        this.highlighter = this.options.highlighter || this.highlighter;

        this.query = '';
        this.hasFocus = true;

        this.renderInput();

        this.bindEvents();
    };

    AutoComplete.prototype = {

        constructor: AutoComplete,

        renderInput: function () {
            var rawHtml =  '<span id="autocomplete">' +
                                '<span id="autocomplete-delimiter">' + this.options.delimiter + '</span>' +
                                '<span id="autocomplete-searchtext"><span class="dummy">\uFEFF</span></span>' +
                            '</span>';

            this.editor.execCommand('mceInsertContent', false, rawHtml);
            this.editor.focus();
            this.editor.selection.select(this.editor.selection.dom.select('span#autocomplete-searchtext span')[0]);
            this.editor.selection.collapse(0);
        },

        bindEvents: function () {
            this.editor.on('keyup', this.editorKeyUpProxy = $.proxy(this.rteKeyUp, this));
            this.editor.on('keydown', this.editorKeyDownProxy = $.proxy(this.rteKeyDown, this));
            this.editor.on('click', this.editorClickProxy = $.proxy(this.rteClicked, this));

            //keydown event has to be called first.
            var keyDownEvent = this.editor.__bindings.keydown.pop();
            this.editor.__bindings.keydown.unshift(keyDownEvent);

            $(this.editor.getWin()).on('scroll', this.rteScroll = $.proxy(function () { this.cleanUp(true); }, this));
            $('body').on('click', this.bodyClickProxy = $.proxy(this.rteLostFocus, this));
        },

        unbindEvents: function () {
            this.editor.off('keyup', this.editorKeyUpProxy);
            this.editor.off('keydown', this.editorKeyDownProxy);
            this.editor.on('click', this.editorClickProxy);
            $(this.editor.getWin()).off('scroll', this.rteScroll);
            $('body').off('click', this.bodyClickProxy);
        },

        rteKeyUp: function (e) {
            switch (e.which || e.keyCode) {
            //DOWN ARROW
            case 40:
            //UP ARROW
            case 38:
            //SHIFT
            case 16:
            //CTRL
            case 17:
            //ALT
            case 18:
                break;

            //BACKSPACE
            case 8:
                if (this.query === '') {
                    this.cleanUp(true);
                } else {
                    this.lookup();
                }
                break;

            //TAB
            case 9:
            //ENTER
            case 13:
                var item = (this.$dropdown !== undefined) ? this.$dropdown.find('li.active') : [];
                if (item.length) {
                    this.select(item.data());
                    this.cleanUp(false);
                } else {
                    this.cleanUp(true);
                }
                break;

            //ESC
            case 27:
                this.cleanUp(true);
                break;

            default:
                this.lookup();
            }
        },

        rteKeyDown: function (e) {
            switch (e.which || e.keyCode) {
             //TAB
            case 9:
            //ENTER
            case 13:
            //ESC
            case 27:
                e.preventDefault();
                break;

            //UP ARROW
            case 38:
                e.preventDefault();
                if (this.$dropdown !== undefined) {
                    this.highlightPreviousResult();
                }
                break;
            //DOWN ARROW
            case 40:
                e.preventDefault();
                if (this.$dropdown !== undefined) {
                    this.highlightNextResult();
                }
                break;
            }

            e.stopPropagation();
        },

        rteClicked: function (e) {
            var $target = $(e.target);

            if (this.hasFocus && $target.parent().attr('id') !== 'autocomplete-searchtext') {
                this.cleanUp(true);
            }
        },

        rteLostFocus: function (ed, e) {
            if (this.hasFocus) {
                this.cleanUp(true);
            }
        },

        lookup: function () {
            this.query = $.trim($(this.editor.getBody()).find("#autocomplete-searchtext").text()).replace('\ufeff', '');

            if (this.$dropdown === undefined) {
                this.show();
            }

            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout($.proxy(function () {
                // Added delimiter parameter as last argument for backwards compatibility.
                var items = $.isFunction(this.options.source) ? this.options.source(this.query, $.proxy(this.process, this), this.options.delimiter) : this.options.source;
                if (items) {
                    this.process(items);
                }
            }, this), this.options.delay);
        },

        matcher: function (item) {
            return ~item[this.options.queryBy].toLowerCase().indexOf(this.query.toLowerCase());
        },

        sorter: function (items) {
            var beginswith = [],
                caseSensitive = [],
                caseInsensitive = [],
                item;

            while ((item = items.shift()) !== undefined) {
                if (!item[this.options.queryBy].toLowerCase().indexOf(this.query.toLowerCase())) {
                    beginswith.push(item);
                } else if (~item[this.options.queryBy].indexOf(this.query)) {
                    caseSensitive.push(item);
                } else {
                    caseInsensitive.push(item);
                }
            }

            return beginswith.concat(caseSensitive, caseInsensitive);
        },

        highlighter: function (text) {
            return text.replace(new RegExp('(' + this.query + ')', 'ig'), function ($1, match) {
                return '<strong>' + match + '</strong>';
            });
        },

        show: function () {
            var rtePosition = $(this.editor.getContainer()).offset(),
                contentAreaPosition = $(this.editor.getContentAreaContainer()).position(),
                nodePosition = $(this.editor.dom.select('span#autocomplete')).position(),
                top = rtePosition.top + contentAreaPosition.top + nodePosition.top + $(this.editor.selection.getNode()).innerHeight() - $(this.editor.getDoc()).scrollTop() + 5,
                left = rtePosition.left + contentAreaPosition.left + nodePosition.left;

            this.$dropdown = $(this.renderDropdown())
                                .css({ 'top': top, 'left': left });

            $('body').append(this.$dropdown);

            this.$dropdown.on('click', $.proxy(this.autoCompleteClick, this));
        },

        process: function (data) {
            if (!this.hasFocus) {
                return;
            }
/*
            var _this = this,
                result = [],
                items = $.grep(data, function (item) {
                    return _this.matcher(item);
                });

            items = _this.sorter(items);

            items = items.slice(0, this.options.items);

            $.each(items, function (i, item) {
                var $element = $(_this.render(item));

                $element.html($element.html().replace($element.text(), _this.highlighter($element.text())));

                $.each(items[i], function (key, val) {
                    $element.attr('data-' + key, val);
                });

                result.push($element[0].outerHTML);
            });
*/
/******** begin my new stuff */
            var _this = this;
            var result = [];
			var options = [];
			var display = [];

			if (data.ok && data.DATA) {
    			for (var i in data.DATA) {
    				if (data.DATA[i].display_name) {
    					options.push(data.DATA[i].display_name);
    					display.push(data.DATA[i].user_nicename);
                    }
    			}

    			for (var i in options) {
                    var $element = $(_this.render(options[i]));
                    $element.html($element.html().replace($element.text(), _this.highlighter($element.text())));
                    $element.attr('data-name', options[i]);
                    $element.attr('data-nice', display[i]);
                    result.push($element[0].outerHTML);
                }
/******** end my new stuff */

                if (result.length) {
                    this.$dropdown.html(result.join('')).show();
                } else {
                    this.$dropdown.hide();
                }
            }  /******* added for if DATA check */
        },

        renderDropdown: function () {
            return '<ul class="rte-autocomplete dropdown-menu"><li class="loading"></li></ul>';
        },

        render: function (item) {
/******** changed item.name to item in return */
            return '<li>' +
                        '<a href="javascript:;"><span>' + item + '</span></a>' +
                    '</li>';
        },

        autoCompleteClick: function (e) {
            var item = $(e.target).closest('li').data();
            if (!$.isEmptyObject(item)) {
                this.select(item);
                this.cleanUp(false);
            }
            e.stopPropagation();
            e.preventDefault();
        },

        highlightPreviousResult: function () {
            var currentIndex = this.$dropdown.find('li.active').index(),
                index = (currentIndex === 0) ? this.$dropdown.find('li').length - 1 : --currentIndex;

            this.$dropdown.find('li').removeClass('active').eq(index).addClass('active');
        },

        highlightNextResult: function () {
            var currentIndex = this.$dropdown.find('li.active').index(),
                index = (currentIndex === this.$dropdown.find('li').length - 1) ? 0 : ++currentIndex;

            this.$dropdown.find('li').removeClass('active').eq(index).addClass('active');
        },

        select: function (item) {
            this.editor.focus();
            var selection = this.editor.dom.select('span#autocomplete')[0];
            this.editor.dom.remove(selection);
/******** changed this.insert(item) to '@' + this.insert(item) */
            this.editor.execCommand('mceInsertContent', false, '@' + this.insert(item) + '&nbsp;');
        },

        insert: function (item) {
/******** changed item.name to item.nice */
            return '<span>' + item.nice + '</span>';
        },

        cleanUp: function (rollback) {
            this.unbindEvents();
            this.hasFocus = false;

            if (this.$dropdown !== undefined) {
                this.$dropdown.remove();
                delete this.$dropdown;
            }

            if (rollback) {
                var text = this.query,
                    $selection = $(this.editor.dom.select('span#autocomplete')),
                    replacement = $('<p>' + this.options.delimiter + text + '</p>')[0].firstChild,
                    focus = $(this.editor.selection.getNode()).offset().top === ($selection.offset().top + (($selection.outerHeight() - $selection.height()) / 2));

                this.editor.dom.replace(replacement, $selection[0]);

                if (focus) {
                    this.editor.selection.select(replacement);
                    this.editor.selection.collapse();
                }
            }
        }

    };

    tinymce.create('tinymce.plugins.Mention', {

        init: function (ed, url) {

            var autoComplete,
                autoCompleteData = ed.getParam('mentions');

            // If the delimiter is undefined set default value to ['@'].
            // If the delimiter is a string value convert it to an array. (backwards compatibility)
            autoCompleteData.delimiter = (autoCompleteData.delimiter !== undefined) ? !$.isArray(autoCompleteData.delimiter) ? [autoCompleteData.delimiter] : autoCompleteData.delimiter : ['@'];

            function prevCharIsSpace() {
                var start = ed.selection.getRng(true).startOffset,
                      text = ed.selection.getRng(true).startContainer.data || '',
                      charachter = text.substr(start - 1, 1);

                return (!!$.trim(charachter).length) ? false : true;
            }

            ed.on('keypress', function (e) {
                var delimiterIndex = $.inArray(String.fromCharCode(e.which || e.keyCode), autoCompleteData.delimiter);
                if (delimiterIndex > -1 && prevCharIsSpace()) {
                    if (autoComplete === undefined || (autoComplete.hasFocus !== undefined && !autoComplete.hasFocus)) {
                        e.preventDefault();
                        // Clone options object and set the used delimiter.
                        autoComplete = new AutoComplete(ed, $.extend({}, autoCompleteData, { delimiter: autoCompleteData.delimiter[delimiterIndex] }));
                    }
                }
            });

        },

        getInfo: function () {
            return {
                longname: 'mention',
                author: 'Steven Devooght',
                version: tinymce.majorVersion + '.' + tinymce.minorVersion
            };
        }
    });

    tinymce.PluginManager.add('mention', tinymce.plugins.Mention);

})(tinymce, jQuery);