<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, ProductRepository $productRepository)
    {
        $panier = $session->get('panier', []);
        // on initialise des variables
        $data = [];
        $total = 0;
        
        foreach ($panier as $id => $quantity) {
            $product = $productRepository->find($id);

            $data[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
            $total += $product->getPrice() * $quantity;
        }
         return $this->render('cart/index.html.twig', compact('data', 'total'));
    }


    #[Route('/add/{id}', name: 'add')]
    public function add(Product $product, SessionInterface $session)
    {
        // onrecupere l'id du produit
        $id = $product->getId();
        // on recupere le panier existant
        $panier = $session->get('panier', []);

        //  on ajoute le produit dans le panier si il n'y ai pas encore
        // sinon on incremente sa quantite
        if(empty($panier[$id])){
            $panier[$id] = 1;
        } else {
            $panier[$id]++;
        }


        $session->set('panier', $panier); 
        // on redirige ver la page du panier
        // return $this->redirectToRoute('cart_index');
        return $this->redirectToRoute('product_details', ['slug' => $product->getSlug()]);
    }

    #[Route('/more/{id}', name: 'more')]
    public function more(Product $product, SessionInterface $session)
    {
        // onrecupere l'id du produit
        $id = $product->getId();
        // on recupere le panier existant
        $panier = $session->get('panier', []);

        //  on ajoute le produit dans le panier si il n'y ai pas encore
        // sinon on incremente sa quantite
        if(empty($panier[$id])){
            $panier[$id] = 1;
        } else {
            $panier[$id]++;
        }


        $session->set('panier', $panier); 
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Product $product, SessionInterface $session)
    {
        // onrecupere l'id du produit
        $id = $product->getId();
        // on recupere le panier existant
        $panier = $session->get('panier', []);

        //  on retir le produit du panier si il n'y a qu'un exemplaire
        // sinon on decremente sa quantite
        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--; 
            } else {
                unset($panier[$id]);
            }
        }

        $session->set('panier', $panier); 
        // on redirige ver la page du panier
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Product $product, SessionInterface $session)
    {
        // onrecupere l'id du produit
        $id = $product->getId();
        // on recupere le panier existant
        $panier = $session->get('panier', []);

        
        if(!empty($panier[$id])){
                unset($panier[$id]);
            }

        $session->set('panier', $panier); 
        // on redirige ver la page du panier
        return $this->redirectToRoute('cart_index');
    }

     #[Route('/empty', name: 'empty')]
    public function empty(SessionInterface $session)
    {
        $session->remove('panier');
        return $this->redirectToRoute('cart_index');

    }

}