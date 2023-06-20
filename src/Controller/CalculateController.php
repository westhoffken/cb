<?php

namespace App\Controller;


use App\Classes\RPN\Evaluator;
use App\Classes\ShuntingYard\Parser\Parser;
use App\Classes\ShuntingYard\Tokenizer\StandardOperations;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CalculateController
{

    #[Route('/calculate', name: 'calculate_sum', methods: ['POST', 'OPTIONS'])]
    public function calculate(Request $request): JsonResponse
    {
        $operationsTokenizer = new StandardOperations();
        $parse = new Parser();


        $tokens = $operationsTokenizer->tokenize('sin( 50 / 3)');
        $resutlStack = (new Evaluator($parse->parse($tokens)))->evaluate();
        dd($resutlStack);

    }

}
