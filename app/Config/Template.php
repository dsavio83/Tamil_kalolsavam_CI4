<?php

/*
 | -------------------------------------------------------------------------
 | Active template
 | -------------------------------------------------------------------------
 | The $template['active_template'] setting lets you choose which template
 | group to make active. By default there is only one group (the "default").
 */
return [
    'active_template' => 'default',

    // Default template configuration
    'default' => [
        'template'       => 'template',
        'regions'        => [
            'header',
            'title',
            'content',
            'sidebar',
            'footer',
            'login_area',
            'menu',
            'error',
            'message',
            'on_load',
            'left_panel',
        ],
        'parser'         => 'parser',
        'parser_method'  => 'parse',
        'parse_template' => false,
    ],
];
