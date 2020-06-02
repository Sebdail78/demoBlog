<?php

namespace App\Form;

use App\Entity\Article;
use app\Form\Entity;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            // On ajoute ke champ 'categorie' pour le formulaire d'ajout d'article
            // On précise de quelle entité provient le champ 'categorie'
            ->add('category', Entity::class, [
                'class' => Category::class, 
                'choice_label' => 'title'
            ])
            ->add('content')
            ->add('image')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
