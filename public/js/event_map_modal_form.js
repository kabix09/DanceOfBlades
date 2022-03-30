
$(document).ready(function () {

    $bossForm = $('form[name="map_form"]');

    $bossForm.on('submit', function(event) {
        event.preventDefault();

        //console.log(new FormData($('form[name*="boss_form"]')[0]))    // https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/formdata_event
        var serializedData = $(this).serializeArray();   // 'form[name*="boss_form"]'
        // console.log($('form').serialize())
        //console.log(new URLSearchParams(new FormData($('form[name*="boss_form"]')[0])));
        var data = new FormData($('form[name="map_form"]')[0]);
        //console.log(event.target.formData());
        //serializedData.map((object) => {data.append(object.name, object.value); })
        $.ajax({
            url: '/map/create-from-data',
            //type: 'POST',
            data: new URLSearchParams(data),
            processData: false,// to prevent 'append' error

            //headers: 'application/x-www-form-urlencoded',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            success: function (uuid) {
                if (!uuid) {
                    return;
                }

                // send this to create boss collection element form input
                $(`#` + $('#createMapModal').data('parent-id')).val(uuid);
            },
            error: function(data) {
                console.log(data); //or whatever
            }
        });

        $('#createMapModal').modal('hide');
    })

})
