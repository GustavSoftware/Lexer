<?php

/*
 * Gustav Lexer - A simple lexer component for parsing strings.
 * Copyright (C) 2014-2016  Gustav Software
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
 * @author  Chris Köcher <ckone@fieselschweif.de>
 * @link    http://gustav.fieselschweif.de
 * @package Gustav.Lexer
 * @since   1.0
 */
class Token {
    /**
     * The type of this token. For example in GQL parser in Gustav ORM this is
     * one of the constants in class \Gustav\Orm\Query\Parser\Types.
     *
     * @var integer
     */
    private $_type;

    /**
     * The value of this token.
     *
     * @var string
     */
    private $_value;

    /**
     * The position of this token in the query string.
     *
     * @var integer
     */
    private $_position;

    /**
     * Constructor of this class. Initializes the values of the properties.
     *
     * @param integer $type     The type
     * @param string  $value    The value
     * @param integer $position The position in query string
     */
    public function __construct($type, $value, $position) {
        $this->_type = (int) $type;
        $this->_value = (string) $value;
        $this->_position = (int) $position;
    }

    /**
     * Returns the type of this token.
     *
     * @return integer The type
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * Returns the value of this token.
     *
     * @return string The value
     */
    public function getValue() {
        return $this->_value;
    }

    /**
     * Returns the position of this token in query string.
     *
     * @return integer The position in query string
     */
    public function getPosition() {
        return $this->_position;
    }
}