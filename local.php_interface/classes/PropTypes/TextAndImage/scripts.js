$(function () {

    var $table = $('#tbl#PROP_ID#');
    var $tr = $table.children('tbody').children('tr');

    if ($tr.length > 1) {
        $tr.each(function(){
            var val = $(this).eq(0).find('table tr').children('td').last().find('input').val();
            if (typeof val === 'undefined') {
                $(this).hide();
            }
        });
    }

    var $btn = $('#tbl#PROP_ID#').next('[type="button"]');
    $(document).on('click', $btn, function (e) {

        if ($(e.target).is('[type="button"]')) {

            var bl = $(e.target).prev('table').children('tbody').children('tr').last();
            var $prefix = bl.find('input').first();
            //var num = bl.find('input').first().attr('name').match(/n([0-9]*)/g)[0].substr(1);
            var fileinput = bl.find('.adm-fileinput-wrapper');

            bl.find('input[type="text"]').val('');

            $.post(
                '/local/php_interface/classes/custom.prop.types/gallery/fileinput.php',
                {name: $prefix.attr('name').replace('[PREFIX]', '[IMAGE]')},
                function (data) {
                    fileinput.replaceWith(data);
                }
            );
        }
    });

    window.BX.addCustomEvent("onFileIsDeleted", function(){
        $tr.find('.shadow_img').remove();
    });
});
