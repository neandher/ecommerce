<?php

namespace App\Component\User\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecurityLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', TextType::class, [
                'label' => 'user.form.email',
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'user.form.password',
            ])
            ->add('_remember_me', CheckboxType::class, [
                'label' => 'user.form.remember_me',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => true,
                'csrf_field_name' => '_token',
                'csrf_token_id' => 'authentication'
            ]
        );
    }
}