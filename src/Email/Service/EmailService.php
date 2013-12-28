<?php 
/**
 * A template based email system
 *
 * Supports the sending of multipart txt/html emails based on templates
 *
 */
namespace Email\Service;

use Email\Email as Email;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\Sendmail as SendmailTransport;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

use Zend\Mvc\I18n\Translator;

use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\AggregateResolver;
use Zend\View\Resolver\TemplatePathStack;
use Zend\View\HelperPluginManager;

class EmailService
{
	protected $config;

	protected $viewHelperManager;

    /**
     * __construct
     *
     * Set default options
     *
     */
    public function __construct (array $config)
    {
    	$this->config = $config;
    }

	public function setViewHelperManager(HelperPluginManager $viewHelperManager)
	{
		$this->viewHelperManager = $viewHelperManager;
	}

	public function getViewHelperManager()
	{
		return $this->viewHelperManager;
	}

	/**
	 * Create a new email
	 * 
	 * @var $data Default template variables
	 */
	public function create($data = array()) {
		return new Email($data);
	}
   
	/**
     * Send the constructed email
     *
     * @todo Add from name
     */
    public function send ($email)
    {
		$message = $this->prepare($email);
		
        //Send email
        if($message && $this->config["active"]) {
        	// Server SMTP config
			$transport = new SendmailTransport();
			// Relay SMTP
			if($this->config["relay"]["active"]) {
				$transport = new SmtpTransport();
				$transportConfig = array(
				    'name'              => "MyZend_Email",
				    'host'              => $this->config["relay"]["host"],
				    'connection_class'  => 'login',
				    'connection_config' => array(
				        'username' => $this->config["relay"]["username"],
				        'password' => $this->config["relay"]["password"]
					)
				);
				// Add port
				if($this->config["relay"]["port"]) {
					$transportConfig["port"] = $this->config["relay"]["port"]; 
				}
				// Add ssl
				if($this->config["relay"]["ssl"]) {
					$transportConfig["connection_config"]["ssl"] = $this->config["relay"]["ssl"]; 
				}
				$options   = new SmtpOptions($transportConfig);
				$transport->setOptions($options);
			}
			
			return $transport->send($message);        	
        }
    }
    
	/**
	 * Return a preview of the email
	 */
    public function preview($email) {
    }
	
	/**
	 * Prepare email to send.
	 */
    private function prepare($email) {

		//Template Variables
        $templateVars = $this->config["template_vars"];
		$templateVars = array_merge($templateVars, $email->toArray());	
		//If not layout, use default
        if(! $email->getLayoutName()) {
        	$email->setLayoutName($this->config["defaults"]["layout_name"]);
        }

        //If not recipient, send to admin
        if(count($email->getTo()) == 0) {
            $email->addTo($this->config["emails"]["admin"]);
        }
		
		//If not sender, use default
		if(! $email->getFrom()) {
			$email->setFrom($this->config["defaults"]["from_email"]);
			$email->setFromName($this->config["defaults"]["from_name"]);
		}

		//Render system
        $renderer = new PhpRenderer();
		$resolver = new AggregateResolver();
		$stack = new TemplatePathStack();
		foreach($this->config["template_path_stack"] as $path) {
			$stack->addPath($path);
		}
		$resolver->attach($stack);
		$renderer->setResolver($resolver);
		$renderer->setHelperPluginManager($this->getViewHelperManager());
		
        // Subject
        if(! $email->getSubject()) {
        	$subjectView = $this->createView($templateVars, "subject", $email->getTemplateName());
	        try {
	        	$email->setSubject($renderer->render($subjectView));
	        } catch (\Exception $e) {
	        	$email->setSubject(null);
	        }
		}

        // Text Content
        if(! $email->getTextContent()) {
        	$textView = $this->createView($templateVars, "txt", $email->getTemplateName());
	        $layoutTextView = new ViewModel($templateVars);
			$layoutTextView->setTemplate("/layout/txt/".$email->getLayoutName());
	        try {
	        	$layoutTextView->setVariable("content", $renderer->render($textView));
	        	$email->setTextContent($renderer->render($layoutTextView));
	        } catch (\Exception $e) {
		    	$email->setTextContent(null);
	        }
		}
		
		// Html Content
        if(! $email->getHtmlContent()) {
        	$htmlView = $this->createView($templateVars, "html", $email->getTemplateName());
	        $layoutHtmlView = new ViewModel($templateVars);
			$layoutHtmlView->setTemplate("/layout/html/".$email->getLayoutName());
			try {
	        	$layoutHtmlView->setVariable("content", $renderer->render($htmlView));
	        	$email->setHtmlContent($renderer->render($layoutHtmlView));
	        } catch (\Exception $e) {
	        	$email->setHtmlContent(null);
	        }
		}

        //Create Zend Message
        $message = new Message();
		
		//From
        $message->setFrom($email->getFrom(), $email->getFromName());
		
		//Reply to		
		if($this->config["defaults"]["reply_to"]) {
			$message->addReplyTo($this->config["defaults"]["reply_to"], $this->config["defaults"]["reply_to_name"]);
		}
		if($email->getReplyTo()) {
			$message->addReplyTo($email->getReplyTo(), $email->getReplyToName());
		}
 		
        //To recipients
        foreach($email->getTo() as $emailAddress => $user) {
			if(is_object($user)) {
				if($user->getMailOpt()) {
					$message->addTo($emailAddress, $user->getFullName());
				}
			}
			else {
				$message->addTo($emailAddress, $user);
			}
        }
		
        //Cc recipients
        foreach($email->getCc() as $emailAddress => $name) {
			if(is_object($user)) {
				if($user->getMailOpt()) {
					$message->addCc($emailAddress, $user->getFullName());
				}
			}
			else {
				$message->addCc($emailAddress, $user);
			}
        }
		
        //Bcc recipients
        foreach($email->getBcc() as $emailAddress => $name) {
			if(is_object($user)) {
				if($user->getMailOpt()) {
					$message->addBcc($emailAddress, $user->getFullName());
				}
			}
			else {
				$message->addBcc($emailAddress, $user);
			}
        }

		//Subject		
		if($email->getSubject()) {
        	$message->setSubject($email->getSubject());
		}

		// Body Multipart
		// Issue - not able to send TXT and HTML multibody
		// http://framework.zend.com/issues/browse/ZF2-196
		/*
		if($textContent) {
			$textContent = new MimePart($textContent);
			$textContent->type = "text/plain";
		}
        if($htmlContent) {
			$htmlContent = new MimePart($htmlContent);
			$htmlContent->type = "text/html";
        }
		$body = new MimeMessage();
		$body->setParts(array($textContent, $htmlContent));
		        
		$message->setBody($body);		
		$message->getHeaders()->get('content-type')->setType('multipart/alternative');
		*/

		//Body (Just html email right now)
		$htmlContent = new MimePart($email->getHtmlContent());
		$htmlContent->type = "text/html";

		$body = new MimeMessage();
		$body->setParts(array($htmlContent));

		$message->setBody($body);		
        
        return $message;
    }
 
 	public function createView($templateVars, $type, $template) {
		$view = new ViewModel($templateVars);
		if(! $template) {
			$template = $this->config["defaults"]["template_name"];
		}
		$view->setTemplate("/".$type."/".$template);
		
		return $view;
	}       
}