<?php
/**
 * Controller to respond to the javascript api call to populate the dropdown options 
 * within the tinymce component that inserts inpagegallery shortcodes
 *
 */
class InPageGalleryController extends Controller
{

    /**
     * @var array $allowed_actions whitelisting of public methods available in this Controller 
     */
    private static $allowed_actions = array(
        'json'
    );

    /**
     * Initialises the controller and ensures that only
     * users who can access InPageGallery (previously only ADMIN level users) can access this controller
     */
    public function init()
    {
        parent::init();
        if (!Permission::check('CMS_ACCESS_CMSMain')) { // 'ADMIN'
            return $this->httpError(403);
        }
    }

    /**
     * Returns a json encoded array of InPageGallery dataobjects in the format: (ID, Name) 
     *
     * @param SS_HTTPRequest $request Expects a pageid param for limiting the objects returned
     * @return SS_HTTPResonse A http response containing a json encoded array
     * @see: /_config/routes.yml
     */
    public function json(SS_HTTPRequest $request)
    {
        $this->response->addHeader('Content-Type', 'application/json');
        $pageid = $request->getVar('pageid');
        if($pageid && ($list = InPageGallery::get()->filter(array('PageID' => intval($pageid)))->map('ID', 'Name'))) {
            $this->response->setBody(json_encode($list->toArray()));
            return $this->response;
        }
    }
}
