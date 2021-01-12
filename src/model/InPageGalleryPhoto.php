<?php

namespace Cwchong\SilverstripeEditorgallery\model;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use TractorCow\Fluent\Extension\FluentExtension;
/**
 * Translatable captions
 */
class InPageGalleryPhoto extends DataObject
{
    private static $table_name = 'Cw_SEG_InPageGalleryPhoto';
    private static $singular_name = 'Photo';
    private static $plural_name = 'Photos';

    private static $extensions = [
        FluentExtension::class,
    ];

    /**
     * @var array
     */
    private static $db = [
        'Caption' => 'Text',
        'SortOrder' => 'Int',
    ];
    
    /**
     * @var array
     */
    public static $translate = [
        'Caption'
    ];
    
    /**
     * @var array
     */
    private static $has_one = [
        'Gallery' => InPageGallery::class,
        'Image' => Image::class,
        'ImageSmall' => Image::class,
    ];
    
    /**
     * @var array
     */
    private static $summary_fields = [
        'Image.CMSThumbnail' => 'Image',
        'Caption' => 'Caption',
    ];

    private static $owns = [
        'Image', 'ImageSmall',
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
            $f->replaceField('Caption', TextField::create('Caption'));// not using textarea as misleading fe supports newline
        });

		$fields = parent::getCMSFields();
        $fields->removeByName([
            'GalleryID',
            'SortOrder',
        ]);
        
        $folder = 'Gallery/' . $this->GalleryID;
        
        $fields->addFieldsToTab('Root.Main', [
            UploadField::create('Image')
                ->setFolderName($folder)
                ->setAllowedFileCategories('image'),
            UploadField::create('ImageSmall', 'Image (small)')
                ->setFolderName($folder)
                ->setAllowedFileCategories('image')
                ->setDescription('Displayed in Wide Carousel'),
        ], 'Caption');
        
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
