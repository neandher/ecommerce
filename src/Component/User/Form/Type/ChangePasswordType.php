<?php

namespace App\Component\User\Form\Type;

use App\Component\User\Model\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangePasswordType extends PlainPasswordType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'current_password',
                PasswordType::class,
                array(
                    'label' => 'user.form.current_password',
                    'mapped' => false,
                    'constraints' => new UserPassword()
                )
            );
        
        parent::buildForm($builder, $options);         
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => UserInterface::class,
                'validation_groups' => ['Default', 'changing'],
                'is_register' => false
            )
        );
    }
}
