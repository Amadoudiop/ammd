<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MediaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$builder->add('name')
                ->add('alt')
                ->add('thumb')
                ->add('extension');*/

        $builder->add('thumb', FileType::class, [
                    'data' => null,
                    'label' => 'Image',
                    'required' => false,
                    'attr' => [
                        'class' => 'fileUploader'
                    ]

                ])
                ->add('alt', TextType::class, [
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Media'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_media';
    }


}
