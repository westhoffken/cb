<?php

namespace App\Classes;

use App\Utils\OperatorUtils;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;

class CommandParser
{

    private array $commands;
    private ExpressionLanguage $evaluater;

    public function __construct(Request $request)
    {
        $content = json_decode($request->getContent());
//        $this->commands = $content->commands;
        $this->evaluater = new ExpressionLanguage();
        $this->commands = [
            "sqrt(50.50*(sqrt(50 * sqrt(50))*50+50-20))",
        ];
    }

    /**
     * Method returns parsed commands into a sum that can be calculated
     * @return string
     */
    public function parse(): string
    {
        foreach ($this->commands as $key => $command) {
            $this->squirt($key, $command);
//            $this->addMissingOperatorsInCommands($key, $command);
//            $this->parseSqrt($key, $command);

        }
        // implode maakt van een array een string en in dit geval een die ik kan gebruiken voor de som
        return implode($this->commands);
    }

    /**
     * @param int $key
     * @param string $command
     * @return void
     */
    private function simpleSum(int $key, string $command): void
    {
        dump('entered simpleSum');
        // Wat hier de bedoeling is, is een methode die kijkt of er een simpele som aanwezig is en deze oplost
        if ($sum = OperatorUtils::isSimpleSum($command)) {
            dump($sum[0]);
            // Vervang de waarde van de som waar we op dit moment mee bezig zijn en ga verder
            $this->commands[$key] = str_replace($sum[0], $this->evaluater->evaluate($sum[0]), $this->commands[$key]);
        }
    }

    /**
     * @param int $key
     * @param string $command
     * @return void
     */
    private function sumInParentheses(int $key, string $command): void
    {
        dump('entered sumInParentheses');
        if (preg_match('/([*\/+-])\((\d+(\.\d+))\)/', $command, $cleanedUpSum)) {
            dump($cleanedUpSum);

            $this->commands[$key] = str_replace($cleanedUpSum[0], $cleanedUpSum[1] . $cleanedUpSum[2], $this->commands[$key]);
        }

    }

    private function squirt(int $key, string $command)
    {
        dump('entered squirt');
        // So firstly we wish to check if there is a squirt
        // then sovle that squirt which results in a calculated number with parenetheses
        // if the quirt contains another sum, fix that  !
        // CHeck for squirt again and repeat the whole proces of checking for a simple sum
        $this->simpleSum($key, $this->commands[$key]);
        $this->sumInParentheses($key, $this->commands[$key]);

        // Only a check on sqrt is enough to make us loop again
        dump('counting simple sums: ' . count(OperatorUtils::isSimpleSum($this->commands[$key])));
        if (str_contains($command, 'sqrt') && count(OperatorUtils::isSimpleSum($this->commands[$key])) === 0) {
            $this->squareRoot($key, $this->commands[$key]);
            // How to stop it ?
            $this->squirt($key, $this->commands[$key]);
        }

        // WHY OF WHY< because i said so.
        if (str_contains($command, 'sqrt') && count(OperatorUtils::isSimpleSum($this->commands[$key])) !== 0) {
            $this->simpleSum($key, $this->commands[$key]);
            $this->squirt($key, $this->commands[$key]);
        }

        dump($this->commands);
        // so there is no more single squirt, now lets find all parenteheses and solve those
        die();

    }

    /**
     * @param int $key
     * @param string $command
     * @return void
     */
    private function squareRoot(int $key, string $command): void
    {
        dump('entered squareRoot');
        // if we encouter a simple sum, the do thatfirst
        // almost there, there is a simple sum hidden sometimes and we need to fix that
        if (preg_match('/sqrt\(([^()]*)\)/', $command, $matches)) {
            dump('working on sqrt: ' . $command, $matches);

            dump($matches);
            preg_match('/\d+(,\d+)?(\.\d+)?/', $matches[0], $number);

            dump('working on number: ', $number);
            // maar hoe ga ik nu het nummer vervangen en de som opnieuw laten draaien. dan moet het command aangepast zijn
            $squirt = sqrt($number[0]);
            dump('matched: ' . $matches[0][0]);
            dump('calculate:' . $squirt);

            $this->commands[$key] = str_replace($matches[0], $squirt, $this->commands[$key]);
            dump($this->commands);

        }
    }

}
