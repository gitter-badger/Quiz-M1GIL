<?php

namespace GIL\QueazyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use GIL\QueazyBundle\Entity\Quiz;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('quiz', new AddQuizType());

        /*$builder->add('questionTypeContenu', 'choice', array(
            'choices' => array(
                'Type de Contenu'  => array('1' => 'Texte', '2' => 'Image', '3' => 'MathML')
            ),
            'multiple' => false,
            'expanded' => true,
            'data' => '1',
            'required' => true,
            'attr' => array(
                'class' => 'large-3 columns',
            )
        ));*/

        $builder->add('questionContenu', 'text', array(
            'label' => false,
            'required' => true,
            'attr' => array(
                'placeholder' => 'Question',
                'class' => 'large-12 columns input-group-field',
            )
        ));

        $builder->add('Valider', 'submit', array(
            'label' => false,
            'attr' => array(
                'class' => 'button',
            )
        ));

        $builder->add('Annuler', 'reset', array(
            'label' => false,
            'attr' => array(
                'class' => 'button',
            )
        ));
    }

    public function getName()
    {
        return 'form_quiz';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver){
        $resolver->setDefaults([
            'data_class' => 'GIL\QueazyBundle\Entity\Question'
        ]);
    }
}