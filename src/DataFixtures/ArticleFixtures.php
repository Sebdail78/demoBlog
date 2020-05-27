<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 10; $i++) // on boucle 10 fois afin de créer 10 articles
        {
            $article = new Article; // on instancie la classe Article afin de renseigner les propriétés private et d'envoyer les objets type Article en BDD, la classe Article reflète la table SQL 'article'

            // On renseigne tout les setteurs de la classe Article afin d'ajouter des titres, du contenu etcc qui seront insérés en BDD 
            $article->setTitle("Titre de l'article n° $i")
                    ->setContent("<p>Contenu de l'article n° $i</p>")
                    ->setImage("https://picsum.photos/250")
                    ->setCreatedAt(new \DateTime()); // objet classe DateTime

            $manager->persist($article); // persist() est une méthode issue de la classe ObjectManager permettant de garder en mémoire les objets Articles crées, il les fait persister dans la temps
        }

        $manager->flush();
    }
}
