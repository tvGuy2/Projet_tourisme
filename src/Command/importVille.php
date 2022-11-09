<?php

namespace App\Command;

use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Reader;
use League\Csv\Statement;



#[AsCommand(
    name: 'app:import-villes-franche-comte',
    description: 'Import villes de Franche-Comté',
    hidden: false,
)]
class ImportVille extends Command
{
    private EntityManagerInterface $manager;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //load the CSV document from a stream
        $stream = fopen('documentation/villes.csv', 'r');
        $csv = Reader::createFromStream($stream);
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

//build a statement
        $stmt = Statement::create();

//query your records from the document
        $records = $stmt->process($csv);
        $i=0;
        foreach ($records as $record) {

            if ($record["Département"]==25 || $record["Département"]==39 || $record["Département"]==70 || $record["Département"]==90){
                $ville = new Ville();
                $ville->setNom($record["Commune"]);
                $ville->setCodePostal($record["Code postal"]);
                $ville->setDepartement($record["Nom département"]);
                $ville->setNumDepartement($record["Département"]);
                $ville->setRegion($record["Région"]);
                $this->manager->persist($ville);


            }

        }
        $this->manager->flush();


        return Command::SUCCESS;

    }
}




