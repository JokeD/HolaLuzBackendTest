<?php

declare(strict_types = 1);

namespace App\Ui\Console;


use App\Application\GetReadingsFromStreamUseCase;
use App\Application\GetSuspiciousReadingsUseCase;
use App\Domain\ParserFactory;
use App\Infrastructure\FileFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SuspiciousReadingsCommand extends Command
{
    protected static $defaultName = 'suspicious:readings';
    protected static $defaultDescription = 'Get suspicious readings based on files placed inside the storage/ folder';

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'File name is required');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        try {

            $getReadingsFromStreamUseCase = new GetReadingsFromStreamUseCase();

            $readings = $getReadingsFromStreamUseCase->execute(
                ParserFactory::parse(new FileFetcher($input->getArgument('file')))
            );

            $suspiciousReadings = (new GetSuspiciousReadingsUseCase())->execute($readings);

            $table = new Table($output);
            $tableStyle = new TableStyle();
            $tableStyle->setPadType(STR_PAD_BOTH);

            $table->setHeaders(
                ['Client', 'Month', 'Suspicious', 'Median', '% Readings deviation against customer median'])
                ->setRows($suspiciousReadings);

            $table->setStyle($tableStyle)->render();

        } catch (\RuntimeException $exception) {
            $errorInfo = $exception->getMessage() . PHP_EOL . $exception->getTraceAsString();
            $output->writeln("<error>$errorInfo</error>");
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }
}