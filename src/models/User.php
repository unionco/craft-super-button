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
use unionco\superbutton\base\ElementButtonLink;
use craft\elements\User as CraftUser;

/**
 * User Link Model
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
class User extends ElementButtonLink
{
    // Static
    // =========================================================================
    public static function elementType()
    {
        return CraftUser::class;
    }
}
