<?php

/*
 +-----------------------------------------------------------------------+
 | This file is part of the Roundcube Webmail client                     |
 |                                                                       |
 | Copyright (C) The Roundcube Dev Team                                  |
 |                                                                       |
 | Licensed under the GNU General Public License version 3 or            |
 | any later version with exceptions for skins & plugins.                |
 | See the README file for a full license statement.                     |
 |                                                                       |
 | CONTENTS:                                                             |
 |   Abstract class for output generation                                |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <roundcube@gmail.com>                        |
 | Author: Aleksander Machniak <alec@alec.pl>                            |
 +-----------------------------------------------------------------------+
*/

/**
 * Class for output generation
 */
abstract class rcmail_output extends rcube_output
{
    public const JS_OBJECT_NAME = 'rcmail';
    public const BLANK_GIF = 'R0lGODlhDwAPAIAAAMDAwAAAACH5BAEAAAAALAAAAAAPAA8AQAINhI+py+0Po5y02otnAQA7';

    public $type = 'html';
    public $ajax_call = false;
    public $framed = false;

    protected $pagetitle = '';
    protected $object_handlers = [];
    protected $devel_mode = false;

    /**
     * Object constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->devel_mode = (bool) $this->config->get('devel_mode');
    }

    /**
     * Setter for page title
     *
     * @param string $title Page title
     */
    public function set_pagetitle($title)
    {
        $this->pagetitle = $title;
    }

    /**
     * Getter for the current skin path property
     */
    public function get_skin_path()
    {
        return $this->config->get('skin_path');
    }

    /**
     * Getter for the current skin meta data
     */
    public function get_skin_info($name = null)
    {
        $skin = $name ?? $this->config->get('skin');
        $data = ['name' => ucfirst($skin)];

        $meta = INSTALL_PATH . "skins/{$skin}/meta.json";
        if (is_readable($meta) && ($json = json_decode(file_get_contents($meta), true))) {
            $data = $json;
            $data['author_link'] = !empty($json['url']) ? html::a(['href' => $json['url'], 'target' => '_blank'], rcube::Q($json['author'])) : rcube::Q($json['author']);
            $data['license_link'] = !empty($json['license-url']) ? html::a(['href' => $json['license-url'], 'target' => '_blank', 'tabindex' => '-1'], rcube::Q($json['license'])) : rcube::Q($json['license']);
        }

        $composer = INSTALL_PATH . "/skins/{$skin}/composer.json";
        if (is_readable($composer) && ($json = json_decode(file_get_contents($composer), true))) {
            $data['version'] = $json['version'] ?? null;

            if (!empty($json['homepage'])) {
                $data['uri'] = $json['homepage'];
            }
        }

        return $data;
    }

    /**
     * Delete all stored env variables and commands
     */
    #[Override]
    public function reset()
    {
        parent::reset();

        $this->object_handlers = [];
        $this->pagetitle = '';
    }

    /**
     * Call a client method
     *
     * @param string $cmd     Method to call
     * @param mixed  ...$args Method arguments
     */
    abstract public function command($cmd, ...$args);

    /**
     * Add a localized label(s) to the client environment
     *
     * @param mixed ...$args Labels (an array of strings, or many string arguments)
     */
    abstract public function add_label(...$args);

    /**
     * Register a template object handler
     *
     * @param string   $name Object name
     * @param callable $func Function name to call
     */
    public function add_handler($name, $func)
    {
        $this->object_handlers[$name] = $func;
    }

    /**
     * Register a list of template object handlers
     *
     * @param array $handlers Hash array with object=>handler pairs
     */
    public function add_handlers($handlers)
    {
        $this->object_handlers = array_merge($this->object_handlers, $handlers);
    }
}
