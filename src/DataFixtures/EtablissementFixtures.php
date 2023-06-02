<?php

namespace App\DataFixtures;

use App\Entity\Etablissement;
use App\Repository\CategorieRepository;
use App\Repository\VilleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;


class EtablissementFixtures extends Fixture
{
    private SluggerInterface $slugger;
    private VilleRepository $villeRepository;
    private CategorieRepository $categorieRepository;


    public function __construct(SluggerInterface $slugger, VilleRepository $villeRepository, CategorieRepository $categorieRepository)
    {
        $this->slugger = $slugger;
        $this->villeRepository = $villeRepository;
        $this->categorieRepository = $categorieRepository;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create("fr_FR");
        $totalVille = $this->villeRepository->findAll();
        $minVille = min($totalVille);
        $maxVille = max($totalVille);

        $totalCategorie = $this->categorieRepository->findAll();
        $minCategorie = min($totalCategorie);
        $maxCategorie = max($totalCategorie);


        for ($i = 0; $i <= 100; $i++) {
            $numVille = $faker->numberBetween($minVille->getId(), $maxVille->getId());
            $etablissement = new Etablissement();
            $etablissement->setNom($faker->realTextBetween(5, 20))
                ->setSlug($this->slugger->slug($etablissement->getNom())->lower())
                ->setDescription($faker->paragraphs(3, true))
                ->setAdresse($faker->streetAddress())
                ->setEmail($faker->email())
                ->setTelephone($faker->phoneNumber())
                ->setActif($faker->boolean())
                ->setAccueil($faker->boolean())
                ->setCreatedAt($faker->dateTimeBetween('-3 years'))
                ->setVille($this->villeRepository->find($numVille))
                ->setImage("https://cdn4.french-property.com/private-vendors/IFPC41291/b5da0934-c55e-4e30-8387-4f002393b1c7.jpg?token=3xqTgF9V7PDqD-ulMOC_KJ5lcxHodRZsZI6loEOv-Vk&expires=4102448461&token_ver=2&aspect_ratio=3:2&height=326&width=490");


                $etablissement->addCategorie($this->categorieRepository->find($faker->numberBetween($minCategorie->getId(),$maxCategorie->getId())))
                ->addCategorie($this->categorieRepository->find($faker->numberBetween($minCategorie->getId(),$maxCategorie->getId())));

            $manager->persist($etablissement);

        }

        $manager->flush();

    }

}
