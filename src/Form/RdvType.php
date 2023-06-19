<?php

namespace App\Form;

use App\Entity\Rdv;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;



class RdvType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entityManager = $this->entityManager;

        $builder
        ->add('date', DateType::class, [
            'widget' => 'single_text',
            'attr' => [
                'class' => 'bg-gray-50 border mr-4 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5',
            ],
        ])
        ->add('time', TimeType::class, [
            'widget' => 'single_text',
            'attr' => [
                'class' => 'bg-gray-50 border mr-4 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5',
            ],
            'constraints' => [
                new Callback([
                    'callback' => function ($value, ExecutionContextInterface $context) use ($entityManager) {
                        $rdv = $context->getRoot()->getData();

                        // Check if there is an existing appointment with the same date and time
                        $existingRdv = $entityManager->getRepository(Rdv::class)->findOneBy([
                            'date' => $rdv->getDate(),
                            'time' => $rdv->getTime(),
                        ]);

                        if ($existingRdv && $existingRdv !== $rdv) {
                            $context->buildViolation('deja reservé')
                                ->addViolation();
                        }
                    },
                ]),
            ],
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rdv::class,
        ]);
    }
}
