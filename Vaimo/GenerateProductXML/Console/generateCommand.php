<?php
/**
 * Copyright © 2009-2016 Vaimo Group. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vaimo\GenerateProductXML\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class generateCommand extends AbstractCommand
{
    const COMMAND_RUN = 'product:xml:generate';

    /**
     * @var string
     */
    protected $type;

    /**
     * Configure the CLI command
     */
    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND_RUN);
        $this->setDescription('Generate product XML file for importing');
//        $this->addArgument(
//            'type', InputArgument::OPTIONAL,
//            'Which type of product need to be created,[simple/configurable:attribute]',
//            'simple');
        $this->addOption(
            'filter',
            'f',
            InputOption::VALUE_OPTIONAL,
            "Multiple filter strings should be seperated by ;\r\n".
            "More paramters see http://makeup-api.herokuapp.com/"
        );
        $this->addOption(
            'numbers',
            't',
            InputOption::VALUE_OPTIONAL,
            "Maximum records to be picked up"
        );

    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $type_list = ['simple','configurable','virtual','bundle'];
//        list($type, $attributes) = explode(':',$input->getArgument('type'));
//        if( !in_array($type, $type_list) ) {
//            $output->writeln(sprintf(
//                '<error>%s</error>',
//                'Argument type should be one of '.\join('/',$type_list).'...')
//            );
//            exit;
//        } else {
//            if($type == 'configurable') {
//                if($attributes == '') {
//                    $output->writeln(sprintf(
//                            '<error>%s</error>',
//                            'Argument attributes should not be empty...')
//                    );
//                    exit;
//                }
//            }
//        }
        $message = '<comment>%s</comment>';
        $filter = trim($input->getOption('filter'));
        $numbers = (int) $input->getOption('numbers');
//        $argv = ['filter'=>$filter, 'numbers'=>$numbers,'type'=>$type, 'attributes'=>$attributes];
        $argv = ['filter'=>$filter, 'numbers'=>$numbers];
        $output->writeln(sprintf($message,'Generating product files...'));

        $product = $this->getObjectManager()->get('Vaimo\GenerateProductXML\Model\Product');
        $product->execute($output, $argv);

        $output->writeln(sprintf($message,'Done'));
        return true;
    }
}
