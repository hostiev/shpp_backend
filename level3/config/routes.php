<?php

return [
    [
        'uriPattern' => '#\/[\?\w\=\&]*#',
        'controller' => 'Books',
        'model' => 'Books',
        'defaultAction' => 'showBooks'
    ],
    [
        'uriPattern' => '#\/books[\w\=\&\?\/]*#',
        'controller' => 'Books',
        'model' => 'Books',
        'defaultAction' => 'showBook'
    ],
	[
	    'uriPattern' => '#\/admin[\w\=\&\?\/]*#',
        'controller' => 'Admin',
        'model' => 'Users',
		'defaultAction' => 'adminPage'
	],
    [
        'uriPattern' => '#\/search[\w\=\&\?\/]*#',
        'controller' => 'Books',
        'model' => 'Books',
        'defaultAction' => 'search'
    ]
];