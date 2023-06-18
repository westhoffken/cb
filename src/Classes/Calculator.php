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
     * @param string $sum
     */
    public function __construct(string $sum)
    {
        $this->sum = $sum;
    }


    public function calculate()
    {

        // TODO: add try catch
        return (new ExpressionLanguage())->evaluate($this->sum);
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

    /**
     *
     * @param int $key
     * @param string $command
     * @return void
     */
    private function addMissingOperatorsInCommands(int $key, string $command): void
    {

        // TODO: see if it concerns a sqrt, then call a method that recursively checks if it has () then call method again for squirt

        preg_match_all('/\(([^()]*)\)/', $command, $matches);

        // Het idee is om het diepste niveau te kijken naar eventuele sommen die geen operators hebben en deze automatisch toe te voegen
        if (isset($matches[0])) {
            $this->addMissingOperatorsInCommands($key, $command);
        } else {
            // Niet elk command in de reeks moet voorzien worden van een command, begint het met een '(" dan mag er gekeken worden!
            // Dit hadden we ook kunnen oplossen met een substr, maar ik ben nu het hele document bezig met regex
            preg_match_all('/\(/', $command, $matches);
            if (isset($matches[0]) && OperatorUtils::needsOperatorBefore($key, $this->commands)) {

                //operator in stack
                $this->commands[$key] = '*' . $command;
            }
            preg_match_all('/\)/', $command, $matches);

            if (isset($matches[0]) && OperatorUtils::needsOperatorAfter($key, $this->commands)) {

                $this->commands[$key] .= '*';
            }
        }


        dump($this->commands);

        dump($matches);
    }


}
