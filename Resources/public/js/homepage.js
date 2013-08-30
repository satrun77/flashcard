var moofc = moofc || {};
moofc.homepage = {
    loadMoreMax: 30,
    loadMoreContent: 'Loading more...',
    loadMoreCompeteMessage: 'All cards are loaded!',
    loadMoreButton: null,
    width: '175px',
    height: '175px',
    _modal: null,
    init: function() {
        var me = this, container = $('#fc-cards');
        container.on('click', '.fc-cardwrap', function(e) {
            return me.click.call(me, this, e);
        });
        container.on('mouseenter', '.fc-cardwrap', function(e) {
            return me.hoverIn.call(me, this, e);
        });
        container.on('mouseleave', '.fc-cardwrap', function(e) {
            return me.hoverOut.call(me, this, e);
        });
        this.loadMore();
        this.search(container);
        return this;
    },
    click: function(el, e) {
        var wrap = $(el);
        if (this.clickEnabled() && !wrap.hasClass('open')) {
            this.expand.call(this, wrap);
        }
    },
    expand: function(el) {
        var card = el.find('.fc-card:first'), me = this;
        card.find('.share').prepend('<button type="button" class="close" aria-hidden="true">×</button>').on('click', function() {
            me.collapse.call(me, el);
        });
        this.hoverCover(card.attr('id'), el);
        var body = $(document.body);
        el.addClass('open');
        this.hoverOut.call(this, el);
        this.modal(el).fadeIn();
        var w = (el.width() / body.width()) * 100;
        el.css({
            marginLeft: ((w / 2) * -1) + '%',
            width: w + '%'
        });
    },
    collapse: function(el) {
        el.find('.close').remove();
        el.removeClass('open');
        this.modal(el).fadeOut();
        el.removeAttr('style');
    },
    hoverIn: function(el, e) {
        var wrap = $(el);
        if (this.clickEnabled() && !wrap.hasClass('open')) {
            var card = wrap.find('.fc-card:first');
            this.hoverCover(card.attr('id'), wrap).show();
        }
    },
    hoverOut: function(el, e) {
        var wrap = $(el);
        if (this.clickEnabled()) {
            var card = wrap.find('.fc-card:first');
            this.hoverCover(card.attr('id'), wrap).hide();
        }
    },
    loadMore: function() {
        var page = 1, me = this;
        this.loadMoreButton.click(function() {
            page += 1;
            moofc.init(moofc.waitingMessage, {
                extraClasses: 'fc-waiting-top',
                content: me.loadMoreContent
            }).show();
            $.ajax({
                url: FC_BASEURL + "api/cards.html?page=" + page + "&limit=" + me.loadMoreMax,
                success: function(html) {
                    $('#fc-cards').append(html);
                    $("html, body").animate({scrollTop: $(document).height()}, 1000);
                },
                complete: function() {
                    moofc.waitingMessage.hide();
                },
                error: function(xhr, status, error) {
                    me.loadMoreButton.attr('disabled', 'disabled');
                    me.loadMoreButton.text(me.loadMoreCompeteMessage);
                    setTimeout(function() {
                        me.loadMoreButton.fadeOut('slow');
                    }, 1000);
                }
            });
        });
    },
    search: function(el) {
        var me = this;
        $('#fc-search-btn').click(function(e) {
            e.preventDefault();
            var form = $(this).parent(), query = $('#fc-search').val();
            moofc.init(moofc.waitingMessage, {
                extraClasses: 'fc-waiting-top',
                content: me.loadMoreContent
            }).show();
            $.ajax({
                url: form.attr('action'),
                type: 'html',
                method: 'get',
                data: {query: query, limit: me.loadMoreMax},
                success: function(html) {
                    el.html(html);
                },
                complete: function() {
                    moofc.waitingMessage.hide();
                    if (query === '') {
                        me.loadMoreButton.fadeIn();
                    } else {
                        me.loadMoreButton.fadeOut('slow');
                    }
                },
                error: function(xhr) {
                    moofc.error(xhr.responseText);
                }
            });
        });
    },
    hoverCover: function(id, wrap) {
        if (!this.clickEnabled()) {
            return;
        }
        var coverid = id + 'cover';
        var cover = wrap.find('#' + coverid);
        if (cover.length === 0) {
            cover = $('<div class="fc-cardcover" id="' + coverid + '"></div>').appendTo(wrap);
        }
        cover.css({
            height: wrap.height()
        });
        return cover;
    },
    clickEnabled: function() {
        if ($('.container').width() <= 767) {
            return false;
        }
        return true;
    },
    modal: function(el) {
        if (this._modal === null) {
            var body = $(document.body), me = this;
            this._modal = $('<div id="fc-modal"></div>').appendTo(body);
            this._modal.css({
                width: '100%',
                height: $(document).outerHeight(true) + 'px'
            });
            this._modal.hide();
        }
        return this._modal;
    }
};
