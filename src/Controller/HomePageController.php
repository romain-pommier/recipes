<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Entity\RecipePicture;
use App\Entity\Ingredient;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class HomePageController extends AbstractController
{
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
