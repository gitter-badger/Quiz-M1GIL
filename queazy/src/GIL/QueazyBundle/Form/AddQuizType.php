<?php

namespace GIL\QueazyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddQuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre', 'text', array(
            'label' => false,
            'required' => true,
            'attr' => array(
                'placeholder' => 'Titre',
                'class' => 'large-12 columns input-group-field',
            )
        ));
    }

    public function getName()
    {
        return 'form_add_quiz';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver){
        $resolver->setDefaults([
            'data_class' => 'GIL\QueazyBundle\Entity\Quiz'
        ]);
    }
}