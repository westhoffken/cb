<?php

namespace App\Classes\Parser;

use App\Classes\Lexer\Definition;
use App\Classes\Lexer\Token;
use App\Classes\Lexer\TokenType;


class Parser
{

    /**
     * @var Token[] $tokens
     */
    protected array $tokens;

    /**
     * @var ArrayStack<Token>
     */
    protected ArrayStack $operatorStack;

    /**
     * var ArrayStack<Token>
     */
    protected ArrayStack $outputStack;

    public function parse(array $tokens)
    {

        $this->outputStack = new ArrayStack();
        $this->operatorStack = new ArrayStack();

        $tokens = $this->filterTokens($tokens);

        $this->tokens = $tokens;

        $this->shuntingYard($tokens);

    }

    /**
     * @param Token[] $tokens
     * @return void
     * @throws \Exception
     */
    private function shuntingYard(array $tokens)
    {

        $lastNode = null;
        $nrOfTokens = count($tokens);
        for ($i = 0; $i < $nrOfTokens; $i++) {
            /** @var Token $token */
            $token = $tokens[$i];
            //sqrt(50.50*50)
            // soeprator stack sqrt ( * )
            // output stack 50.05  50 *
            if ($token->getTokenType() === TokenType::CLOSE_PARENTHESIS) {
                // when you find a closing parentheses we pop until we find the opening and continue
                // And always discard the parentheses
                $this->handleSubExpression();
                dd('testing', $token);
            } elseif (
                $token->getTokenType() === TokenType::OPEN_PARENTHESIS ||
                $token->getTokenType() === TokenType::FUNCTION_NAME
            ) {
                $this->operatorStack->push($token);
            } else {
                // what we need to do is the following
                // do we encouter an operator? then check the precendece
                // if precende higher than last operator, keep it in the stack
                // same precende, pop last one to stack

                // Numbers always go to the output sack so we need a little if check here which is not particullary pretty

                if (
                    !in_array(
                        $token->getTokenType(),
                        [TokenType::REAL_NUMBER, TokenType::POS_INT, TokenType::INTEGER],
                        true
                    )
                ) {
                    $this->operatorStack->push($token);
                    if ($this->operatorStack->peek() === null) {
                        dd($token, $this->operatorStack, $this->outputStack);
                    }
                    // Push it so the while loop can work it out

                    while ($this->lowerPrecendeThan($token, $this->operatorStack->peek())) {
                        dd($this->operatorStack, $token);// * should not be compared to (
//                            dd($token, $this->operatorStack->peek());
                        $poppedToken = $this->operatorStack->pop();
                        // TODO: keep track of left and right and put it proprly
                        $this->outputStack->push($poppedToken);
                    }


                } else {

                    $this->outputStack->push($token);
                }


            }

            $lastNode = $token;
        }
        dd($this->operatorStack, $this->outputStack);

        // Process any remaining operators
        while (!$this->operatorStack->isEmpty()) {
            $poppedToken = $this->operatorStack->pop();
            // TODO: keep track of left and right and put it proprly
            $this->outputStack->push($poppedToken);
        }
        // TODO check for stack? we dont wish to calculate the sum yet
        dd($this->outputStack);
    }

    /**
     * @param Token $currentToken
     * @param Token $lastTokenInStack
     * @return bool
     */
    private
    function lowerPrecendeThan(
        Token $currentToken,
        ?Token $lastTokenInStack = null
    ): bool {
        if (!$lastTokenInStack) {
            return false;
        }
        if ($currentToken->getPrecedence() < $lastTokenInStack->getPrecedence()) {
            return true;
        }
        if ($currentToken->getPrecedence() > $lastTokenInStack->getPrecedence()) {
            return false;
        }

        if ($currentToken->getAssoctiotivity() === Definition::LEFT_ASOC) {
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
        dd($this->operatorStack);
        while ($poppedToken = $this->operatorStack->pop()) {
            if ($poppedToken->getTokenType() === TokenType::OPEN_PARENTHESIS) {
                $cleanSum = true;
                // TODO: pop it and let it go
                $this->operatorStack->pop();
                break;
            }
            dump('popped token', $poppedToken);

            $this->outputStack->push($poppedToken);

            if (!$cleanSum) {
                throw new \Exception('Mismatching parentheses');
            }

            // should be all done now since we will write an evaluater later on and calculate properly
        }

    }

    protected
    function filterTokens(
        array $tokens
    ): array {
        //Filter out any unwanted whitespaces
        $filteredTokens = array_filter($tokens, function (Token $t) {
            return $t->getTokenType() !== TokenType::WHITESPACE;
        });

        // Return the array values only, because array_filter preserves the keys
        return array_values($filteredTokens);
    }


}
