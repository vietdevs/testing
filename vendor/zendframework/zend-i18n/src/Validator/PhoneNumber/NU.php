<?php
/**
 * @see       https://github.com/zendframework/zend-i18n for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-i18n/blob/master/LICENSE.md New BSD License
 */

return [
    'code' => '683',
    'patterns' => [
        'national' => [
            'general' => '/^[1-5]\\d{3}$/',
            'fixed' => '/^[34]\\d{3}$/',
            'mobile' => '/^[125]\\d{3}$/',
            'emergency' => '/^999$/',
        ],
        'possible' => [
            'general' => '/^\\d{4}$/',
            'emergency' => '/^\\d{3}$/',
        ],
    ],
];
