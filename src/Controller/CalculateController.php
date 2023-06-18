<?php

namespace App\Controller;

use App\Classes\Calculator;
use App\Classes\CommandParser;
use App\Classes\Parser\Parser;
use App\Classes\StandardOperations;
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
        $tokens = $operationsTokenizer->tokenize('sqrt(50.50* 50)');
        $parse->parse($tokens);
        dd($tokens);
        return new JsonResponse(['response' => 'ok'], 200);
    }

}
