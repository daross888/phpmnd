<?php

namespace Povils\PHPMND;

use JakubOnderka\PhpConsoleColor\ConsoleColor;
use JakubOnderka\PhpConsoleHighlighter\Highlighter;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Printer
 *
 * @package Povils\PHPMND
 */
class Printer
{
    const LINE_LENGTH = 80;
    const TAB = 4;

    /**
     * @var FileReport[]
     */
    private $fileReports = [];

    /**
     * @param FileReport $fileReport
     */
    public function addFileReport(FileReport $fileReport)
    {
        $this->fileReports[] = $fileReport;
    }

    /**
     * @param OutputInterface $output
     * @param HintList        $hintList
     */
    public function printData(OutputInterface $output, HintList $hintList)
    {
        $separator = str_repeat('-', self::LINE_LENGTH);
        $output->writeln(PHP_EOL . $separator . PHP_EOL);

        $total = 0;
        foreach ($this->fileReports as $fileReport) {
            $entries = $fileReport->getEntries();
            $total += count($entries);
            foreach ($entries as $entry) {
                $output->writeln(sprintf(
                    '%s:%d. Magic number: %s',
                    $fileReport->getFile()->getRelativePathname(),
                    $entry['line'],
                    $entry['value']
                ));

                $highlighter = new Highlighter(new ConsoleColor());
                $output->writeln($highlighter->getCodeSnippet($fileReport->getFile()->getContents(), $entry['line'], 0, 0));

                if ($hintList->hasHints()) {
                    $hints = $hintList->getHintByValue($entry['value']);
                    if (false === empty($hints)) {
                        $output->writeln('Suggestions:');
                        foreach ($hints as $hint) {
                            $output->writeln(str_repeat(' ', 2 * self::TAB) . $hint);
                        }
                        $output->write(PHP_EOL);
                    }
                }

            }
            $output->writeln($separator . PHP_EOL);
        }
        $output->writeln('<info>Total of Magic Numbers: ' . $total . '</info>');
    }
}
