$("form[name = 'search_recipe']").submit(function(event){
    const url = this.action;
    const keyWord = new Object();

    event.preventDefault();

    $("form[name = 'search_recipe'] input").each(function(){
        keyWord[$(this).attr("id")] = $(this).val();
    });

    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        data: {'search_recipe':keyWord},
        success: function(response) {
            console.log(response);
            $('#recipesContent').children().remove();
            response.forEach(function(item){
                displayRecipes(item);
            });


        },
        error: function(jxh,errorThrown){
            console.log('erreur ajax xhr');
            console.log(errorThrown);
        }
    });

});

function displayRecipes(data){
    let urlCoverImage = "";
    let url = '/recipes/'+data["slug"];
    if(data['coverImageName'].startsWith('http')){
        urlCoverImage = data['coverImageName'];
    }
    else{
        urlCoverImage = "/images/coverimage/"+data['coverImageName'];
    }
    let recipe ='<div class="col-md-6 col-sm-12">' +
                '<div class="card bg-light mb-3">' +
                    '<a href="'+url+'">' +
                        '<div class="card-header text-center">'
                            +data["cookingTime"]+' Min , <strong>'+data["people"]+' Personnes </strong><br>' +
                        '</div>' +
                    '</a>' +
                    '<a href="'+url+'"> ' +
                        '<img src="'+urlCoverImage+'" alt="image de la recette"style="height : 200px; width: 100%; display: block"> ' +
                    '</a> ' +
                    '<div class="card-body">' +
                        ' <h4 class="card-title">' +
                            ' <a href="'+url+'">'+data["title"]+'</a> ' +
                        '</h4> ' +
                        '<p class="card-body">'+data["description"]+'</p>' +
                        ' <a class="btn btn-primary float-right" href="'+url+'" >En savoir plus</a>' +
                    '</div>' +
                '</div>' +
            '</div>';
    $('#recipesContent').append(recipe);
}
// {% if app.user and app.user == recipe.author %}
// <a href="{{ path('recipes_edit', {'slug': recipe.slug}) }}" class="btn btn-secondary">Modifier la recette</a>
// <a href="{{ path('recipes_delete', {'slug': recipe.slug}) }}" class="btn btn-danger" onclick="return confirm(`Voulez vous supprimer la recette : {{ recipe.title }} ?`)">Supprimer</a>
//
// {% endif %}
