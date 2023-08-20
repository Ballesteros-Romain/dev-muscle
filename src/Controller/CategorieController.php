<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/categorie', name: 'categorie_')]

class CategorieController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig');
    }

     #[Route('/{slug}', name: 'list')]
    public function list(Categorie $category,): Response
    {
        // on va cherhcer la liste des porduits de la categorie
        $products = $category->getProducts();
        return $this->render('categorie/list.html.twig', compact('category', 'products'));
    }
}