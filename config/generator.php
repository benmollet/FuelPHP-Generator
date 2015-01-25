<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return array(
	'form'	=>	array(
		'default'	=>	array(
			'inline_errors'              => true,
			'addSubmit'	=>	array(
				'field'	=>	'submit',
				'value'	=>	'value',
			),
			'text'	=>	array(
						'attributes'    =>  array(
							'class'	=>	'form-control',
						),
			),
					'submit'  =>  array(
						'attributes'    =>  array(
							'class' =>  'btn btn-primary',
							'template'  =>  '_inline_field',
						),
					),
				'textarea' => array(
					'attributes' => array(
						'class' => 'form-control',
					),
				),
					'resetAttributes'   =>  array(
						'class' =>  'btn btn-primary',
						'template'  =>  '_inline_field',
					),
					'textareaAttributes'    =>  array(
						'class' =>  'form-control',
					),
					'multiSelect'  =>  array(
						'attributes'	=>	array(
							'class' =>  'chosen-select',
							'style' =>  'width: 300px',
						),
					),
					'select'  =>  array(
						'attributes'	=>	array(
							'class' =>  'chosen-select',
							'style' =>  'width: 300px',
						),
					),
					'link'  =>  array(
						'attributes'    =>  array(

						),
					),
		),
	),
	'list'	=>	array(
		'default'	=>	array(
			'outerElement'	=>	array(
				'tag'	=>	'ul',
				'attributes'	=>	array(
					'class'	=>	'list-group',
				),
			),
			'innerElement'	=>	array(
				'tag'	=>	'li',
				'attributes'	=>	array(
					'class'	=>	'list-group-item',
				),
			),
		),
		'breadcrumbs'	=>	array(
			'outerElement'	=>	array(
				'tag'	=>	'ol',
				'attributes'	=>	array(
					'class'	=>	'breadcrumb',
				),
			),
			'innerElement'	=>	array(
				'tag'	=>	'li',
			),
		),
	),
	'table'	=>	array(
		'default'	=>	array(
			'attributes' => array(
				'class'	=>	'table table-bordered',
			),
			'link'  =>  array(
				'class' =>  'btn btn-xs',
				'target'    =>  '_blank',
			),
		),
		'panel'	=>  array(
			'attributes' => array(
				'class'	=>	'table table-bordered',
			),
			'link'	=>	array(
				'attributes'	=>	array(
					'class'	=>	'btn btn-primary btn-xs',
				),
			),
			'sortable'	=>	true,
			'rowsLimit' =>  2,
		),
        'box'	=>	array(
			'attributes' => array(
				'class'	=>	'table',
			),
			'link'  =>  array(
				'class' =>  'btn btn-xs',
				'target'    =>  '_blank',
			),
			'rowsLimit' =>  100,
		),
	),
	'menu'	=>	array(
		'default'	=>	array(
			'menu'	=>	array(
				'tag'	=>	'ul',
				'attributes'	=>	array(
					'class'	=>	'nav navbar-nav',
				),
			),
			'link'	=>	array(
				'tag'	=>	'li',
			),
			'linkContainer'	=>	array(
				'tag'	=>	'span',
				'attributes'	=>	array(
					'class'	=>	'btn btn-ribbon hidden-xs',
					'id'	=>	'add',
				),
			),
		),
		'topMenu'	=>	array(
			'menu'	=>	array(
				'tag'	=>	'span',
				'attributes'	=>	array(
					'id'	=>	'sub_menu',
					'class'	=>	'ribbon-button-alignment pull-right',
				),
			),
			'link'	=>	array(
				'attributes'	=>	array(
					'style'	=>	'color:#FFFFFF !important',
				),
			),
			'linkContainer'	=>	array(
				'tag'	=>	'span',
				'attributes'	=>	array(
					'class'	=>	'btn btn-ribbon hidden-xs',
					'id'	=>	'add',
				),
			),
		),
	),
);