<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
    ){}

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@demo.fr');
        $admin->setLastname('ballesteros');
        $admin->setFirstname('romain');
        $admin->setAdress('10 rue du port');
        $admin->setZipcode('7500');
        $admin->setCity('paris');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $faker = Faker\Factory::create('fr_FR');

        for ($usr=1; $usr < 5; $usr++) { 
             $user = new User();
        $user->setEmail($faker->email);
        $user->setLastname($faker->lastName);
        $user->setFirstname($faker->firstName);
        $user->setAdress($faker->streetAddress);
        $user->setZipcode(str_replace(' ', '',$faker->postcode));
        $user->setCity($faker->city);
        $user->setPassword(
            $this->passwordEncoder->hashPassword($user, 'secret')
        );

        $manager->persist($user);
        }

        $manager->flush();

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}