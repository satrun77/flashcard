var moofc = moofc || {};
moofc.search = {
    page: 1,
    limit: 30,
    container: '#fc-cards',
    waitMessage: 'Loading more...',
    lastQuery: {},
    sendRequest: function (options) {
        options = $.extend({
            url: '',
            type: 'html',
            method: 'get',
            data: {},
            success: $.proxy(function (html) {
                this.container.html(html);
            }, this),
            complete: function () {
                moofc.waitingMessage.hide();
            },
            error: function (xhr) {
                moofc.error(xhr.responseText);
            }
        }, options);
        options.beforeSend = $.proxy(function(xhr, settings) {
            this.lastQuery = $.parseParams(settings.url);
        }, this);
        moofc.init(moofc.waitingMessage, {
            extraClasses: 'fc-waiting-top',
            content: this.waitMessage
        }).show();
        $.ajax(options);
    },
    actions: {
        loadMore: {
            completeMessage: 'All cards are loaded!',
            selector: '#fc-loadmore',
            instance: null,
            search: function (manager, el) {
                manager.page += 1;
                var data = manager.lastQuery;
                data.page = manager.page;
                data.limit = manager.limit;
                manager.sendRequest({
                    url: FC_BASEURL + 'api/cards.html',
                    data: manager.lastQuery,
                    success: function (html) {
                        manager.container.append(html);
                        $('html, body').animate({scrollTop: $(document).height()}, 1000);
                    },
                    error: $.proxy(function () {
                        el.attr('disabled', 'disabled');
                        el.text(this.completeMessage);
                        setTimeout(this.hide, 1000);
                    }, this)
                });
            },
            hide: function() {
                this.instance.fadeOut('slow');
            },
            show: function() {
                this.instance.fadeIn();
            }
        },
        keywordSearch: {
            selector: '#fc-search-btn',
            keyword: function () {
                return $('#fc-search').val()
            },
            search: function (manager, el) {
                manager.page = 1;
                var form = el.parents('form'), query = this.keyword();
                manager.sendRequest({
                    url: form.attr('action'),
                    data: {query: query, limit: manager.limit},
                    complete: function () {
                        moofc.waitingMessage.hide();
                        if (query === '') {
                            manager.actions.loadMore.show();
                        } else {
                            manager.actions.loadMore.hide();
                        }
                    }
                });
            }
        },
        categoryFilter: {
            selector: '#navbar .fc-categories .dropdown-menu a',
            search: function (manager, el) {
                manager.page = 1;
                manager.sendRequest({
                    url: el.attr('href'),
                    data: {limit: manager.limit}
                });
            }
        }
    },
    init: function () {
        this.container = $(this.container);
        $.map(this.actions, this.initEvent);
        return this;
    },
    initEvent: function (obj) {
        if (obj !== false) {
            obj.instance = $(obj.selector);
            obj.instance.on('click', function (e) {
                e.preventDefault();
                obj.search(moofc.search, $(this), e);
            });
        }
    }
};
