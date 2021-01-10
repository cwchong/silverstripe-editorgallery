<?php
/**
 * Translatable captions
 */
class InPageGalleryPhoto extends MultilingualDataObject
{
    private static $singular_name = 'Photo';
    private static $plural_name = 'Photos';

    /**
     * @var array
     */
    private static $db = array(
        // 'SortOrder' => 'Int', // ML-DO already has SortOrder field defined
        'Caption' => 'Text'
    );
    
    /**
     * @var array
     */
    public static $multilingual_fields = array(
        'Caption'
    );
    
    /**
     * @var array
     */
    private static $has_one = array(
        'Gallery' => 'InPageGallery',
        'Image' => 'Image',
        'ImageSmall' => 'Image'
    );
    
    /**
     * @var array
     */
    private static $summary_fields = array(
        'Image.CMSThumbnail' => 'Image',
        'Caption' => 'Caption',
    );
    
    /**
     * @var string|array
     */
    private static $default_sort = 'SortOrder ASC';
    
    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
		$fields = parent::getCMSFields();
        $fields->removeByName('GalleryID');
        $fields->removeByName('SortOrder');
        $fields->removeByName('Caption'); // else we cannot reposition
        
        $folder = 'Gallery/' . $this->GalleryID;
        
        $fields->addFieldsToTab('Root.Main', array(
            UploadField::create('Image')
                ->setFolderName($folder)
                ->setDisplayFolderName($folder)
                ->setAllowedFileCategories('image'),
            UploadField::create('ImageSmall', 'Image (small)')
                ->setFolderName($folder)
                ->setDisplayFolderName($folder)
                ->setAllowedFileCategories('image')
                ->setDescription('Displayed in Wide Carousel'),
            TextField::create('Caption') // not using textarea as misleading fe supports newline
        ));
        
        $this->doExtend('updateCMSFields', $fields, get_class());
        return $fields;
    }
    
    /**
     * fix for multiple entries added without re-ordering, resulting in all having sortorder 0
     */
    protected function onBeforeWrite() {
        if(!$this->SortOrder) {
            $this->SortOrder = 1;
            /* calling ->write() can potentially be recursively calling onBeforeWrite; instead use direct query */
            /* for ML-DOs, the sort column is on base MLDO, but Parent is in descendant do */
            $update = SQLUpdate::create('"MultilingualDataObject","InPageGalleryPhoto"')
                /*->addInnerJoin('"VideoSeries"', // inner join dun work for SQLUpdate atm 
                    '"MultilingualDataObject"."ID" = "VideoSeries"."ID"',
                    null,
                    10,
                    array()
                )*/
                ->addWhere('"MultilingualDataObject"."ID" = "InPageGalleryPhoto"."ID"')
                ->addWhere(array(
                    '"InPageGalleryPhoto"."GalleryID" = ?' => $this->GalleryID,
                    '"MultilingualDataObject"."ID" != ?' => $this->ID
                ))
            ;
            $update->assignSQL('"MultilingualDataObject"."SortOrder"', '"MultilingualDataObject"."SortOrder" + 1');
            $update->execute();
        }
        parent::onBeforeWrite();
    }
    
    public function canView($member = null) {
        return Permission::check('CMS_ACCESS_CMSMain');
    }
    
    public function canCreate($member = null) {
	   return Permission::check('CMS_ACCESS_CMSMain');
    }
    
    public function canEdit($member=null) {
    	return Permission::check('CMS_ACCESS_CMSMain');
    }
    
    public function canDelete($member = null) {
    	return Permission::check('CMS_ACCESS_CMSMain');
    }

}
