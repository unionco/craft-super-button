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
use craft\base\SavableComponent;
use Reflection;
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
abstract class ButtonLink extends SavableComponent implements ButtonLinkInterface
{
    // Public Properties
    // =========================================================================

    public $customLabel;
    public $customPlaceholder;
    public $fieldSettings;
    public $value;
    public $customText;
    public $target;

    // Static
    // =========================================================================

    public static function group(): string
    {
        return Craft::t('super-button', 'Off Site');
    }

    public static function groupTitle(): string
    {
        return static::group() . ' ' . Craft::t('super-button', 'Links');
    }

    public static function defaultLabel(): string
    {
        $classNameParts = explode('\\', static::class);
        return array_pop($classNameParts);
    }

    public static function settingsTemplatePath(): string
    {
        return 'super-button/_settings/text';
    }

    public static function inputTemplatePath(): string
    {
        return 'super-button/_input/text';
    }

    public static function defaultPlaceholder(): string
    {
        return static::defaultLabel();
    }

    public static function hasSettings(): bool
    {
        return true;
    }

    public static function elementType()
    {
        return null;
    }

    public static function hasElement(): bool
    {
        return (static::elementType() ?? false) ? true : false;
    }

    // Public Methods
    // =========================================================================

    public function getType(): string
    {
        return get_class($this);
    }

    public function getNiceName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function getSettingsHtml(): string
    {
        return Craft::$app->getView()->renderTemplate(
            static::settingsTemplatePath(),
            [
                'type' => $this,
            ]
        );
    }
}
