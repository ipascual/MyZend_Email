MyZend / Email
=======
Version 0.9

Introduction
------------

MyZend Email is a ZF2 module to manage the process of sending emails with templates.

It encapsulates Zend\Mail\Message to keep it a simple use.

Install
------------
Every module will have its own email templates.

Add these to your module/YourModule/config/module.config.php
```
	'email' => array(
		"template_path_stack" => array(
				__DIR__ . "/../view/email/"
		),
	),
```
Add this structure to you module module/YourModule/view/email
```	
	/html/your-module/*
	/subject/your-module/*
	/txt/your-module/*
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




