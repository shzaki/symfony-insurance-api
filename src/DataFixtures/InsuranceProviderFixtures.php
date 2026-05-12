<?php

namespace App\DataFixtures;

use App\Entity\InsuranceProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class InsuranceProviderFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $providers = [
            ['name' => 'Allianz', 'code' => 'ALL'],
            ['name' => 'AXA', 'code' => 'AXA'],
            ['name' => 'HUK', 'code' => 'HUK'],
        ];

        foreach ($providers as $index => $data) {
            $provider = new InsuranceProvider();

            $provider->setName($data['name']);
            $provider->setCode($data['code']);
            $provider->setIsActive(true);

            $manager->persist($provider);

            $this->addReference('provider_' . $index, $provider);
        }

        $manager->flush();
    }
}
