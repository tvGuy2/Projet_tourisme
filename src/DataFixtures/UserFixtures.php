<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        for ($i=0;$i<10;$i++){
            $user = new User();
            $pseudo = $faker->numberBetween(0,2);
            $user->setNom($faker->lastName())
                ->setPrenom($faker->firstName())
                ->setCreatedAt(new \DateTime())
                ->setEmail($faker->email())
                ->setPassword(password_hash("secret123",PASSWORD_BCRYPT))
                ->setActif($faker->boolean());
            if ($pseudo == 0){
                $user->setPseudo($faker->word());
            }

            $randomArray = rand(0,5);
            if ($randomArray == 0){
                $user->setRoles(['ROLE_ADMIN']);
            }
            elseif ($randomArray == 1){
                $user->setRoles(['ROLE_RESTAURANT']);
            }
            else{
                $user->setRoles(['ROLE_USER']);
            }

            $manager->persist($user);

        }

        $manager->flush();
    }
}
