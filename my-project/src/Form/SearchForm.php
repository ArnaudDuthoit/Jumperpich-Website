<?php
/**
 * Created by PhpStorm.
 * User: arnau
 * Date: 16/12/2019
 * Time: 11:31
 */

namespace App\Form;


use App\Data\SearchData;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('q', TextType::class,[
               'label' => false,
               'required' => false,
               'attr'=>[
                   'placeholder' => 'Rechercher'
               ]
           ])

           ->add('Tags', EntityType::class, [
               'label' =>false,
               'required' =>false,
               'class'=> Tag::class,
               'expanded' => true,
               'multiple' =>true
           ])


       ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver ->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }


    public function getBlockPrefix()
    {
        return '';
    }



}