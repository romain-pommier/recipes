<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MealStyle;
use App\Entity\Recipe;
use App\Entity\RecipePicture;
use App\Entity\Ingredient;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\RecipeType;
use App\Form\SearchRecipeType;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use function count;
use function dump;
use function is_null;
use function json_encode;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function var_dump;


class RecipeController extends AbstractController
{


    /**
     * Permet d'afficher un formulaire d'ajout de recette
     *
     * @Route("/recipes/new", name="recipes_new")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function create ( Request $request, ObjectManager $manager){
        $recipe = new Recipe();
        $mealStyle = new MealStyle();
        $ingredient = new Ingredient();

        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach ($recipe->getRecipePictures() as $image){
                $image->setRecipe($recipe);
                $manager->persist($image);
            }
            foreach ($recipe->getIngredients() as $ingredient){
                $ingredient->addRecipe($recipe);
                $manager->persist($ingredient);
            }
            foreach ($recipe->getMealStyles() as $mealStyle){
                $mealStyle->addRecipe($recipe);
                $manager->persist($mealStyle);
            }

            $recipe->setAuthor($this->getUser());

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash('success',"La recette <strong>{$recipe->getTitle()}</strong> a bien été enregistrée !");

            return $this->redirectToRoute('recipes_show',[
                'slug' => $recipe->getSlug()
            ]);
        }

        return $this->render('recipe/new.html.twig',[
            'form' => $form->createView()
        ]);


    }


    /**
     * Permet d'afficher la rectte correspondante
     *
     * @Route("/recipes/{slug}", name="recipes_show")
     * @return Response
     */
    public function show(Recipe $recipe, ObjectManager $manager, Request $request){
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setRecipe($recipe)
                ->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre Commentaire a bien été enregistré"
            );
        }
        return $this->render('recipe/show.html.twig',[
            'recipe' => $recipe,
            'form' => $form->createView()


        ]);
    }

    /**
     * @Route("/recipes", name="recipe_index")
     */
    public function index(RecipeRepository $repo, Request $request)
    {
       $recipes = $repo -> findAll();
       $form = $this->createForm(SearchRecipeType::class);
        return $this->render('recipe/index.html.twig', [
                    'controller_name' => 'RecipeController',
                    'recipes' => $recipes,
                    'form'=> $form->createView(),
            ]);
    }

    /**
     * @Route("/recipesSearch", name="recipe_search")
     * @param RecipeRepository $repo
     * @return \Symfony\Component\HttpFoundation\Response;
     */
    public function researchRecipe(RecipeRepository $repo, Request $request){

        $data = $request->request->get('search_recipe');
        if(isset($data['search_recipe_searchText'])){
            $resultRecipes = $repo->findByWord($data['search_recipe_searchText']);
        }
        else{
            $resultRecipes = $repo->findAll();
        }

        $dataArray = [];
        foreach ($resultRecipes as $key => $recipeData){
            $dataArray[$key]['title'] = $recipeData->getTitle();
            $dataArray[$key]['slug'] = $recipeData->getSlug();
            $dataArray[$key]['coverImageName'] = $recipeData->getCoverImageName();
            $dataArray[$key]['description'] = $recipeData->getDescription();
            $dataArray[$key]['people'] = $recipeData->getPeople();
            $dataArray[$key]['cookingTime'] = $recipeData->getCookingTime();
        }
        return new JsonResponse($dataArray);
    }




    /**
     * Page edition recette
     * @Route("/recipes/{slug}/edit", name = "recipes_edit")
     * @Security("is_granted('ROLE_USER') and user === recipe.getAuthor()", message="Vous ne pouvez pas modifier cette recette")
     */
    public function edit(Recipe $recipe, Request $request, ObjectManager $manager ){

        //récupération des nom des images coverImage && recipiepicture
        $coverImagePath = $recipe->getCoverImageName();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach ($recipe->getRecipePictures() as $image){
               $image->setRecipe($recipe);
               $manager->persist($image);
            }

            foreach ($recipe->getIngredients() as $ingredient){
                $ingredient->addRecipe($recipe);
                $manager->persist($ingredient);
            }
            foreach ($recipe->getMealStyles() as $mealStyle){
                $mealStyle->addRecipe($recipe);
                $manager->persist($mealStyle);
            }

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash('success',"La recette <strong>{$recipe->getTitle()}</strong> a bien été modifiée !");

            return $this->redirectToRoute('recipes_show',[
                'slug' => $recipe->getSlug()
            ]);
        }


        return $this->render('recipe/edit.html.twig', [
            'form' => $form->createView(),
            'recipe' => $recipe,
            'coverImagePath' => $coverImagePath,
        ]);
    }

    /**
     *
     * Suppression d'une recette
     * @Route("/recipes/{slug}/delete", name="recipes_delete")
     * @Security("is_granted('ROLE_USER') and user == recipe.getAuthor()", message="Vous ne disposez pas des droits")
     *
     * @return Response
     */

    public function delete (Recipe $recipe, ObjectManager $manager){

        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash('success',
            "La recette <strong>{$recipe->getTitle()}</strong>a bien été supprimé");

        return $this->redirectToRoute("recipe_index");

    }

    /**
     * @Route("/", name="home")
     */
    public function home(RecipeRepository $recipeRepository, UserRepository $userRepository){
        $recipes = $recipeRepository->findBestRecipes(3);
        $users = $userRepository->findBestUsers(2);


        return $this->render('home/index.html.twig',[
            'recipes' => $recipes,
            'users' => $users
        ]);



    }


}
