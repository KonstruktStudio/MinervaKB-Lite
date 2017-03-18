(function($) {

    var GLOBAL_DATA = window.MinervaKB;
    var ui = window.MinervaUI;

    var OPTION_PREFIX = GLOBAL_DATA.optionPrefix;

    var $form = $('#mkb-plugin-settings');
    var $saveBtn = $('#mkb-plugin-settings-save');
    var $resetBtn = $('#mkb-plugin-settings-reset');

    /**
     * TODO make deps globally available
     * @type {Array}
     */

    var dependencies = [];

    function onDependencyTargetChange() {
        var data = ui.getFormData($form);

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

    function initDependencies() {
        var data = ui.getFormData($form);

        var $deps = $form.find('.mkb-control-wrap[data-dependency]');

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
                $form
                    .find('.mkb-control-wrap[data-name="' + OPTION_PREFIX + dependencyConfig['target'] + '"]')
                    .addClass('fn-dependency-target');

                dependencies.push({
                    _id: name.replace(OPTION_PREFIX, ''),
                    $el: $el,
                    config: dependencyConfig
                });
            }
        });

        $form.on('change', '.fn-dependency-target', onDependencyTargetChange);

        onDependencyTargetChange();
    }

    /**
     * Displays errors to user
     * @param response
     */
    function handleErrors(response) {
        if (response.errors && response.errors.global) {
            response.errors.global.forEach(function(error) {
                toastr.error('<strong>Error ' + error.code + '</strong>: ' + error.error_message);
            });
        } else {
            // unknown error
            toastr.error('Some unknown error happened');
        }
    }

    /**
     * Settings save
     * @param e
     */
    function onSaveSettings(e) {
        e.preventDefault();

        if ($saveBtn.hasClass('mkb-disabled')) {
            return;
        }

        $saveBtn.addClass('mkb-disabled');

        ui.fetch({
            action: 'mkb_save_settings',
            settings: ui.getFormData($form)
        }).always(function(response) {
            var text = $saveBtn.text();

            if (response.status == 1) {
                // error

                $saveBtn.text('Error');
                $saveBtn.removeClass('mkb-disabled').addClass('mkb-action-danger');

                handleErrors(response);

            } else {
                // success

                $saveBtn.text('Success!');
                $saveBtn.removeClass('mkb-disabled').addClass('mkb-success');
            }

            setTimeout(function() {
                $saveBtn.text(text);
                $saveBtn.removeClass('mkb-success mkb-action-danger');
            }, 700);
        }).fail(function() {
            toastr.error('Some error happened, try to refresh page');
        });
    }

    /**
     * Settings reset
     * @param e
     */
    function onResetSettings(e) {
        e.preventDefault();

        if ($resetBtn.hasClass('mkb-disabled')) {
            return;
        }

        $resetBtn.addClass('mkb-disabled');

        ui.fetch({
            action: 'mkb_reset_settings'
        }).always(function(response) {
            var text = $resetBtn.text();

            if (response.status == 1) {
                // error

                $resetBtn.text('Error');
                $resetBtn.removeClass('mkb-disabled').addClass('mkb-action-danger');

                handleErrors(response);

            } else {
                // success

                $resetBtn.text('Success!');
                $resetBtn.removeClass('mkb-disabled').addClass('mkb-success');
            }

            setTimeout(function() {
                $resetBtn.text(text);
                $resetBtn.removeClass('mkb-success mkb-action-danger');

                window.location.reload();
            }, 700);
        });
    }

    /**
     * Sticky header for settings
     */
    function setupStickyHeader() {
        var STICKY_OFFSET = 150;
        var sticky = false;
        var $header = $('.mkb-admin-page-header');
        var $body = $('body');
        var win = window;
        var doc = document.documentElement;

        $(win).on('scroll', ui.throttle(300, function() {
            var top = win.pageYOffset || doc.scrollTop;

            if (sticky && top >= STICKY_OFFSET || !sticky && top < STICKY_OFFSET) {
                return;
            }

            sticky = !sticky;
            $header.toggleClass('mkb-fixed', sticky);
            $body.toggleClass('mkb-header-fixed', sticky);
        }));
    }

    /**
     * Displays a form
     */
    function formReady() {
        $form.removeClass('mkb-loading');
    }

    /**
     * Init
     */
    function init() {
        $saveBtn.on('click', onSaveSettings);
        $resetBtn.on('click', onResetSettings);

        ui.setupColorPickers($form);
        ui.setupIconSelect($form);
        ui.setupImageSelect($form);
        ui.setupTopicsSelect($form);
        ui.setupCSSSize($form);
        ui.setupPageSelect($form);
        ui.setupTabs($form);

        initDependencies();
        formReady();

        toastr.options.positionClass = "toast-top-right";
        toastr.options.timeOut = 10000;
        toastr.options.showDuration = 200;

        setupStickyHeader();
    }

    $(document).ready(init);
})(jQuery);