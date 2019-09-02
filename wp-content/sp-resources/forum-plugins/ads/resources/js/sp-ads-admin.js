/* ---------------------------------
 Simple:Press ADS Admin Javascript
 ------------------------------------ */
/*! jquery.cookie v1.4.1 | MIT */
!function(a){"function" == typeof define && define.amd?define(["jquery"], a):"object" == typeof exports?a(require("jquery")):a(jQuery)}(function(a){function b(a){return h.raw?a:encodeURIComponent(a)}function c(a){return h.raw?a:decodeURIComponent(a)}function d(a){return b(h.json?JSON.stringify(a):String(a))}function e(a){0 === a.indexOf('"') && (a = a.slice(1, - 1).replace(/\\"/g, '"').replace(/\\\\/g, "\\")); try{return a = decodeURIComponent(a.replace(g, " ")), h.json?JSON.parse(a):a} catch (b){}}function f(b, c){var d = h.raw?b:e(b); return a.isFunction(c)?c(d):d}var g = /\+/g, h = a.cookie = function(e, g, i){if (void 0 !== g && !a.isFunction(g)){if (i = a.extend({}, h.defaults, i), "number" == typeof i.expires){var j = i.expires, k = i.expires = new Date; k.setTime( + k + 864e5 * j)}return document.cookie = [b(e), "=", d(g), i.expires?"; expires=" + i.expires.toUTCString():"", i.path?"; path=" + i.path:"", i.domain?"; domain=" + i.domain:"", i.secure?"; secure":""].join("")}for (var l = e?void 0:{}, m = document.cookie?document.cookie.split("; "):[], n = 0, o = m.length; o > n; n++){var p = m[n].split("="), q = c(p.shift()), r = p.join("="); if (e && e === q){l = f(r, g); break}e || void 0 === (r = f(r)) || (l[q] = r)}return l}; h.defaults = {}, a.removeCookie = function(b, c){return void 0 === a.cookie(b)?!1:(a.cookie(b, "", a.extend({}, c, {expires: - 1})), !a.cookie(b))}});
/**
 * 
 */
(function (spj, $, undefined) {
    // admin menu forum links block id 
    var idForums = null;
    if ($('#toplevel_page_simple-press-admin-panel-forums-spa-forums').length) {
        idForums = 'toplevel_page_simple-press-admin-panel-forums-spa-forums';
    } else if ($('#toplevel_page_simplepress-admin-panel-forums-spa-forums').length) {
        idForums = 'toplevel_page_simplepress-admin-panel-forums-spa-forums';
    }
    if (idForums) {
        // action open panel name (cookie name)
        var cName = 'sp-open-panel';
        $(document).ready(function () {
            var panelName = $.trim($.cookie(cName));
            if (panelName.length && $('#sfadminmenu').length) {
                // if exists cookie action open panel
                setTimeout(function () {
                    openPanel(panelName);
                }, 50);
            }
        });
        // handler of click "open panel"
        $(document).on('click', 'a[href=#' + cName + ']', function (e) {
            e.preventDefault();
            var $el = $(this);
            var name = $.trim($el.text());
            if ($('#sfadminmenu').length) {
                openPanel(name);
            } else {
                // set cookie action open panel
                $.cookie(cName, name);
                // goto admin main forum page
                window.location = $('#' + idForums + '>a:first').attr('href');
            }
        });
        /**
         * open pahel by name
         */
        function openPanel(name) {
            $('#sfadminmenu .ui-accordion-header').each(function () {
                if ($.trim($(this).text()) == name) {
                    // add class "current" to this submenu item only
                    $('#' + idForums + ' ul.wp-submenu li').removeClass('current').each(function () {
                        if ($.trim($(this).text()) == name) {
                            $(this).addClass('current');
                        }
                    });
                    // expand if needed current forum admin menu list 
                    if (!JSON.parse($(this).attr('aria-selected'))) {
                        $(this).click();
                    }
                    // load form
                    $(this).next('.ui-accordion-content').find('.spAccordionLoadForm').click();
                    $.removeCookie(cName);
                }
            });
        }
    }
}(window.spj = window.spj || {}, jQuery));

(function (spj, $, undefined) {
    /**
     * Load Ajax Form
     */
    spj.adsLoadAjaxForm = function (aForm, reLoad) {
        $(document).ready(function () {
            var $el = $('#sfmsgspot');
            $('#' + aForm).ajaxForm({
                target: '#sfmsgspot',
                beforeSerialize: function () {
                    if (typeof tinymce !== 'undefined') {
                        tinymce.triggerSave();
                    }
                },
                beforeSubmit: function () {
                    $el.stop(true, true).fadeOut().show();
                    $el.html(sp_platform_vars.pWait);
                },
                success: function (res) {
                    if (res.error) {
                        $el.html(res.error);
                        $el.addClass('error');
                    } else if (res.success) {
                        $el.html(res.success);
                    }
                    if (!res.error && reLoad != '') {
                        $el.fadeOut(6000);
                        $('#' + reLoad).click();
                    } else {
                        $el.show().fadeIn().fadeOut(6000, function () {
                            $el.removeClass('error');
                        });
                    }
                }
            });
        });
    };
    // handler of click [data-ads-load-form] 
    $(document).on('click', '[data-ads-load-form]', function (e) {
        var mydata = $(this).data();
        e.preventDefault();
        spj.loadForm(
                mydata.form || "plugin",
                SP_ADS.ajaxurl,
                mydata.target || "sfmaincontainer",
                mydata.img || SP_ADS.SPADMINIMAGES,
                mydata.id,
                mydata.open,
                mydata.upgrade,
                mydata.adsLoadForm, // required (php function name)
                mydata.save,
                mydata.sform,
                mydata.reload
                );
    });
    // handler action delete with confirm
    $(document).on('click', '.ads .row-actions .delete a', function (e) {
        e.preventDefault();
        var $el = $(this);
        if (!$(this).data('confirmed')) {
            if (confirm('A you sure?')) {
                $(this).data('confirmed', true);
                $(this).click();
            }
        } else {
            var $form = $('#' + $el.data('formId'));
            if ($form.length) {
                $form.attr('action', $el.attr('href')).submit();
                $(this).data('confirmed', false);
            }
        }
    });
    $('#sfmaincontainer').on('adminformloaded', function (e) {
        // ad sets form
        if ($('#ad-set-add-form').length) {
            initAdSetAddForm();
        }
        // ad set edit form
        if ($('#ad-set-edit-form').length) {
            initAdSetEditForm();
        }
        // ad set delete form
        if ($('#ad-set-delete-form').length) {
            initAdSetDeleteForm();
        }
        // ads form
        if ($('#ad-add-form').length) {
            initAdAddForm();
        }
        // ads edit form
        if ($('#ad-edit-form').length) {
            initAdEditForm();
        }
        // ads delete form
        if ($('#ad-delete-form').length) {
            initAdDeleteForm();
        }
        // reporting
        if ($('.ads-filters-reporting').length) {
            initAdReporting();
        }
    });
    /**
     * init AdSet delete form
     */
    function initAdSetDeleteForm() {
        $('[data-reload="ad-sets-list"]').attr('id', 'ads-ad-set-list-link');
        spj.adsLoadAjaxForm('ad-set-delete-form', 'ads-ad-set-list-link');
    }
    /**
     * init AdSet add new form
     */
    function initAdSetAddForm() {
        initDateRanges();
        initAdKeywords();
        initSelected();
        //spj.adsLoadAjaxForm('ad-set-add-form', 'ads-reload-ad-set-add');
        $('[data-reload="ad-sets-list"]').attr('id', 'ads-ad-set-list-link');
        spj.adsLoadAjaxForm('ad-set-add-form', 'ads-ad-set-list-link');
    }
    /**
     * init AdSet edit form
     */
    function initAdSetEditForm() {
        initDateRanges();
        initAdKeywords();
        initSelected();
        spj.adsLoadAjaxForm('ad-set-edit-form', 'ads-reload-ad-set-edit');
    }
    /**
     * init edit ad content
     */
    function initEditAdContent() {
        var $textarea = $('.wp-editor-area');
        $textarea.closest('form').find('[name="script_allowed"]').change(clearScripts);
        $textarea.closest('form').find('input[type="submit"]').click(clearScripts);
        function clearScripts() {
            if (!$textarea.closest('form').find('[name="script_allowed"]').prop('checked')) {
                var $wrap = $('<div/>');
                var isTmce = $textarea.closest('.wp-editor-wrap').hasClass('tmce-active');
                var content = isTmce ? tinymce.activeEditor.getContent() : $textarea.val();
                $wrap.html(content).find('script').remove();
                $textarea.val($wrap.html());
                isTmce && tinymce.activeEditor.setContent($textarea.val());
            }
        }
    }
    /**
     * init date ranges
     */
    function initDateRanges() {
        $('#add-range').click(function () {
            var $row = $($('#sf-ad-range-row-tmpl').html()), maxDt;
            maxDt = new Date();
            maxDt.setDate(maxDt.getDate() - 1);
            $('#dl-range-list tbody tr').each(function () {
                maxDt = Math.max.apply(null, [
                    new Date(maxDt || Date.now()),
                    new Date($('[name^=dt_from]', this).val() || Date.now()),
                    new Date($('[name^=dt_to]', this).val() || Date.now())
                ]);
            });
            maxDt = new Date(maxDt || Date.now());
            maxDt.setDate(maxDt.getDate() + 1);
            maxDt = maxDt.toISOString().substr(0, 10);
            $row.find('[name^=dt_from]').val(maxDt);
            $row.find('[name^=dt_to]').val(maxDt);
            $('#dl-range-list tbody').append($row);
        });
        $(document).on('click', '.spDeleteDates', function () {
            $(this).closest('tr').remove();
        });
    }
    /**
     * init Ad keywords
     */
    function initAdKeywords() {
        $('#sf-ads-add-keyword').click(function () {
            $('#sf-ads-keywords-container').append($('#sf-ad-keyword-tmpl').html());
        });
        $(document).on('blur', '#sf-ads-keywords-container .ads-keyword', function () {
            if (!$(this).val().length) {
                $(this).remove();
            }
        });
    }
    /**
     * init select 2
     */
    function initSelected() {
        $(".js-select2-data-ajax").each(function () {
            var $el = $(this);
            $el.select2({
                ajax: {
                    url: $el.data('url'),
                    dataType: 'json',
                    delay: 250,
                },
                minimumInputLength: 1,
                placeholder: 'Choose topics',
            });
        });
    }
    /**
     * init Ad add new form
     */
    function initAdAddForm() {
        initEditAdContent();
        spj.adsLoadAjaxForm('ad-add-form', 'ads-load-ads-list');
    }
    /**
     * init Ad edit form
     */
    function initAdEditForm() {
        initEditAdContent();
        spj.adsLoadAjaxForm('ad-edit-form', 'ads-reload-ad-edit');
    }
    /**
     * init Ad delete form
     */
    function initAdDeleteForm() {
        spj.adsLoadAjaxForm('ad-delete-form', 'ads-reload-ads-list');
    }
    /**
     * init Ad reporting
     */
    function initAdReporting() {
        function getDate(d) {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth() + 1;
            var yyyy = today.getFullYear();
            var retdat = '';
            switch (d) {
                case 'today':
                    break;
                case 'yesterday':
                    today.setDate(today.getDate() - 1);
                    dd = today.getDate();
                    mm = today.getMonth() + 1;
                    yyyy = today.getFullYear();
                    break;
                case 'week':
                    today.setDate(today.getDate() - 7);
                    dd = today.getDate();
                    mm = today.getMonth() + 1;
                    yyyy = today.getFullYear();
                    break;
                case 'month':
                    today.setDate(today.getDate() - 30);
                    dd = today.getDate();
                    mm = today.getMonth() + 1;
                    yyyy = today.getFullYear();
                    break;
                case 'year':
                    today.setDate(today.getDate() - 365);
                    dd = today.getDate();
                    mm = today.getMonth() + 1;
                    yyyy = today.getFullYear();
                    break;
                default:
                    break;
            }
            if (dd < 10) {
                dd = '0' + dd;
            }
            if (mm < 10) {
                mm = '0' + mm;
            }
            retdat = yyyy + '-' + mm + '-' + dd;
            return retdat;
        }
        $('#sp-freeday0').hide();
        $('#sp-today').on('click', function (e) {
            $('#sp-freeday1').html(getDate('today'));
            $('#sp-freeday2').html('');
            $('#sp-freeday0').hide();
            get_report(this);
        });
        $('#sp-yesterday').on('click', function (e) {
            $('#sp-freeday1').html(getDate('yesterday'));
            $('#sp-freeday2').html('');
            $('#sp-freeday0').hide();
            get_report(this);
        });
        $('#sp-week').on('click', function (e) {
            $('#sp-freeday1').html(getDate('week'));
            $('#sp-freeday2').html(getDate('today'));
            $('#sp-freeday0').show();
            get_report(this);
        });
        $('#sp-month').on('click', function (e) {
            $('#sp-freeday1').html(getDate('month'));
            $('#sp-freeday2').html(getDate('today'));
            $('#sp-freeday0').show();
            get_report(this);
        });
        $('#sp-year').on('click', function (e) {
            $('#sp-freeday1').html(getDate('year'));
            $('#sp-freeday2').html(getDate('today'));
            $('#sp-freeday0').show();
            get_report(this);
        });
        /**
         * load report for selected dates
         */
        function get_report(el) {
            $.ajax({
                url: ajaxurl,
                type: "post",
                data: {
                    action: 'ads-report-ad',
                    ad_set_id: $('.ads-filters-reporting').data('id'),
                    date_from: $('#sp-freeday1').html(),
                    date_to: ($('#sp-freeday2').html() == '' ? $('#sp-freeday1').html() : $('#sp-freeday2').html())
                },
                success: function (data) {
                    $('table.ads-list').replaceWith(data);
                    $('.ads-filters-reporting .active').removeClass('active');
                    $(el).addClass('active');
                    $('#sp-freeday').show();
                },
                error: function (e) {
                    console.log('There was an error updating the settings');
                }
            });
        }
    }

}(window.spj = window.spj || {}, jQuery));
