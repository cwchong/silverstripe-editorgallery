<?php
/**
 * This is translatable, as the name may be displayed in fe
 */
namespace Cwchong\SilverstripeEditorgallery\model;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\View\ArrayData;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use TractorCow\Fluent\Extension\FluentExtension;

class InPageTimeline extends DataObject
{
    private static $table_name = 'Cw_SEG_InPageTimeline';
    private static $singular_name = 'Timeline';
    private static $plural_name = 'Timelines';

    private static $extensions = [
        FluentExtension::class
    ];

    /**
     * @var array
     */
    private static $db = [
        'Name' => 'Varchar(255)',
    ];
    
    /**
     * @var array
     */
    public static $translate = [
        'Name'
    ];
    
    /**
     * @var array
     */
    private static $has_one = [
        'Page' => \Page::class,
    ];
    
    /**
     * @var array
     */
    private static $has_many = [
        'Items' => InPageTimelineItem::class,
    ];
    
    /**
     * @var array
     */
    private static $summary_fields = [
        'Name' => 'Name',
        'NumItems' => '# Items'
    ];
    
    /**
     * @var string|array
     */
    private static $default_sort = '"Name" ASC'; // double quote for fluent compat
    
    /**
     * @return integer
     */
    public function getNumItems() 
    {
        return $this->Items()->Count();
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function(FieldList $f) {
            $nameField = $f->dataFieldByName('Name');
            $nameField->setDescription('Displayed in Horizontal Timeline');
        });
        $fields = parent::getCMSFields();
        $fields->removeByName([
            'PageID',
            'Items', // in order to remove the extra tab
        ]);
        
        if($this->ID) {
            $gridConfig = GridFieldConfig_RecordEditor::create();
            $gridConfig->addComponent(new GridFieldOrderableRows('SortOrder'));
            $gridConfig->removeComponentsByType(GridFieldPaginator::class);
            $gridConfig->removeComponentsByType(GridFieldPageCount::class);
            $gridField = GridField::create(
                'Items',
                'Items',
                $this->Items(),
                $gridConfig
            );
            $fields->addFieldToTab('Root.Main', $gridField);
        } else {
            $fields->addFieldToTab('Root.Main', LiteralField::create('notice', '<em>Save this Timeline before adding Items</em>'));
        }

        return $fields;
    }
    
    /**
     * Shortcode parser callback to replace "[inpagetimeline id=n]Name[/inpagetimeline]" with InPageTimeline.Content contents 
     * Note: any content passed in via the $content parameter will be ignored.
     *
     * @param array $arguments Any parameters attached to the shortcode as an associative array (keys are lower-case).
     * @param string $content Any content enclosed within the shortcode (if it is an enclosing shortcode). Note that
     * any content within this will not have been parsed, and can optionally be fed back into the parser.
     * @param ShortcodeParser The ShortcodeParser instance used to parse the content.
     * @return mixed (null | string)
     * @see \ShortcodeParser
     */
    public static function inpagetimeline_shortcode_handler($arguments, $content = null, $parser = null)
    {
        // return null if the id argument is not valid
        if (!isset($arguments['id']) || !is_numeric($arguments['id'])) {
            return;
        }
        
        // Load the DataObject using the id from $arguments array, else return null if it was not found
        if (!($inpagetimeline = DataObject::get_by_id(InPageTimeline::class, $arguments['id']))) {
            return;
        }
        
        $type = 'horizontal';
        if(isset($arguments['type']))
            $type = $arguments['type'];

        $data = new ArrayData(['Timeline' => $inpagetimeline]);
        return $data->renderWith('InPageTimeline_'.$type);
        
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
