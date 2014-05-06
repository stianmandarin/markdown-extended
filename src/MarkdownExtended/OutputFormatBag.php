<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2014 Pierre Cassat
 *
 * original MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * original PHP Markdown & Extra
 * Copyright (c) 2004-2012 Michel Fortin  
 * <http://michelf.com/projects/php-markdown/>
 *
 * original Markdown
 * Copyright (c) 2004-2006 John Gruber  
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended;

use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;
use \MarkdownExtended\API\OutputFormatInterface;
use \MarkdownExtended\API\OutputFormatHelperInterface;

/**
 * PHP Markdown Extended OutputFormat container
 */
class OutputFormatBag
{

    /**
     * @var  array   Table of grammar output tags called by filters (must be defined in the output formater)
     */
    public static $tag_names = array(
        'comment',
        'meta_title', 'meta_data',
        'bold', 'italic',
        'new_line', 'horizontal_rule',
        'paragraph', 'title', 'block', 'span',
        'link', 'image',
        'blockquote', 'abbreviation',
        'preformated', 'code',
        'list', 'list_item',
        'ordered_list', 'ordered_list_item',
        'unordered_list', 'unordered_list_item',
        'definition_list', 'definition_list_item_term', 'definition_list_item_definition',
        'table', 'table_caption', 'table_header', 'table_body', 'table_footer', 'table_line', 'table_cell', 'table_cell_head',
    );

    /**
     * @var     \MarkdownExtended\API\OutputFormatInterface
     */
    protected $formater;

    /**
     * @var     \MarkdownExtended\API\OutputFormatHelperInterface
     */
    protected $helper;

    /**
     * Loads a new formater
     *
     * @param   string  $format     The formater name
     */
    public function load($format)
    {
        $class_name = $format;
        if (!class_exists($class_name)) {
            $class_name = 'MarkdownExtended\OutputFormat\\'.MDE_Helper::toCamelCase($format);
        }
        $_obj = MarkdownExtended::get($class_name);
        $this->setFormater($_obj);
        $this->loadHelper($format);
    }

    /**
     * Loads a formater helper if it exists
     *
     * @param   string  $format     The formater name
     */
    public function loadHelper($format)
    {
        $class_name = $format.'Helper';
        if (!class_exists($class_name)) {
            $class_name = 'MarkdownExtended\OutputFormat\\'.MDE_Helper::toCamelCase($format).'Helper';
        }
        if (!class_exists($class_name)) {
            $class_name = 'MarkdownExtended\OutputFormat\\DefaultHelper';
        }
        $_obj = MarkdownExtended::get($class_name);
        $this->setHelper($_obj);
    }

    /**
     * Magic method to pass any called method from the bag to its formater
     *
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if the method doesn't
     *          exist in the formater class
     */
    public function __call($name, array $arguments = null)
    {
        if (empty($this->formater)) return;

        if (method_exists($this->getFormater(), $name)) {
            if (!empty($arguments)) {
                return call_user_func_array(array($this->getFormater(), $name), $arguments);
            } else {
                return call_user_func(array($this->getFormater(), $name));
            }
        } else {
            throw new MDE_Exception\InvalidArgumentException(sprintf(
                'Call to undefined method "%s" on formater "%s"!',
                $name, get_class($this->getFormater())
            ));
        }
    }

    /**
     * Set the current formater
     *
     * @param   \MarkdownExtended\API\OutputFormatInterface
     * @return  self
     */
    public function setFormater(OutputFormatInterface $formater)
    {
        $this->formater = $formater;
        return $this;
    }

    /**
     * Get current formater
     *
     * @return  \MarkdownExtended\API\OutputFormatInterface
     */
    public function getFormater()
    {
        return $this->formater;
    }

    /**
     * Set the current formater helper
     *
     * @param   \MarkdownExtended\API\OutputFormatHelperInterface
     * @return  self
     */
    public function setHelper(OutputFormatHelperInterface $helper)
    {
        $this->helper = $helper;
        return $this;
    }

    /**
     * Get current formater helper
     *
     * @return  \MarkdownExtended\API\OutputFormatHelperInterface
     */
    public function getHelper()
    {
        return $this->helper;
    }

}

// Endfile
