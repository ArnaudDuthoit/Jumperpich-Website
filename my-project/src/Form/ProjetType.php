<?php

namespace App\Form;

use App\Entity\Projet;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('tags', EntityType::class,[
                'class'=> Tag::class,
                'required' => false,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('imageFile', FileType::class, [
                'required' => false
            ])
            ->add('YTB_link')
            ->add('mp3File', FileType::class,[
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '300M',
                    ])
            ]])
            ->add('soundcloud')
            ->add('mixcloud')
            ->add('fileLength')
            ->add('fileSize')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
