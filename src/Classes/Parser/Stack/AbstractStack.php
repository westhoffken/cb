<?php

namespace App\Classes\Parser\Stack;

use App\Classes\Lexer\Token;

class AbstractStack implements StackInterface
{

    /**
     * @var Token[] $tokens
     */
    protected array $tokens = [];

    /**
     * @param Token|null $element
     * @return void
     */
    public function push(?Token $element): void
    {
        if ($element) {
            $this->tokens[] = $element;
        }

    }

    /**
     * @return Token|null
     */
    public function peek(): ?Token
    {
        $end = end($this->tokens);
        if (!$end) {
            return null;
        }
        return $end;
    }

    /**
     * @return Token|null
     */
    public function pop(): ?Token
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

}