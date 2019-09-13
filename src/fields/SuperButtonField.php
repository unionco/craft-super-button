<?php
/**
 * Super Button plugin for Craft CMS 3.x
 *
 * A beautiful button component field.
 *
 * @link      https://github.com/unionco
 * @copyright Copyright (c) 2019 Union
 */

namespace unionco\superbutton\fields;

use Craft;
use yii\db\Schema;

use craft\base\Field;
use craft\helpers\Db;
use craft\helpers\Json;
use craft\base\ElementInterface;
use unionco\superbutton\SuperButton;
use unionco\superbutton\assetbundles\superbuttonfield\SuperButtonFieldAsset;
use unionco\superbutton\assetbundles\superbuttonsettings\SuperButtonSettingsAsset;
use unionco\superbutton\base\ButtonLink;
use unionco\superbutton\base\ButtonLinkInterface;

/**
 * SuperButton Field
 *
 * Whenever someone creates a new field in Craft, they must specify what
 * type of field it is. The system comes with a handful of field types baked in,
 * and we’ve made it extremely easy for plugins to add new ones.
 *
 * https://craftcms.com/docs/plugins/field-types
 *
 * @author    Union
 * @package   SuperButton
 * @since     1.0.0
 */
class SuperButtonField extends Field
{
    // Public Properties
    // =========================================================================

    public $selectLinkText = '';
    public $sources = '*';
    public $entryTypes = '*';
    public $types;
    public $defaultLinkLayout = 'row';
    public $defaultText;
    public $allowTarget;
    public $allowRepeat = false;
    public $minLinks;
    public $maxLinks;

    // Private Properties
    // =========================================================================

    /**
     * Some attribute
     *
     * @var array
     */
    private $_availableLinkTypes = [];

    /**
     * Some attribute
     *
     * @var array
     */
    private $_enabledLinkTypes;

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('super-button', 'Super Button');
    }

    /**
     * @inheritdoc
     */
    public static function defaultSelectLinkText(): string
    {
        return Craft::t('super-button', 'Select link type');
    }

    /**
     * @inheritdoc
     */
    public static function hasContentColumn(): bool
    {
        return true;
    }

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        // $rules = array_merge($rules, [
        //     ['someAttribute', 'string'],
        //     ['someAttribute', 'default', 'value' => 'Some Default'],
        // ]);
        return $rules;
    }

    /**
     * Returns the column type that this field should get within the content table.
     *
     * This method will only be called if [[hasContentColumn()]] returns true.
     *
     * @return string The column type. [[\yii\db\QueryBuilder::getColumnType()]] will be called
     * to convert the give column type to the physical one. For example, `string` will be converted
     * as `varchar(255)` and `string(100)` becomes `varchar(100)`. `not null` will automatically be
     * appended as well.
     * @see \yii\db\QueryBuilder::getColumnType()
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * Normalizes the field’s value for use.
     *
     * This method is called when the field’s value is first accessed from the element. For example, the first time
     * `entry.myFieldHandle` is called from a template, or right before [[getInputHtml()]] is called. Whatever
     * this method returns is what `entry.myFieldHandle` will likewise return, and what [[getInputHtml()]]’s and
     * [[serializeValue()]]’s $value arguments will be set to.
     *
     * @param mixed                 $value   The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return mixed The prepared field value
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if ($value instanceof ButtonLink) {
            return $value;
        }

        if (is_string($value)) {
            $value = Json::decodeIfJson($value);
        }

        if (isset($value['type']) && $value['type'] != '') {
            if (isset($value['value']) && $value['value'] == '') {
                return null;
            }

            if (isset($value['values'])) {
                $postedValue = $value['values'][$value['type']] ?? '';
                $value['value'] = is_array($postedValue) ? $postedValue[0] : $postedValue;
                unset($value['values']);
            }

            $link = $this->_getLinkTypeModelByType($value['type']);
            $link->setAttributes($value, false); // TODO: Get Rules added for these and remove false
            $link->ownerElement = $element;
            return $link;
        }

        return null;
    }

    /**
     * Modifies an element query.
     *
     * This method will be called whenever elements are being searched for that may have this field assigned to them.
     *
     * If the method returns `false`, the query will be stopped before it ever gets a chance to execute.
     *
     * @param ElementQueryInterface $query The element query
     * @param mixed                 $value The value that was set on this field’s corresponding [[ElementCriteriaModel]] param,
     *                                     if any.
     *
     * @return null|false `false` in the event that the method is sure that no elements are going to be found.
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        $serialized = [];

        if ($value instanceof ButtonLink) {
            $serialized = [
                'type' => $value->type,
                'value' => $value->value,
                'customText' => $value->customText,
                'target' => $value->target,
            ];
        }

        return parent::serializeValue($serialized, $element);
    }

    /**
     * Returns the component’s settings HTML.
     *
     * @return string|null
     */
    public function getSettingsHtml()
    {
        $view = Craft::$app->getView();
        $view->registerAssetBundle(SuperButtonSettingsAsset::class);

        // Render the settings template
        $prefix = $view->namespaceInputId('');

        return $view->renderTemplate(
            'super-button/_settings/index',
            [
                'prefix' => $prefix,
                'field' => $this,
            ]
        );
    }

    /**
     * Returns the field’s input HTML.
     *
     * @param mixed                 $value           The field’s value. This will either be the [[normalizeValue() normalized value]],
     *                                               raw POST data (i.e. if there was a validation error), or null
     * @param ElementInterface|null $element         The element the field is associated with, if there is one
     *
     * @return string The input HTML.
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(SuperButtonFieldAsset::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
        ];
        $jsonVars = Json::encode($jsonVars);
        Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').SuperButtonSuperButton(" . $jsonVars . ");");

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'super-button/_components/fields/SuperButton_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
            ]
        );
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getLinkTypes(): array
    {
        if (!$this->_availableLinkTypes) {
            $linkTypes = SuperButton::$plugin->service->getLinkTypes();
            if ($linkTypes) {
                foreach ($linkTypes as $linkType) {
                    $this->_availableLinkTypes[] = $this->_populateLinkTypeModel($linkType);
                }
            }
        }
        return $this->_availableLinkTypes;
    }

    // Private Methods
    // =========================================================================

    /**
     * Undocumented function
     *
     * @param string $type
     * @param boolean $populate
     * @return void
     */
    private function _getLinkTypeModelByType(string $type, bool $populate = true)
    {
        try {
            $linkType = Craft::createObject($type);
            if ($populate) {
                $linkType = $this->_populateLinkTypeModel($linkType);
            }
            return $linkType;
        } catch (ErrorException $exception) {
            $error = $exception->getMessage();
            return false;
        }
    }

    /**
     * Undocumented function
     *
     * @param ButtonLinkInterface $linkType
     * @return void
     */
    private function _populateLinkTypeModel(ButtonLinkInterface $linkType)
    {
        // Get Type Settings
        $attributes = $this->types[$linkType->type] ?? [];
        $linkType->setAttributes($attributes, false);
        $linkType->fieldSettings = $this->getSettings();
        return $linkType;
    }
}
