<?php
/**
 * see http://support.teamleader.eu/topics/119-available-fields-for-web2leads/
 */
return [
    'forename' => [
        'title' => 'First name',
        'required' => true,
        'type' => 'text',
        'description' => 'Required',
    ],
    'surname' => [
        'title' => 'Last name',
        'required' => true,
        'type' => 'text',
        'description' => 'Required',
    ],
    'email' => [
        'title' => 'Email',
        'required' => true,
        'type' => 'text',
        'description' => 'Required',
    ],
    'street' => [
        'title' => 'Street',
        'required' => false,
        'type' => 'text',
        'description' => '',
    ],
    'number' => [
        'title' => 'House number',
        'required' => false,
        'type' => 'text',
        'description' => '',
    ],
    'zipcode' => [
        'title' => 'Zip Code',
        'required' => false,
        'type' => 'text',
        'description' => '',
    ],
    'city' => [
        'title' => 'City',
        'required' => false,
        'type' => 'text',
        'description' => '',
    ],
    'telephone' => [
        'title' => 'Telephone number',
        'required' => false,
        'type' => 'text',
        'description' => '',
    ],
    'gsm' => [
        'title' => 'Mobile/Fax',
        'required' => false,
        'type' => 'text',
        'description' => '',
    ],
    'website' => [
        'title' => 'Website',
        'required' => false,
        'type' => 'text',
        'description' => '',
    ],
    'language' => [
        'title' => 'Language',
        'required' => false,
        'type' => 'text',
        'description' => '',
    ],
    'company' => [
        'title' => 'Company',
        'required' => false,
        'type' => 'text',
        'description' => '',
    ],
    'taxcode' => [
        'title' => 'VAT',
        'required' => false,
        'type' => 'text',
        'description' => '',
    ],
    'kvk' => [
        'title' => 'KVK',
        'required' => false,
        'type' => 'text',
        'description' => 'KVK (The Netherlands only)',
    ],
];