<?php

namespace App\Controller;

use App\Classes\Calculator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CalculateController
{
    #[Route('/calculate', name: 'calculate_sum', methods: ['POST', 'OPTIONS'])]
    public function calculate(Request $request): JsonResponse
    {
        // TODO: lets have the backend make the sum? with regex ^((?:[^()]|\((?1)\))*+)$
        // TODO: validate for valid entries and make sure there is an enclosing parenthises
        // TODO: store it in the session and persist or something
        $content = json_decode($request->getContent());

        $calculator = new Calculator($content->sum, $content->commands);
        $result = $calculator->calculate();

        return new JsonResponse(['response' => $result], 200);
    }

}
