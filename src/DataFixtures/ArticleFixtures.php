<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // On utilise la bibliotheque FAKER qui permet d'envoyer des fausses données aléatoire dans la BDD
        // On a demandé à composer d'installer cette librairie sur notre application
        $faker = \Faker\Factory::create('fr_FR');

        // Création de 3 catégories 
        for($i = 1; $i <= 3; $i++)
        {
            // Nous avons besoin d'un objet $category vide afin de renseigner de nouvelles catégories
            $category = new Category;

            // On appel les setteurs de l'entité Category
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());

            $manager->persist($category);// On garde en mémoire les objets $category

            // Création de 4 à § articles 
            for($j = 1; $j <= mt_rand(4,6); $j++)
            {
                // Nous avons besoin d'un objet $article vide afin de créer et d'insérer de nouveaux articles en BDD
                $article = new Article; 

                // On demande à FAKER de créer 5 paragraphes aléatoire pour nos nouveaux articles 
                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

                // On renseigne tout les setteurs de la classe Article grace aux méthodes de la librairies FAKER (phrase aléatoire
                //(sentence), image aléatoire (imageUrl()) etc....)
                $article->setTitle($faker->sentence())
                        ->setContent($content)
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween('-6 months')) // création de la date d'article, d'il ya 6 mois à aujourd'huit
                        ->setCategory($category); // on renseigne la clé étrangere qui permet de relier les articles au catégories

                $manager->persist($article);

                // Création de 4 à 10 commentaires
                for($k = 1; $k <= mt_rand(4,10); $k++)
                {
                    $comment = new Comment;

                    $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';

                    $now = new \DateTime(); // objet dateTime avec l'heure et la date du jour
                    $interval = $now->diff($article->getCreatedAt()); // représente entre maintenant et la date de création de l'article (timestamp)
                    $days = $interval->days; // nombre de jour entre maintenant et la date de création de l'article 
                    $minimun = '-' . $days . 'days'; /* -100 days entre la date de création de l'article et maintenant */

                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween($minimun))
                            ->setArticle($article); // on relie (clé étrangere) nos commentaires aux articles

                    $manager->persist($comment);

                }
            }
        }





        // for($i = 1; $i <= 10; $i++) // on boucle 10 fois afin de créer 10 articles 
        // {
        //     $article = new Article; // on instance la classe Article afin de renseigner les propriétés private et d'envoyer les objets 
        //     // type Article en BDD, la classe Article reflète la table SQL 'article'

        //     // On renseigne tout les setteurs de la classe Article afin d'ajouter des titres, du contenu etc qui seront insérés en BDD
        //     $article->setTitle("Titre de l'article n° $i")
        //             ->setContent("<p>Contenu de l'article n° $i</p>")
        //             ->setImage("https://picsum.photos/seed/picsum/200/300")
        //             ->setCreatedAt(new \DateTime()); // objet classe DateTime
            
        //     $manager->persist($article); // persist() est une méthode issue de la classe objetManager permettant de garder en mémoire
        //     // les objets Articles crées, il est fait persister dans le temps
        // }

        // // $product = new Product();
        // // $manager->persist($product);

        // $manager->flush(); // flush() est une méthode issue de la classe ObjetManager qui permet véritablement de générer l'insertion
        // // en BDD 

        $manager->flush();
    }
}
