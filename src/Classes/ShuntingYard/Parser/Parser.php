<?php

namespace App\Classes\ShuntingYard\Parser;


use App\Classes\ShuntingYard\Lexer\Token;
use App\Classes\ShuntingYard\Lexer\TokenType;
use App\Classes\ShuntingYard\ShuntingYard;
use App\Classes\Stack\OperatorStack;
use App\Classes\Stack\OutputStack;


/**
 * The parse class that tokenies our sum!
 * I work with 2 stacks here that keep track of our output and operators
 * The stack principle is all according to the shunting yard algoritme
 *
 * Why make use of stacks and not the basic operations of php? Stacks are a bit cleaner and for math I need a clear view of it all
 */
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

    /**
     * @param array $tokens
     * @return OutputStack
     * @throws \Exception
     */
    public function parse(array $tokens): OutputStack
    {

        $tokens = $this->filterTokens($tokens);

        $this->tokens = $tokens;
        // Exceptions will not be caught here, but will be handled in the controller
        return (new ShuntingYard($tokens))->shuntingYard();

    }


    /**
     * @param array $tokens
     * @return array
     */
    protected function filterTokens(
        array $tokens
    ): array {
        //Filter out any unwanted whitespaces white spaces cannot be used in math ... duh
        $filteredTokens = array_filter($tokens, static function (Token $t) {
            return $t->getTokenType() !== TokenType::WHITESPACE;
        });

        // Return the array values only, because array_filter preserves the keys
        return array_values($filteredTokens);
    }


}
