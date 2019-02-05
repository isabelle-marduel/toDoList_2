<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\ToDoList;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => "Nom de la tâche",
                ]
                )

            ->add(
                'list',
                // select sur une entité Doctrine
                EntityType::class,
                [
                    'label' => 'Liste',
                    // nom de l'entité
                    'class' => ToDoList::class,
                    // nom de l'attribut utilisé pour l'affichage des options
                    'choice_label' => 'name',
                    // pour avoir une 1ère option vide
                    'placeholder' => 'Choisissez une liste'
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
