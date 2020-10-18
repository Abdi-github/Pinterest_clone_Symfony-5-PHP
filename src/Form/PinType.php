<?php

namespace App\Form;

use App\Entity\Pin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Vich\UploaderBundle\Form\Type\VichImageType;

class PinType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('title')
                ->add('description')
                ->add('imageFile', VichImageType::class, [
                    'label' => 'Image (JPG/PNG format)',
                    'required' => false,
                    'allow_delete' => true,
                    'delete_label' => 'Delete Image',
                    'download_label' => 'Download Image',
                    'download_uri' => true,
                    'image_uri' => true,
                    'imagine_pattern' => 'thumbnail_small',
                    'asset_helper' => true,
                ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Pin::class,
        ]);
    }

}
