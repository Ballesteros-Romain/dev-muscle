<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/oubli-pass', name: 'forgotten_password')]
    public function forgotten_password(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGeneratorInterface,
        EntityManagerInterface $entityManagerInterface,
        SendMailService $mail
    ): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //on va cherhcer l'utilisateur par son email
            $user = $userRepository->findOneByEmail($form->get('email')->getData());

            // on verifie si on a un utilisateur
            if($user){
                // on genere un token de reinitialisation
                $token = $tokenGeneratorInterface->generateToken();
                $user->setResetToken($token);
                $entityManagerInterface->persist($user);
                $entityManagerInterface->flush();

                // on genere un lien de reinitialisation de mot de passe
                $url = $this->generateUrl('reset_pass', ['token' =>$token], UrlGeneratorInterface::ABSOLUTE_URL);
                
                // on cree les donnée du mail
                $context = compact('url', 'user');
                // on envoie le mail
                $mail->send(
                    'no-reply@e-commerce.fr',
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    'password_reset',
                    $context
                );

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');
            }
                // si $user est null
                $this->addFlash('danger', 'Un probleme est survenu');
                return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }
    #[Route('/oubli-pass/{token}', name: 'reset_pass')]
    public function resetPass(
    string $token,
    Request $request,
    UserRepository $userRepository,
    EntityManagerInterface $entityManagerInterface,
    UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        // on verifie si on a ce token dans la base de donées
        $user = $userRepository->findOneByResetToken($token);
        
        if($user){
            // on crée notre formulaire et on lui ajoute nos champs
            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                // on efface le token 
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                    );
                    $entityManagerInterface->persist($user);
                    $entityManagerInterface->flush();

                    $this->addFlash('success', 'Mot de passe changer avec succes ');
                    return $this->redirectToRoute("app_login"); 
            }
            return $this->render('security/reset_password.html.twig',[
                'passForm' => $form->createView()
            ]);
        }
            $this->addFlash('danger', 'Jeton invalide');
            return $this->redirectToRoute('app_login');
    }
}