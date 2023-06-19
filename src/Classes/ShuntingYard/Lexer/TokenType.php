<?php

namespace App\Classes\ShuntingYard\Lexer;

final class TokenType
{

    public const POS_INT = 1;

    public const INTEGER = 2;

    public const REAL_NUMBER = 3;

    public const IDENTIFIER = 20;

    public const OPEN_PARENTHESIS = 31;

    public const CLOSE_PARENTHESIS = 32;


    public const ADDITION_OPERATOR = 100;

    public const SUBTRACTION_OPERATOR = 101;

    public const MULTIPLY_OPERATOR = 102;

    public const DIVISION_OPERATOR = 103;

    public const EXP_OPERATOR = 104;

    public const FACTORIAL_OPERATOR = 105;

    public const SEMI_FACTORIAL_OPERATOR = 105;


    public const FUNCTION_NAME = 200;


    public const CONSTANT = 300;


    public const WHITESPACE = 999;


}
