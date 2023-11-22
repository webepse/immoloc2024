<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $slugify = new Slugify();

        // création d'un admin
        $admin = new User();
        $admin->setFirstName('Jordan')
            ->setLastName('Berti')
            ->setEmail('berti@epse.be')
            ->setPassword($this->passwordHasher->hashPassword($admin, 'password'))
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
            ->setRoles(['ROLE_ADMIN'])
            ->setPicture('');
        
        $manager->persist($admin);

        // gestion des utilisateurs 
        $users = []; // init d'un tableau pour récup des user pour les annonces
        $genres = ['male','femelle'];

        for($u=1 ; $u <= 10; $u++)
        {
            $user = new User();
            $genre = $faker->randomElement($genres);
       

            $hash = $this->passwordHasher->hashPassword($user, 'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
                ->setPassword($hash)
                ->setPicture('');

            $manager->persist($user);    

            $users[] = $user; // ajouter un user au tableau (pour les annonces)

        }



        for($i=1; $i<=30; $i++)
        {
            $ad = new Ad();
            $title = $faker->sentence();
            $coverImage = 'https://picsum.photos/seed/picsum/1000/350';
            $introduction= $faker->paragraph(2);
            $content = '<p>'.join('</p><p>', $faker->paragraphs(5)).'</p>';

           // liaison avec l'user
           $user = $users[rand(0, count($users)-1)];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(rand(40,200))
                ->setRooms(rand(1,5))
                ->setAuthor($user);

          
            // Gestion de la galerie image de l'annonce
            for($g=1; $g <= rand(2,5); $g++)
            {
                $image = new Image();
                $image->setUrl('https://picsum.photos/id/'.$g.'/900')
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                $manager->persist($image);    
            }

            // gestion des réservation
            for($b = 1; $b <= rand(0,10); $b++)
            {
                $booking = new Booking();
                $createdAt = $faker->dateTimeBetween('-6 months','-4 months');
                $startDate = $faker->dateTimeBetween('-3 months');
                $duration = rand(3,10);
                // comme c'est un dateTime
                $endDate = (clone $startDate)->modify("+".$duration." days");
                $amount = $ad->getPrice() * $duration;
                $comment = $faker->paragraph();
                $booker = $users[rand(0,count($users)-1)];

                $booking->setBooker($booker)
                    ->setAd($ad)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setCreatedAt($createdAt)
                    ->setAmount($amount)
                    ->setComment($comment);

                $manager->persist($booking);

                //gestion des commentaires 
                $comment = new Comment();
                $comment->setContent($faker->paragraph())
                    ->setRating(rand(1,5))
                    ->setAuthor($booker)
                    ->setAd($ad);

                $manager->persist($comment);

                
            }

            $manager->persist($ad);



        }

        $manager->flush();
    }
}
