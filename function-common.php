<?php defined('ABSPATH') OR exit;

function wpcsp_kses_allowed_options()
{
	$default = wp_kses_allowed_html('post');

	$default['input'] = [
		'type' => 1,
		'name' => 1,
		'value' => 1,
		'class' => 1,
		'id' => 1,
	];

	$default['form'] = [
		'type' => 1,
		'name' => 1,
		'value' => 1,
		'class' => 1,
		'id' => 1,
	];

	$default['option'] = [
		'type' => 1,
		'name' => 1,
		'value' => 1,
		'class' => 1,
		'id' => 1,
		'selected' => 1,
	];

	return $default;
}