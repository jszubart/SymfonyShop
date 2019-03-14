<?php

namespace App\Command;

use App\Entity\Product;
use League\Csv\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportProductsCommand extends Command
{
    protected static $defaultName = 'app:import-products';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }
    protected function configure()
    {
        $this
            ->setDescription('This command allows you to import products from csv file')
            ->addArgument('filename', InputArgument::REQUIRED, 'Input filename');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Importing products...');

        $fileName = $input->getArgument('filename');

        $csv = Reader::createFromPath('%kernel.project_dir%/../public/assets/csv/'.$fileName.'.csv', 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(';');

        foreach ($csv as $product) {
            $productId = $product['id'];
            $productName = $product['name'];
            $productPrice = $product['price'];
            $product_to_update = $this->entityManager->getRepository(Product::class)->findOneBy([
                'id' => $productId
            ]);
            if (null === $product_to_update){
                $product = (new Product())
                    ->setName($productName)
                    ->setPrice($productPrice);
            } else {
                $product = $this->entityManager->getRepository(Product::class)->find($productId);
                $product->setName($productName);
                $product->setPrice($productPrice);
            }
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }
            $io->success('Imported successfully!');
        }
}
