<?php
/**
 * Gustav Lexer - A simple lexer component for parsing strings.
 * Copyright (C) since 2015  Gustav Software
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Gustav\Lexer;

/**
 * This class is used for preprocessing the tokens returned by the lexer before parsing process. This means, this is an
 * additional layer between lexer and parser. Its main purpose is to modify Tokens in context of their neighbors, and
 * to introduce a peek pointer (i.e., we can look some steps ahead and move back).
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
abstract class AScreener
{
    /**
     * The input GQL string.
     *
     * @var string
     */
    protected string $_string;

    /**
     * The lexer, that is used by this screener.
     *
     * @var ALexer
     */
    protected ALexer $_lexer;

    /**
     * Current lexer position in input string.
     *
     * @var int
     */
    protected int $_position = 0;

    /**
     * Current peek of current lexer position.
     *
     * @var integer
     */
    protected int $_peek = 1;

    /**
     * Constructor of this class.
     *
     * @param string $gql
     *   The query string
     * @param ALexer $lexer
     *   The lexer to be used in screening process
     */
    public function __construct(string $string, ALexer $lexer)
    {
        $this->_string = $string;
        $this->_lexer = $lexer;
    }

    /**
     * Resets the lexer.
     *
     * @return self
     *   This object
     */
    public function reset(): self
    {
        $this->_peek = 1;
        $this->_position = 0;

        return $this;
    }

    /**
     * Resets the peek pointer to 1.
     *
     * @return self
     *   This object
     */
    public function resetPeek(): self
    {
        $this->_peek = 1;

        return $this;
    }

    /**
     * Resets the lexer position on the input to the given position.
     *
     * @param int $position
     *   Position to place the lexical scanner
     * @return self
     *   This object
     */
    public function resetPosition(int $position = 0): self
    {
        $this->_position = $position;

        return $this;
    }

    /**
     * Retrieve the original lexer's input until a given position.
     *
     * @param int $position
     *   The position to place the lexical scanner
     * @return string
     *   The input until given position
     */
    public function getInputUntilPosition(int $position): string
    {
        return \mb_substr($this->_string, 0, $position);
    }

    /**
     * Returns the current token on input.
     *
     * @return Token|null
     *   The current token
     */
    public function getToken(): ?Token
    {
        return $this->_fetchToken($this->_position);
    }

    /**
     * Returns the peeked token on input.
     *
     * @return Token|null
     *   The next token
     */
    public function getNextToken(): ?Token
    {
        return $this->_fetchToken($this->_position + $this->_peek);
    }

    /**
     * Moves to the next token in the input string.
     *
     * @return bool
     *   false, if there's no more token to read, otherwise true
     */
    public function moveNext(): bool
    {
        $this->_peek = 1;
        $this->_position++;

        return ($this->_fetchToken($this->_position) !== null);
    }

    /**
     * Moves the peek token forward.
     *
     * @return Token|null
     *   The next token or NULL if there are no more tokens ahead
     */
    public function peek(): ?Token
    {
        $token = $this->_fetchToken($this->_position + $this->_peek);
        if($token !== null) {
            $this->_peek++;
        }
        return $token;
    }

    /**
     * Checks whether the current token matches the given token type.
     *
     * @param int $type
     *   The token type to match
     * @return bool
     *   true, if types match, otherwise false
     */
    public function isToken(int $type): bool
    {
        $token = $this->_fetchToken($this->_position);
        return ($token !== null && $token->getType() == $type);
    }

    /**
     * Checks whether the current token matches at least one of the given token
     * types.
     *
     * @param int[] $types
     *   The token types to match
     * @return bool
     *   true, if types match, otherwise false
     */
    public function isTokenAny(array $types): bool
    {
        $token = $this->_fetchToken($this->_position);
        return ($token !== null && \in_array($token->getType(), $types));
    }

    /**
     * Checks whether a given token type matches the type of the peeked token
     * on input.
     *
     * @param int $type
     *   The type to check
     * @return bool
     *   true, if types match, otherwise false
     */
    public function isNextToken(int $type): bool
    {
        $next = $this->_fetchToken($this->_position + $this->_peek);
        return ($next !== null && $next->getType() == $type);
    }

    /**
     * Checks whether any of the given token types matches the type of the
     * peeked token on input.
     *
     * @param int[] $types
     *   The types to check
     * @return bool
     *   true, if any type match, otherwise false
     */
    public function isNextTokenAny(array $types): bool
    {
        $next = $this->_fetchToken($this->_position + $this->_peek);
        return ($next !== null && \in_array($next->getType(), $types));
    }

    /**
     * Tells the lexer to skip input tokens until it sees a token with the given
     * value.
     *
     * @param int $type
     *   The token type to skip until.
     * @return bool
     *   false, if there's no more token to read, otherwise true
     */
    public function skipUntil(int $type): bool
    {
        $this->_peek = 1;
        $token = $this->_fetchToken($this->_position);
        while($token !== null && $token->getType() != $type) {
            $this->_position++;
            $token = $this->_fetchToken($this->_position);
        }

        return ($token !== null);
    }

    /**
     * Tells the lexer to skip input tokens until it sees a token of different
     * type.
     *
     * @param int $type
     *   The token type to skip
     * @return bool
     *   false, if there's no more token to read, otherwise true
     */
    public function skipWhile(int $type): bool
    {
        $this->_peek = 1;
        $token = $this->_fetchToken($this->_position);
        while($token !== null && $token->getType() == $type) {
            $this->_position++;
            $token = $this->_fetchToken($this->_position);
        }

        return ($token !== null);
    }

    /**
     * Moves the peek pointer to the next position with at least one of the
     * given token types.
     *
     * @param int[] $types
     *   The types to find
     * @return Token|null
     *   The peeked token
     */
    public function peekUntilAny(array $types): ?Token
    {
        $token = $this->peek();
        while($token !== null && !\in_array($token->getType(), $types)) {
            $token = $this->peek();
        }

        return $token;
    }

    /**
     * Moves the peek pointer to the next position with none of the given token
     * types.
     *
     * @param int[] $types
     *   The types to skip
     * @return Token|null
     *   The peeked token
     */
    public function peekWhileAny(array $types): ?Token
    {
        $token = $this->peek();
        while($token !== null && \in_array($token->getType(), $types)) {
            $token = $this->peek();
        }

        return $token;
    }

    /**
     * Fetches the token on the given position. This method allows to modify the token stream (e.g., one can merge
     * multiple tokens).
     *
     * @param integer $pos
     *   The token's position
     * @return Token|null
     *   The token
     */
    protected abstract function _fetchToken(int $pos): ?Token;
}