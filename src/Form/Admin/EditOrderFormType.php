<?php

namespace App\Form\Admin;

use App\Entity\Order;
use App\Entity\User;
use App\Utils\StaticStorage\OrderStaticStorage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditOrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'required' => true,
                'choices' => array_flip(OrderStaticStorage::getOrderStatuses()),
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('owner', EntityType::class, [
                'label' => 'User',
                'required' => true,
                'class' => User::class,
                'choice_label' => function (User $user) {
                    $label = '#' . $user->getId() . ' ' . $user->getEmail() . ' ' . $user->getFullName();
                    return $label;
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
