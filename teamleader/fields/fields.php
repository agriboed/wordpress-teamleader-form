<?php
/**
 * See more information here http://support.teamleader.eu/topics/119-available-fields-for-web2leads/
 */
return [
    'forename' => [
        'title' => 'First name',
        'required' => true,
        'type' => 'textfield',
        'description' => 'Required',
    ],
    'surname' => [
        'title' => 'Last name',
        'required' => true,
        'type' => 'textfield',
        'description' => 'Required',
    ],
    'email' => [
        'title' => 'Email',
        'required' => true,
        'type' => 'textfield',
        'description' => 'Required',
    ],
    'street' => [
        'title' => 'Street',
        'required' => false,
        'type' => 'textfield',
        'description' => '',
    ],
    'number' => [
        'title' => 'House number',
        'required' => false,
        'type' => 'textfield',
        'description' => '',
    ],
    'zipcode' => [
        'title' => 'Zip Code',
        'required' => false,
        'type' => 'textfield',
        'description' => '',
    ],
    'city' => [
        'title' => 'City',
        'required' => false,
        'type' => 'textfield',
        'description' => '',
    ],
    'telephone' => [
        'title' => 'Telephone number',
        'required' => false,
        'type' => 'textfield',
        'description' => '',
    ],
    'gsm' => [
        'title' => 'Mobile/Fax',
        'required' => false,
        'type' => 'textfield',
        'description' => '',
    ],
    'website' => [
        'title' => 'Website',
        'required' => false,
        'type' => 'textfield',
        'description' => '',
    ],
    'language' => [
        'title' => 'Language',
        'required' => false,
        'type' => 'textfield',
        'description' => '',
    ],
    'company' => [
        'title' => 'Company',
        'required' => false,
        'type' => 'textfield',
        'description' => '',
    ],
    'taxcode' => [
        'title' => 'VAT',
        'required' => false,
        'type' => 'textfield',
        'description' => '',
    ],
    'kvk' => [
        'title' => 'KVK',
        'required' => false,
        'type' => 'textfield',
        'description' => 'KVK (The Netherlands only)',
    ],
    'remarks' => [
        'title' => 'Remarks',
        'required' => false,
        'type' => 'textarea',
        'description' => '',
    ],
];