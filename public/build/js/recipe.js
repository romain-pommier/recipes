




//Modification nom imput browser
$(document).ready(function() {
    $(document).on('change', '.custom-file-input', function () {
        let fileName = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
        $(this).parent('.custom-file').find('.custom-file-label').text(fileName);
    });
    //initialisation size homePage
    let heightRecipeSection = $('.best-recipe').height();
    $('.picture-best-author').height(heightRecipeSection);
})



//Ajoute d'input
$('#add-image').click(function(){
    addInputOnClick('#recipe_recipePictures')
});
$('#add-ingredient').click(function(){
    addInputOnClick('#recipe_ingredients')
});
$('#add-mealStyle').click(function(){
    addInputOnClick('#recipe_mealstyles')
});


function addInputOnClick(target){
    //récupération le numéro des futurs champs que je vais créer
    const index = +$('#widgets-counter').val();

    //récupération le prototype des entrées
    const tmpl = $(target).data('prototype').replace(/_name_/g, index);

    //Injection du code dans la div
    $(target).append(tmpl);
    //incrementation widgets-counter
    $('#widgets-counter').val(index +1 );

    //Initialisation fonction suppression
    handleDeleteButtons();

}

//Suppression boutonAddPicture
function handleDeleteButtons(){
    $('button[data-action = "delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    })
}

function updateCounter(target){
    const count = +$(target +' div.form-group').length;


    $('#widgets-counter').val(count);
}


updateCounter('#recipe_recipePictures');
updateCounter('#recipe_ingredients');
updateCounter('#recipe_mealstyles');

handleDeleteButtons();