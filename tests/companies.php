<?php
require_once __DIR__ .'/../dash-loader.php';

$companies = new \Dash\Companies($pdo);

$default = [
        'company_name'          => 'Test Company',
        'address'               => '',
        'city'                  => '',
        'country'               => '',
        'vat_number'            => '',
        'primary_email'         => '',
        'primary_phone'         => '',
        'company_status'        => 'active',
        'user_id'               => 1,
        'created_by_user_id'    => 1
    ];

$id = $companies->save($default);
print_r($id);

$deleted = $companies->delete($id, 1);

$records = $companies->getAll();
print_r($records);

$active = $companies->getByStatus('active');
print_r($active);

$byId = $companies->getById($id);
print_r($byId);

$default = [
    'company_name'          => 'SecondSite',
    'address'               => 'test Address 1',
    'city'                  => 'Cape Town',
    'country'               => 'South Africa',
    'vat_number'            => '123456',
    'primary_email'         => 'gregory@realhost.co.za',
    'primary_phone'         => '0799959818',
    'company_status'        => 'active',
    'updated_by_user_id'    => 1
];
$default['company_id'] = 3;
$companies->update($default);