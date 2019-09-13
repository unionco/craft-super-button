<?php

/**
 * Super Button plugin for Craft CMS 3.x
 *
 * A beautiful button component field.
 *
 * @link      https://github.com/unionco
 * @copyright Copyright (c) 2019 Union
 */

namespace unionco\superbutton\models;

use Craft;

use unionco\superbutton\SuperButton;
use unionco\superbutton\base\ButtonLink;

/**
 * Url Link Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Union
 * @package   SuperButton
 * @since     1.0.0
 */
class Url extends ButtonLink
{
    // Static
    // =========================================================================
    public static function defaultLabel(): string
    {
        return Craft::t('super-button', 'URL');
    }

    public static function defaultPlaceholder(): string
    {
        return Craft::t('super-button', Craft::$app->getSites()->getCurrentSite()->baseUrl);
    }
}
