<?php
/**
 * Translatable captions
 */
namespace Cwchong\SilverstripeEditorgallery\model;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use TractorCow\Fluent\Extension\FluentExtension;

class InPageTimelineItem extends DataObject
{
    private static $table_name = 'Cw_SEG_InPageTimelineItem';
    private static $singular_name = 'Item';
    private static $plural_name = 'Items';

    private static $extensions = [
        FluentExtension::class,
    ];

    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(255)',
        'SubTitle' => 'Varchar(255)',
        'Content' => 'HTMLText',
        'SortOrder' => 'Int',
    ];
    
    /**
     * @var array
     */
    public static $translate = [
        'Title', 'SubTitle', 'Content'
    ];
    
    /**
     * @var array
     */
    private static $has_one = [
        'Timeline' => InPageTimeline::class,
        'Image' => Image::class,
    ];
    
    /**
     * @var array
     */
    private static $summary_fields = [
        'Title' => 'Title',
        'SubTitle' => 'SubTitle'
    ];
    
    /**
     * @var string|array
     */
    private static $default_sort = 'SortOrder ASC';
    
    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function(FieldList $f) {
            $subField = $f->dataFieldByName('SubTitle');
            $subField->setDescription('Displayed in Horizontal/Accordion Timeline');
        });

		$fields = parent::getCMSFields();
        $fields->removeByName([
            'TimelineID',
            'SortOrder',
        ]);
        
        $folder = 'Timeline/' . $this->TimelineID;
        
        $fields->addFieldsToTab('Root.Main', [
            UploadField::create('Image')
                ->setFolderName($folder)
                ->setAllowedFileCategories('image')
                ->setDescription('Displayed in Vertical Timeline')
        ]);
        
        return $fields;
    }
    
    public function canView($member = null) {
        return Permission::check('CMS_ACCESS_CMSMain');
    }
    
    public function canCreate($member = null, $context = []) {
	   return Permission::check('CMS_ACCESS_CMSMain');
    }
    
    public function canEdit($member=null) {
    	return Permission::check('CMS_ACCESS_CMSMain');
    }
    
    public function canDelete($member = null) {
    	return Permission::check('CMS_ACCESS_CMSMain');
    }

}
