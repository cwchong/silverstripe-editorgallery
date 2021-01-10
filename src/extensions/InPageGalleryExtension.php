<?php

/**
 * This class is responsible for adding In-Page-Galleries to pages.
 */
class InPageGalleryExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $has_many = array(
        'InPageGalleries' => 'InPageGallery',
    );

    /**
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
