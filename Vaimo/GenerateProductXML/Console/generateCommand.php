<?php
/**
 * Copyright © 2009-2016 Vaimo Group. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vaimo\GenerateProductXML\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
//        parent::configure();
        $this->setName(self::COMMAND_RUN);
        $this->setDescription('Generate product XML file for importing');
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
        $message = '<comment>%s</comment>';
        $output->writeln(sprintf($message,'Generating product files...'));
        //TODO accept some words as filter for Reader class
        $product = $this->getObjectManager()->get('Vaimo\GenerateProductXML\Model\Product');
        $product->execute($output);

        $output->writeln(sprintf($message,'Done'));
        return true;
    }
}
