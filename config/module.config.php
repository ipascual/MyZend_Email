<?php
return array(
	'email' => array(
		"active" => true,
		"defaults" => array(
				"layout_name" => "default",
				"template_name" => "default",
				"from_email" => "info@youcompany.com",
				"from_name" => "Your Company Name",
				"reply_to" => "",
				"reply_to_name" => ""
		),
		"emails" => array(
			"support" => "info@yourcompany.com",
			"admin" => "webmaster@yourcompany.com"
		),
		"template_path_stack" => array(
					__DIR__ . "/../view/email/"
		),
		'template_vars' => array(
			"company" => "Your Company Name",
			"slogan" => "Find, promotion and success",
			"baseUrl" => "http://www.yourcompany.com/"
		),
	),
);