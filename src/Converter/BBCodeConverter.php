<?php

/**
 * @file BBCodeConverter.php
 * @brief This file contains the BBCodeConverter class.
 * @details
 *
 * @author Filippo F. Fadda
 * @author Mohammad @Taweel
 */

namespace Converter;

use RuntimeException;

/**
 * @brief A rudimental converter that takes as input a BBCode formatted text and converts it to Markdown.
 */
class BBCodeConverter extends Converter
{
    /**
     * Conaining all callable cleaners.
     *
     * @var array
     */
    protected $cleaners = [];

    public function __construct(string $text = null, string $id = null)
    {
        parent::__construct($text, $id);

        foreach (get_class_methods($this) as $method) {
            if (preg_match('#^(remove|replace)[A-Z][a-z]+#', $method)) {
                call_user_func([$this, $method]);
            }
        }
    }

    public function addCleaner($name, $callback)
    {
        if (is_callable($callback)) {
            $this->cleaners[$name] = $callback;
        }
    }

    /**
     * @brief Converts the provided BBCode text to an equivalent Markdown text.
     */
    public function toMarkdown(string $text = null, $id = null)
    {
        if (is_null($text) && $this->text) {
            $text = $this->text;
        }

        if ( ! $text) {
            return $text;
        }

        if (is_null($id) && $this->id) {
            $id = $this->id;
        }

        foreach ($this->cleaners as $cleaner) {
            if (is_callable($cleaner)) {
                $text = $cleaner($text, $id);
            }
        }

        return $text;
    }

    /**
     * @brief Removes BBCode color.
     */
    protected function removeColor()
    {
        $this->cleaners['removeColor'] = function ($text) {
            return preg_replace_callback('%\[color=\#?\w+\]([\W\D\w\s]*?)\[/color\]%iu',
                function ($matches) {
                    return $matches[1];
                },

                $text
            );
        };
    }

    /**
     * @brief Removes BBCode size.
     */
    protected function removeSize()
    {
        $this->cleaners['removeSize'] = function ($text) {
            return preg_replace_callback('%\[size=\d*\]([\W\D\w\s]*?)\[/size\]%iu',
                function ($matches) {
                    return $matches[1];
                },

                $text
            );
        };
    }

    /**
     * @brief Removes BBCode center.
     */
    protected function removeCenter()
    {
        $this->cleaners['removeCenter'] = function ($text) {
            return preg_replace_callback('%\[center\]([\W\D\w\s]*?)\[/center\]%iu',
                function ($matches) {
                    return $matches[1];
                },

                $text
            );
        };
    }

    /**
     * @brief Replaces BBCode bold.
     */
    protected function replaceBold()
    {
        $this->cleaners['replaceBold'] = function ($text) {
            return preg_replace_callback('%\[b\]([\W\D\w\s]*?)\[/b\]%iu',
                function ($matches) {
                    return '**'.trim($matches[1], ' ').'**';
                },

                $text
            );
        };
    }

    /**
     * @brief Replaces BBCode italic.
     */
    protected function replaceItalic()
    {
        $this->cleaners['replaceItalic'] = function ($text) {
            return preg_replace_callback('%\[i\]([\W\D\w\s]*?)\[/i\]%iu',
                function ($matches) {
                    return '*'.trim($matches[1], ' ').'*';
                },

                $text
            );
        };
    }

    /**
     * @brief Replaces BBCode underline. Hoedown support underline.
     */
    protected function replaceUnderline()
    {
        $this->cleaners['replaceUnderline'] = function ($text) {
            return preg_replace_callback('%\[u\]([\W\D\w\s]*?)\[/u\]%iu',
                function ($matches) {
                    return '_'.trim($matches[1], ' ').'_';
                },

                $text
            );
        };
    }

    /**
     * @brief Replaces BBCode strikethrough.
     */
    protected function replaceStrikethrough()
    {
        $this->cleaners['replaceStrikethrough'] = function ($text) {
            return preg_replace_callback('%\[s\]([\W\D\w\s]*?)\[/s\]%iu',
                function ($matches) {
                    return '~~'.trim($matches[1], ' ').'~~';
                },

                $text
            );
        };
    }

    /**
     * @brief Replaces BBCode lists.
     */
    protected function replaceLists()
    {
        $this->cleaners['replaceLists'] = function ($text, $id = null) {
            return preg_replace_callback('%\[list(?P<type>=1)?\](?P<items>[\W\D\w\s]*?)\[/list\]%iu',
                function ($matches) use ($id) {
                    $buffer = '';

                    $list = preg_replace('/\s*$|^\s*/mu', '', $matches['items']);
                    if (is_null($list)) {
                        throw new RuntimeException(sprintf("Text identified by '%d' has malformed BBCode lists", $id));
                    }
                    $items = preg_split('/\[\*\]/u', $list) || [];

                    $counter = count($items);

                    if (isset($matches['type']) && '=1' == $matches['type']) { // ordered list
                        // We start from 1 to discard the first string, in fact, it's empty.
                        for ($i = 1; $i < $counter; ++$i) {
                            if ( ! empty($items[$i])) {
                                $buffer .= (string) ($i).'. '.trim($items[$i]).PHP_EOL;
                            }
                        }
                    } else { // unordered list
                        // We start from 1 to discard the first string, in fact, it's empty.
                        for ($i = 1; $i < $counter; ++$i) {
                            if ( ! empty($items[$i])) {
                                $buffer .= '- '.trim($items[$i]).PHP_EOL;
                            }
                        }
                    }

                    // We need a like break above the list and another one below.
                    if ( ! empty($buffer)) {
                        $buffer = PHP_EOL.$buffer.PHP_EOL;
                    }

                    return $buffer;
                },

                $text
            );
        };
    }

