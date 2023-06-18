<?php

namespace App\Classes\Lexer;

/**
 *
 */
class Token
{
    /**
     * @var string
     */
    private string $match;
    /**
     * @var int
     */
    private int $tokenType;
    /**
     * @var string|null
     */
    private ?string $value;
    /**
     * @var int
     */
    private int $precedence;
    /**
     *  This decided the way it should be set in the stack
     * @var string
     */
    private string $assoctiotivity;

    /**
     * @param string $match
     * @param int $tokenType
     * @param int $precedence
     * @param string $assoctiotivity
     * @param string|null $value
     */
    public function __construct(
        string $match,
        int $tokenType,
        int $precedence,
        string $assoctiotivity,
        ?string $value = null
    ) {
        $this->match = $match;
        $this->tokenType = $tokenType;
        $this->value = $value;
        $this->precedence = $precedence;
        $this->assoctiotivity = $assoctiotivity;
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return strlen($this->match);
    }

    /**
     * @return string
     */
    public function getMatch(): string
    {
        return $this->match;
    }

    /**
     * @return int
     */
    public function getPrecedence(): int
    {
        return $this->precedence;
    }

    /**
     * @return string
     */
    public function getAssoctiotivity(): string
    {
        return $this->assoctiotivity;
    }


    /**
     * @return int
     */
    public function getTokenType(): int
    {
        return $this->tokenType;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }


}
