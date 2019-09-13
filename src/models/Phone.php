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
 * Phone Link Model
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
class Phone extends ButtonLink
{
    // Private
    // =========================================================================

    /**
     * Undocumented variable
     *
     * @var string
     */
    private $_match = '/^(?:\+\d{1,3}|0\d{1,3}|00\d{1,2})?(?:\s?\(\d+\))?(?:[-\/\s.]|\d)+$/';

    // Static
    // =========================================================================

    /**
     * Undocumented function
     *
     * @return string
     */
    public static function defaultLabel(): string
    {
        return Craft::t('super-button', 'Phone Number');
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public static function defaultPlaceholder(): string
    {
        return Craft::t('super-button', '+44(0)0000 000000');
    }
}
