MyZend / Email
=======

Introduction
------------

MyZend Email is a ZF2 module to manage the process of sending emails with templates.

It encapsulates Zend\Mail\Message to keep it a simple use.

This module uses 
* html email body with templates
* subject with templates
* txt email body with templates (alternative to html)

Usage
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

Install
------------
##### Every module will have its own email templates. 

##### Add these config to `module/Application/config/module.config.php`
```
	'email' => array(
		"template_path_stack" => array(
				__DIR__ . "/../view/email/"
		),
	),
```
Email layout - Add this structure to your Application module
```	
	module/Application/view/email/layout/html/default.phtml
	module/Application/view/email/layout/txt/default.phtml
```	                  
##### Module - Add these config to `module/MODULE/config/module.config.php`
```
	'email' => array(
		"template_path_stack" => array(
				__DIR__ . "/../view/email/"
		),
	),
```
Email templates - Add this structure to your MODULE
```	
	module/MODULE/view/email/html/MODULE/example.phtml
	module/MODULE/view/email/subject/MODULE/example.phtml
	module/MODULE/view/email/txt/MODULE/example.phtml
```	            
##### Now, you could easily use your layout and template
```	
$email = $this->email->create();
$email->setTemplateName("MODULE/example");
$email->addTo("ignacio@yourproject.net", "Ignacio Pascual");
$this->email->send($email);

```	

      
Config file
------------

##### Setup your config under config/autoload folder `module.email.local.php`

```	
return array(
	'email' => array(
		"active" => true,
		"defaults" => array(
				"layout_name" => "default",
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
		),
		'relay' => array(
			'active'	=> false,
			'host'		=> '', 
			'port'		=> '', // it could be empty
			'username'	=> '',
			'password'	=> '',
			'ssl'		=> '' // it could be empty
		)
	)
);
```	

How to avoid email going to SPAM folder (SMTP relay)
------------
You could spend hours working on server side, but the easiest solution, that I've found, it's setup your web app for relaying on SMTP provider.
 
##### MailJet (www.mailjet.com)
```
...
		'relay' => array(
			'active'	=> true,
			'host'		=> 'in.mailjet.com', 
			'port'		=> '',
			'username'	=> 'bc8c7xxxxxxxxxxxxxxxxxxxxxxxx42b',
			'password'	=> 'bc8c7xxxxxxxxxxxxxxxxxxxxxxxx42b',
			'ssl'		=> ''
		)
```

##### GMAIL (using your gmail account)
```
...
		'relay' => array(
			'active'	=> true,
			'host'		=> 'smtp.gmail.com', 
			'port'		=> '587',
			'username'	=> 'youremail@gmail.com',
			'password'	=> 'xxxxxxxxxx',
			'ssl'		=> 'tls'
		)
```

