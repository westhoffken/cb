<?php

namespace App\Classes\Evaluator\Stack;


use App\Classes\ShuntingYard\Lexer\Token;

/**
 * This stack will help us calculate the sum of the RNP expresion and help us solve the sum
 */
class ResultStack
{

    /**
     * @var array<float> $tokens
     */
    protected array $tokens = [];

    /**
     * @param float|null $element
     * @return void
     */
    public function push(?float $element): void
    {
        if ($element) {
            $this->tokens[] = $element;
        }

    }

    /**
     * @return Token|null
     */
    public function peek(): ?float
    {
        $end = end($this->tokens);
        if (!$end) {
            return null;
        }
        return $end;
    }

    /**
     * @return float|null
     */
    public function pop(): ?float
    {
        return array_pop($this->tokens);
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return count($this->tokens);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->length() === 0;
    }

    public function getResult()
    {
        return $this->tokens[0];
    }
}
