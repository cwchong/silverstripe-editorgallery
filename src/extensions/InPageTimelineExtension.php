<?php

namespace Cwchong\SilverstripeEditorgallery\extensions;

use Cwchong\SilverstripeEditorgallery\model\InPageTimeline;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\Tab;
use SilverStripe\ORM\DataExtension;
/**
 * This class is responsible for adding In-Page-Timelines to pages.
 */

class InPageTimelineExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $has_many = array(
        'InPageTimelines' => InPageTimeline::class,
    );

    /**
     * {@inheritdoc}
     */
    public function updateCMSFields(FieldList $fields)
    {
        $tab = new Tab('Timelines', 'Timelines');

        $gridConfig = GridFieldConfig_RecordEditor::create();
        $gridField = GridField::create(
            'InPageTimelines',
            'Timelines',
            $this->owner->InPageTimelines(),
            $gridConfig
        );

        $tab->Fields()->add($gridField);

        $fields->addFieldToTab('Root', $tab);

        return $fields;
    }
}
