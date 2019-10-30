<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use function password_verify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AccountController extends AbstractController
{


//(19)


    /**
     * Connection utilisateur
     * @Route("/login", name="account_login")
     *
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();



        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * déconnection utilisateur
     * @Route("/logout", name="account_logout")
     */
    public function logout(){

    }

    /**
     * Enregistrement utilisateur
     * @Route("/register", name="account_register")
     *
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){

        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',"Votre compte a bien été créé !"
            );

            return $this->redirectToRoute("account_login");

        }



        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Traitement modification profil
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     */
    public function profile( Request $request, ObjectManager $manager){
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',"Votre compte a bien été modifié !"
            );
        }



        return $this->render('account/profile.html.twig',[
            'form' => $form->createView()
        ]);

    }

    /**
     * modification mot de passe
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     *
     */
    public function updatePassword(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){

        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //Vérification oldPassword
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())){

                $form->get('oldPassword')->addError(new FormError("Le mot de passe entré n'est pas le mot de passe actuel"));
            }
            else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', "Votre mot de passe a bien été modifier");

                return $this->redirectToRoute('home');
            }


        }

        return $this->render('account/password.html.twig',[
            'form' => $form->createView()
        ]);
    }


    /**
     * affichage profil perso
     *
     * @Route ("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     */
    public function myAccount (){

        return $this->render('user/index.html.twig',[
            'user' => $this->getUser()
        ]);

    }
}
