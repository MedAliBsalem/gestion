<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'constraints' => [
                new Email(['message' => 'Please enter a valid email address.']),
            ],
        ])
        ->add('nom')
        ->add('prenom')
        ->add('sexe', ChoiceType::class, [
            'choices' => [
                'Homme' => 'homme',
                'Femme' => 'femme',
            ],
            'required' => true,
            'attr' => [
                'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5',
            ],
            ])
        ->add('matricule')
        ->add('dateNaiss',DateType::class, [
            'widget' => 'single_text',
            'html5' => true,
            'attr' => [
                'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5',
                'placeholder' => '01/01/2023',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter your date of birth.',
                ]),
                new LessThan([
                    'value' => '-18 years',
                    'message' => 'You must be at least 18 years old.',
                ]),
                new GreaterThanOrEqual([
                    'value' => '-100 years',
                    'message' => 'Please provide a valid date of birth.',
                ]),
            ],
        ])
        ->add('adresse')
        ->add('codePostal')
        ->add('ville',ChoiceType::class, [
            'choices' => [
                'Sousse' => 'Sousse',
                'Monastir' => 'Monastir',
            ],
            'required' => true,
            'attr' => [
                'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5',
            ],
        ])
        ->add('numTel')
        ->add('password',RepeatedType::class, [
            'type'=>PasswordType::class,
            'invalid_message'=>'mot de passe ne correspondent pas ',
            'first_options'=>['label'=>'Password'],
            'second_options'=>['label'=>'Confirm Password']
        ])
        ->add('ajouter', SubmitType::class, [
            'label' => 'Terminer',
            'attr' => [
                'class' => 'flex items-center text-text-sky-950 bg-amber-400 hover:bg-amber-500 focus:amber-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5'
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
