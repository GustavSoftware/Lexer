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
 * This class is used for scanning and splitting of string into tokens. Please
 * note, that this class is a fork of Doctrine's Lexer for DQL.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 * @see    https://github.com/doctrine/lexer/blob/master/lib/Doctrine/Common/Lexer/AbstractLexer.php
 */
abstract class ALexer
{
    /**
     * This constant represents the default token type that signalizes the end
     * of the parsed string. This token type is needed in top-down parsers to
     * parse the whole string.
     *
     * @const int
     */
    public const END_OF_STRING = 0;

    /**
     * The regular expression for tokenizing the input string. This property is
     * only used as cache of this regex.
     *
     * @var string|null
     */
    private static ?string $_regex = null;

    /**
     * Lexer original input string.
     *
     * @var string
     */
    private string $_input;

    /**
     * Array of scanned tokens.
     *
     * @var Token[]
     */
    private array $_tokens = [];

    /**
     * Current lexer position in input string.
     *
     * @var int
     */
    private int $_position = 0;

    /**
     * Constructor of this class. Sets the input data to be tokenized and starts
     * scanning process.
     *
     * @param string $input
     *   The input to be tokenized
     */
    public function __construct(string $input)
    {
        $this->_input = $input;
        $this->_scan();
    }

    /**
     * Resets the lexer.
     *
     * @return ALexer
     *   This object
     */
    public function reset(): self
    {
        $this->_position = 0;
        return $this;
    }

    /**
     * Resets the lexer position on the input to the given position.
     *
     * @param int $position
     *   Position to place the lexical scanner
     * @return ALexer
     *   This object
     */
    public function resetPosition(int $position = 0): self
    {
        $this->_position = $position;
        return $this;
    }

    /**
     * Returns the position of the current token in the token stream.
     *
     * @return int
     *   The current position
     */
    public function getPosition(): int
    {
        return $this->_position;
    }

    /**
     * Returns the current token on input.
     *
     * @return Token|null
     *   The current token
     */
    public function getToken(): ?Token
    {
        if(isset($this->_tokens[$this->_position])) {
            return $this->_tokens[$this->_position];
        } else {
            return null;
        }
    }

    /**
     * Moves to the next token in the input string.
     *
     * @return bool
     *   false, if there's no more token to read, otherwise true
     */
    public function moveNext(): bool
    {
        $this->_position++;
        return isset($this->_tokens[$this->_position]);
    }

    /**
     * Scans the input string for tokens.
     */
    protected function _scan(): void
    {
        if(self::$_regex === null) {
            self::$_regex = \sprintf(
                '/(%s)|%s/%s',
                implode(')|(', $this->_getCatchablePatterns()),
                implode('|', $this->_getNonCatchablePatterns()),
                $this->_getModifiers()
            );
        }

        $flags = PREG_SPLIT_NO_EMPTY |
            PREG_SPLIT_DELIM_CAPTURE |
            PREG_SPLIT_OFFSET_CAPTURE;
        $matches = preg_split(self::$_regex, $this->_input, -1, $flags);

        foreach($matches as $match) {
            // Must remain before construction of Token since it can change
            $type = $this->_getType($match[0]);

            $this->_tokens[] = new Token($type, $match[0], $match[1]);
        }

        //adds another token signalizing end of the query
        $this->_tokens[] = new Token(
            self::END_OF_STRING,
            "",
            mb_strlen($this->_input)
        );
    }

    /**
     * Returns the regex modifiers.
     *
     * @return string
     *   The modifiers
     */
    protected function _getModifiers(): string
    {
        return 'i';
    }

    /**
     * Returns the lexical catchable patterns.
     *
     * @return array
     *   The patters
     */
    abstract protected function _getCatchablePatterns(): array;

    /**
     * Lexical non-catchable patterns.
     *
     * @return array
     *   The patterns
     */
    abstract protected function _getNonCatchablePatterns(): array;

    /**
     * Retrieve token type. Also processes the token value if necessary.
     *
     * @param mixed $value
     *   The token value
     * @return integer
     *   The matching token type
     */
    abstract protected function _getType(&$value): int;
}