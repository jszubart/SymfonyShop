<?php

namespace App\Command;

use App\Entity\Product;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportProductsCommand extends Command
{
    protected static $defaultName = 'app:export-products';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
             ->setDescription('This command allows you to export chosen products')
             ->addArgument('filename', InputArgument::REQUIRED, 'Input filename')
             ->addArgument('ids', InputArgument::IS_ARRAY, 'Ids of products');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Exporting products...');

        $fileName = $input->getArgument('filename');

        $ids = $input->getArgument('ids');

        $this->makeCsvFileOfProduct($fileName, $ids);

        $io->success('Your products have been exported to '.$fileName.' file !');
    }

        public function makeCsvFileOfProduct($fileName, $ids)
        {
            $product = array();
            if(empty($ids)){
                $products = $this->entityManager->getRepository(Product::class)->findAll();

                foreach ($products as $value) {
                    $product[]= [$value->getId(),$value->getName(),$value->getPrice()];
                }
            } else {
                $products = $this->entityManager->getRepository(Product::class)->findBy(array('id' => $ids));

                foreach ($products as $value) {
                    $product[] = [$value->getId(),$value->getName(),$value->getPrice()];
                }
            }
            if (empty($product)) {
                echo "\nThe records you selected do not exist \n";
                exit;
            }
            $file = fopen('public/assets/csv/' . $fileName . '.csv', 'w');
            fputcsv($file, array('id','name','price'),';');
            foreach ($product as $line) {
                fputcsv($file, $line,';');
            }
            fclose($file);
        }
}