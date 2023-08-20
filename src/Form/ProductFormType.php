<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Product;
use App\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options: [
                'label' => "Nom",
            ])
            ->add('descripton')
            ->add('price', options:[
                'label'=>'Prix'
            ])
            ->add('stock', options:[
                'label'=> 'Unités en stock'
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class, 
                'choice_label' =>'name',
                'label'=> 'Categorie',
                'group_by' => 'parent.name',
                'query_builder' => function(CategorieRepository $cr)
                {
                    return $cr->createQueryBuilder('c')
                        ->where('c.parent IS NOT NULL')
                        ->orderBy('c.name', 'ASC')
                        ;
                }
            ])
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}