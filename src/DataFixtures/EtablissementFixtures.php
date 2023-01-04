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
            $numImage = $faker->numberBetween(1,5);
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
                ->setVille($this->villeRepository->find($numVille));
                if ($numImage>=3){
                    $etablissement->setImage($faker->imageUrl(500,300,$etablissement->getNom(),true));
                }

                $etablissement->addCategorie($this->categorieRepository->find($faker->numberBetween($minCategorie->getId(),$maxCategorie->getId())))
                ->addCategorie($this->categorieRepository->find($faker->numberBetween($minCategorie->getId(),$maxCategorie->getId())));

            $manager->persist($etablissement);

        }

        $manager->flush();

    }

}
