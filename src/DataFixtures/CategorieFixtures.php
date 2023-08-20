<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategorieFixtures extends Fixture
{
    private $counter = 1;

    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        
        // $product = new Product();
        // $manager->persist($product);
            $parent = $this-> CreateCategoy('informatique', null, $manager);
           

            $this->CreateCategoy('Ordinateurs portable', $parent, $manager);
            $this->CreateCategoy('Ecrans', $parent, $manager);
            $this->CreateCategoy('Souris', $parent, $manager);

            $parent = $this-> CreateCategoy('Mode', null, $manager);

            $this->CreateCategoy('Homme', $parent, $manager);
            $this->CreateCategoy('Femme', $parent, $manager);
            $this->CreateCategoy('Enfant', $parent, $manager);

        

            $manager->flush();
    }

    public function CreateCategoy(string $name, Categorie $parent = null, ObjectManager $manager)
    {
         $category = new Categorie();
            $category->setName($name);
            $category->setSlug($this->slugger->slug($category->getName())->lower());
            $category->setParent($parent);
            $manager->persist($category);

             $this->addReference('cat-'.$this->counter, $category);
            $this->counter++;

            return $category;
    }
}