<?php

namespace App\Classes;

use App\Classes\Lexer\Definition;
use App\Classes\Lexer\Token;
use App\Classes\Lexer\TokenType;

/**
 * We can directly call this class if we wish to make standard math operations
 * This class will also hold and add all our operations possible based on regexp patterns
 */
class StandardOperations extends Tokenizer
{
    public function __construct()
    {
        $this->add(new Definition('/\d+[,\.]\d+(e[+-]?\d+)?/', TokenType::REAL_NUMBER, 999));

        $this->add(new Definition('/\d+/', TokenType::POS_INT, 999));

        $this->add(new Definition('/sqrt/', TokenType::FUNCTION_NAME, 999));

        $this->add(new Definition('/round/', TokenType::FUNCTION_NAME, 999));
        $this->add(new Definition('/ceil/', TokenType::FUNCTION_NAME, 999));
        $this->add(new Definition('/floor/', TokenType::FUNCTION_NAME, 999));

        $this->add(new Definition('/sinh/', TokenType::FUNCTION_NAME, 999));
        $this->add(new Definition('/cosh/', TokenType::FUNCTION_NAME, 999));
        $this->add(new Definition('/tanh/', TokenType::FUNCTION_NAME, 999));
        $this->add(new Definition('/coth/', TokenType::FUNCTION_NAME, 999));

        $this->add(new Definition('/sind/', TokenType::FUNCTION_NAME, 999));
        $this->add(new Definition('/cosd/', TokenType::FUNCTION_NAME, 999));
        $this->add(new Definition('/tand/', TokenType::FUNCTION_NAME, 999));
        $this->add(new Definition('/cotd/', TokenType::FUNCTION_NAME, 999));

        $this->add(new Definition('/sin/', TokenType::FUNCTION_NAME, 999));
        $this->add(new Definition('/cos/', TokenType::FUNCTION_NAME, 999));
        $this->add(new Definition('/tan/', TokenType::FUNCTION_NAME, 999));
        $this->add(new Definition('/cot/', TokenType::FUNCTION_NAME, 999));

        $this->add(new Definition('/arsinh|arcsinh|asinh/', TokenType::FUNCTION_NAME, 999, 'arsinh'));
        $this->add(new Definition('/arcosh|arccosh|acosh/', TokenType::FUNCTION_NAME, 999, 'arcosh'));
        $this->add(new Definition('/artanh|arctanh|atanh/', TokenType::FUNCTION_NAME, 999, 'artanh'));
        $this->add(new Definition('/arcoth|arccoth|acoth/', TokenType::FUNCTION_NAME, 999, 'arcoth'));

        $this->add(new Definition('/arcsin|asin/', TokenType::FUNCTION_NAME, 999, 'arcsin'));
        $this->add(new Definition('/arccos|acos/', TokenType::FUNCTION_NAME, 999, 'arccos'));
        $this->add(new Definition('/arctan|atan/', TokenType::FUNCTION_NAME, 999, 'arctan'));
        $this->add(new Definition('/arccot|acot/', TokenType::FUNCTION_NAME, 999, 'arccot'));

        $this->add(new Definition('/exp/', TokenType::FUNCTION_NAME, 999));
        $this->add(new Definition('/log10|lg/', TokenType::FUNCTION_NAME, 999, 'lg'));
        $this->add(new Definition('/log/', TokenType::FUNCTION_NAME, 999, 'log'));
        $this->add(new Definition('/ln/', TokenType::FUNCTION_NAME, 999, 'ln'));

        $this->add(new Definition('/abs/', TokenType::FUNCTION_NAME, 999));
        $this->add(new Definition('/sgn/', TokenType::FUNCTION_NAME, 999));

        $this->add(new Definition('/\(/', TokenType::OPEN_PARENTHESIS, 999));
        $this->add(new Definition('/\)/', TokenType::CLOSE_PARENTHESIS, 999));

        //Below precendeces are according to the shunting yard algoritm
        $this->add(new Definition('/\+/', TokenType::ADDITION_OPERATOR, 2));
        $this->add(new Definition('/\-/', TokenType::SUBTRACTION_OPERATOR, 2));
        $this->add(new Definition('/\*/', TokenType::MULTIPLY_OPERATOR, 3));
        $this->add(new Definition('/\//', TokenType::DIVISION_OPERATOR, 3));
        $this->add(new Definition('/\^/', TokenType::EXP_OPERATOR, 4));

        // Postfix operators
        $this->add(new Definition('/\!\!/', TokenType::SEMI_FACTORIAL_OPERATOR, 999));
        $this->add(new Definition('/\!/', TokenType::FACTORIAL_OPERATOR, 999));

        $this->add(new Definition('/pi/', TokenType::CONSTANT, 999));
        $this->add(new Definition('/e/', TokenType::CONSTANT, 999));
        $this->add(new Definition('/NAN/', TokenType::CONSTANT, 999));
        $this->add(new Definition('/INF/', TokenType::CONSTANT, 999));

        $this->add(new Definition('/[a-zA-Z]/', TokenType::IDENTIFIER, 999));

        $this->add(new Definition('/\s+/', TokenType::WHITESPACE, 999));

    }

}