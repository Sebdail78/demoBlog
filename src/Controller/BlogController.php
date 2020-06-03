<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            'title' => 'Bienvenue sur le blog Symfony',
            'age' => 25
        ]);
    }


    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, EntityManagerInterface $manager)
    {
        // initialement méthode create()
        /*
            La classe Request est une classe prédéfinie en symfony qui stocke toutes les données véhiculées par les superglobales
            ($_POST, $_GET, $_SERVER etc...)
            La propriété 'request' représente la superglobale $_POST, les données saisies dans le formulaire sont accessibles 
            via cettte propriétés, ca renvoi des  parameterBag / sac de paramètres)
            Pour insérer un nouvel article, nous devons instancier la classe pour avoir un article vide, toutes les propriétés private
            ($title, $content, $image), il faut donc les remplir, pour cela nous faisons appel au setter.

            EntityManagerInterface est une méthode prédéfinie de Symfony qui permet de manipuler les lignes de la BDD
            (INSERT, UPDATE, DELETE)

            persist() est une méthode issue de la classe EntityManagerInterface qui permet de stocker et de preparer la requete SQL d'insertion
            flush() est une méthode issue de la class EntityManagerInterfzce qui permet de liberer la requete d'insertion, c'est elle qui 
            envoie véritablement dans la BDD

            redirectToRoute() méthode prédéfinie de Qymfony qui permet de redirigé vers une route spécifique, dans notre cas on redirige après insertion
            vers la route blog_show (avec le bon dernier id insérer) afin de renvoyer le détail de l'article qui vient d'être insérer.


        */



        dump($request);

        // if($request->request->count() > 0)
        // {
        //     $article = new Article;
        //     $article->setTitle($request->request->get('title'))
        //             ->setContent($request->request->get('content'))
        //             ->setImage($request->request->get('image'))
        //             ->setCreatedAt(new \DateTime());
               
        //     $manager->persist($article);
        //     $manager->flush();   

        //     dump($article);
            
        //     return $this->redirectToRoute('blog_show', [
        //         'id' => $article->getId()
        //     ]);
        // }

        /*

            createFormBuilder() est une méthode prédéfinie de Symfony qui permet de créer un formulaire à partir d'une entité,
            dans notre cas de la classe Article, cela permet aussi de dire que le formulaire permettra de remplir l'objet issue
            de la classe Article $article

            add() est une méthode qui permet de créer les différents champs du formulaire 
            getform() est une méthode qui permet de terminer et de valider le formulaire

            handleRequest() est une méthode qui permet de récupérer dans notre cas les informations stockés dans $_POST et de 
        remplir notre objet $article, plus besoin de faire appel aux setters de la classe Article.


        */
        if(!$article)
        {
            $article = new Article;
        }


        // On observe quand remplissant l'objet $article via les setteurs, les getteurs envoeyent 
        // les données de l'article directement à l'intérieur des champs du formulaire

        // $article->setTitle("Titre à la con")
        //         ->setContent("Contenu de l'article à la con");

        // on construit le formulaire :
        // $form = $this ->createFormBuilder($article)
        //               ->add('title')
        //               ->add('content')
        //               ->add('image')
        //               ->getForm();


            // Permet de faire appel à la classe ArticleType permettant de generer le formulaire d'ajout/modification
            // On précise que ce formulaire permettra de remplir un objet issu de la class Article $article 
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);    
        
        if($form->isSubmitted() && $form->isValid()) // si le formulaire est soumit et est valide
        {
            if(!$article->getId())
            {
                $article->setCreatedAt(new \DateTime());
            }
            
            $manager->persist($article); // persist récupère l'objet $article et prépare la requete d'insertion
            $manager->flush(); // flush() libère réellement la requete SQL d'insertion


            // on redirige après l'insertion vers le détail de l'article qui vient dêtre insérer.
            return $this->redirectToRoute('blog_show', [
                   'id' => $article->getId()
                ]);
        }
        
        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !==null  // on teste si l'article possède un ID ou non, si l'article possède un ID c'est une modification
            // si il n'a pas d'ID c'est une insertion
        ]);
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
