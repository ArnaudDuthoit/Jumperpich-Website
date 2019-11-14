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
            ->add('title',null,['label'  => 'Titre'])
            ->add('description',null,['label' =>'Tracklist'])
            ->add('tags', EntityType::class,[
                'class'=> Tag::class,
                'required' => false,
                'choice_label' => 'name',
                'label' => 'Genre',
                'multiple' => true,
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'Image'
            ])
            ->add('YTB_link',null,['label' => "Lien Youtube"])
            ->add('mp3File', FileType::class,[
                'required' => false,
                'label' => 'Fichier MP3',
                'constraints' => [
                    new File([
                        'maxSize' => '300M',
                    ])
            ]])
            ->add('soundcloud',null,['label' => 'Lien Soundcloud'])
            ->add('mixcloud',null,['label' => 'Lien Mixcloud (iFrame)'])
            ->add('fileLength',null,['label' => 'Durée (Optionnel)'])
            ->add('fileSize',null,['label' => 'Taille (en Mo)'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
