<?php

namespace App\DataFixtures;

use App\Entity\Etablissement;
use App\Repository\VilleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;


class EtablissementFixtures extends Fixture
{
    private SluggerInterface $slugger;
    private VilleRepository $villeRepository;

    //Demander à symfony d'injecter le slugger au niveau du constructeur

    public function __construct(SluggerInterface $slugger, VilleRepository $villeRepository)
    {
        $this->slugger = $slugger;
        $this->villeRepository = $villeRepository;
    }

    public function load(ObjectManager $manager): void
    {
        // Initialiser faker
        $faker = Factory::create("fr_FR");
        $totalVille = $this->villeRepository->findAll();
        $minVille = min($totalVille);
        $maxVille = max($totalVille);


        for ($i=0;$i<=100;$i++) {
            $numVille = $faker->numberBetween($minVille->getId(),$maxVille->getId());
            $etablissement = new Etablissement();
            $etablissement->setNom($faker->realTextBetween(5,20))
                ->setSlug($this->slugger->slug($etablissement->getNom())->lower())
                ->setDescription($faker->paragraphs(3,true))
                ->setAdresse($faker->streetAddress())
                ->setEmail($faker->email())
                ->setImage($faker->imageUrl(360, 360, 'établissement', true, 'hotel'))
                ->setTelephone($faker->phoneNumber())
                ->setActif($faker->boolean())
                ->setAccueil($faker->boolean())
                ->setCreatedAt($faker->dateTimeBetween('-20 years'))
                ->setVille($this->villeRepository->find($numVille));
            $this->addReference("etablissement".$i,$etablissement);
            $manager->persist($etablissement);

        }
        // Envoyer l'ordre INSERT vers la BDD
        $manager->flush();

    }

}
