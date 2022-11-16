<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        $categories = ["restaurant", "hôtel", "gîte", "musée", "artisanat"];

        foreach ($categories as $category){
            $categorie = new Categorie();

            $categorie->setNom($category)
                ->setCreatedAt($faker->dateTime());

            $manager->persist($categorie);
            // $product = new Product();
            // $manager->persist($product);

            $manager->flush();
        }


    }
}
