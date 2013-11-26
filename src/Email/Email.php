<?php 
/**
 * Email class
 *
 */
namespace Email;

use Zend\Mail\Message;

class Email
{
	
	/**
	 * To recipients
	 * @var array
	 */
	 protected $to = array();
	 
	 /**
	  * Cc recipients
	  * @var array
	  */
	  protected $cc = array();
	
    /**
     * __construct
     *
     * Set default options
     * @param array $data
     */
    public function __construct ($data = array())
    {
    	$this->setProperties($data);
    }

	
	
    /**
     * Bcc recipients
     * @var array
     */
    protected $bcc = array();
	
	/**
	 * Subject
	 * @var string
	 */
	 protected $subject = "";

	/**
	 * Add TO recipient
	 * 
	 * @param string $var Email string or User object
	 * @param string $name of the recipient
	 * @return \Email\Email
	 */
	public function addTo($var, $user = null) {
		if(is_object($var)) {
			//to[email] = UserObject
			$this->to[$var->getEmail()] = $var;
		}
		else {
			//to[email] = user_name
			$this->to[$var] = $user;	
		}
		return $this;
	}

	/**
	 * Add CC recipient
	 * 
	 * @param string $var Email string or User object
	 * @param string $name of the recipient
	 * @return \Email\Email
	 */
	public function addCc($var, $user = null) {
		if(is_object($var)) {
			//to[email] = UserObject
			$this->cc[$var->getEmail()] = $var;
		}
		else {
			//to[email] = user_name
			$this->cc[$var] = $user;	
		}
		return $this;
	}

	/**
	 * Add BCC recipient
	 * 
	 * @param string $var Email string or User object
	 * @param string $name of the recipient
	 * @return \Email\Email
	 */
	public function addBcc($var, $user = null) {
		if(is_object($var)) {
			//to[email] = UserObject
			$this->bcc[$var->getEmail()] = $var;
		}
		else {
			//to[email] = user_name
			$this->bcc[$var] = $user;	
		}
		return $this;
	}

    /**
     * Set/Get Magic function
	 * 
	 * Set
	 * $user->setSubject("This is a test")
	 * 
	 * Get
	 * $user->getName();
	 * $user->getPhonenumbers(3);
	 * 
     * @param   string $method
     * @param   array $args
     * @return  mixed
     */
    public function __call($method, $args)
    {
        switch (substr($method, 0, 3)) {
            case 'get' :
                $key = $this->_underscore(substr($method,3));
				$index = isset($args[0]) ? $args[0] : null;
		    	//Try to find a property
		    	if(!$index && isset($this->$key)) {
		    		return $this->$key;
		    	}
				return "";
            case 'set' :
                $key = $this->_underscore(substr($method,3));
				$result = isset($args[0]) ? $args[0] : null;
				$this->$key = $result;
                return $result;
        }
        throw new Exception("Invalid method ".$method);
    }

    /**
     * Converts field names for setters and geters
     *
     * $this->setMyField($value) === $this->setData('my_field', $value)
     * Uses cache to eliminate unneccessary preg_replace
     *
     * @param string $name
     * @return string
     */
    protected function _underscore($name)
    {
        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
        return $result;
    }
	
	/**
	 * Set all values from $data to each property.
	 * 
	 * @param $data array set
	 * @return \Email\Email
	 */	
    public function setProperties(array $data)
    {
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
        return $this;
    }

    /**
     * Export all class properties to array
     * E.g.: ["full_name"] => "Ignacio Pascual" 
     * 
     * Check all variables if exists the method getVariable() then is added to the Array.
     * @return array
     */
    public function toArray() {
    	$values = array();
		foreach (get_object_vars($this) as $key => $val) {		
   			$values[$key] = $val;
    	}
        return $values;
    }
	
}
