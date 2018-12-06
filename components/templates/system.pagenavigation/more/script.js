$(function () {

    $(document).on('click', '.load_more', function () {

        var targetContainer = $('.ajax-list');
        var button = $('.load_more');
        var url = $(this).attr('data-url');

        if (url !== undefined) {

            button.addClass('.loading');

            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'html',
                success: function (data) {

                    var elements = $(data).find('.ajax-item, .load_more');
                    button.remove();
                    targetContainer.append(elements.hide());
                    elements.fadeIn();
                }
            });
        }
    });
});