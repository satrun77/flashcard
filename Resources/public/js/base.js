var moofc = {
    init: function(core, override) {
        return $.extend(core, override).init();
    },
    waitingMessage: {
        waiting: null,
        content: 'Please wait...',
        extraClasses: '',
        template: function() {
            return '<div class="well well-sm fc-waiting ' + this.extraClasses + '">'
                    + this.content
                    + '<div class="progress progress-striped active">'
                    + '<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">'
                    + '</div></div>'
                    + '</div>';
        },
        appendTo: null,
        init: function() {
            this.appendTo = $(document.body);
            return this;
        },
        show: function() {
            if (!this.waiting) {
                this.waiting = $(this.template.call(this));
                this.waiting.appendTo(this.appendTo);
            }
            this.waiting.fadeIn();
            return this;
        },
        hide: function() {
            if (this.waiting) {
                this.waiting.fadeOut();
            }
            return this;
        }
    },
    error: function(message) {
        return alert(message);
    }
};