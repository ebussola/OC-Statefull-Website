window.ebussolaStatefullCheckMessages = (function() {

    var showAlert = function(message, type) {
        var $template = $('#ebussola-statefull-message-template');
        var delay = $('#ebussola-statefull-ajax-flash-message-script').data('delay');
        var wrapper = $('#ebussola-statefull-ajax-flash-message-script').data('wrapper');

        var html = $template
            .clone()
            .html(function (i, html) {
                html = html.replace('{{ type }}', type);
                html = html.replace('{{ message }}', message);

                return html;
            })
            .html();

        $html = $(html);
        $(wrapper).append($html);

        if (delay > 0) {
            setTimeout(function(){
                $html.fadeOut();
            }, delay);
        }
    };

    var checkMessages = function() {
        var domain = $('#ebussola-statefull-ajax-flash-message-script').data('domain');

        $.ajax({
            url: domain + '/ebussola-statefull-ajax-flash-message',
            method: 'get',
            dataType: 'json',

            success: function(data) {
                Object.keys(data).forEach(function(type) {
                    data[type].forEach(function(message) {
                        showAlert(message, type);
                    })
                });
            }
        });
    };

    $(function() {
        checkMessages();
    });

    return checkMessages;
})();