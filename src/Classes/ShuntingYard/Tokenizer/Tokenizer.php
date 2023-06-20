<?php

namespace App\Classes\ShuntingYard\Tokenizer;


use App\Classes\ShuntingYard\Lexer\Definition;
use App\Classes\ShuntingYard\Lexer\Token;

class Tokenizer
{
    /**
     * @var Definition[] $definitions
     */
    private array $definitions = [];

    public function add(Definition $definition): void
    {
        $this->definitions[] = $definition;
    }

    /**
     * @return Definition[]
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    /**
     * @param string $input
     * @return Token[]
     * @throws \Exception
     */
    public function tokenize(string $input): array
    {
        $tokens = [];

        $currentIndex = 0;

        // Loops through the whole string and will try to find a token in it
        // When the token is found it will make a Token and move the index on for the length of the found token
        // Above will make it, so we can never handle the same part of the string twice
        while ($currentIndex < strlen($input)) {
            // keep find only wat we haven't processed yet
            $token = $this->findToken(substr($input, $currentIndex));

            if (!$token) {
                throw new \Exception(substr($input, $currentIndex));
            }

            $tokens[] = $token;

            // Addvance the index beyoned the parsed token in the string
            $currentIndex += $token->length();
        }

        return $tokens;
    }

    /**
     * @param string $input
     * @return Token|null
     * @throws \Exception
     */
    private function findToken(string $input): ?Token
    {
        // Lets find out of theres any definition and finalize the token creation
        foreach ($this->definitions as $definition) {
            $token = $definition->match($input);
            if ($token) {
                return $token;
            }
        }

        return null;
    }

}
