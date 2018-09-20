<?php

/**
 * @file Converter.php
 * @brief This file contains the Converter class.
 * @details
 *
 * @author Filippo F. Fadda
 * @author Mohammad @Taweel
 */

/**
 * @brief This namespace contains all the converters.
 */

namespace Converter;

/**
 * @brief This is an abstract converter.
 */
abstract class Converter
{
    protected $text;
    protected $id;

    /**
     * @brief Constructor.
     *
     * @param string $text the text to be converted
     * @param string $id   you can provide an identifier which is used in case an exception is raised during the
     *                     conversion process
     */
    public function __construct(string $text = null, string $id = null)
    {
        $this->text = $text;
        $this->id = $id;
    }
}
