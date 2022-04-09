$(document).ready(function() {

    // 1. dynamic form - modify map terrain select options based on map area
    var $groupSelect = $('.js-form-item-group').find('select');
    var $specificTypeTarget = $('.js-item-type-specific-target');

    const ajaxItemSpecificTypeFunction = (data) => {
        $.ajax({
            url: $groupSelect.data('item-type-url'),
            data: {
                group: data
            },
            success: function (html) {
                if (!html) {
                    $specificTypeTarget.find('select').remove();
                    return;
                }

                $specificTypeTarget
                    .html(html)
                    .removeClass('d-none')
            }
        });
    }

        // form beginning state - e.g. after page refresh when `specificGroupTarget` is not selected otherwise it is edit form and refresh will clear select terrain input
    if($groupSelect.val().length !== 0) {
        ajaxItemSpecificTypeFunction($groupSelect.val());
    }

        // action after change - update form
    $groupSelect.on('change', function(e) {
        ajaxItemSpecificTypeFunction(e.target.options[e.target.selectedIndex].value); // .val())
    });
});