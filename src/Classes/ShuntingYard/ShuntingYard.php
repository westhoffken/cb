<?php

namespace App\Classes\ShuntingYard;

use App\Classes\ShuntingYard\Lexer\Definition;
use App\Classes\ShuntingYard\Lexer\Token;
use App\Classes\ShuntingYard\Lexer\TokenType;
use App\Classes\Stack\OperatorStack;
use App\Classes\Stack\OutputStack;

class ShuntingYard
{
    /**
     * @var Token[] $tokens
     */
    protected array $tokens;

    /**
     * @var OperatorStack<Token>
     */
    protected OperatorStack $operatorStack;

    /**
     * var OutputStack<Token>
     */
    protected OutputStack $outputStack;

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
        $this->outputStack = new OutputStack();
        $this->operatorStack = new OperatorStack();

    }

    /**
     * I have to admit it is not as pretty as i want it, but it gets the job done
     * @throws \Exception
     */
    public function shuntingYard(): OutputStack
    {
        // loop through all tokens and handle them accordingly
        foreach ($this->tokens as $token) {


            if ($token->getTokenType() === TokenType::CLOSE_PARENTHESIS) {
                // when you find a closing parentheses we pop until we find the opening and continue
                // And always discard the parentheses because shunting yard means to eleminate parentheses
                $this->handleSubExpression();

            } elseif (
                $token->getTokenType() === TokenType::OPEN_PARENTHESIS ||
                $token->getTokenType() === TokenType::FUNCTION_NAME
            ) {
                // an opening '(' means we eitehr have a new function or a new expression to be handles
                // Always push to the stack
                $this->operatorStack->push($token);

            } elseif (
                !in_array(
                    $token->getTokenType(),
                    [TokenType::REAL_NUMBER, TokenType::POS_INT, TokenType::INTEGER],
                    true
                )
            ) {
                // token is not a number and should be processed according to the laws of anything that is not a token
                if ($this->lowerPrecendeThan($token, $this->operatorStack->peek())) {

                    // precende tells us that the token needs to be popped from the operator stack
                    // and needs to be added to the output stack
                    $poppedToken = $this->operatorStack->pop();
                    $this->outputStack->push($poppedToken);

                }
                // after handling the precende and comparing with the last item in the stack
                // we still need to add the token to the operator stack
                $this->operatorStack->push($token);
            } else {
                // Anything else will just be pushed to the stack
                $this->outputStack->push($token);
            }

        }


        $this->processRemainingStack();

        return $this->outputStack;

    }

    /**
     * @return void
     */
    private function processRemainingStack(): void
    {
        // Process any remaining operators and put them in the output stack
        while (!$this->operatorStack->isEmpty()) {
            $poppedToken = $this->operatorStack->pop();
            $this->outputStack->push($poppedToken);
        }
    }

    /**
     * Precende according to the rules of shunting yard
     * @param Token $currentToken
     * @param Token|null $lastTokenInStack
     * @return bool
     */
    private function lowerPrecendeThan(
        Token $currentToken,
        ?Token $lastTokenInStack = null
    ): bool {

        // These if's could be combined a bit more, but in my opinion that affects the readability

        if (!$lastTokenInStack || $currentToken->getPrecedence() > $lastTokenInStack->getPrecedence()) {
            return false;
        }

        // To make sure right evaluated operators don't pop eachother out
        if (
            $currentToken->getPrecedence() < $lastTokenInStack->getPrecedence() ||
            $currentToken->getAssoctiotivity() !== Definition::RIGHT_ASOC
        ) {
            return true;
        }

        return false;

    }


    /**
     * @return void
     * @throws \Exception
     */
    private function handleSubExpression(): void
    {
        $cleanSum = false;

        while ($poppedToken = $this->operatorStack->pop()) {
//            dump('Parsing token until we find opening: ' . $poppedToken->getMatch());
            if ($poppedToken->getTokenType() === TokenType::OPEN_PARENTHESIS) {
//                dump('found matching opening');
                $cleanSum = true;

                break;
            }

            $this->outputStack->push($poppedToken);


            // should be all done now since we will write an evaluater later on and calculate properly
        }
        // Mismatching parentheses are a bitch
        if (!$cleanSum) {
            throw new \Exception('Mismatching parentheses');
        }

        // We need to make sure we don't handle a ( without the proper function that goes along with it
        // To prevent this we pop the function if the previous token in the current operator start is a function name
        // for example: max ( should pop '(' and 'max' to make the notation right
        $previous = $this->operatorStack->peek();
        if ($previous && $previous->getTokenType() === TokenType::FUNCTION_NAME) {
            $poppedToken = $this->operatorStack->pop();
            $this->outputStack->push($poppedToken);
        }
    }

}
