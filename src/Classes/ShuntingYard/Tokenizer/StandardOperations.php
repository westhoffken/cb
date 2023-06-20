<?php

namespace App\Classes\ShuntingYard\Tokenizer;




use App\Classes\ShuntingYard\Lexer\Definition;
use App\Classes\ShuntingYard\Lexer\TokenType;

/**
 * This gives us a standard math definitions to be used in our calculator.
 */
class StandardOperations extends Tokenizer
{
    public function __construct()
    {
        $this->add(new Definition('/\d+[,\.]\d+(e[+-]?\d+)?/', TokenType::REAL_NUMBER, 0));

        $this->add(new Definition('/\d+/', TokenType::POS_INT, 0));

        $this->add(new Definition('/sqrt/', TokenType::FUNCTION_NAME, 0));

        $this->add(new Definition('/round/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/ceil/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/floor/', TokenType::FUNCTION_NAME, 0));

        $this->add(new Definition('/sinh/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/cosh/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/tanh/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/coth/', TokenType::FUNCTION_NAME, 0));

        $this->add(new Definition('/sind/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/cosd/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/tand/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/cotd/', TokenType::FUNCTION_NAME, 0));

        $this->add(new Definition('/sin/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/max/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/cos/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/tan/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/cot/', TokenType::FUNCTION_NAME, 0));

        $this->add(new Definition('/arsinh|arcsinh|asinh/', TokenType::FUNCTION_NAME, 0, 'arsinh'));
        $this->add(new Definition('/arcosh|arccosh|acosh/', TokenType::FUNCTION_NAME, 0, 'arcosh'));
        $this->add(new Definition('/artanh|arctanh|atanh/', TokenType::FUNCTION_NAME, 0, 'artanh'));
        $this->add(new Definition('/arcoth|arccoth|acoth/', TokenType::FUNCTION_NAME, 0, 'arcoth'));

        $this->add(new Definition('/arcsin|asin/', TokenType::FUNCTION_NAME, 0, 'arcsin'));
        $this->add(new Definition('/arccos|acos/', TokenType::FUNCTION_NAME, 0, 'arccos'));
        $this->add(new Definition('/arctan|atan/', TokenType::FUNCTION_NAME, 0, 'arctan'));
        $this->add(new Definition('/arccot|acot/', TokenType::FUNCTION_NAME, 0, 'arccot'));

        $this->add(new Definition('/exp/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/log10|lg/', TokenType::FUNCTION_NAME, 0, 'lg'));
        $this->add(new Definition('/log/', TokenType::FUNCTION_NAME, 0, 'log'));
        $this->add(new Definition('/ln/', TokenType::FUNCTION_NAME, 0, 'ln'));

        $this->add(new Definition('/abs/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/sgn/', TokenType::FUNCTION_NAME, 0));
        $this->add(new Definition('/pi/', TokenType::FUNCTION_NAME, 0));

        $this->add(new Definition('/\(/', TokenType::OPEN_PARENTHESIS, 0));
        $this->add(new Definition('/\)/', TokenType::CLOSE_PARENTHESIS, 0));

        //Below precendeces are according to the shunting yard algoritm
        $this->add(new Definition('/\+/', TokenType::ADDITION_OPERATOR, 10));
        $this->add(new Definition('/\-/', TokenType::SUBTRACTION_OPERATOR, 10));
        $this->add(new Definition('/\*/', TokenType::MULTIPLY_OPERATOR, 20));
        $this->add(new Definition('/\//', TokenType::DIVISION_OPERATOR, 20));
        $this->add(new Definition('/\^/', TokenType::EXP_OPERATOR, 30));

        // Postfix operators
        $this->add(new Definition('/\!\!/', TokenType::SEMI_FACTORIAL_OPERATOR, 0));
        $this->add(new Definition('/\!/', TokenType::FACTORIAL_OPERATOR, 0));


        $this->add(new Definition('/e/', TokenType::CONSTANT, 0));
        $this->add(new Definition('/NAN/', TokenType::CONSTANT, 0));
        $this->add(new Definition('/INF/', TokenType::CONSTANT, 0));

        $this->add(new Definition('/[a-zA-Z]/', TokenType::IDENTIFIER, 0));

        $this->add(new Definition('/\s+/', TokenType::WHITESPACE, 0));

    }

}
