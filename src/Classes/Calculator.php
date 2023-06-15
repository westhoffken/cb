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
    private ExpressionLanguage $evaluater;


    /**
     * @param string $sum
     * @param array $commands
     */
    public function __construct(string $sum, array $commands)
    {
        $this->sum = $sum;
//        $this->commands = $commands;
        $this->commands = [
            "sqrt(50.50*(sqrt(50 * sqrt(50))))",
        ];
        $this->evaluater = new ExpressionLanguage();
    }


    public function calculate()
    {


        //TODO: recerusive function that solves any regex on teh deepest lever in a single command
//
        // Revursively call the command and check if there are opperators needed
        // CLean up gaat eerst door de array heen om te kijken of we niet beginnen en eindigen met operators
//        $this->cleanUp();

        dump($this->commands);
//        // Parse all commands into a proper sum
        $this->parseCommands();
        dump($this->commands);

        return (new ExpressionLanguage())->evaluate('10 * (10 + 10*(100 * 100 - 20))');
    }

    private function simpleSum(int $key, string $command): void
    {
        // Wat hier de bedoeling is, is een methode die kijkt of er een simpele som aanwezig is en deze oplost
        if (preg_match('/-?\d+(\.\d+)?\h*[-+*\/]\h*-?\d+(\.\d+)?/', $command, $sum)) {
            dump($sum[0]);

            // Vervang de waarde van de som waar we op dit moment mee bezig zijn en ga verder
            $this->commands[$key] = str_replace($sum[0], $this->evaluater->evaluate($sum[0]), $this->commands[$key]);
        }

    }

    private function squirt(int $key, string $command)
    {
        // Het lijkt heel veel, maar ik wil graag in de som kijken op diepste nivuea of er nog ergens een ({getal}) stata
        // wat tussenhaakjes staat en verder geen zaken erbij heeft. in dit geval kan ik de haakjes namelijk verwijderen
        if (preg_match('/([*\/+-])\((\d+(\.\d+))\)/', $this->commands[$key], $cleanedUpSum)) {
            dump($cleanedUpSum);
            $this->commands[$key] = str_replace($cleanedUpSum[0], $cleanedUpSum[1] . $cleanedUpSum[2], $this->commands[$key]);
        }
        // TODO: change the order, the logic is in the place but the order sucks xD
        $this->simpleSum($key, $this->commands[$key]);


        // so i need to figure that out
        // This is fun and all but yolo xD
        // SOmehow we need to keep track of the command we are working on and calculate stuff without sqrt
        if (preg_match('/sqrt\(([^()]*)\)/', $command, $matches)) {


            dump($matches);
            preg_match('/\d+(,\d+)?/', $matches[0], $number);


            // maar hoe ga ik nu het nummer vervangen en de som opnieuw laten draaien. dan moet het command aangepast zijn
            $squirt = sqrt($number[0]);
            dump('matched: ' . $matches[0][0]);
            dump('calculate:' . $squirt);
            // What we also need to do is remove ( and ) because it just became a sperate item
            $this->commands[$key] = str_replace($matches[0], $squirt, $this->commands[$key]);


            //Het ziet er misschien wat gek uit, maar bovenstaande regex rekent alle haakjes
            // Als matches 1,0 gebruik voor het uitlezen van een srt(*) operation dan krijg ik een haakje teveel mee
            // Om dat te voorkomen doe ik nog een extra filter en pas ik daar de som op toe
            dump($this->commands);
            $this->squirt($key, $this->commands[$key]);
        }

        dump($this->commands);
        // so there is no more single squirt, now lets find all parenteheses and solve those
        die();

    }

    /**
     * @return void
     */
    private function parseCommands(): void
    {
        foreach ($this->commands as $key => $command) {
            $this->squirt($key, 'sqrt(50.50(sqrt(50)))');
//            $this->addMissingOperatorsInCommands($key, $command);
//            $this->parseSqrt($key, $command);

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
