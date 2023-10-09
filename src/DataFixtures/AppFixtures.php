<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Image;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $slugify = new Slugify();

        for($i=1; $i<=30; $i++)
        {
            $ad = new Ad();
            $title = $faker->sentence();
            $slug = $slugify->slugify($title);
            $coverImage = 'https://picsum.photos/seed/picsum/1000/350';
            $introduction= $faker->paragraph(2);
            $content = '<p>'.join('</p><p>', $faker->paragraphs(5)).'</p>';

            // $tableau ['Kim','Alexandre,'Audrey','Antoine']
            // join ou implode ('<br>', $tableau)
            // result 
            // Kim<br>Alexandre<br>Audrey<br>Antoine

            // '<p>'.lorem1</p><p>lorem2</p><p>lorem3</p><p>lorem4</p><p>lorem5.'</p>'

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(rand(40,200))
                ->setRooms(rand(1,5));

          
            // Gestion de la galerie image de l'annonce
            for($g=1; $g <= rand(2,5); $g++)
            {
                $image = new Image();
                $image->setUrl('https://picsum.photos/id/'.$g.'/900')
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                $manager->persist($image);    
            }

            $manager->persist($ad);



        }

        $manager->flush();
    }
}
