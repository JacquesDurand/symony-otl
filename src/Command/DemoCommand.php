<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:demo')]
class DemoCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption('fail', mode: InputOption::VALUE_NONE);
        $this->addOption('throw', mode: InputOption::VALUE_NONE);
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fail = $input->getOption('fail');
        $throw = $input->getOption('throw');
        if ($fail) {
            return Command::FAILURE;
        }
        if ($throw) {
            throw new \Exception('A command exception occurred');
        }
        return Command::SUCCESS;
    }
}
