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

    //Demander à symfony d'injecter le slugger au niveau du constructeur

    public function __construct(SluggerInterface $slugger, VilleRepository $villeRepository, CategorieRepository $categorieRepository)
    {
        $this->slugger = $slugger;
        $this->villeRepository = $villeRepository;
        $this->categorieRepository = $categorieRepository;
    }

    public function load(ObjectManager $manager): void
    {
        // Initialiser faker
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

                // a modifier car chaque établissement n'a pas forcement une image


                ->setTelephone($faker->phoneNumber())
                ->setActif($faker->boolean())
                ->setAccueil($faker->boolean())
                ->setCreatedAt($faker->dateTimeBetween('-20 years'))
                ->setVille($this->villeRepository->find($numVille))

                ->addCategorie($this->categorieRepository->find($faker->numberBetween($minCategorie->getId(),$maxCategorie->getId())))
                ->addCategorie($this->categorieRepository->find($faker->numberBetween($minCategorie->getId(),$maxCategorie->getId())));

            $manager->persist($etablissement);

        }
        // Envoyer l'ordre INSERT vers la BDD
        $manager->flush();

    }

}
