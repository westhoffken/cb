<?php

namespace App\Classes\ShuntingYard\Parser\Stack;


use App\Classes\ShuntingYard\Lexer\Token;

interface StackInterface
{

    /**
     * @param Token|null $element
     * @return void
     */
    public function push(?Token $element): void;


    /**
     * @return Token|null
     */
    public function peek(): ?Token;


    /**
     * @return Token|null
     */
    public function pop(): ?Token;

    /**
     * @return int
     */
    public function length(): int;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

}
