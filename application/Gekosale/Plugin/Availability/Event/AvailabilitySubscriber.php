<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Gekosale\Plugin\Availability\Event;

use Symfony\Component\EventDispatcher\Event,
	Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Gekosale\Plugin\CurrencyFormEvent;

/**
 * Class AvailabilitySubscriber
 *
 * @package Gekosale\Plugin\Availability\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class AvailabilitySubscriber implements EventSubscriberInterface
{

	public function onFormInitAction(Event $event)
	{
		$form = $event->getForm();

		$form->fields['required_data']->addTextField(
		                              Array(
		                                   'name'   => 'value1222',
		                                   'label'  => 'Test1',
		                                   'suffix' => '%'
		                              )
		);

		$repository = $event->getDispatcher()->getContainer()->get('currency.repository');

		$event->setPopulateData(
		      Array(
		           'required_data2' => Array(
			           'value2' => 'required_data2'
		           )
		      )
		);
	}

	public static function getSubscribedEvents()
	{
		return array(
			Gekosale\Plugin\Currency\Event\CurrencyFormEvent::FORM_INIT_EVENT
			'currency.form.init' => 'onFormInitAction'
		);
	}
}