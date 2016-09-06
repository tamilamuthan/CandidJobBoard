<?php

class SJB_GuestAlertDetails extends SJB_ObjectDetails
{
	public function getDetails()
	{
		return array (
			array (
				'id'		=> 'email',
				'caption'	=> 'Email',
				'type'		=> 'email',
				'table_name' => 'guest_alerts',
				'length'	=> '200',
				'is_required'=> true,
				'is_system'=> true,
				'order'			=> 1,
			),
			array (
				'id'			=> 'email_frequency',
				'caption'		=> 'Email frequency',
				'type'			=> 'list',
				'list_values'	=> array(
					array(
						'id'		=> 'daily',
						'caption'	=> 'Daily',
					),
					array(
						'id'		=> 'weekly',
						'caption'	=> 'Weekly',
					),
					array(
						'id'		=> 'monthly',
						'caption'	=> 'Monthly',
					),
				),
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 2,
			)
		);
	}

	public function addDataProperty($data)
	{
		$this->addProperty(array(
			'id'			=> 'data',
			'caption'		=> 'Data',
			'type'			=> 'text',
			'is_required'	=> true,
			'is_system'		=> true,
			'order'			=> 3,
			'value'			=> $data,
		));
	}

	public function addSubscriptionDateProperty($value)
	{
		$this->addProperty(array(
			'id'			=> 'subscription_date',
			'caption'		=> 'Subscription date',
			'type'			=> 'date',
			'is_required'	=> false,
			'is_system'		=> true,
			'order'			=> 5,
			'value'			=> $value,
		));
	}

	public function addStatusProperty($value)
	{
		$this->addProperty(array(
			'id' => 'status',
			'caption' => 'Status',
			'type' => 'list',
			'list_values' => array(
				array(
					'id' => '1',
					'caption' => 'Active',
				),
				array(
					'id' => '0',
					'caption' => 'Inactive',
				),
			),
			'is_required' => false,
			'is_system' => true,
			'value' => $value,
		));
	}
}
