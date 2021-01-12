<?php

namespace Cwchong\SilverstripeEditorgallery\model;

use Colymba\BulkUpload\BulkUploader;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Gallery is not translatable, but its images are due to captions
 */
class InPageGallery extends DataObject
{
    private static $table_name = 'Cw_SEG_InPageGallery';
    private static $singular_name = 'Gallery';
    private static $plural_name = 'Galleries';

    /**
     * @var array
     */
    private static $db = [
        'Name' => 'Varchar(255)',
    ];
    
    /**
     * @var array
     */
    private static $has_one = [
        'Page' => \Page::class,
        'Background' => Image::class
    ];
    
    /**
     * @var array
     */
    private static $has_many = [
        'Photos' => InPageGalleryPhoto::class,
    ];
    
    /**
     * @var array
     */
    private static $summary_fields = [
        'Name' => 'Name',
        'NumPhotos' => '# Photos'
    ];
    
    /**
     * @var string|array
     */
    private static $default_sort = 'Name ASC';
    
    /**
     * @return integer
     */
    public function getNumPhotos() 
    {
        return $this->Photos()->Count();
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName([
            'PageID',
            'Photos' // in order to remove the extra tab
        ]);
        
        if($this->ID) {
            $folder = 'Gallery/' . $this->ID;
            
            $gridConfig = GridFieldConfig_RecordEditor::create();
            $gridConfig->addComponent(new GridFieldOrderableRows('SortOrder'));
            $gridConfig->addComponent(new BulkUploader());
            /** @var BulkUploader $uploadConfig */
            $uploadConfig = $gridConfig->getComponentByType(BulkUploader::class);
            $uploadConfig->setUfSetup('setFolderName', 'Gallery/' . $this->ID);
            $gridConfig->removeComponentsByType(GridFieldPaginator::class);
            $gridConfig->removeComponentsByType(GridFieldPageCount::class);
            $gridField = GridField::create(
                'Photos',
                'Photos',
                $this->Photos(),
                $gridConfig
            );
            $fields->addFieldsToTab('Root.Main', [
                UploadField::create('Background', 'Background')
                    ->setFolderName($folder)
                    ->setAllowedFileCategories('image')
                    ->setDescription('Displayed in Wide Carousel'),
                $gridField
            ]);
        } else {
            $fields->addFieldToTab('Root.Main', LiteralField::create('notice', '<em>Save this Gallery before adding Photos</em>'));
        }
        
        return $fields;
    }
    
    /**
     * Shortcode parser callback to replace "[inpagegallery id=n]Gallery Name[/inpagegallery]" with InPageGallery.Content contents 
     * Note: any content passed in via the $content parameter will be ignored.
     *
     * @param array $arguments Any parameters attached to the shortcode as an associative array (keys are lower-case).
     * @param string $content Any content enclosed within the shortcode (if it is an enclosing shortcode). Note that
     * any content within this will not have been parsed, and can optionally be fed back into the parser.
     * @param ShortcodeParser The ShortcodeParser instance used to parse the content.
     * @return mixed (null | string)
     * @see \ShortcodeParser
     */
    // xxxx kiv
    // public static function inpagegallery_shortcode_handler($arguments, $content = null, $parser = null)
    // {
    //     // return null if the id argument is not valid
    //     if (!isset($arguments['id']) || !is_numeric($arguments['id'])) {
    //         return;
    //     }
        
    //     // Load the Advertisement DataObject using the id from $arguments array, else return null if it was not found
    //     if (!($inpagegallery = DataObject::get_by_id('InPageGallery', $arguments['id']))) {
    //         return;
    //     }
        
    //     $type = 'grid';
    //     if(isset($arguments['type']))
    //         $type = $arguments['type'];

    //     $data = new ArrayData(array('Gallery' => $inpagegallery));
    //     return $data->renderWith('InPageGallery_'.$type);
        
    // }
    
    public function canView($member = null) 
    {
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
