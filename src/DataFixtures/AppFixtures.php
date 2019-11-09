<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipePicture;
use App\Entity\Role;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\DateTime;
use function mt_rand;
use function set_include_path;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('FR-fr');

        $fakerFood = \Faker\Factory::create();
        $fakerFood->addProvider(new \FakerRestaurant\Provider\fr_FR\Restaurant($faker));

        //gestion des roles

        $adminRole = new Role();

        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Romain')
                  ->setLastName('POMMIER')
                  ->setEmail('romain-p31@hotmail.fr')
                  ->setHash($this->encoder->encodePassword($adminUser, '12345678'))
                  ->setPicture('https://randomuser.me/api/portraits/men/35.jpg')
                  ->setIntroduction($faker->sentence())
                  ->setDescription('<p>' . join('</p><p>' , $faker->paragraphs(3)).'</p>')
                  ->addUserRole($adminRole);
        $manager->persist($adminUser);


        //gestion Users
        $users = [];
        $genres = ['male', 'female'];

        for($i = 1; $i <= 10; $i++){
            $user = new User();

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1,99) . '.jpg';

            if($genre == "male"){
                $picture .= "men/".$pictureId;
            }
            else{
                $picture .=  "women/".$pictureId;

            }

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>' . join('</p><p>' , $faker->paragraphs(3)).'</p>')
                ->setHash($hash)
                ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;

        }

        //gestion recettes
        for($i = 1; $i <= 30; $i++){
            $recipe = new Recipe();
            $title = $fakerFood->foodName();
            $coverImage = $faker->imageUrl(1000,350,'food');
            $description = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>' , $faker->paragraphs(5)).'</p>';
            $mealStyle = $faker->word();
            $cookingTime = $faker->time($format = 'H:i:s', $max = 'now');

            $user = $users[mt_rand(0, count($users) -1)];
            $fakePictureName = 'defaultFood'.mt_rand(1,12).'.jpg';
            $recipe->setTitle($title)
                    ->setDescription($description)
                    ->setContent($content)
                    ->setPeople(mt_rand(1,8))
                    ->setMealStyle($mealStyle)
                    ->setCookingTime(mt_rand(10,240))
                    ->setAuthor($user)
                    ->setCoverImageName($coverImage);





            for($j = 1; $j <= mt_rand(2,5); $j++){
                $image = new RecipePicture();
                $fakePictureName = $faker->imageUrl(1000,350,'food');
                $image->setCaption($faker->sentence());
                $image->setRecipe($recipe);
                $image->setRecipePictureName($fakePictureName);

                $manager->persist($image);
            }
            for($k = 1; $k <= mt_rand(5,9); $k++){
                $ingredient = new Ingredient();
                $random = mt_rand(0,3);
                $nameFakerFood = [$fakerFood->dairyName(), $fakerFood->vegetableName(), $fakerFood->meatName(), $fakerFood->meatName()];
                $resultMethod = $nameFakerFood[$random];

                $ingredient->setName($resultMethod)
                    ->addRecipe($recipe);

                $manager->persist($ingredient);

            }

            if(mt_rand(0,1)){
                $comment = new Comment();
                $date = new \DateTime();
                $comment->setContent($faker->paragraph())
                    ->setRating(mt_rand(1,5))
                    ->setAuthor($users[mt_rand(0,count($users)-1)])
                    ->setRecipe($recipe);



                $manager->persist($comment);
            }

            $manager->persist($recipe);


        }


        $manager->flush();
    }


}
