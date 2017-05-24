<?php

/*
* SilverStripe UserForms Pushover Extension
* Stewart Cossey
*
* Overrides UserDefinedForms to add Pushover Settings to CMS
*/
class UserFormsPushoverExtension extends DataExtension
{

	private static $db = array(
		'ApplicationKey' => 'Varchar'
	);
	
	private static $has_many = array(
        'PushoverEndpoints' => 'PushoverEndpoint'
    );
	
	public function updateCMSFields(FieldList $fields)
    {
		$fields->addFieldsToTab("Root.Recipients", array (
			$PushAPIKey = TextField::create('ApplicationKey', 'Pushover Application API Key'),
			$POEndPoints = GridField::create('PushoverEndpoints', 'Pushover Notifications', $this->owner->PushoverEndpoints(), GridFieldConfig_RecordEditor::create())
		));
		$PushAPIKey->setRightTitle('Enter your application key. To create a new application <a href="https://pushover.net/apps/build">click here</a>.');
		
		return $fields; 
	}
}