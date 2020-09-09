<?php

namespace App\Form;

use App\Entity\Hands;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class uploadFile extends AbstractType{
  public function BuildForm(FormBuilderInterface $builder, array $options){
    $builder

    ->add('hands', FileType::class, [
      'label' => 'Hands (Text File)',

      'mapped' => 'false',

      'constraints' => [
        new File([
          'maxSize' => '1024k',
          'mimeTypes' => 'text/plain',
          'mimeTypesMessage' => 'Please upload a valid .txt document',
        ])
      ],
    ])
    ->add('save', SubmitType::class, ['label' => 'Submit File']);
  }
  public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Hands::class,
        ]);
    }
}

?>
