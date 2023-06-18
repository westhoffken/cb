<?php

namespace App\Classes;

use App\Classes\Lexer\Definition;
use App\Classes\Lexer\Token;

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
        foreach ($this->definitions as $definition) {
            $token = $definition->match($input);
            if ($token) {
                return $token;
            }
        }

        return null;
    }

}
