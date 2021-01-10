<?php
/**
 * Translatable captions
 */
class InPageTimelineItem extends MultilingualDataObject
{
    private static $singular_name = 'Item';
    private static $plural_name = 'Items';

    /**
     * @var array
     */
    private static $db = array(
        // 'SortOrder' => 'Int', // ML-DO already has SortOrder field defined
        'Title' => 'Varchar(255)',
        'SubTitle' => 'Varchar(255)',
        'Content' => 'HTMLText'
    );
    
    /**
     * @var array
     */
    public static $multilingual_fields = array(
        'Title', 'SubTitle', 'Content'
    );
    
    /**
     * @var array
     */
    private static $has_one = array(
        'Timeline' => 'InPageTimeline',
        'Image' => 'Image'
    );
    
    /**
     * @var array
     */
    private static $summary_fields = array(
        'Title' => 'Title',
        'SubTitle' => 'SubTitle'
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
        $fields->removeByName('TimelineID');
        $fields->removeByName('SortOrder');
        
        $subField = $fields->dataFieldByName('SubTitle');
        $subField->setDescription('Displayed in Horizontal/Accordion Timeline');
        
        $folder = 'Timeline/' . $this->TimelineID;
        
        $fields->addFieldsToTab('Root.Main', array(
            UploadField::create('Image')
                ->setFolderName($folder)
                ->setDisplayFolderName($folder)
                ->setAllowedFileCategories('image')
                ->setDescription('Displayed in Vertical Timeline')
        ));
        
        //$this->doExtend('updateCMSFields', $fields, get_class());
        $this->extend('updateCMSFields', $fields);
        // observation: use ->extend if translatable parent field, and ->doExtend if fields defined here
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
            $update = SQLUpdate::create('"MultilingualDataObject","InPageTimelineItem"')
                /*->addInnerJoin('"VideoSeries"', // inner join dun work for SQLUpdate atm 
                    '"MultilingualDataObject"."ID" = "VideoSeries"."ID"',
                    null,
                    10,
                    array()
                )*/
                ->addWhere('"MultilingualDataObject"."ID" = "InPageTimelineItem"."ID"')
                ->addWhere(array(
                    '"InPageTimelineItem"."TimelineID" = ?' => $this->TimelineID,
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
