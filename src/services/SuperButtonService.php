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
use craft\elements\Entry as CraftEntry;
use craft\models\Section;
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

    public function getEntryTypesForSourceByUid(string $section): array
    {
        $sectionService = Craft::$app->getSections();

        if ($section === "*" || $section === 'singles') {
            return [];
        }

        $uid = explode(":", $section);

        $section = $sectionService->getSectionByUid($uid[1]);

        $entryTypes = Craft::$app->getSections()->getEntryTypesBySectionId($section->id);

        return array_map(function ($entryType) {
            return [
                'value' => $entryType->id,
                'id' => $entryType->id,
                'label' => $entryType->name,
            ];
        }, $entryTypes);
    }

    public function getSourceOptions($elementType): array
    {
        $sources = Craft::$app->getElementIndexes()->getSources($elementType, 'modal');
        $options = [];
        $optionNames = [];
        foreach ($sources as $source) {
            // Make sure it's not a heading
            if (!isset($source['heading'])) {
                $options[] = [
                    'label' => $source['label'],
                    'value' => $source['key'],
                    'types' => $elementType === "craft\\elements\\Entry" ? $this->getEntryTypesForSourceByUid($source['key']) : null
                ];
                $optionNames[] = $source['label'];
            }
        }
        // Sort alphabetically
        array_multisort($optionNames, SORT_NATURAL | SORT_FLAG_CASE, $options);

        return $options;
    }

    public function getEntrySourceOptions($activeSources): array
    {
        $sources = Craft::$app->getElementIndexes()->getSources(CraftEntry::class, 'modal');
        $options = [];
        $optionNames = [];
        $sectionService = Craft::$app->getSections();

        foreach ($sources as $source) {
            // Make sure it's not a heading
            if (!isset($source['heading'])) {
                $entryTypes = [];
                $sectionUid = strpos($source['key'], 'section') !== false ? explode(":", $source['key'])[1] : false;

                if ($sectionUid) {
                    $section = $sectionService->getSectionByUid($sectionUid);
                    $entryTypes = array_map(
                        function ($type) {
                            return [
                                'label' => $type->name,
                                'value' => $type->uid,
                            ];
                        },
                        $section->getEntryTypes() ?? []
                    );
                }

                $options[] = [
                    'label' => $source['label'],
                    'value' => $source['key'],
                    'active' => is_array($activeSources) ? in_array($source['key'], $activeSources ?? []) : true,
                    'types' => $entryTypes ? $entryTypes : null
                ];
                $optionNames[] = $source['label'];
            }
        }

        // Sort alphabetically
        array_multisort($optionNames, SORT_NATURAL | SORT_FLAG_CASE, $options);

        return $options;
    }
}
