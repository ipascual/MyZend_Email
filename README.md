MyZend / Email
=======
Version 0.9

Introduction
------------

MyZend Email is a ZF2 module to manage the process of sending emails with templates.

It encapsulates Zend\Mail\Message to keep it a simple use.

This module uses 
* html email body with templates
* subject with templates
* txt email body with templates (alternative to html)

Install
------------
1. Every module will have its own email templates. (Replace Application with your module name)
File: module/Application/config/module.config.php
```
	'email' => array(
		"template_path_stack" => array(
				__DIR__ . "/../view/email/"
		),
	),
```
Add this structure to you module 
```	
	module/Application/view/email/html/your-module/*
	module/Application/view/email/subject/your-module/*
	module/Application/view/email/txt/your-module/*
```	                  

2. Override the default layout with
```	
	module/Application/view/email/layout/html/layout.phtml
	module/Application/view/email/layout/txt/layout.phtml
```	                  
3. Setup your config under config/autoload folder
*File: module.email.local.php*
```	
return array(
	'email' => array(
		"active" => true,
		"defaults" => array(
				"layout_name" => "layout",
				"from_email" => "no-reply@yourproject.com",
				"from_name" => "MyZend Project"
		),
		"emails" => array(
			"support" => "ipascual@yourproject.com",
			"admin" => "ipascual@yourproject.com"
		),
		'template_vars' => array(
			"company" => "MyZend Project",
			"slogan" => "",
			"baseUrl" => "http://www.yourproject.com"
		)
	)
);
```	

Examples
------------
```
/*
 * Basic use
 */
$email = $this->email->create();
$email->addTo("ignacio@yourproject.net", "Ignacio Pascual");
$this->email->send($email);
```

```
/*
 * Admin mail
 */
$email = $this->email->create(array("html_content" => "this is a notice", "subject" => "error"));
$this->email->send($email);
```

```
/*
 * Using template variables
 */
$email = $this->email->create(array(
							"name" => "I. Pascual",
							"location" => "Barcelona"
						));
$email->setTemplateName("welcome");
$email->addTo("ignacio@yourproject.net", "Ignacio Pascual");
$this->email->send($email);
```

```
/**
 * Without templates
 */
$email = $this->email->create();
$email->setSubject("Test");
$email->setTextContent("Hi, this is a test!");
$email->setHtmlContent("<h1>Hi,</h1> <p>this is a test!</p>");
$email->addTo("ignacio@yourproject.net", "Ignacio Pascual");
$this->email->send($email);
```

```
/*
 * Optional arguments
 */
$email = $this->email->create();
$email->addTo("ignacio@yourproject.net", "Ignacio Pascual");

//From
$email->setFrom("postmaster@yourproject.com");
$email->setFromName("Do-Not-Reply");
//ReplyTo
$email->setReplyTo("support@yourproject.com");
$email->setReplyToName("Support Team");
//Layout
$email->setLayoutName("1column");
//Template
$email->setTemplateName("welcome");
//To
$email->addTo("other@example.com", "Mr. Other Recipient");
//Cc
$email->addCc("copy@example.com", "Mr. Copy Recipient");
//Bcc
$email->addBcc("other-copy@example.com", "Mr.Not Revealing");
		
$this->email->send($email);
```        




