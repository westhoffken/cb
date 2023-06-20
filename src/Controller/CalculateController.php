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

    /**
     * Klein beetje uitleg van mijn kant. ik gebruik het shunting yard algoritme om de haakjes weg te werken
     * Dit algoritme poept een reeks getallen, functies en operators uit die middels een ander algoritme berekend kunnen worde
     * Stap 1: het tokenizen van de string van de som! op deze manier kan er makkelijk onderscheid
     * gemaakt worden in alle vershcillende characters en functies
     * Stap 2: het parsen van de tokens middels shunting yard
     * Stap 3: het berekenen van de reverse polish notatie
     * Stap 4: result naar de voorkant pasen
     *
     * Ik zal nog kort toelichten waarom ik voor dit algoritme gekozen heb.
     * Ik had eerst het idee om dit te gaan doen emt reguliere expressies, maar na wat nadenken en hier endaar proberen
     * ben ik tot de conclusie gekomen dat je niet alles maar zelf moet willen uitvinden en er vast een slim iemand
     * is geweest die hier al eens overnagedacht heeft. Daarnaast ben ik geen held in wiskunde (ondankse de hoge cijfers).
     * In het kader van het wiel niet opnieuw uitvinden en hetgeen boven genoemd heb ik dit algoritme gekozen.
     *
     * In het kader van wel laten zien dat ik toch een beetje kan programmeren heb ik er voor gekozen om niet een packege te gebruiken
     * maar dit zelfeen poging gegeven tot uitwerken.
     *
     * P.S. ik heb ergens een hekel aan nederlands commentaar en heb het meeste in het engels uitgewerkt.
     *
     */


    /**
     * This framework is MVC!11!!!
     * Make a new route that can handle our sum input.
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    #[Route('/calculate', name: 'calculate_sum', methods: ['POST', 'OPTIONS'])]
    public function calculate(Request $request): JsonResponse
    {
        // Init our much-needed classes
        $operationsTokenizer = new StandardOperations();
        $parse = new Parser();

        //below a few example sums I tried this code with
        // sin (50/3)
        //sqrt(50.50*(sqrt(50 * sqrt(50))*50+50-20))
        //3 + 4 * 2 / (1 - 5) ^ 2
        //4+18/(9−3)
        $tokens = $operationsTokenizer->tokenize('sqrt(50.50*(sqrt(50 * sqrt(50))*50+50-20))');
        $resultStack = (new Evaluator($parse->parse($tokens)))->evaluate();

        // frontend accepts JSON, because why not
        return new JsonResponse($resultStack);

    }

}
