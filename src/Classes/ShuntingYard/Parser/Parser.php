<?php

namespace App\Classes\ShuntingYard\Parser;


use App\Classes\ShuntingYard\Lexer\Token;
use App\Classes\ShuntingYard\Lexer\TokenType;
use App\Classes\ShuntingYard\ShuntingYard;
use App\Classes\Stack\OperatorStack;
use App\Classes\Stack\OutputStack;

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


        $tokens = $this->filterTokens($tokens);

        $this->tokens = $tokens;
        return (new ShuntingYard($tokens))->shuntingYard();

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
