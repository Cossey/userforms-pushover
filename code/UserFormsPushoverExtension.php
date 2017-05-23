<?php

/*
SilverStripe UserForms Pushover Extension
Developed by Stewart Cossey
*/

class UserFormsPushoverExtension extends DataExtension
{
	
	public $pushoverTemplateLocation = 'userforms-pushover/templates/pushover';
	
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
	
	public function updateFilteredEmailRecipients($recipients, $data, $form)
	{	
		$submittedFields = new ArrayList();
		
		foreach ($this->owner->Fields() as $field) {
            if (!$field->showInReports()) {
                continue;
            }

            $submittedField = $field->getSubmittedFormField();
            //$submittedField->ParentID = $submittedForm->ID;
            $submittedField->Name = $field->Name;
            $submittedField->Title = $field->getField('Title');

            // save the value from the data
            if ($field->hasMethod('getValueFromData')) {
                $submittedField->Value = $field->getValueFromData($data);
            } else {
                if (isset($data[$field->Name])) {
                    $submittedField->Value = $data[$field->Name];
                }
            }

            $submittedFields->push($submittedField);
        }
		
		foreach($this->owner->PushoverEndpoints() as $poend) {
			//Build Fields for Template
			$fdata = ArrayData::create(array(
				'Fields' => $submittedFields,
				'PushoverUserKey' => $poend->UserKey,
				'PageTitle' => $this->owner->Title,
			));
			
			$pri = 0;
			//Convert Priority Enum to Integer
			switch($poend->Priority) {
				case 'Lowest':
					$pri = -2;
					break;
				case 'Low':
					$pri = -1;
					break;
				case 'Normal':
					$pri = 0;
					break;
				case 'High':
					$pri = 1;
					break;					
				case 'Emergency':
					$pri = 2;
					break;					
			}
			
			if(isset($poend->DeviceNames)) {
				curl_setopt_array($ch = curl_init(), array(
				  CURLOPT_URL => "https://api.pushover.net/1/messages.json",
				  CURLOPT_POSTFIELDS => array(
					"token" => $this->owner->ApplicationKey,
					"user" => $poend->UserKey,
					"device" => $poend->DeviceNames,
					"priority" => $pri,
					"message" => $fdata->renderWith($poend->PoTemplate)
				  ),
				  CURLOPT_SAFE_UPLOAD => true,
				  CURLOPT_RETURNTRANSFER => true,
				));
				curl_exec($ch);
				curl_close($ch);
			} else {
				curl_setopt_array($ch = curl_init(), array(
				  CURLOPT_URL => "https://api.pushover.net/1/messages.json",
				  CURLOPT_POSTFIELDS => array(
					"token" => $this->owner->ApplicationKey,
					"user" => $poend->UserKey,
					"priority" => $pri,
					"message" => $fdata->renderWith($poend->PoTemplate)
				  ),
				  CURLOPT_SAFE_UPLOAD => true,
				  CURLOPT_RETURNTRANSFER => true,
				));
				curl_exec($ch);
				curl_close($ch);		
			}			
		}

	}
}

