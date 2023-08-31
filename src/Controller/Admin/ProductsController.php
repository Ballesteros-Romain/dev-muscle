<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductFormType;
use App\Service\PictureService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/produits', name: 'admin_products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        
        return $this->render('admin/products/index.html.twig');
    }

     #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // on cree un nouveau produit
        $product = new Product();
        // on cree le formulaire
        $productForm = $this->createForm(ProductFormType::class, $product);
        // on traite la requete du form
        $productForm -> handleRequest($request) ;
        // on verifie si le formulaire est soumis ET valide (dans cet ordre)
        if($productForm->isSubmitted() && $productForm->isValid()){
            // on recupere les images
            $images = $productForm->get('images')->getData();
            foreach ($images as $image) {
                // on definie le dossier de destination
                $folder = 'products';
                // on appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);
                
                $img = new Image();
                $img->setName($fichier);
                $product->addImage($img);
            }
            // on genere le slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);
            // on arrondi le prix 
            // $prix = $product->getPrice()*100;   // car dans le product form type on a mis un moneytype
            // $product->setPrice($prix);
           
            // on stocke
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Produits ajouté avec succès');
            // on redirige
            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/add.html.twig', [
            'productForm' => $productForm->createView()
        ]);

    }

     #[Route('/edition/{id}', name: 'edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, PictureService $pictureService): Response
    {
        // on verifie si l'utilisateur peut editer avec le voter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);
        // on divise le prix par 100 
            // $prix = $product->getPrice() / 100;
            // $product->setPrice($prix);  Idem moneytype
        // on cree le formulaire
        $productForm = $this->createForm(ProductFormType::class, $product);
        // on traite la requete du form
        $productForm -> handleRequest($request) ;
        // on verifie si le formulaire est soumis ET valide (dans cet ordre)
        if($productForm->isSubmitted() && $productForm->isValid()){
              // on recupere les images
            $images = $productForm->get('images')->getData();
            foreach ($images as $image) {
                // on definie le dossier de destination
                $folder = 'products';
                // on appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);
                
                $img = new Image();
                $img->setName($fichier);
                $product->addImage($img);
            }
            // on genere le slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);
            // on arrondi le prix 
            // $prix = $product->getPrice()*100;
            // $product->setPrice($prix);
            // on stocke
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Produits modifié avec succès');
            // on redirige
            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/edit.html.twig', [
            'productForm' => $productForm->createView(),
            'product'=> $product
        ]);
    }


     #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Product $product): Response
    {
        // on verifie si l'utilisateur peut supprimer avec le voter
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);
        return $this->render('admin/products/index.html.twig');
    }

    // #[Route('/suppression/image/{id}', name: 'delete_image', methods:['DELETE'])]
    // public function deleteImage(Image $image, Request $request, EntityManagerInterface $em, PictureService $pictureService): JsonResponse
    // {
    //     // on recupere le contenu de la requete
    //     $data = json_decode($request->getContent(), true);

    // if(isset($data['data-token']) && $this->isCsrfTokenValid('delete' . $image->getId(), $data['data-token'])){
        
    // // le token csrf est valide
    // // alors on recuepere le nom de l'image
    // $nom =$image->getName();

    // if($pictureService->delete($nom, 'products', 300, 300)){
    //     // on supprime l'image de la base dedonnées
    //     $em->remove($image);
    //     $em->flush();
    //     return new JsonResponse(['success' => true], 200);
    //     }
    //      // la suppression a echoué
    //     return new JsonResponse(['error' => 'Erreur de suppression'], 400);
    //     }
    //     return new JsonResponse(['error' => 'Token invalide'], 400);
    // }
    

    #[Route('/suppression/image/{id}', name: 'delete_image', methods: ['DELETE'])]
public function deleteImage(Image $image, Request $request, EntityManagerInterface $em, PictureService $pictureService): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (isset($data['_token']) && $this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
        $nom = $image->getName();

        if ($pictureService->delete($nom, 'products', 300, 300)) {
            $em->remove($image);
            $em->flush();
            return new JsonResponse(['success' => true], 200);
        }

        return new JsonResponse(['error' => 'Erreur de suppression'], 400);
    }

    return new JsonResponse(['error' => 'Token invalide'], 400);

}


}