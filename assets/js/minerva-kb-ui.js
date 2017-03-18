(function($) {
    'use strict';

    var GLOBAL_DATA = window.MinervaKB;
    var ajaxUrl = GLOBAL_DATA.ajaxUrl;
    var OPTION_PREFIX = GLOBAL_DATA.optionPrefix;

    /**
     * Server
     * @param data
     * @returns {*}
     */
    function addAjaxNonce (data) {
        data['nonce_key'] = GLOBAL_DATA.nonce.nonceKey;
        data['nonce_value'] = GLOBAL_DATA.nonce.nonce;

        return data;
    }

    /**
     * Debounces function execution
     * @param func
     * @param wait
     * @param immediate
     * @returns {Function}
     */
    function debounce(func, wait, immediate) {
        var timeout;
        return function () {
            var context = this, args = arguments;
            var later = function () {
                timeout = null;
                if (!immediate) {
                    func.apply(context, args);
                }
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) {
                func.apply(context, args);
            }
        };
    }

    /**
     * Throttles function execution. Based on Ben Alman implementation
     * @param delay
     * @param noTrailing
     * @param callback
     * @param atBegin
     * @returns {wrapper}
     */
    function throttle(delay, noTrailing, callback, atBegin) {
        var timeoutId;
        var lastExec = 0;

        if (typeof noTrailing !== 'boolean') {
            atBegin = callback;
            callback = noTrailing;
            noTrailing = undefined;
        }

        function wrapper() {
            var elapsed = +new Date() - lastExec;
            var args = arguments;

            var exec = function _exec() {
                lastExec = +new Date();
                callback.apply(this, args );
            }.bind(this);

            function clear() {
                timeoutId = undefined;
            }

            if (atBegin && !timeoutId) {
                exec();
            }

            timeoutId && clearTimeout(timeoutId);

            if (atBegin === undefined && elapsed > delay) {
                exec();
            } else if (noTrailing !== true) {
                timeoutId = setTimeout(
                    atBegin ?
                        clear :
                        exec,
                    atBegin === undefined ?
                    delay - elapsed :
                        delay
                );
            }
        }

        return wrapper;
    }

    /**
     * Simple WP ajax wrapper
     * @param data
     * @param options
     * @returns {*}
     * @private
     */
    function _fetch (data, options) {
        var _defaults = {
            method: 'POST',
            url: ajaxUrl,
            dataType: 'json',
            data: {}
        };
        var _options = options ? _.extend({}, _defaults, options) : _defaults;

        return jQuery.ajax(_.extend({}, _options, {
            data: addAjaxNonce(data)
        }), options);
    }

    /**
     * Form data
     * @param data
     * @param el
     * @returns {*}
     */
    function formControlReducer(data, el) {
        var type = el.dataset.type;
        var $el = $(el);
        var $control;
        var value = null;
        var name = null;

        switch(type) {
            case 'checkbox':
            case 'toggle':
                $control = $el.find('input[type="checkbox"]');
                name = $control.attr('name');
                value = Boolean($control.attr('checked'));
                break;

            case 'input':
            case 'color':
                $control = $el.find('input[type="text"]');
                name = $control.attr('name');
                value = $control.val();
                break;

            case 'textarea':
                $control = $el.find('textarea');
                name = $control.attr('name');
                value = $control.val();
                break;

            case 'icon_select':
            case 'image_select':
            case 'layout_select':
                $control = $el.find('input[type="hidden"]');
                name = $control.attr('name');
                value = $control.val();
                break;

            case 'select':
            case 'page_select':
                $control = $el.find('select');
                name = $control.attr('name');
                value = $control.val();
                break;

            case 'css_size':
                var $size = $el.find('.fn-css-size-value');
                var $unit = $el.find('.fn-css-size-unit-value');
                name = $size.attr('name');

                value = {
                    'unit': $unit.val(),
                    'size': $size.val()
                };
                break;

            default:
                return data;
                break;
        }

        data[name.replace('mkb_option_', '')] = value;

        return data;
    }

    function getFormData($form) {
        return Array.prototype.reduce.apply($form.find('.fn-control-wrap'), [formControlReducer, {}]);
    }

    /**
     * Icon Select
     * @param $container
     */
    function setupIconSelect($container) {
        $container.on('click', '.mkb-icon-button__link', function(e) {
            e.preventDefault();

            var $btn = $(e.currentTarget);
            var $wrap = $btn.parents('.mkb-control-wrap');
            var $iconsBox = $wrap.find('.mkb-icon-select');

            $iconsBox.toggleClass('mkb-hidden');
            $btn.toggleClass('mkb-pressed');
        });

        $container.on('click', '.mkb-icon-select__item', function(e) {
            e.preventDefault();

            var $icon = $(e.currentTarget);
            var icon = $icon.data('icon');
            var $wrap = $icon.parents('.mkb-control-wrap');
            var $btn = $wrap.find('.mkb-icon-button__link');
            var $btnText = $btn.find('.mkb-icon-button__text');
            var $btnIcon = $btn.find('.mkb-icon-button__icon');
            var $iconsBox = $wrap.find('.mkb-icon-select');
            var $input = $wrap.find('.mkb-icon-hidden-input');
            var $allIcons = $wrap.find('.mkb-icon-select__item');

            $iconsBox.addClass('mkb-hidden');
            $btn.removeClass('mkb-pressed');
            $btnText.text(icon);
            $btnIcon.attr('class', 'mkb-icon-button__icon fa fa-lg ' + icon);
            $input.val(icon).trigger('change');
            $allIcons.removeClass('mkb-icon-selected');
            $icon.addClass('mkb-icon-selected');
        });
    }

    /**
     * Tabs
     * @param $container
     */
    function setupTabs($container) {
        $container.each(function(index, container) {
            var $container = $(container);

            $container.on('click', '.mkb-settings-tab a', function(e) {
                var $link = $(e.currentTarget);
                var $links = $container.find('.mkb-settings-tab a');
                var href = $link.attr('href');
                var $tabContainers = $container.find('.mkb-settings-tab__container');
                var $tabContainer = $tabContainers.filter('[id="' + href.replace("#", '') + '"]');

                $links.removeClass('active');
                $link.addClass('active');

                $tabContainers.removeClass('active');
                $tabContainer.addClass('active');

                e.preventDefault();
            });

            $container.find('.mkb-settings-tab a').eq(0).click();
            $container.find('form, .mkb-form').removeClass('mkb-loading');
        });
    }

    /**
     * ColorPickers
     * @param $container
     * @param options
     */
    function setupColorPickers($container, options) {
        $container.find('.mkb-color-picker').wpColorPicker({
            /**
             * @param {Event} event - standard jQuery event, produced by whichever
             * control was changed.
             * @param {Object} ui - standard jQuery UI object, with a color member
             * containing a Color.js object.
             */
            change: function (event, ui) {
                var element = event.target;
                var color = ui.color.toString();

                if (options && options.onChange) {
                    setTimeout(options.onChange, 300);
                }
            },

            /**
             * @param {Event} event - standard jQuery event, produced by "Clear"
             * button.
             */
            clear: function (event) {
                var element = jQuery(event.target).siblings('.wp-color-picker')[0];
                var color = '';

                if (element) {
                    if (options && options.onChange) {
                        setTimeout(options.onChange, 300);
                    }
                }
            }
        });
    }

    /**
     * Image select
     * @param e
     */
    function onImageSelectClick(e) {
        var $image = $(e.currentTarget);
        var value = $image.data('value');
        var $wrap = $image.parents('.mkb-control-wrap');
        var $images = $wrap.find('.mkb-image-select__item');
        var $input = $wrap.find('.mkb-image-hidden-input');

        $images.removeClass('mkb-image-selected');
        $image.addClass('mkb-image-selected');

        $input.val(value).trigger('change');
    }

    function setupImageSelect($container) {
        $container.on('click', '.mkb-image-select__item', onImageSelectClick);
    }

    /**
     * Topics select
     * @param $container
     */
    function setupTopicsSelect($container) {
        var $layoutSelectWrap = $container.find('.mkb-layout-select');

        $layoutSelectWrap.each(function(index, item) {
            var $layout = $(item);

            $layout.find('.mkb-layout-select__available, .mkb-layout-select__selected').sortable({
                connectWith: ".mkb-layout-select__container",
                receive: function( event, ui ) {
                    var $available = $layout.find('.mkb-layout-select__available .mkb-layout-select__item');
                    var $selected = $layout.find('.mkb-layout-select__selected .mkb-layout-select__item');
                    var $selectedContainer = $layout.find('.mkb-layout-select__selected');
                    var $wrap = $selectedContainer.parents('.mkb-control-wrap');
                    var $input = $wrap.find('.mkb-layout-hidden-input');
                    var selected = Array.prototype.reduce.apply($selected, [function(list, item) {
                        list.push(item.dataset.value);
                        return list;
                    }, []]);

                    $input.val(selected.join(',')).trigger('change');
                },
                stop: function () {
                    var $available = $layout.find('.mkb-layout-select__available .mkb-layout-select__item');
                    var $selected = $layout.find('.mkb-layout-select__selected .mkb-layout-select__item');
                    var $selectedContainer = $layout.find('.mkb-layout-select__selected');
                    var $wrap = $selectedContainer.parents('.mkb-control-wrap');
                    var $input = $wrap.find('.mkb-layout-hidden-input');
                    var selected = Array.prototype.reduce.apply($selected, [function(list, item) {
                        list.push(item.dataset.value);
                        return list;
                    }, []]);

                    $input.val(selected.join(',')).trigger('change');
                }
            });

            var $closestWrap = $layout.parents('.fn-layout-editor-section');

            if (!$closestWrap.length) {
                $closestWrap = $layout.parents('.mkb-settings-tab__container');
            }

            var $homeViewSelect = $closestWrap.length ?
                $closestWrap.find('[data-name="mkb_option_home_view"]') :
                $layout.find('[data-name="mkb_option_home_view"]');

            if ($homeViewSelect.length) {
                var $input = $homeViewSelect.find('input[type="hidden"]');

                $input.on('change', function(e) {
                    $layout.toggleClass('mkb-layout-select--box-view', e.currentTarget.value === 'box');
                });

                $input.trigger('change');
            }
        });
    }

    /**
     * Handles dependencies
     * @param $container
     */
    function setupDependencies($container) {
        var data = getFormData($container);
        var dependencies = [];
        var $deps = $container.find('.mkb-control-wrap[data-dependency]');

        function onDependencyTargetChange() {
            var data = getFormData($container);

            dependencies.forEach(function(dep) {
                var targetValue = data[dep.config.target];

                switch (dep.config.type) {
                    case 'EQ':
                        if (targetValue == dep.config.value) {
                            dep.$el.slideDown();
                        } else {
                            dep.$el.hide();
                        }
                        break;

                    case 'NEQ':
                        if (targetValue != dep.config.value) {
                            dep.$el.slideDown();
                        } else {
                            dep.$el.hide();
                        }
                        break;

                    default:
                        break;
                }
            });
        }

        $deps.each(function(index, el) {
            var $el = $(el);
            var name = $(el).data('name');
            var dependencyConfig;

            try {
                dependencyConfig = JSON.parse(
                    el.dataset.dependency
                        .replace(/^"/, '')
                        .replace(/"$/, '')
                );
            } catch (e) {
                console.log('DEV_INFO: Could not parse dependency config');
            }

            if (dependencyConfig) {
                $container
                    .find('.mkb-control-wrap[data-name="' + OPTION_PREFIX + dependencyConfig['target'] + '"]')
                    .addClass('fn-dependency-target');

                dependencies.push({
                    _id: name.replace(OPTION_PREFIX, ''),
                    $el: $el,
                    config: dependencyConfig
                });
            }
        });

        $container.on('change input', '.fn-dependency-target', onDependencyTargetChange);
        onDependencyTargetChange();
    }

    /**
     * CSS size control
     * @param $container
     */
    function setupCSSSize($container) {
        $container.on('click', '.fn-css-unit', function(e) {
            e.preventDefault();

            var $el = $(e.currentTarget);
            var $wrap = $el.parents('.mkb-css-size');
            var $input = $wrap.find('.fn-css-size-unit-value');

            $wrap.find('.fn-css-unit').removeClass('mkb-css-unit--selected');
            $el.addClass('mkb-css-unit--selected');
            $input.val($el.data('unit'));
        });
    }

    /**
     * Home page selector
     * @param $container
     */
    function setupPageSelect($container) {
        $container.on('change', '.fn-page-select-wrap .fn-control', function(e) {
            var $select = $(e.currentTarget);
            var $selected = $select.find('option:selected');
            var link = $selected.data('link');
            var $previewLink = $select.parents('.fn-page-select-wrap').find('.fn-page-select-link');

            link = link !== '' ? link : '#';

            $previewLink.attr('href', link);
            $previewLink.attr('target', link === '#' ? '_self' : '_blank');
            $previewLink.toggleClass('mkb-disabled', link === '#');
        });

        $container.find('.fn-page-select-wrap .fn-control').trigger('change');
    }

    /**
     * Wrapper for localStorage
     * @type {{set: Function, get: Function, remove: Function}}
     */
    var storage = {
        set: function(key, value) {
            localStorage.setItem(key, value);
        },

        get: function(key) {
            return localStorage.getItem(key);
        },

        remove: function (key) {
            localStorage.removeItem(key);
        }
    };

    /**
     * Exports
     *
     */
    window.MinervaUI = {
        addAjaxNonce: addAjaxNonce,
        fetch: _fetch,
        debounce: debounce,
        throttle: throttle,
        getFormData: getFormData,
        setupIconSelect: setupIconSelect,
        setupTabs: setupTabs,
        setupColorPickers: setupColorPickers,
        setupCSSSize: setupCSSSize,
        setupPageSelect: setupPageSelect,
        setupImageSelect: setupImageSelect,
        setupTopicsSelect: setupTopicsSelect,
        setupDependencies: setupDependencies,
        storage: storage
    };
})(jQuery);