<?php

namespace Cossey\UserForms;

use SilverStripe\ORM\DataExtension;
use SilverStripe\View\ArrayData;
use SilverStripe\Core\Config\Config;

use Serhiy\Pushover\Application;
use Serhiy\Pushover\Recipient;
use Serhiy\Pushover\Api\Message\Message;
use Serhiy\Pushover\Api\Message\Notification;
use Serhiy\Pushover\Api\Message\Priority;
use Serhiy\Pushover\Api\Message\Sound;

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
		$config = Config::inst();
		
		if(
			$page->exists()
			&& $this->owner->Values()->exists()
			&& $config->exists('Pushover', 'application_key')
		)
		{
			$application = new Application($config->get('Pushover', 'application_key'));
				
			$fields = $this->owner->Values();

			foreach($page->PushoverRecipients() as $poend) {
				//Build Fields and Data for Pushover Template
				$fdata = ArrayData::create(array(
					'Fields' => $fields,
					'UserKey' => $poend->UserKey,
					'PageTitle' => $page->owner->Title,
					'Devices' => $poend->DeviceNames,
					'Sound' => $poend->Sound,
					'Priority' => $poend->Priority,
					'Title' => $poend->PoTitle
				));
				
				$pri = Priority::NORMAL;
				//Convert Priority Enum to Integer
				switch($poend->Priority) {
					case 'Lowest':
						$pri = Priority::LOWEST;
						break;
					case 'Low':
						$pri = Priority::LOW;
						break;
					case 'Normal':
						$pri = Priority::NORMAL;
						break;
					case 'High':
						$pri = Priority::HIGH;
						break;					
					case 'Emergency':
						$pri = Priority::EMERGENCY;
						break;					
				}

				$recipient = new Recipient($poend->UserKey);

				if ($poend->DeviceNames) {
					$devices = explode(",", $poend->DeviceNames);
					foreach ($devices as $device) {
						$recipient->addDevice(trim($device));
					}
				}

				$message = new Message($fdata->renderWith($poend->PoTemplate));
				$message->setPriority(new Priority($pri));
				
				if ($poend->PoTitle != "") {
					$message->setTitle($poend->PoTitle);
				}
				$notification = new Notification($application, $recipient, $message);
				
				if ($poend->Sound != "") {
					$notification->setSound(new Sound($poend->Sound));
				}

				$response = $notification->push();

			}		
		}
	}
}