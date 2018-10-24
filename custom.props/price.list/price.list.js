$(function () {

    var $table = $('#tr_PROPERTY_#PROP_ID#');

    $table.children('td').last().attr({width: '100%', colspan: 2}).prev().remove();

    $table
        .on('click', '.adm-btn-add', function () {

            $table.find('table.inner').each(function () {

                var $last = $(this).find('td').not('.add-col, .add-row').last();
                var $clone = $last.clone();

                $clone.find('input').each(function () {
                    var $input = $(this);
                    var search = '\\[VALUE\\]\\[PRICES\\]\\[([0-9]*)\\]';
                    var id = parseInt($input.attr('name').match(new RegExp(search, 'i'))[1]) + 1;
                    var new_name = $input.attr('name').replace(new RegExp(search, 'i'), '[VALUE][PRICES][' + id + ']');

                    $input.attr('name', new_name).val('');
                });

                $clone.insertAfter($last);
            })
        })
        .on('click', '.adm-btn-del', function () {

            $table.find('table.inner').each(function () {
                $(this).find('td:gt(2)').not('.add-col, .add-row').last().remove();
            });
        })
        .on('click', '.adm-btn-remove', function () {
            var $row = $(this).closest('table').closest('tr');
            $row.hide().find('.service')[0].selectedIndex = 0;
        });
});