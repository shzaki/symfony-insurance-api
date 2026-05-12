<?php

namespace App\DataFixtures;

use App\Entity\InsuranceProduct;
use App\Entity\Tariff;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TariffFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $tariffs = [
            ['productReference' => 'product_0', 'name' => 'Basic', 'monthlyPrice' => '18.90', 'coverageAmount' => 250000, 'deductible' => 1000, 'score' => 72],
            ['productReference' => 'product_0', 'name' => 'Comfort', 'monthlyPrice' => '24.90', 'coverageAmount' => 500000, 'deductible' => 500, 'score' => 84],
            ['productReference' => 'product_0', 'name' => 'Premium', 'monthlyPrice' => '34.90', 'coverageAmount' => 1000000, 'deductible' => 250, 'score' => 93],

            ['productReference' => 'product_1', 'name' => 'Basic', 'monthlyPrice' => '21.50', 'coverageAmount' => 300000, 'deductible' => 900, 'score' => 76],
            ['productReference' => 'product_1', 'name' => 'Comfort', 'monthlyPrice' => '29.50', 'coverageAmount' => 750000, 'deductible' => 400, 'score' => 88],
            ['productReference' => 'product_1', 'name' => 'Premium', 'monthlyPrice' => '39.50', 'coverageAmount' => 1200000, 'deductible' => 200, 'score' => 96],

            ['productReference' => 'product_2', 'name' => 'Basic', 'monthlyPrice' => '17.90', 'coverageAmount' => 250000, 'deductible' => 1200, 'score' => 70],
            ['productReference' => 'product_2', 'name' => 'Comfort', 'monthlyPrice' => '25.90', 'coverageAmount' => 600000, 'deductible' => 600, 'score' => 85],
            ['productReference' => 'product_2', 'name' => 'Premium', 'monthlyPrice' => '36.90', 'coverageAmount' => 1000000, 'deductible' => 300, 'score' => 91],

            ['productReference' => 'product_3', 'name' => 'Basic', 'monthlyPrice' => '20.90', 'coverageAmount' => 300000, 'deductible' => 1000, 'score' => 74],
            ['productReference' => 'product_3', 'name' => 'Comfort', 'monthlyPrice' => '28.90', 'coverageAmount' => 700000, 'deductible' => 500, 'score' => 87],
            ['productReference' => 'product_3', 'name' => 'Premium', 'monthlyPrice' => '41.90', 'coverageAmount' => 1250000, 'deductible' => 250, 'score' => 95],

            ['productReference' => 'product_4', 'name' => 'Basic', 'monthlyPrice' => '15.90', 'coverageAmount' => 200000, 'deductible' => 1500, 'score' => 68],
            ['productReference' => 'product_4', 'name' => 'Comfort', 'monthlyPrice' => '22.90', 'coverageAmount' => 500000, 'deductible' => 750, 'score' => 82],
            ['productReference' => 'product_4', 'name' => 'Premium', 'monthlyPrice' => '32.90', 'coverageAmount' => 900000, 'deductible' => 400, 'score' => 90],

            ['productReference' => 'product_5', 'name' => 'Basic', 'monthlyPrice' => '19.90', 'coverageAmount' => 300000, 'deductible' => 1000, 'score' => 73],
            ['productReference' => 'product_5', 'name' => 'Comfort', 'monthlyPrice' => '27.90', 'coverageAmount' => 700000, 'deductible' => 500, 'score' => 86],
            ['productReference' => 'product_5', 'name' => 'Premium', 'monthlyPrice' => '38.90', 'coverageAmount' => 1100000, 'deductible' => 250, 'score' => 94],
        ];

        foreach ($tariffs as $data) {
            $tariff = new Tariff();

            $tariff->setName($data['name']);
            $tariff->setMonthlyPrice($data['monthlyPrice']);
            $tariff->setCoverageAmount($data['coverageAmount']);
            $tariff->setDeductible($data['deductible']);
            $tariff->setScore($data['score']);
            $tariff->setIsActive(true);

            $tariff->setProduct(
                $this->getReference($data['productReference'], InsuranceProduct::class)
            );

            $manager->persist($tariff);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            InsuranceProductFixtures::class,
        ];
    }
}
