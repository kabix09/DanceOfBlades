
$(document).ready(function () {
    // add select boss button - to modal window

    // add create boss button - to modal window

    //
    $bossForm = $('form[name="boss_form"]');

    $bossForm.on('submit', function(event) {
        event.preventDefault();

        //console.log(new FormData($('form[name*="boss_form"]')[0]))    // https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/formdata_event
        var serializedData = $(this).serializeArray();   // 'form[name*="boss_form"]'
        // console.log($('form').serialize())
        //console.log(new URLSearchParams(new FormData($('form[name*="boss_form"]')[0])));
        var data = new FormData($('form[name="boss_form"]')[0]);
        //console.log(event.target.formData());
        //serializedData.map((object) => {data.append(object.name, object.value); })
        $.ajax({
            url: '/boss/create-from-data',
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
                $(`#` + $('#createBossModal').data('parent-id')).val(uuid);
            },
            error: function(data) {
                console.log(data); //or whatever
            }
        });


        $('#createBossModal').modal('hide');
    })

})
/*

document.addEventListener('DOMContentReady', (event) => {
    //the event occurred
    function processForm(e) {
        if (e.preventDefault) e.preventDefault();

        /!* do what you want with the form *!/
        console.log(e);
        // You must return false to prevent the default form behavior
        return false;
    }
     console.log(jQuery($boss).data( "events" ));

    var form = document.getElementById('boss_form');
    if (form.attachEvent) {
        form.attachEvent("submit", processForm);
    } else {
        form.addEventListener("submit", processForm);
    }

})
*/

