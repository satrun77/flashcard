var moofc = moofc || {};
moofc.card = {
    loadMoreContent: 'Please wait...',
    searchField: '#fc-search',
    searchButton: '#fc-search-btn',
    init: function() {
        this.search.call(this);
        return this;
    },
    show: function(card) {
        var id = card.attr('id');
        $('#' + id).remove();
        $(document.body).append(card);
        if (card) {
            card.css({
                top: $(document).scrollTop() + (($(window).height() / 2) - (card.height() / 2)) + 'px',
                position: 'absolute',
                margin: '0px 0px 0px -' + (card.outerWidth(true) / 2) + 'px'
            });
            card.draggable({cursor: 'move'});
            card.find('.close').click(function(e) {
                card.fadeOut(function() {
                    card.remove();
                });
            });
            if (typeof twttr !== 'undefined') {
                twttr.widgets.load();
            }
            card.show();
        }
        return this;
    },
    complete: function(data, form) {
        var card = $(data);
        this.show(card);
        return this;
    },
    error: function(xhr, textStatus, errorThrown) {
        return moofc.error(xhr.responseText);
    },
    search: function() {
        var me = this;
        $(this.searchButton).click(function(e) {
            e.preventDefault();
            var form = $(this).parent();
            moofc.init(moofc.waitingMessage, {
                extraClasses: 'fc-waiting-top',
                content: me.loadMoreContent
            }).show();
            $.ajax({
                url: form.attr('action'),
                type: 'html',
                method: 'get',
                data: {query: $(me.searchField).val()},
                success: function(html) {
                    me.complete.call(me, html, form);
                },
                complete: function() {
                    moofc.waitingMessage.hide();
                },
                error: function(xhr, textStatus, errorThrown) {
                    me.error.call(me, xhr, textStatus, errorThrown);
                }
            });
        });
    }
};
