<?php
/**
 * Copyright © 2009-2016 Vaimo Group. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vaimo\GenerateProductXML\Console;

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
        $options = [
            new InputOption(
                'filter',
                null,
                InputOption::VALUE_OPTIONAL,
                "Multiple filter strings should be seperated by ;\r\n".
                "More paramters see http://makeup-api.herokuapp.com/"
            )
        ];
        $this->setName(self::COMMAND_RUN);
        $this->setDefinition($options);
        $this->setDescription('Generate product XML file for importing');
        parent::configure();
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
        $filter = trim($input->getOption('filter'));
        $message = '<comment>%s</comment>';
        $output->writeln(sprintf($message,'Generating product files...'));
        //TODO accept some words as filter for Reader class
        $product = $this->getObjectManager()->get('Vaimo\GenerateProductXML\Model\Product');
        $product->execute($output, $filter);

        $output->writeln(sprintf($message,'Done'));
        return true;
    }
}
