<?php

namespace App\Component\User\Form\Type;

use App\Component\User\Model\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlainPasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['is_register'] = empty($options['is_register']) ? true : $options['is_register'];
        
        $labelFirstOptions = $options['is_register'] ? 'user.form.password' : 'user.form.new_password';
        $labelSecondOptions = $options['is_register'] ? 'user.form.confirm_password'
            : 'user.form.new_password_confirmation';

        $builder
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => $labelFirstOptions],
                    'second_options' => ['label' => $labelSecondOptions],
                    'invalid_message' => 'user.password.mismatch'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => UserInterface::class,
            ]
        );
    }
}
