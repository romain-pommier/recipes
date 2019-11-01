<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//fze
class HomeController extends AbstractController
{

//    C10
    /**
     * @Route("/", name="home")
     */
    public function index(RecipeRepository $recipeRepo, UserRepository $userRepo)
    {

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'recipes' => $recipeRepo->findBestRecipes(3),
            'users' => $userRepo->findBestUsers(2)
        ]);
    }
}
