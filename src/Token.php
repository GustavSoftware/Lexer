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
 * This class represents tokens found by the lexer.
 *
 * @author Chris Köcher <ckone@fieselschweif.de>
 * @link   http://gustav.fieselschweif.de
 * @since  1.0
 */
class Token
{
    /**
     * The type of this token. For example in GQL parser in Gustav ORM this is
     * one of the constants in class \Gustav\Orm\Query\Parser\Types.
     *
     * @var int
     */
    private int $_type;

    /**
     * The value of this token.
     *
     * @var string
     */
    private string $_value;

    /**
     * The position of this token in the query string.
     *
     * @var int
     */
    private int $_position;

    /**
     * Constructor of this class. Initializes the values of the properties.
     *
     * @param int $type
     *   The type
     * @param string $value
     *   The value
     * @param int $position
     *   The position in query string
     */
    public function __construct(int $type, string $value, int $position) 
    {
        $this->_type = $type;
        $this->_value = $value;
        $this->_position = $position;
    }

    /**
     * Returns the type of this token.
     *
     * @return int
     *   The type
     */
    public function getType(): int 
    {
        return $this->_type;
    }

    /**
     * Returns the value of this token.
     *
     * @return string
     *   he value
     */
    public function getValue(): string
    {
        return $this->_value;
    }

    /**
     * Returns the position of this token in query string.
     *
     * @return int
     *   The position in query string
     */
    public function getPosition(): int
    {
        return $this->_position;
    }
}