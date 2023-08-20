<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($img=1; $img < 100; $img++) { 
            $image= new Image();
            $image->setName($faker->image(null, 640,480));
            $product = $this->getReference('prod-'.rand(1, 8));
            $image->setProduct($product) ;
            $manager->persist($image);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
    // sert a faire charger dans ce cas : productfixtures avant imagefixture car en general c'est par ordre alphabetique
    public function getDependencies(): array
    {
        return [
            ProductFixtures::class
        ];
    }
}