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
		'UserKey',
		'DeviceNames',
		'Priority',
	);
	
	public function fieldLabels($includerelations = true)
	{
		$labels = parent::fieldLabels(true);
		
		$labels['UserKey'] = _t('PushoverRecipient.USERKEY', 'User/Group Key');
		$labels['DeviceNames'] = _t('PushoverRecipient.DEVICENAMES', 'Device Name(s)');
		$labels['Priority'] = _t('PushoverRecipient.NPRIORITY', 'Notification Priority');
		return $labels;
	}
		
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
			TextField::create('UserKey', _t('PushoverRecipient.USERKEY', 'User/Group Key')),
			$DevNames = TextField::create('DeviceNames', _t('PushoverRecipient.DEVICENAMES', 'Device Name(s)')),
			DropdownField::create('Priority', _t('PushoverRecipient.NPRIORITY', 'Notification Priority'), $this->dbObject('Priority')->enumValues()),
			DropdownField::create('PoTemplate', _t('PushoverRecipient.TEMPLATE', 'Pushover Template'), $this->getPushoverTemplateDropdownValues())
		);
		$DevNames->setRightTitle(_t('PushoverRecipient.DEVICENAMESINFO', 'Comma seperated list of device names. Leave empty for all devices.'));

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