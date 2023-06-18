<?php

namespace App\Classes\Lexer;

/**
 *
 */
class Definition
{
    public const LEFT_ASOC = 'left';

    public const RIGHT_ASOC = 'right';
    /**
     * @var string
     */
    private string $patternForToken;

    /**
     * @var string| null
     */
    private ?string $value;
    private int $tokenType;
    private int $precedence;
    /**
     * @var string
     */
    private string $associotivity = self::LEFT_ASOC;

    /**
     * @param string $patternForToken
     * @param int $tokenType
     * @param int $precedence
     * @param string|null $value
     */
    public function __construct(string $patternForToken, int $tokenType, int $precedence, ?string $value = null)
    {
        $this->patternForToken = $patternForToken;
        $this->value = $value;
        $this->tokenType = $tokenType;
        $this->precedence = $precedence;
        if ($tokenType === TokenType::EXP_OPERATOR) {
            $this->associotivity = self::RIGHT_ASOC;
        }
    }

    /**
     * @param string $input
     * @return Token|null
     * @throws \Exception
     */
    public function match(string $input): ?Token
    {
        $matchedResult = preg_match($this->patternForToken, $input, $matches, PREG_OFFSET_CAPTURE);

        if ($matchedResult === false) {
            throw new \Exception('Malformed sum');
        }

        if ($matchedResult === 0) {
            // Nothing was found so just return nothing
            return null;
        }

        return $this->convertMatchToToken($matches[0]);
    }

    private function convertMatchToToken($match): ?Token
    {
        $value = $match[0];

        // If we don't match at the beginning of the string, it fails.
        if ($match[1] !== 0) {
            return null;
        }

        if ($this->value) {
            $value = $this->value;
        }

        return new Token($value, $this->tokenType, $this->precedence, $this->associotivity, $match[0]);
    }
}
