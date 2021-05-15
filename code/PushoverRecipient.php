<?php 

namespace Cossey\UserForms;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Assets\FileFinder;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Core\Config\Config;
use SilverStripe\UserForms\Model\UserDefinedForm;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\CMS\Controllers\CMSMain;
use SilverStripe\Control\Controller;
use SilverStripe\UserForms\UserForm;

use Serhiy\Pushover\Api\Message\Sound;
/*
* SilverStripe UserForms Pushover Extension
* Stewart Cossey
*
* Adds Pushover Notification Endpoints
*/
class PushoverRecipient extends DataObject {

	private static $table_name = 'UserDefinedForm_PORecipient';

	private static $db = array(
		'UserKey' => 'Varchar',
		'DeviceNames' => 'Varchar',
		'Priority' => "Enum('Lowest,Low,Normal,High,Emergency','Normal')",
		'PoTemplate' => 'Varchar',
		'Sound' => 'Varchar',
		'PoTitle' => 'Varchar'
	);
	
	public function populateDefaults() {
		$parent = $this->getFormParent();

		$this->Sound = ''; //Default
		$this->Priority = 'Normal';
		$this->PoTemplate = $parent->config()->get('pushover_default_template');
	}
	
	private static $summary_fields = array (
		'UserKey',
		'DeviceNames',
		'Priority',
	);
	
	public function fieldLabels($includerelations = true)
	{
		$labels = parent::fieldLabels(true);
		
		$labels['UserKey'] = _t('Cossey\\UserForms\\PushoverRecipient.USERKEY', 'USERKEY');
		$labels['DeviceNames'] = _t('Cossey\\UserForms\\PushoverRecipient.DEVICENAMES', 'DEVICENAMES');
		$labels['Priority'] = _t('Cossey\\UserForms\\PushoverRecipient.NPRIORITY', 'NPRIORITY');
		return $labels;
	}
		
	private static $has_one = array(
		'UserDefinedForm' => UserDefinedForm::class
	);

	//Show title as UserKey: DeviceNames
	public function getTitle()
	{
		if ($this->DeviceNames) {
			return $this->UserKey . ": " . $this->DeviceNames;
		}
		if ($this->UserKey) {
			return $this->UserKey;
		}
		return parent::getTitle();
	}

	public function getCMSFields() 
	{
		$fields = FieldList::create(
			TextField::create('UserKey', _t('Cossey\\UserForms\\PushoverRecipient.USERKEY', 'USERKEY')),
			$DevNames = TextField::create('DeviceNames', _t('Cossey\\UserForms\\PushoverRecipient.DEVICENAMES', 'DEVICENAMES')),
			TextField::create('PoTitle', _t('Cossey\\UserForms\\PushoverRecipient.NTITLE', 'NTITLE')),
			DropdownField::create('Priority', _t('Cossey\\UserForms\\PushoverRecipient.NPRIORITY', 'NPRIORITY'), $this->dbObject('Priority')->enumValues()),
			DropdownField::create('Sound', _t('Cossey\\UserForms\\PushoverRecipient.NSOUND', 'NSOUND'), $this->getPushoverSoundDropdownValues()),
			DropdownField::create('PoTemplate', _t('Cossey\\UserForms\\PushoverRecipient.TEMPLATE', 'TEMPLATE'), $this->getPushoverTemplateDropdownValues())
		);
		$DevNames->setRightTitle(_t('Cossey\\UserForms\\PushoverRecipient.DEVICENAMESINFO', 'DEVICENAMESINFO'));

		return $fields;
	}

	/**
     * Get instance of UserForm when editing in getCMSFields
     *
     * @return UserDefinedForm|UserForm|null
     */
    protected function getFormParent()
    {
        // If polymorphic relationship is actually defined, use it
        if ($this->FormID && $this->FormClass) {
            $formClass = $this->FormClass;
            return $formClass::get()->byID($this->FormID);
        }

        // Revert to checking for a form from the session
        // LeftAndMain::sessionNamespace is protected. @todo replace this with a non-deprecated equivalent.
        $sessionNamespace = $this->config()->get('session_namespace') ?: CMSMain::class;

        $formID = Controller::curr()->getRequest()->getSession()->get($sessionNamespace . '.currentPage');
        if ($formID) {
            return UserDefinedForm::get()->byID($formID);
        }
    }

	public function getPushoverSoundDropdownValues()
	{
		$sounds = [];

		$sounds[''] = _t('Cossey\\UserForms\\PushoverRecipient.DEFAULT', 'DEFAULT'); //Set default sound

		foreach(Sound::getAvailableSounds() as $snd) {
			$sounds[$snd] = $snd;
		}

		return $sounds;
	}

	//Gets a list of Pushover SilverStripe templates for Dropdowns
	public function getPushoverTemplateDropdownValues()
	{
		$templates = [];

		$finder = new FileFinder();
		$finder->setOption('name_regex', '/^.*\.ss$/');

        $parent = $this->getFormParent();
		
        if (!$parent) {
            return [];
        }

        $pushoverTemplateDirectory = $parent->config()->get('pushover_template_directory');
        $templateDirectory = ModuleResourceLoader::resourcePath($pushoverTemplateDirectory);

        if (!$templateDirectory) {
			return [];
        }
		
        $found = $finder->find(BASE_PATH . DIRECTORY_SEPARATOR . $templateDirectory);

		foreach ($found as $key => $value) {
			$template = pathinfo($value);
            $absoluteFilename = $template['dirname'] . DIRECTORY_SEPARATOR . $template['filename'];

            // Optionally remove vendor/ path prefixes
            $resource = ModuleResourceLoader::singleton()->resolveResource($templateDirectory);
            if ($resource instanceof ModuleResource && $resource->getModule()) {
                $prefixToStrip = $resource->getModule()->getPath();
            } else {
                $prefixToStrip = BASE_PATH;
            }
            $templatePath = substr($absoluteFilename, strlen($prefixToStrip) + 1);

            // Optionally remove "templates/" prefixes
            if (preg_match('/(?<=templates\/).*$/', $templatePath, $matches)) {
                $templatePath = $matches[0];
            }

            $templates[$templatePath] = $template['filename'];
        }
        return $templates;
	}
}