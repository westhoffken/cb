<?php

namespace App\Classes\RPN;

use App\Classes\RPN\Stack\ResultStack;
use App\Classes\ShuntingYard\Lexer\Token;
use App\Classes\ShuntingYard\Lexer\TokenType;
use App\Classes\Stack\OutputStack;

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

    //Returns the evaluated sum and thus the answer
    public function evaluate()
    {

        for ($i = 0; $i < $this->outputStack->length(); $i++) {

            $token = $this->outputStack->showAtIndex($i);
            dump('Parsing token: ', $token);
            // If it concerns a number token type we push it to the stack until we encounter function or operator
            if (in_array(
                $token->getTokenType(),
                [TokenType::REAL_NUMBER, TokenType::POS_INT, TokenType::INTEGER],
                true
            )
            ) {
                // push to stack!
                $this->resultStack->push($token->getValue());
                dump('encountered number token: ', $this->resultStack);
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
                // we need two numbers to perform an operations against, lets make sure there are 2
                if ($this->resultStack->length() <= 1) {
                    dd($this->resultStack);
                    throw new \Exception('Malformed sum, cannot perform RPN');
                }
                dump('operator token, calculating');

                $this->handleCalculation($token);
//                dump('After : ', $this->resultStack);

            } elseif ($token->getTokenType() === TokenType::FUNCTION_NAME) {
//                dump('Handling func. calc.');
                $this->handleFunctionCalculation($token);
//                dump('After func.: ', $this->resultStack);
            }
        }
        if ($this->resultStack->length() !== 1) {
            throw new \Exception('Sum could not be calculated properly ');
        }
//        dd($this->resultStack->getResult());
        return $this->resultStack->getResult();
    }

    /**
     * @param Token $token
     * @return void
     */
    private function handleFunctionCalculation(Token $token): void
    {
//        dump('simple cacl', $this->resultStack);
        $number1 = $this->resultStack->pop();

        switch ($token->getMatch()) {
            case 'sqrt':

                $result = sqrt($number1);
//                dump('Result: ' . $result);
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
                // error
                break;
        }

//        dump($this->resultStack);  $number1 = $this->resultStack->pop();
    }

    /**
     * @param Token $token
     * @return void
     */
    private function handleCalculation(Token $token): void
    {
        $number1 = $this->resultStack->pop();
        dump('Number stack: ', $this->resultStack);
        $number2 = $this->resultStack->pop();
        dump('calucalting: ' . $number1 . ' and ' . $number2);

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
                // do nothing
                break;
        }
    }

}