    /**
     * @brief Replaces BBCode urls.
     */
    protected function replaceUrls()
    {
        $this->cleaners['replaceUrls'] = function ($text, $id = null) {
            return preg_replace_callback('%\[url\s*=\s*("(?:[^"]*")|\A[^\']*\Z|(?:[^\'">\]\s]+))\s*(?:[^]\s]*)\]([\W\D\w\s]*?)\[/url\]%iu',
                function ($matches) use ($id) {
                    if (isset($matches[1]) && isset($matches[2])) {
                        return '['.$matches[2].']('.$matches[1].')';
                    }

                    throw new RuntimeException(sprintf("Text identified by '%d' has malformed BBCode urls", $id));
                },

                $text
            );
        };
    }

    /**
     * @brief Replaces BBCode images.
     */
    protected function replaceImages()
    {
        $this->cleaners['replaceImages'] = function ($text, $id = null) {
            return preg_replace_callback('%\[img\s*\]\s*("(?:[^"]*")|\A[^\']*\Z|(?:[^\'">\]\s]+))\s*(?:[^]\s]*)\[/img\]%iu',
                function ($matches) use ($id) {
                    if (isset($matches[1])) {
                        return PHP_EOL.'![]'.'('.$matches[1].')'.PHP_EOL;
                    }

                    throw new RuntimeException(sprintf("Text identified by '%d' have malformed BBCode images", $id));
                },

                $text
            );
        };
    }

    /**
     * @brief Replaces BBCode quotes.
     * @details Thanks to Casimir et Hippolyte for helping me with this regex.
     */
    protected function replaceQuotes()
    {
        $this->cleaners['replaceQuotes'] = function ($text, $id = null) {
            // Removes the inner quotes, leaving just one level.
            $text = preg_replace('~\G(?<!^)(?>(\[quote\b[^]]*](?>[^[]++|\[(?!/?quote)|(?1))*\[/quote])|(?<!\[)(?>[^[]++|\[(?!/?quote))+\K)|\[quote\b[^]]*]\K~i', '', $text);

            // Replaces all the remaining quotes with '> ' characters.
            $text = preg_replace_callback('%\[quote\b[^]]*\]((?>[^[]++|\[(?!/?quote))*)\[/quote\]%i',
                function ($matches) {
                    $quote = preg_replace('/^\s*/mu', '', trim($matches[1]));

                    return '> '.$quote.PHP_EOL.PHP_EOL;
                },

                $text
            );

            return $text;
        };
    }

    /**
     * @brief Replaces BBCode snippets.
     */
    protected function replaceSnippets()
    {
        $this->cleaners['replaceSnippets'] = function ($text, $id = null) {
            return preg_replace_callback('%\[code\s*=?(?P<language>\w*)\](?P<snippet>[\W\D\w\s]*?)\[\/code\]%iu',
                function ($matches) use ($id) {
                    if (isset($matches['snippet'])) {
                        $language = strtolower($matches['language']);

                        if ('html4strict' == $language or 'div' == $language) {
                            $language = 'html';
                        } elseif ('shell' == $language or 'dos' == $language or 'batch' == $language) {
                            $language = 'sh';
                        } elseif ('xul' == $language or 'wpf' == $language) {
                            $language = 'xml';
                        } elseif ('asm' == $language) {
                            $language = 'nasm';
                        } elseif ('vb' == $language or 'visualbasic' == $language or 'vba' == $language) {
                            $language = 'vb.net';
                        } elseif ('asp' == $language) {
                            $language = 'aspx-vb';
                        } elseif ('xaml' == $language) {
                            $language = 'xml';
                        } elseif ('cplusplus' == $language) {
                            $language = 'cpp';
                        } elseif ('txt' == $language or 'gettext' == $language) {
                            $language = 'text';
                        } elseif ('basic' == $language) {
                            $language = 'cbmbas';
                        } elseif ('lisp' == $language) {
                            $language = 'clojure';
                        } elseif ('aspnet' == $language) {
                            $language = 'aspx-vb';
                        }

                        return PHP_EOL.'```'.$language.PHP_EOL.trim($matches['snippet']).PHP_EOL.'```'.PHP_EOL;
                    }

                    throw new RuntimeException(sprintf("Text identified by '%d' has malformed BBCode snippet.", $id));
                },

                $text
            );
        };
    }
}
