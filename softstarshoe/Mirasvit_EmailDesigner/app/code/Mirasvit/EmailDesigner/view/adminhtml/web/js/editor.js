define([
    'jquery',
    'underscore',
    'codemirror/codemirror'
], function ($, _, CodeMirror) {
    'use strict';

    $.widget('mirasvit.editor', {
        options: {
            name: null
        },

        _create: function () {
            var self = this;

            self.editors = {};
            self.autoRefresh = false;

            self.$previewFrame = $('[data-role=preview]');
            self.$preview = $(self.$previewFrame[0].contentDocument || self.$previewFrame[0].contentWindow.document);
            self.$desktopView = $('[data-role=desktop-view]');
            self.$mobileView = $('[data-role=mobile-view]');
            self.$autoRefresh = $('[data-role=auto-refresh]');
            self.$refresh = $('[data-role=refresh]');
            self.$spinner = $('[data-role=spinner]');

            _.bindAll(this,
                'fitFrame',
                'updatePreview',
                'changeView'
            );

            self.$desktopView.on('click', function (e) {
                e.preventDefault();
                self.changeView('desktop');
            });

            self.$mobileView.on('click', function (e) {
                e.preventDefault();
                self.changeView('mobile');
            });

            self.$autoRefresh.on('change', function (e) {
                self.$refresh.parent().toggle();
                self.autoRefresh = self.$autoRefresh.attr('checked');
            });

            self.$refresh.on('click', function (e) {
                e.preventDefault();
                self.updatePreview();
            });

            $('.editor').each(function (index, item) {
                var editor = CodeMirror.fromTextArea(item, {
                    mode: 'htmlmixed',
                    tabMode: 'indent',
                    matchTags: true,
                    viewportMargin: Infinity,
                    tabSize: 4,
                    lineWrapping: false
                });

                var delay = 0;

                editor.on('change', function () {
                    if (self.autoRefresh) {
                        clearTimeout(delay);
                        delay = setTimeout(self.updatePreview, 300);
                    }
                });

                self.editors[$(item).attr('name')] = editor;
            });

            setInterval(function () {
                self.fitFrame();
            }, 500);

            self.updatePreview();
        },

        fitFrame: function () {
            var self = this;

            var areasHeight = $('.email-designer__editor-areas').height();
            var previewHeight = $('body', self.$preview).height();

            var max = areasHeight;
            if (previewHeight > max) {
                max = previewHeight;
            }

            self.$previewFrame.height(max);
        },

        updatePreview: function () {
            var self = this;
            var data = {};

            self.$previewFrame.css('opacity', '0.5');
            self.$spinner.show();

            _.each(self.editors, function (editor, key) {
                editor.refresh();
                editor.save();

                var $textarea = $(editor.getTextArea());
                data[$textarea.attr('data-area')] = $textarea.val();
            });

            self.abortAllAjax();

            var jqXHR = $.ajax(self.options.url, {
                type: 'POST',
                data: data,

                complete: function (response) {
                    self.$preview[0].open();
                    self.$preview[0].write(response.responseText);
                    self.$preview[0].close();

                    self.fitFrame();

                    self.$previewFrame.css('opacity', '1');
                    self.$spinner.hide();
                }
            });

            window.xhrPool.push(jqXHR);
        },

        changeView: function (mode) {
            var self = this;

            if (mode == 'desktop') {
                self.$desktopView.removeClass('tertiary').addClass('secondary');
                self.$mobileView.removeClass('primary').addClass('tertiary');
                self.$previewFrame.width('100%');
            } else {
                self.$mobileView.removeClass('tertiary').addClass('secondary');
                self.$desktopView.removeClass('primary').addClass('tertiary');
                self.$previewFrame.width('300px');
            }

            self.fitFrame();
        },

        abortAllAjax: function () {
            $(window.xhrPool).each(function (idx, jqXHR) {
                jqXHR.abort();
            });
            window.xhrPool = [];
        }
    });

    window.xhrPool = [];

    $.ajaxSetup({
        complete: function (jqXHR) {
            var index = window.xhrPool.indexOf(jqXHR);
            if (index > -1) {
                window.xhrPool.splice(index, 1);
            }
        }
    });

    return $.mirasvit.editor;
});