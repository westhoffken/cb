<?php

namespace App\Classes\Parser;

use App\Classes\Lexer\Definition;
use App\Classes\Lexer\Token;
use App\Classes\Lexer\TokenType;
use App\Classes\Parser\Stack\OperatorStack;
use App\Classes\Parser\Stack\OutputStack;


class Parser
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

    public function parse(array $tokens)
    {

        $this->outputStack = new OutputStack();
        $this->operatorStack = new OperatorStack();

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

        $nrOfTokens = count($tokens);
        for ($i = 0; $i < $nrOfTokens; $i++) {
            /** @var Token $token */
            $token = $tokens[$i];

            dump('parsing token with operator: ' . $token->getMatch());
            //sqrt(50.50*50)
            // soeprator stack sqrt ( * )
            // output stack 50.05  50 *
            if ($token->getTokenType() === TokenType::CLOSE_PARENTHESIS) {
                dump('Found closing operator, handling sub expression');
                // when you find a closing parentheses we pop until we find the opening and continue
                // And always discard the parentheses
                $this->handleSubExpression();
//                dd('testing', $token);
            } elseif (
                $token->getTokenType() === TokenType::OPEN_PARENTHESIS ||
                $token->getTokenType() === TokenType::FUNCTION_NAME
            ) {
                $this->operatorStack->push($token);
                dump('token is open or function, pushing to operator stack', $this->operatorStack);
            } else {
                if (
                    !in_array(
                        $token->getTokenType(),
                        [TokenType::REAL_NUMBER, TokenType::POS_INT, TokenType::INTEGER],
                        true
                    )
                ) {
                    if ($this->lowerPrecendeThan($token, $this->operatorStack->peek())) {

                        $poppedToken = $this->operatorStack->pop();
                        dump('Popped token', $poppedToken->getMatch());

                        $this->outputStack->push($poppedToken);
                        dump('pushed to output stack', $this->outputStack);
                    }
                    $this->operatorStack->push($token);
                    dump('Token not in number array, poushing to operator stack', $this->operatorStack);


                } else {
                    $this->outputStack->push($token);
                    dump('Regular number, pushting to output stack', $this->outputStack);
                }


            }

        }


        // Process any remaining operators
        while (!$this->operatorStack->isEmpty()) {
            $poppedToken = $this->operatorStack->pop();

            $this->outputStack->push($poppedToken);
        }
        // TODO check for stack? we dont wish to calculate the sum yet
        dd($this->outputStack);
    }

    /**
     * @param Token $currentToken
     * @param Token|null $lastTokenInStack
     * @return bool
     */
    private function lowerPrecendeThan(
        Token $currentToken,
        ?Token $lastTokenInStack = null
    ): bool {

        // TODO: combine all ifs
        dump('checking lower precedence :' . ($lastTokenInStack ? $lastTokenInStack->getMatch() : ''));
        if (!$lastTokenInStack) {
            dump('no last token set, false');
            return false;
        }
//        // This always comes first to make sure ^ is evaluated correctly
//        if ($currentToken->getAssoctiotivity() === Definition::RIGHT_ASOC) {
//            dump('RIGHT ASOC');
//            return true;
//        }

        if ($currentToken->getPrecedence() > $lastTokenInStack->getPrecedence()) {
            dump('precende is higher');
            return false;
        }

        if (
            $currentToken->getPrecedence() < $lastTokenInStack->getPrecedence()
        ) {
            dump('precende is lower');
        }
        // To make sure right evaluated operators don't pop eachother out
        if ($currentToken->getPrecedence() === $lastTokenInStack->getPrecedence() && $currentToken->getAssoctiotivity() !== Definition::RIGHT_ASOC) {
            dump('precende is same');
            return true;
        }


        dump('nothing found');
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
            dump('Parsing token until we find opening: ' . $poppedToken->getMatch());
            if ($poppedToken->getTokenType() === TokenType::OPEN_PARENTHESIS) {
                dump('found matching opening');
                $cleanSum = true;

//                $this->operatorStack->pop();
                break;
            }
            dump('popped token', $poppedToken);

            $this->outputStack->push($poppedToken);


            // should be all done now since we will write an evaluater later on and calculate properly
        }

        if (!$cleanSum) {
            dd($this->operatorStack, $this->outputStack);
            throw new \Exception('Mismatching parentheses');
        }

    }

    protected function filterTokens(
        array $tokens
    ): array {
        //Filter out any unwanted whitespaces
        $filteredTokens = array_filter($tokens, static function (Token $t) {
            return $t->getTokenType() !== TokenType::WHITESPACE;
        });

        // Return the array values only, because array_filter preserves the keys
        return array_values($filteredTokens);
    }


}
