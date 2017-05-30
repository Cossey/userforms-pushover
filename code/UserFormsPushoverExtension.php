<?php

/*
* SilverStripe UserForms Pushover Extension
* Stewart Cossey
*
* Overrides UserDefinedForms to add Pushover Settings to CMS
*/
class UserFormsPushoverExtension extends DataExtension
{

	private static $has_many = array(
        'PushoverRecipients' => 'PushoverRecipient'
    );
	
	public function updateCMSFields(FieldList $fields)
    {
		$fields->addFieldsToTab("Root.Recipients", array (
			$POEndPoints = GridField::create('PushoverRecipients', _t('UserDefinedForm.Pushover.PUSHOVERRCPT', 'Pushover Recipients'), $this->owner->PushoverRecipients(), GridFieldConfig_RecordEditor::create())
		));
		
		return $fields; 
	}
}