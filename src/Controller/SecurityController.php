<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription" , name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User;

        $form = $this->createForm(RegistrationType::class, $user);

        dump($request);

        $form->handleRequest($request); 

        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            // on récupère le mot de passe du formulaire (non haché) pour le transmettre à la méthode encvodePassword()
            //qui va se chargé d'encoder / crypter / hacher le mot de passe
            $user->setPassword($hash); // on envoi le mot de passe haché dans le setteur de l'oblet $user afin qu'il soit
            // insérer dans la BDD

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security_login'); // on redirige vers la page de connexion après inscription

        }


        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // renvoie un message d'erreur en cas de mauvais indentifiant au moment de la connexion
        $error = $authenticationUtils->getLastAutenticationError();

        // recupère le dernier username (email) que l'internaute a saisie dans le formulaire de connexion
        $lastUsernam = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
        // cette fonction ne retourne rien, il nous suffit d'avoir une route pour la déconnexion (voir security.yaml / firewalls)
    }

    /*
    security.yaml :

    providers : ou se trouvent les données à controler
    firewalls : quelles parties du site nous allons proteger et par quel moyen


    */
}