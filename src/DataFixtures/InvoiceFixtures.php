<?php

namespace App\DataFixtures;

use App\Entity\Invoice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class InvoiceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $invoice = new Invoice();
        $invoice->setCompanyName('Company1');
        $invoice->setCompanyStreet('Street1');
        $invoice->setCompanyStreetNumber('17');
        $invoice->setCompanyStreetFlatNumber('500');
        $invoice->setCompanyCity('City1');
        $invoice->setCompanyPostCode('55-555');
        $invoice->setCreated(new \DateTime());
        $invoice->setUpdated(new \DateTime());
        $invoice->setEmail('mkurnaz@cdv.pl');
        $invoice->setPhone('111111111');
        $invoice->setTaxNumber('1111111111');

        $manager->persist($invoice);
        $manager->flush();
    }
}
