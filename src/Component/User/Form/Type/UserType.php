<?php

namespace App\Component\User\Form\Type;

use App\Component\User\Model\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends PlainPasswordType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'E-mail'])
            ->add('isEnabled', CheckboxType::class, ['required' => false, 'label' => 'user.form.is_enabled']);

        if (!$options['is_edit']) {
            parent::buildForm($builder,$options);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => UserInterface::class,
                'is_edit'    => false,
            ]
        );
    }

}