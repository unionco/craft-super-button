<?php
/**
 * Super Button plugin for Craft CMS 3.x
 *
 * A beautiful button component field.
 *
 * @link      https://github.com/unionco
 * @copyright Copyright (c) 2019 Union
 */

namespace unionco\superbutton\services;

use Craft;

use craft\base\Component;
use unionco\superbutton\models\Url;
use unionco\superbutton\models\User;
use unionco\superbutton\SuperButton;
use unionco\superbutton\models\Asset;
use unionco\superbutton\models\Email;
use unionco\superbutton\models\Entry;
use unionco\superbutton\models\Phone;
use unionco\superbutton\models\Product;
use unionco\superbutton\models\Category;

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
class SuperButtonService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     *
     */
    public function getLinkTypes(): array
    {
        $linkTypes = [
            new Asset(),
            new Category(),
            new Email(),
            new Entry(),
            new Phone(),
            new Url(),
            new User()
        ];

        if (SuperButton::$isCommerce) {
            $linkTypes[] = new Product();
        }

        return $linkTypes;
    }
}
