<?php
/**
 * Created by PhpStorm.
 * User: steven
 * Date: 01.02.16
 * Time: 23:36
 */

namespace StevenBuehner\Console;

use Data33\ExcelWrapper\ExcelWrapper;
use StevenBuehner\Service\KingOfSatScrapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class KingofsatCommand extends Command {
    protected $excel;

    protected function configure() {
        $this
            ->setName('load')
            ->setDescription('Load from KingOfSat')
            ->addArgument('output-file',
                InputArgument::REQUIRED,
                'Dateipfad in welche die Ausgabe geschrieben werden soll.')
            ->addArgument('url',
                InputArgument::IS_ARRAY,
                'SubUrl to parse');

    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $subUrls = $input->getArgument('url');
        $outputFile = $input->getArgument('output-file');


        // Daten laden
        $kosS = new KingOfSatScrapper();
        $output->writeln('Start Pulling Data');


        if (count($subUrls) > 0) {
            $firstRun = true;
            foreach ($subUrls as $url) {
                $data = $kosS->getTransponderData($url);
                $output->writeln(count($data) . ' Transponders found in ' . $url);

                if (true === $firstRun && count($data) > 0) {
                    $this->setupExcel(array_keys($data[0]));
                    $firstRun = false;
                }

                $this->addRowsToExcel($data);
            }

            // Daten ausgeben
            $output->writeln('Generate Excelfile: ' . $outputFile);
            $this->saveExcel($outputFile);
        }

    }

    protected function setupExcel($headlines) {
        $this->excel = new ExcelWrapper();
        $this->excel->setTitle('Transponders');
        $this->excel->addRow($headlines, 'header');
    }

    protected function addRowsToExcel($rows) {
        foreach ($rows as $d) {
            $this->excel->addRow(array_values($d));
        }
    }

    protected function saveExcel($filename) {
        $this->excel->save($filename);

    }
}