<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return array(
	'default'	=>	array(
            'table' =>  array(
                'attributes'    =>  array(
                    'class' =>  'table',
                ),
            ),
		'link'	=>	array(
			'attributes'	=>	array(
				'class'	=>	'btn btn-primary btn-xs',
			),
			
		),
                'multipleLinks' =>  array(
                    'attributes'    =>  array(
                        'class'	=>	'btn btn-primary btn-xs',
			'style'	=>	'margin-right: 5px',
                    ),
                ),
		'sortable'	=>	true,
	),
	'panel'	=>	array(
		'table' =>  array(
                'attributes'    =>  array(
                    'class' =>  'table',
                ),
            ),
		'custom'	=>	array(
			'tableTitle'	=>	'Default Table Title',
		),
	),
        'box'	=>	array(
            'table' =>  array(
                'attributes'    =>  array(
                    'class' =>  'table',
                ),
            ),
            'column'    =>  array(
            ),
            'cell'  =>  array(
            ),
            'timestamp' =>  array(
                'attributes'    =>  array(
                    'style' =>  'display: inline',
                    'data-toggle'   =>  'tooltip',
                    'data-placement'    =>  'top',
                ),
            ),
            'link'	=>	array(
                'attributes'    =>  array(
                    'class'	=>	'btn btn-primary btn-xs',
                    'target'    =>  '_blank',
                ),
            ),
            'multipleLinks' =>  array(
                'attributes'    =>  array(
                    'class'	=>	'btn btn-primary btn-xs',
                    'style'	=>	'margin-right: 5px',
                    'target'=>      '_blank',
                ),
            ),
            'rowsLimit' =>  100,
            'custom' => array(
                'tableTitle' => null,
            ),
		'sortable'	=>	true,
	),
);