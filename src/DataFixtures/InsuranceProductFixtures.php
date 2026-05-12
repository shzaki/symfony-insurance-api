<?php

namespace App\DataFixtures;

use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InsuranceProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            [
                'name' => 'Standard Building Insurance',
                'type' => 'building',
                'description' => 'Standard residential building insurance for common risks.',
                'providerReference' => 'provider_0',
                'reference' => 'product_0',
            ],
            [
                'name' => 'Premium Building Insurance',
                'type' => 'building',
                'description' => 'Premium residential building insurance with extended coverage.',
                'providerReference' => 'provider_0',
                'reference' => 'product_1',
            ],
            [
                'name' => 'AXA Home Protect',
                'type' => 'building',
                'description' => 'Building protection package for homeowners.',
                'providerReference' => 'provider_1',
                'reference' => 'product_2',
            ],
            [
                'name' => 'AXA Home Protect Plus',
                'type' => 'building',
                'description' => 'Extended building protection package for homeowners.',
                'providerReference' => 'provider_1',
                'reference' => 'product_3',
            ],
            [
                'name' => 'HUK Building Basic',
                'type' => 'building',
                'description' => 'Affordable basic building insurance.',
                'providerReference' => 'provider_2',
                'reference' => 'product_4',
            ],
            [
                'name' => 'HUK Building Comfort',
                'type' => 'building',
                'description' => 'Comfort-level building insurance with stronger protection.',
                'providerReference' => 'provider_2',
                'reference' => 'product_5',
            ],
        ];

        foreach ($products as $data) {
            $product = new InsuranceProduct();

            $product->setName($data['name']);
            $product->setType($data['type']);
            $product->setDescription($data['description']);
            $product->setIsActive(true);

            $product->setProvider(
                $this->getReference($data['providerReference'], InsuranceProvider::class)
            );

            $manager->persist($product);

            $this->addReference($data['reference'], $product);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            InsuranceProviderFixtures::class,
        ];
    }
}
