<?php

namespace Cossey\UserForms;

use Cossey\UserForms\PushoverRecipient;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Core\Config\Configurable;

/*
* SilverStripe UserForms Pushover Extension
* Stewart Cossey
*
* Extends UserDefinedForms to add Pushover Settings to CMS
*/
class UserFormsPushoverExtension extends DataExtension
{
	use Configurable;

	/**
     * @var string
     */
    private static $pushover_template_directory = 'stewartcossey/userforms-pushover:templates/pushover/';

	/**
	 * @var string
	 */
	private static $pushover_default_template = 'SubmittedFormPushover';

	private static $has_many = array(
        'PushoverRecipients' => PushoverRecipient::class
    );
	
	public function updateCMSFields(FieldList $fields)
    {
		$fields->addFieldsToTab("Root.Recipients", array (
			$POEndPoints = GridField::create('PushoverRecipients', _t('SilverStripe\\UserForms\\Model\\UserDefinedForm.PUSHOVERRCPT', 'PUSHOVERRCPT'), $this->owner->PushoverRecipients(), GridFieldConfig_RecordEditor::create())
		));
		
		return $fields; 
	}
}