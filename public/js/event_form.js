
$(document).ready(function() {

    prepareCollection("boss");

    prepareCollection("map");
})

function prepareCollection(collectionType) {
    var $newAddBtn = $('<button id="add' + capitalizeFirstLetter(collectionType) + '" type="button" class="btn btn-success btn-block">Add new</button>');

    let $collectionHandler = $(`#${collectionType}Event_list`);

    $collectionHandler.data('index', $collectionHandler.find('.row').length);
    $collectionHandler.parent().find('#add' + capitalizeFirstLetter(collectionType) + 'ButtonBox').append($newAddBtn);

    // add new item to the collection
    $newAddBtn.click(function (e) {
        e.preventDefault();

        // create new boss form and append it to the collectionHolder
        addNewForm($collectionHandler, collectionType);
    });

    // add 'remove card button' to items && update 'select boss button' click event
    $collectionHandler.find('.row').each(function (index) {
        addSelectButton($(this), index, collectionType);
        addRemoveButton($(this));
    });

}

function addNewForm($handler, collectionType) {
    // get the index
    var index = $handler.data('index');

    // get boss list card template
    // var newForm = $collectionHolder.children().first()[0].outerHTML;

    // create new form
    var newForm;

    $.ajax({
        url: $handler.data(`${collectionType}-collection-card`),
        data: {},
        success: function (html) {
            newForm = html
            if (!html) {
                return;
            }

            // fix collection element indexing
            if(collectionType == 'map')
            {
                newForm = newForm.replace(/event_map_form_/g, `event_form_${collectionType}_${index}_`);
                newForm = newForm.replace(/event_map_form\[/g, `event_form[${collectionType}][${index}][`);
            }else if(collectionType == 'boss')
            {
                newForm = newForm.replace(/event_boss_form_/g, `event_form_${collectionType}_${index}_`);
                newForm = newForm.replace(/event_boss_form\[/g, `event_form[${collectionType}][${index}][`);
            }

            $handler.data('index', index+1);

            newForm = $(newForm);

            addSelectButton(newForm, index, collectionType);
            // add remove button
            addRemoveButton(newForm, "remove" + capitalizeFirstLetter(collectionType));

            // append row before 'add button'; 'parent' to skip 'add button' div wrapper
            $handler.append(newForm);
        }
    });
}

// ### COLLECTION - CARDS SECTION
// remove button
function addRemoveButton($row, idName) {
    // create remove button
    var $removeButton = $('<button id="'+idName+'" type="button" class="btn btn-danger btn-block">&#10008;</button>');

    var $removeButtonColumn = $('<div id="removeButtonBox" class="col-2 d-flex flex-column align-items-center justify-content-center">').append($removeButton)

    $removeButton.click(function (e) {
        $(e.target).parents('.row').slideUp(1000, function () {
            $(this).remove();
        });
    })

    $row.append($removeButtonColumn);
}

function addSelectButton($row, index, collectionType) {

    var $selectCollectionElementButton = $row.find(`#event_form_${collectionType}_${index}_select`);
    var $createCollectionElementButton = $row.find(`#event_form_${collectionType}_${index}_create`);

    var $selectCollectionElementInput = $row.find(`#event_form_${collectionType}_${index}_${collectionType}`);   //event_form_boss_N_boss or event_form_map_N_map

    // rewrite using $(this) - to prevent ...
    $selectCollectionElementButton.click(function (e) {
        e.preventDefault();

        $(`#select` +  capitalizeFirstLetter(collectionType) + `Modal`).data('parent-id', `event_form_${collectionType}_${index}_${collectionType}`);


        //$('#selectBossModal').find('#select_boss_form_select').val($selectCollectionElementInput.val()).change();
        applyModalForm($selectCollectionElementInput, collectionType);
    })

    $createCollectionElementButton.click(function (e) {
        e.preventDefault();

        $(`#create` + capitalizeFirstLetter(collectionType) + `Modal`).data('parent-id', `event_form_${collectionType}_${index}_${collectionType}`);
    })
}

function applyModalForm($selectColletionElementInput, collectionType) {

    var $selectFormButton = $(`#addSelected` + capitalizeFirstLetter(collectionType) +`ModelFromModal`);

    $selectFormButton.on('click',function(e){
        e.preventDefault();

        $modalForm = $(`form[name="select_${collectionType}_form"]`);

        $(`#` + $(`#select` + capitalizeFirstLetter(collectionType) + `Modal`).data('parent-id')).val($modalForm.find(`#select_${collectionType}_form_select`).val());


        //console.log($selectBossInput);
        // hide modal
        $(`#select` + capitalizeFirstLetter(collectionType) + `Modal`).modal('hide');
    });
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}