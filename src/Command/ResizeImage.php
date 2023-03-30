<?php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Aspera\Spreadsheet\XLSX\Reader;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'app:resize-image')]
class ResizeImage extends Command
{
    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file-path');

        $reader = new Reader();
        $reader->open($filePath);


        foreach ($reader as $index => $row) {
            foreach ( $row as $key => $value ) {
                $pattern = '/^https/';
                $saveDir = $input->getArgument('output-dir');
                if(is_writable($saveDir)){
                    if(preg_match($pattern, $value)){

                        $img = Image::make($value)->resize(600,600);
                        $urlSplit = explode('/', $value);
                        $totalSplits = count($urlSplit);
                        $imageName = $urlSplit[$totalSplits - 1];
                        $imagePath = $saveDir . '/' . $imageName;

                        if(!file_exists($imagePath)){
                            $img->save($imagePath);
                        }
                    }
                } else {
                    print_r('Directory not writable');
                }
                
            }
        }        

        $reader->close();
        return Command::SUCCESS;
    }

    /**
     * Add arguments to the command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('file-path', InputArgument::REQUIRED, 'Path to xlsx file');

        $this->addArgument('output-dir', InputArgument::REQUIRED, 'Path to save directory');

    }
}