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

        //below a few example sums I tried this code with
        // sin (50/3)
        //sqrt(50.50*(sqrt(50 * sqrt(50))*50+50-20))
        //3 + 4 * 2 / (1 - 5) ^ 2
        //4+18/(9−3)
        $tokens = $operationsTokenizer->tokenize('3 + 4 * 2 / (1 - 5) ^ 2');
        $resultStack = (new Evaluator($parse->parse($tokens)))->evaluate();

        return new JsonResponse($resultStack);

    }

}
