<?php

namespace Cwchong\SilverstripeEditorgallery\extensions;

use Cwchong\SilverstripeEditorgallery\model\InPageGallery;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\Tab;
use SilverStripe\ORM\DataExtension;
/**
 * This class is responsible for adding In-Page-Galleries to pages.
 */
class InPageGalleryExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $has_many = [
        'InPageGalleries' => InPageGallery::class,
    ];

    /**
     * Adds a new Tab 'Galleries' and grid for populating the page's gallery photos
     * {@inheritdoc}
     */
    public function updateCMSFields(FieldList $fields)
    {
        $tab = new Tab('Galleries', 'Galleries');

        $gridConfig = GridFieldConfig_RecordEditor::create();
        $gridField = GridField::create(
            'InPageGalleries',
            'Galleries',
            $this->owner->InPageGalleries(),
            $gridConfig
        );

        $tab->Fields()->add($gridField);

        $fields->addFieldToTab('Root', $tab);

        return $fields;
    }
}
