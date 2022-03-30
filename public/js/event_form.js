var $collectionHolder;

var $addNewBossBtn = $('<button id="addBoss" type="button" class="btn btn-success btn-block">Add new</button>');
var $modalForm = $(`form[name="select_boss_form"]`);

$(document).ready(function() {
    // get the collection
    $collectionHolder = $('#bossEvent_list');

    $collectionHolder.data('index', $collectionHolder.find('.row').length);

    $collectionHolder.parent().find('#addBossButtonBox').append($addNewBossBtn);

                //console.log($collectionHolder.data('modal-boss-url'))

    // add new item to the collection
    $addNewBossBtn.click(function (e) {
        e.preventDefault();

        // create new boss form and append it to the collectionHolder
        addNewBossForm();
    })

    // add 'remove card button' to items && update 'select boss button' click event
    $collectionHolder.find('.row').each(function (index) {
        addSelectButton($(this), index);
        addRemoveButton($(this));
    })
})

function addNewBossForm() {
    // get the index
    var index = $collectionHolder.data('index');

    // get boss list card template
    // var newForm = $collectionHolder.children().first()[0].outerHTML;

    // create new form
    var newForm;

    $.ajax({
        url: $collectionHolder.data('boss-collection-card'),
        data: {},
        success: function (html) {
            newForm = html
            if (!html) {
                return;
            }

            // fix collection element indexing
            //newForm = newForm.replace(/\[0]/g, `[${index}]`);
            //newForm = newForm.replace(/_0_/g, `_${index}_`);

            // fix collection element indexing
            newForm = newForm.replace(/event_boss_form_/g, `event_form_boss_${index}_`);
            newForm = newForm.replace(/event_boss_form\[/g, `event_form[boss][${index}][`);

            $collectionHolder.data('index', index+1);

            newForm = $(newForm);

            addSelectButton(newForm, index);
            // add remove button
            addRemoveButton(newForm);

            // append row before 'add button'; 'parent' to skip 'add button' div wrapper
            $collectionHolder.append(newForm);
        }
    });


/*
    var $cardText = $('<p class="card-text"></p>').append(newForm);
    var $cardBody = $('<div class="card-body"></div>').append($cardText);
    var $card = $('<div class="card"><h5 class="card-header">Boss</h5></div>').append($cardBody);
    var $column = $('<div class="col-10 pe-0"></div>').append($card);
    var $row = $('<div class="row mt-2" style="width: 97%"></div>').append($column);
*/


}

// remove button
function addRemoveButton($row) {
    // create remove button
    var $removeButton = $('<button id="removeBoss" type="button" class="btn btn-danger btn-block">&#10008;</button>');

    var $removeButtonColumn = $('<div id="removeButtonBox" class="col-2 d-flex flex-column align-items-center justify-content-center">').append($removeButton)

    $removeButton.click(function (e) {
        $(e.target).parents('.row').slideUp(1000, function () {
            $(this).remove();
        });
    })

    $row.append($removeButtonColumn);
}

function addSelectButton($row, index) {
    // create remove button
    var $selectBossButton = $row.find(`#event_form_boss_${index}_select`);
    var $selectBossInput = $row.find(`#event_form_boss_${index}_boss`);

    var $createBossButton = $row.find(`#event_form_boss_${index}_create`);

    // rewrite using $(this) - to prevent ...
    $selectBossButton.click(function (e) {
        e.preventDefault();

        $('#selectBossModal').data('parent-id', `event_form_boss_${index}_boss`);


        //$('#selectBossModal').find('#select_boss_form_select').val($selectBossInput.val()).change();
        applyModalForm($selectBossInput);
    })

    $createBossButton.click(function (e) {
        e.preventDefault();

        $('#createBossModal').data('parent-id', `event_form_boss_${index}_boss`);
    })
}

function applyModalForm($selectBossInput) {

    var $selectFormButton = $(`#addSelectedBossModelFromModal`);

    $selectFormButton.on('click',function(e){
        e.preventDefault();

        $(`#` + $('#selectBossModal').data('parent-id')).val($modalForm.find(`#select_boss_form_select`).val());


        //console.log($selectBossInput);
        // hide modal
        $('#selectBossModal').modal('hide');
    });
}
