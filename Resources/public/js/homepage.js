var moofc = moofc || {};
moofc.homepage = {
    container: '#fc-cards',
    _modal: null,
    init: function () {
        this.container = $(this.container);
        this.container.on({
            click: this.event,
            mouseenter: this.event,
            mouseleave: this.event
        }, '.fc-cardwrap');
        return this;
    },
    mouseenter: function (card, el) {
        this.hover(card.attr('id'), el).show();
    },
    mouseleave: function (card, el) {
        this.hover(card.attr('id'), el).hide();
    },
    click: function (card, el) {
        var body = $(document.body);
        card.find('.share')
            .prepend('<button type="button" class="close" aria-hidden="true">×</button>')
            .on('click',
            $.proxy(this.close, this, el)
        );
        this.hover(card.attr('id'), el);
        el.addClass('open');
        this.mouseleave.call(this, card, el);
        this.modal(el).fadeIn();
        var w = (el.width() / body.width()) * 100;
        el.css({
            marginLeft: ((w / 2) * -1) + '%',
            width: w + '%'
        });
    },
    close: function (el) {
        el.find('.close').remove();
        el.removeClass('open');
        this.modal(el).fadeOut();
        el.removeAttr('style');
    },
    hover: function (id, wrap) {
        var cover = false, coverId;
        if (this.enabled()) {
            coverId = id + 'cover';
            cover = wrap.find('#' + coverId);
            if (cover.length === 0) {
                cover = $('<div class="fc-cardcover" id="' + coverId + '"></div>').appendTo(wrap);
            }
        }
        return cover;
    },
    event: function (e) {
        var wrap = $(e.currentTarget);
        if (moofc.homepage.enabled() && !wrap.hasClass('open')) {
            var card = wrap.find('.fc-card:first');
            moofc.homepage[e.type].call(moofc.homepage, card, wrap);
        }
    },
    enabled: function () {
        return $(window).width() > 767;
    },
    modal: function (el) {
        if (this._modal === null) {
            var body = $(document.body);
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
