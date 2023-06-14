<?php

namespace App\Classes;

use App\Utils\OperatorUtils;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 *
 */
class Calculator
{

    /**
     * @var string
     */
    private string $sum;
    /**
     * @var array
     */
    private array $commands;


    /**
     * @param string $sum
     * @param array $commands
     */
    public function __construct(string $sum, array $commands)
    {
        $this->sum = $sum;
        $this->commands = $commands;
    }


    public function calculate()
    {

        // CLean up gaat eerst door de array heen om te kijken of we niet beginnen en eindigen met operators
        $this->cleanUp();
        dump($this->commands);
        // Parse all commands into a proper sum
        $this->parseCommands();

        return (new ExpressionLanguage())->evaluate($this->sum);
    }

    /**
     * @return void
     */
    private function parseCommands(): void
    {
        foreach ($this->commands as $key => $command) {
            $this->parseSqrt($key, $command);

        }
        // implode maakt van een array een string en in dit geval een die ik kan gebruiken voor de som
        $this->sum = implode($this->commands);
    }

    /**
     * No matches no glory !
     * @param int $key
     * @param string $item
     * @return void
     */
    private function parseSqrt(int $key, string $item): void
    {
        // Omdat square root niet onderdeel uitmaakt van de evaluater die ik gebruik moet deze eerst omgezet worden naar een getal


        if (preg_match('/sqrt\((\d+(,\d+)?)\)/', $item, $matches)) {
            // We have a match, now we just want to find the number, so we can calculate it properly
            preg_match('/\d+(,\d+)?/', $matches[0], $number);

            $squirt = sqrt($number[0]);
            // Het idee hierachter is om te kijken of er nog een operator voor of na moet

            // Ik kijk hier eerst of er uberhaupt een eerder array item is en plak er dan iets aanvast als het niet een operator betref
            if (OperatorUtils::needsOperatorBefore($key, $this->commands)) {
                //operator in stack
                $squirt = '*' . $squirt;
            }
            // Nu ga ik kijken of er na deze square root een operator zit en zo niet dan altijd * doen
            if (OperatorUtils::needsOperatorAfter($key, $this->commands)) {
                //operator in stack
                $squirt .= '*';
            }
            $this->commands[$key] = preg_replace('/sqrt\((\d+(,\d+)?)\)/', $squirt, $item);
        }
    }


    /**
     * @return void
     */
    private function cleanUp(): void
    {
        // Om er voor te zorgen dat een som niet begint met een operator doen wij een clean up
        $previousItem = null;
        // Om errors te voorkomen met de keys en items in de originele array, plak ik eerst even alle allowed data in een nieuwe array
        // Ik vervang daarna de cleaned up array en ga dan verder met het parsen
        $newCommands = [];
        foreach ($this->commands as $command) {
            $previousItem = $command;
            if (in_array($previousItem, OperatorUtils::ALLOWED_OPERATORS, true)) {
                continue;
            }
            $newCommands[] = $command;
        }
        $this->commands = $newCommands;
    }


}
