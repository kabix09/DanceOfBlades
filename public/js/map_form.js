$(document).ready(function() {

    // 1. dynamic form - modify map terrain select options based on map area
    var $areaSelect = $('.js-form-map-area').find('select');
    var $specificLocationTarget = $('.js-map-terrain-specific-target');

    const ajaxMapSpecificTerrainFunction = (data) => {
        $.ajax({
            url: $areaSelect.data('map-area-url'),
            data: {
                area: data
            },
            success: function (html) {
                if (!html) {
                    $specificLocationTarget.find('select').remove();
                    return;
                }

                $specificLocationTarget
                    .html(html)
                    .removeClass('d-none')
            }
        });
    }

        // form beginning state - e.g. after page refresh when `specificLocationTarget` is not selected otherwise it is edit form and refresh will clear select terrain input
    if($areaSelect.val().length !== 0 && !$specificLocationTarget.find('select').val()) {
        ajaxMapSpecificTerrainFunction($areaSelect.val());
    }

        // action after change - update form
    $areaSelect.on('change', function(e) {
        console.log('change');
        ajaxMapSpecificTerrainFunction(e.target.options[e.target.selectedIndex].value); // .val())
    });

    // 2. dynamic form - modify map climate influence options depending on whether the climate has an influence
    var $climateInfluence = $('.js-map-form-influence');
    var $specificClimateInfluenceTarget = $('.js-map-climate-influence-select-target');

        // form beginning state - e.g. after page refresh
    const ajaxClimateInfluenceFunction = () => {
        $.ajax({
            url: $climateInfluence.data('map-climate-influence-url'),
            data: {
                climateInfluence: $climateInfluence.is(":checked")
            },
            success: function (html) {
                if (!html) {
                    $specificClimateInfluenceTarget.find('.form-group').remove();
                    $specificClimateInfluenceTarget.addClass('d-none');

                    return;
                }

                $specificClimateInfluenceTarget
                    .html(html)
                    .removeClass('d-none')
            }
        });
    }

        // action after change - update form
    if($climateInfluence.is(":checked")) {
        //ajaxClimateInfluenceFunction()
    }else {
        $specificClimateInfluenceTarget.addClass('d-none');
    }

    $climateInfluence.on('change', function(e) {
        ajaxClimateInfluenceFunction()
    });
});