<?php
/*

	xmoovStream Logger
	########################################################################
	
	xmoovStream Server Version: 0.8.4b

	Author: Eric Lorenzo Benjamin jr. stream (AT) xmoov (DOT) com
	License: Creative Commons Attribution-Noncommercial-Share Alike 3.0 United States License. http://stream.xmoov.com/support/licensing/	
	
	Description:
	########################################################################
	
	This handles logging, email notifications and debugging output
	
	Notes:
	########################################################################
	
	Be careful how you configure this!
	Clients accessing your xmoovStream server using http_range request (iPhones and quicktime player X for example) will generate very large amounts of data.

*/

class xsLog
{
	var $config=null;
	
	# assemble logging event
    function log($event_type=null,$event=null,$conf=null)
    {
    	if ($conf) $this->config = $conf;
        $event_stamp = '['.date('m.d.Y, H:i:s').', '.$this->get_ip().']';
        $event_provider = defined('XS_PROVIDER') ? 'server: (' . XS_PROVIDER . ')' : '';
		$event_file = isset($this->config['file']) ? 'file: (' . $this->config['file'] . ')' : '';
		$show_event = false;
		$notify_event = false;
		$log_event = false;
		
		switch ($event_type)
		{
			case 'error' :
				switch($event)
				{
					case 'file_open' :
						$event_string = ('Could not open');			
						break;		
					case 'file_empty' :
						$event_string = ('A file was not set');			
						break;	
					case 'file_path' :
						$event_string = ('Server path does not exist ('.$this->config['file_path'].')'); 
						break;		
					case '404' :
						$event_string = ('File not found');			
						break;			
					case 'security' :
						$event_string = ('Security error! A possible hack was attempted'); 
						break;			
					case 'file_type' :
						$event_string = ('Mime type not allowed'); 
						break;	
					case 'time_limit' :
						$event_string = ('Could not set time limit'); 
						break;
					case 'magic_quotes' :
						$event_string = ('Could not set magic_quotes_runtime'); 
						break;			
					default :
						$event_string = $error; 
						break;			
				}
				
				if(isset($this->config['show_errors'])) $show_event = $this->do_output ($this->config['show_errors'],$event);		
				if(isset($this->config['log_errors'])) $log_event = $this->do_output ($this->config['log_errors'],$event);			
				if(isset($this->config['notify_email']) && isset ($this->config['notify_errors'])) $notify_event = $this->do_output ($this->config['notify_errors'],$event);	
				break;
			case 'activity' :
				if(isset($this->config['log_activity'])) $log_event = $this->do_output ($this->config['log_activity'],$event);
				$event_string = $event;
				break;
		}
		
		$event_output = $event_stamp . ' xmoovStream ' .  strtoupper($event_type) . ': [' . $event_string . '] ' . $event_file. ' ' . $event_provider;		
		
		if($show_event) $this->diplay_event ($event_output);
		
		if($notify_event) $this->notify_event($event_type, $event_output);
		
		if($log_event) $this->log_event ($event_type, $event_output);
    }
    
    # check configuration for output conditions
    function do_output ($conf,$event)
    {
    	if(is_bool($conf)) return $conf;
    	
    	if (is_array($conf) && in_array($event, $conf)) return true;
    	
    	if (!is_array($conf) && (strtolower ($conf) == 'all' || $conf == $event)) return true; 
    	
    	return false;
    }
    
    # output event to the browser
    function diplay_event ($output)
    {
    	echo $output;
    }
    
    # send email notification
	function notify_event($subject, $message)
	{	
		if ($this->config['notify_email']) mail($this->config['notify_email'],' xmoovStream ' . $subject,$message,'From: noreply@'.$_SERVER['HTTP_HOST'].'\r\n' . 'Reply-To: noreply@'.$_SERVER['HTTP_HOST']."\r\n" . 'X-Mailer: PHP/'.phpversion());
	}
	
	# output event to log file
	function log_event ($event_type, $output)
	{
		if ($logfile = $this->log_file ($event_type) && is_writable(XS_LOGS))
		{	
			if (function_exists('file_put_contents'))
			{
				file_put_contents($logfile, $output."\n", FILE_APPEND);
			}
			else
			{
				$f = @fopen($logfile, 'ab');
		        if ($f)
		        {
		            $bytes = fwrite($f, $output."\n");
		            fclose($f);
		            return $bytes;
		        }
			}
		}
		return false;
	}
	
	# get current logfile or create it if it doesn't exist
	function log_file ($event_type)
	{
		if (!is_writable(XS_LOGS)) return false;
		$logfile = XS_LOGS . '/' . date('m-d-Y') . '.' . $event_type . '.log';
		if (!file_exists($logfile))
		{
			if ($this->create_logfile ($logfile))
			{
				return $logfile;
			}
			return false;
		}
		return $logfile;
	}
	
	# try to create logfile
	function create_logfile ($file)
	{
		$file_handle = fopen($file, 'w');
		if($file_handle)
		{
			fclose($file_handle);
			return true;
		}
		return false;
	}
	
	# get client ip
	function get_ip()
	{			
		if(!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			return $_SERVER['REMOTE_ADDR'];
		}
	}
}

?>