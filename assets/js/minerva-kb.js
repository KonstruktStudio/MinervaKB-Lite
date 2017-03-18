/**
 * MinervaKB main js file
 */
(function ($) {

    var GLOBAL_DATA = window.MinervaKB;

    var i18n = GLOBAL_DATA.i18n;
    var platform = GLOBAL_DATA.platform;
    var settings = GLOBAL_DATA.settings;
    var info = GLOBAL_DATA.info;

    /**
     * libs
     */
    if (!String.prototype.trim) {
        String.prototype.trim = function () {
            return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
        };
    }

    /**
     * Debounces function execution
     * TODO: make shared utils lib
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
     * TODO: make shared utils lib
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

    function addAjaxNonce(data) {
        data['nonce_key'] = GLOBAL_DATA.nonce.nonceKey;
        data['nonce_value'] = GLOBAL_DATA.nonce.nonce;

        return data;
    }

    // theme
    var ajaxUrl = GLOBAL_DATA.ajaxUrl;
    var $kbSearch = $('.kb-search__input');
    var NO_RESULTS_CLASS = 'kb-search__input-wrap--no-results';
    var HAS_CONTENT_CLASS = 'kb-search__input-wrap--has-content';
    var HAS_RESULTS_CLASS = 'kb-search__input-wrap--has-results';
    var REQUEST_CLASS = 'kb-search__input-wrap--request';
    var hasResults = false;
    var resultsCount = 0;
    var activeResult = -1;
    var ESC = 27;
    var ARROW_UP = 38;
    var ARROW_DOWN = 40;
    var $doc = $('html, body');
    var $adminBar = $('#wpadminbar');
    var adminOffset = $adminBar.length ? $adminBar.height() : 0;

    /**
     * Live search result handler
     * @param $search
     * @param response
     */
    function handleSearchResultsReceive($search, response) {
        var $wrap = $search.parents('.kb-search__input-wrap');
        var $summary = $wrap.find('.kb-search__results-summary');
        var $results = $wrap.find('.kb-search__results');
        var results = response.result;
        var searchNeedle = response.search;
        var resultsContent;
        var searchShowTopics = $search.data('show-results-topic') === 1;
        var showTopicsLabel = $search.data('topic-label');

        if (results && results.length) {
            hasResults = true;
            resultsCount = results.length;
            activeResult = -1;
            $wrap.removeClass(NO_RESULTS_CLASS).addClass(HAS_RESULTS_CLASS);
            $summary.html(results.length + ' ' + (results.length === 1 ? i18n['result'] : i18n['results']));
            resultsContent = results.reduce(function ($el, result) {
                return $el.append(
                    '<li>' +
                        '<a href="' + result.link + '">' +
                            '<span class="kb-search__result-title">' +
                            result.title +
                            '</span>' +
                            (searchShowTopics ?
                                '<span class="kb-search__result-topic">' +
                                    '<span class="kb-search__result-topic-label">' + showTopicsLabel + '</span>' +
                                    result.topics[0] +
                                '</span>' :
                                '') +
                        '</a>' +
                    '</li>'
                );
            }, $('<ul></ul>'));
            $results.html(resultsContent);
        } else {
            hasResults = false;
            resultsCount = 0;
            activeResult = -1;
            $wrap.removeClass(HAS_RESULTS_CLASS).addClass(NO_RESULTS_CLASS);
            $summary.html(i18n['no-results']);
            $results.html('');
        }
    }

    function focusInput() {
        $kbSearch.filter('[data-autofocus="1"]').focus();
    }

    function nextSearchResult() {
        var $resultItems = $('.kb-search__results li a');

        activeResult = activeResult + 1 >= resultsCount ? 0 : activeResult + 1;
        $resultItems.eq(activeResult).focus();
    }

    function prevSearchResult() {
        var $resultItems = $('.kb-search__results li a');

        activeResult = activeResult - 1 < 0 ? resultsCount - 1 : activeResult - 1;
        $resultItems.eq(activeResult).focus();
    }

    /**
     * Live search keypress handler
     * @param e
     */
    function onSearchKeyPress(e) {

        if (!$(".kb-search__input").is(":focus") && !$(".kb-search__results a").is(":focus")) {
            return; //we do not to mess with keypress unless search is in focus
        }

        switch (e.keyCode) {
            case ESC:
                focusInput();
                break;

            case ARROW_UP:
                prevSearchResult();
                break;

            case ARROW_DOWN:
                nextSearchResult();
                break;

            default:
                return;
        }

        e.preventDefault(); // prevent the default action (scroll / move caret)
    }

    /**
     * Lice search type handler
     * @param $search
     */
    function onSearchType($search) {
        var $wrap = $search.parents('.kb-search__input-wrap');
        var needle = $search.val() && $search.val().trim();
        var topics = $search.data('topic-ids');

        if (needle) {
            $wrap.addClass(HAS_CONTENT_CLASS);
        } else {
            $wrap.removeClass(HAS_CONTENT_CLASS);
        }

        if (!needle || needle.length < 3) {
            hasResults = false;
            resultsCount = 0;
            activeResult = -1;
            $wrap.removeClass(HAS_RESULTS_CLASS).removeClass(NO_RESULTS_CLASS);
            return;
        }

        $search.attr('disabled', 'disabled');
        $wrap.addClass(REQUEST_CLASS);

        jQuery.ajax({
            method: 'POST',
            url: ajaxUrl,
            dataType: 'json',
            data: addAjaxNonce({
                action: 'mkb_kb_search',
                search: needle,
                topics: topics
            })
        })
            .then(handleSearchResultsReceive.bind(this, $search))
            .always(function () {
                $search
                    .attr('disabled', false)
                    .focus();

                $wrap.removeClass(REQUEST_CLASS);
            });
    }

    /**
     * Article pageview tracking
     */
    function trackArticleView() {
        var $tracking_meta = $('.mkb-article-extra__tracking-data');

        if (!$tracking_meta.length) {
            return;
        }

        var $id = $tracking_meta.data('article-id');

        if (!$id) {
            return;
        }

        jQuery.ajax({
            method: 'POST',
            url: ajaxUrl,
            dataType: 'json',
            data: addAjaxNonce({
                action: 'mkb_article_pageview',
                id: $id
            })
        });
    }

    /**
     * Search clear
     * @param e
     */
    function handleSearchClear(e) {
        e.preventDefault();

        $(e.currentTarget)
            .parents('.kb-search__input-wrap')
            .find('.kb-search__input')
            .val('')
            .trigger('input')
            .focus();
    }

    function initSearchInputs() {
        $kbSearch.each(function (index, el) {
            var $search = $(el);

            $search.on('input', debounce(
                onSearchType.bind(this, $search), 1000, false));
        });
    }

    function init() {
        var $body = $('body');

        if ($kbSearch.length) {
            initSearchInputs();
            $body.on('keydown', onSearchKeyPress);
            $body.on('click', '.kb-search__clear', handleSearchClear);
            focusInput();
            onSearchType($kbSearch.eq(0)); // restore previous search
        }

        // article related code
        if (info.isSingle) {
            trackArticleView();
        }
    }

    // start
    $(document).ready(init);

})(jQuery);