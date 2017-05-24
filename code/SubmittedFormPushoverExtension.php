<?php

/*
* SilverStripe UserForms Pushover Extension
* Stewart Cossey
*
* Overrides SubmittedForm to capture Form Fields and process sending notification.
*/
class SubmittedFormPushoverExtension extends DataExtension
{
	
	public function updateAfterProcess()
	{
		//Get User forms page
		$page = $this->owner->Parent();
		
		if(
			$page->exists()
			&& $this->owner->Values()->exists()
		)
		{
			$fields = $this->owner->Values();

			foreach($page->PushoverEndpoints() as $poend) {
				//Build Fields and Data for Pushover Template
				$fdata = ArrayData::create(array(
					'Fields' => $fields,
					'UserKey' => $poend->UserKey,
					'PageTitle' => $page->owner->Title,
					'Devices' => $poend->DeviceNames
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

				//Send Pushover Message using PHP CURL
				curl_setopt_array($ch = curl_init(), array(
				  CURLOPT_URL => "https://api.pushover.net/1/messages.json",
				  CURLOPT_POSTFIELDS => array(
					"token" => $page->ApplicationKey,
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
			}		
		}
	}
}