<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    // Un commentaire qui commence par un '@' est une annotation trés importante, synfony explique que lorsqu'on lancera 
    // www.monsite.com/blog on fera appel à la méthode index()
    // Pas besoin de péciser tamplates/blog/index.html.twig, Symfony sait où se trouve les fichiers templates de rendu

    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        /*
            Pour selectionner des donnéess en BDD, nous avons besoin de la classe Repository de la classe Article
            Une classe Repository permet uniquement de selectionner des données en BDD (requete SQL SELECT)
            On a besoin de l'ORM DOCTRINE pour faire la relation entre la BDD et notre appilication (getDoctrine())
            getRepository() : méthode issue de l'objet DOCTRINE qui permet d'importer une classe Repository (SELECT)

            $repo est un objet issu de la classe ArticleRepository, cette dernieres contient des méthodes prédéfinies par SYMFONY
            permettant de selectionner des données en BDD (find, findBy, findOneBy, findAll)

            dump() : équivalent de var_dump(), permet d'observer le resultat de la requete de selection en bas de la page dans 
            la barre administrative (cible à droite)
        */

        $repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();
        // findAll() est une méthode issue de la classe ArticleRepository qui permet de selectionner l'ensemble de la table (similaire à SELECT * FROM article)

        dump($articles);

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles 
        ]);
        // On envoie les articles selectionnés en BDD directement sur le navigateur dans le template index.html.twig
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'titre' => 'Bienvenue sur le blog Symfony',
            'age' => 25
        ]);
    }


    /**
     * @Route("/blog/new", name="blog_create")
     */
    public function create(Request $request)
    {
        dump($request);
        
        return $this->render('blog/create.html.twig');
    }




    // show() : méthode permettant d'afficher le détail d'1 article 
                    //3
    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article) // 1
    {
        /*
            Pour selectionner un article dans la BDD, nous utilisons le principe de route paramétrées 
            Dans la route, on définit un paramètres de type{id}
            Lorsque nous transmettons dans l'URL par exemple une route '/blog/9', donc on envoie un id connu en BDD dans l'URL
            Symfony va automatiquement récupérer ce paramètre et le transmettre en argument de la méthode show()
            Cela veut dire que nous avons accées à l'{id} à l'intérieur de la méthode show()
            Le but est de seléctioner les données en BDD de l'{id} récupéré en parametre de la méthode show()
            Nous avons besoin pour cela de la classe ArticleRepository afin de pouvoir selectionner en BDD
            La méthode find() est issue de la classe ArticleRepository et permet de selectionner des données en BDD a partir 
            d'un parametre de type {id}
            getDoctrine() : l'ORM fait le travail pour nous, c'est a dire qu"elle récupere la requete de selection pour l'executer 
            en BDD et Doctrine récupère le résultat de la requete de selection pour l'envoyer dans le controller, 

            $repo est un objet issu de la classe ArticleRepository, nous avons accès a toute les méthode déclarées dans cette classe
            (find, findAll, findBy, findOneBy etc ...)
        */


        //$repo = $this->getDoctrine()->getRepository(Article::class);

        //$article  = $repo->find($id); // 1, on transmet en argument de la méthode find(), le paramètre {id} récupéré dans l'URL

        dump($article);

        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
        // On envoie dans le template show.html.twig, les données selectionnées en BDD, c'est a dire le detail d'un artcile
        // extract(['article' => $article]) => 'article' devient une variable TWIG dans le template show.html.twig

      
        /*            doctrine
                BDD  <_______  
                |             |  
                |              CONTROLLER _______ > libère les templates + données BDD sur la navigateur
                |____________>|
                    doctrine
        */
    }

    
    
}
