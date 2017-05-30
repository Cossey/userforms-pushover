<?php 

/*
* SilverStripe UserForms Pushover Extension
* Stewart Cossey
*
* Adds Pushover Notification Endpoints
*/
class PushoverRecipient extends DataObject {

	private static $db = array(
		'UserKey' => 'Varchar',
		'DeviceNames' => 'Varchar',
		'Priority' => "Enum('Lowest,Low,Normal,High,Emergency','Normal')",
		'PoTemplate' => 'Varchar',
	);
	
	public function populateDefaults() {
		$this->Priority = 'Normal';
		$this->PoTemplate = 'SubmittedFormPushover';
		
		switch (strtolower(Config::inst()->get('Pushover', 'priority')))
		{
			case 'lowest':
			case 'low':
			case 'normal':
			case 'high':
			case 'emergency':
				$this->Priority = ucfirst(strtolower(Config::inst()->get('Pushover', 'priority')));
				break;
		}
		
		if (Config::inst()->get('Pushover', 'default_template'))
		{
			$this->PoTemplate = Config::inst()->get('Pushover', 'default_template');
		}
		
	}
	
	private static $summary_fields = array (
		'UserKey' => 'User Key',
		'DeviceNames' => 'Devices',
		'Priority' => 'Priority',
	);

	private static $has_one = array(
		'UserDefinedForm' => 'UserDefinedForm'
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
			TextField::create('UserKey','User Key'),
			$DevNames = TextField::create('DeviceNames','Devices'),
			DropdownField::create('Priority', 'Notification Priority', $this->dbObject('Priority')->enumValues()),
			DropdownField::create('PoTemplate', 'Pushover Template', $this->getPushoverTemplateDropdownValues())
		);
		$DevNames->setRightTitle('Comma seperated list of device names. Leave blank for all devices.');

		return $fields;
	}

	//Gets a list of Pushover SilverStripe templates for Dropdowns
	public function getPushoverTemplateDropdownValues()
	{
		$templates = array();
		$finder = new SS_FileFinder();
		$finder->setOption('name_regex', '/^.*\.ss$/');
		$found = $finder->find(BASE_PATH . '/userforms-pushover/templates/pushover');

		foreach ($found as $key => $value) {
            $template = pathinfo($value);
            $templates[$template['filename']] = $template['filename'];
        }
        return $templates;
	}
}