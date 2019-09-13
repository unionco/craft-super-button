<?php

/**
 * Super Button plugin for Craft CMS 3.x
 *
 * A beautiful button component field.
 *
 * @link      https://github.com/unionco
 * @copyright Copyright (c) 2019 Union
 */

namespace unionco\superbutton\base;

use Craft;
use unionco\superbutton\SuperButton;

/**
 * Base Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Union
 * @package   SuperButton
 * @since     1.0.0
 */
abstract class ElementButtonLink extends ButtonLink
{
    // Private
    // =========================================================================

    /**
     * @var element
     */
    private $_element;

    // Public
    // =========================================================================
    public $sources = '*';
    public $customSelectionLabel;

    // Static
    // =========================================================================
    public static function group(): string
    {
        return Craft::t('super-button', 'Site');
    }

    public static function settingsTemplatePath(): string
    {
        return 'super-button/_settings/element';
    }

    public static function inputTemplatePath(): string
    {
        return 'super-button/_input/text';
    }

    // Public Methods
    // =========================================================================
    public function __toString(): string
    {
        return $this->isAvailable() ? $this->getLink([], false) : '';
    }

    public function defaultSelectionLabel(): string
    {
        return Craft::t('super-button', 'Select') . ' ' . $this->defaultLabel();
    }

    public function getSelectionLabel(): string
    {
        if (!is_null($this->customSelectionLabel) && $this->customSelectionLabel != '') {
            return $this->customSelectionLabel;
        }
        return $this->defaultSelectionLabel();
    }

    public function getSourceOptions(): array
    {
        return SuperButton::$plugin->service->getSourceOptions($this->elementType());
    }
}
