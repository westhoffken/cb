<?php

namespace App\Classes\Evaluator;

use App\Classes\Evaluator\Stack\ResultStack;
use App\Classes\ShuntingYard\Lexer\Token;
use App\Classes\ShuntingYard\Lexer\TokenType;
use App\Classes\Stack\OutputStack;
use App\Exception\MalformedSumException;
use App\Exception\NoSumException;
use App\Exception\NotImplementedException;

/**
 * Basic class that performs the actuall calculation
 */
class Evaluator
{

    /**
     * @var OutputStack
     */
    private OutputStack $outputStack;

    /**
     * @var ResultStack
     */
    private ResultStack $resultStack;

    /**
     * @param OutputStack $outputStack
     */
    public function __construct(OutputStack $outputStack)
    {
        $this->outputStack = $outputStack;
        // Introducing a new stack to help us perform a bunch of stuff
        $this->resultStack = new ResultStack();
    }

    /**
     * Evaluates the output stack and does some math
     * always return as float even if it is not a float. this is tp prevent and dumb rounding shit
     * if the application accidentally casts to int when it is a float
     * @return float
     * @throws MalformedSumException
     * @throws NoSumException
     * @throws NotImplementedException
     */
    public function evaluate(): float
    {
        // forloop because im not directly working with an array
        // Could implement a toArray() method but this works just as fine
        for ($i = 0; $i < $this->outputStack->length(); $i++) {

            $token = $this->outputStack->showAtIndex($i);

            // If it concerns a number token type we push it to the stack until we encounter function or operator
            if (in_array(
                $token->getTokenType(),
                [TokenType::REAL_NUMBER, TokenType::POS_INT, TokenType::INTEGER],
                true
            )
            ) {
                // Numbers always get pushed
                $this->resultStack->push($token->getValue());

            } elseif (
                in_array(
                    $token->getTokenType(),
                    [
                        TokenType::DIVISION_OPERATOR,
                        TokenType::MULTIPLY_OPERATOR,
                        TokenType::EXP_OPERATOR,
                        TokenType::ADDITION_OPERATOR,
                        TokenType::SUBTRACTION_OPERATOR
                    ],
                    true
                )
            ) {
                // Non numbers need to be pushed as well, but we also calculate them against the last 2
                // numbers on the stack


                // we need two numbers to perform an operations against, lets make sure there are 2
                if ($this->resultStack->length() <= 1) {
                    throw new MalformedSumException('Malformed sum, cannot perform RPN');
                }

                $this->handleCalculation($token);

            } elseif ($token->getTokenType() === TokenType::FUNCTION_NAME) {
                // Handle any math functions according
                $this->handleFunctionCalculation($token);
            }
        }

        // The result stack should only have 1 result left and thats the calculated sy
        if ($this->resultStack->length() !== 1) {
            throw new NoSumException('Sum could not be calculated properly ');
        }

        return $this->resultStack->getResult();
    }

    /**
     * This only houses a few basic math function because i have little time to implement all!
     * @param Token $token
     * @return void
     * @throws NotImplementedException
     */
    private function handleFunctionCalculation(Token $token): void
    {
        // TODO: add more mathemtical functions
        $number1 = $this->resultStack->pop();

        switch ($token->getMatch()) {
            case 'sqrt':
                $result = sqrt($number1);
                $this->resultStack->push($result);
                break;
            case 'sin':
                // Calculator doesn't take degrees or radiants, so we convert it, so it calculates properly
                $result = sin(deg2rad($number1));
                $this->resultStack->push($result);
                break;
            case 'pi':
                $result = M_PI;
                // I push the number back because i don't need it ot be calculatd since pi is different from
                // the other functions
                $this->resultStack->push($number1);
                $this->resultStack->push($result);
                break;
            default:
                throw new NotImplementedException('Math function is not implemented yet');
        }

    }

    /**
     * @param Token $token
     * @return void
     */
    private function handleCalculation(Token $token): void
    {
        $number1 = $this->resultStack->pop();

        $number2 = $this->resultStack->pop();


        switch ($token->getTokenType()) {
            case TokenType::SUBTRACTION_OPERATOR:
                $result = $number2 - $number1;
                dump('Result: ' . $result);
                $this->resultStack->push($result);
                break;
            case TokenType::ADDITION_OPERATOR:
                $result = $number2 + $number1;
                dump('Result: ' . $result);
                $this->resultStack->push($result);
                break;
            case TokenType::DIVISION_OPERATOR:
                // Let's keept it real, this is just... don't do it
                if ($number2 === 0) {
                    throw new \DivisionByZeroError();
                }
                $result = $number2 / $number1;
                dump('Result: ' . $result);
                $this->resultStack->push($result);
                break;
            case TokenType::MULTIPLY_OPERATOR:
                $result = $number2 * $number1;
                dump('Result: ' . $result);
                $this->resultStack->push($result);
                break;
            case TokenType::EXP_OPERATOR:
                $result = $number2 ** $number1;
                dump('Result: ' . $result);
                $this->resultStack->push($result);
                break;
            default:
                // I don't expect to ever reach here, but just in case
                throw new NotImplementedException('Operator not implemented');

        }
    }

}
