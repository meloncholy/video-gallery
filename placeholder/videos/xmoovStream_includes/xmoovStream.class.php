<?
/*

	xmoovStream class
	########################################################################
	
	xmoovStream Server Version: 0.8.4b
	
	Author: Eric Lorenzo Benjamin jr. stream (AT) xmoov (DOT) com
	License: Creative Commons Attribution-Noncommercial-Share Alike 3.0 United States License. http://stream.xmoov.com/support/licensing/	
	
	Description:
	########################################################################
	
	This is the center of the xmoovStream system
	
	Notes:
	########################################################################
	
	Many thanks to Stefan Richter (flashcomguru.com), PHP4FLV project, Joeren Wijering (longtailvideo.com) and many others who contributed to the concept.
	
	Changes from 0.8.2b:
	#######################################################################
	
	1. Server configuration files no longer need the dot (.) before the file extension when defining MIME types.
	2. xmoovStream Server no longer includes burst data in calculation of throttling.
	3. Changed default buffer_size to 8KB, the maximum possible setting.
	4. Improved installer rewrite_engine detection
	
	Changes from 0.8.3b:
	#######################################################################
	
	1. Added cross platform rewrite engine detection to install helper.
	2. Added RewriteBase to install helper htaccess files.

*/

class xmoovStream
{
	# xmoovStream version
	var $version='0.8.4b';
	# minimum php version
	var $min_php_version='4';
	# file to stream
	var $file=null;
	# modified date
	var $file_modified=0;
	# parial file download
	var $partial_download=0;
	# http range request
	var $http_range_download=0;
	# file name to be sent to the client
	var $file_name=null;
	# configuration array from server.inc file
	var $config=array();
	# mime type
	var $mime=null;
	# buffer size
	var $buffer_size=8;
	# start seek position
	var $seek_start=0;
	# end seek position
	var $seek_end=-1;
	# original file size
	var $file_size=0;
	# human readable original file size
	var $human_file_size=0;
	# output file size
	var $content_length=0;
	# event handler class
	var $xsLog=0;
	# magic_quotes_runtime setting memory
	var $mqrM=0;
	
	function xmoovStream($defaults=0,$config=0)
	{
		if($defaults) $this->set_config($defaults);
		if($config) $this->set_config($config);
	}
	
	function init($start=false)
	{
		
		# initialize logging class
		if(class_exists('xsLog')
		&&(isset($this->config['use_error_handling']) || isset($this->config['use_activity_handling'])) 
		&&($this->config['use_error_handling'] || $this->config['use_activity_handling'])) 
		$this->xsLog=new xsLog();		
		
		# start download
		if (isset($this->config['file']))
		{
			if($this->set_file($this->config['file']))
			{
				if($start) $this->download();
				return true;
			}
			else
			{
				return false;
			}
		}
		return true;
	}
	
	# parse configuration arguments
	function set_config($args=null)
	{			
		# make sure arguments have been defined
		if(isset($args))
		{				
			$config=null;		
			
			# check if the arguments are an array
			if(is_array($args))
			{					
				$config=$args;			
			}
			else
			{					
				# assume arguments as a string and parse into an array
				parse_str($args,$config);		
			}
			
			# final check if the arguments are an array,if so parse into the configuration
			if(is_array($config)) $this->config=array_merge($this->config, $config);			
		}
	}
	
	# http range
	function http_range()
	{
		global $HTTP_SERVER_VARS;
		if(isset($_SERVER['HTTP_RANGE']) || isset($HTTP_SERVER_VARS['HTTP_RANGE'])) 
		{ 
			if(isset($HTTP_SERVER_VARS['HTTP_RANGE']))
			{
				$seek_range=substr($HTTP_SERVER_VARS['HTTP_RANGE'] , strlen('bytes='));
			} 
			else
			{
				$seek_range=substr($_SERVER['HTTP_RANGE'] , strlen('bytes='));
			}
			$range=explode('-',$seek_range); 
			if($range[0] > 0) $this->seek_start=intval($range[0]); 
			if($range[1] > 0)
			{
				$this->seek_end=intval($range[1]);
			}
			else
			{
				$this->seek_end=-1;
			}
		    $this->partial_download=true;
		    $this->http_range_download=true;
		}
	
	}
	
	# header output
	function header()
	{
		header('Pragma: public');

		# force file download dialog
		if(isset($this->config['force_download']) && $this->config['force_download'])
		{
			if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');
			
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private',false);
			
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Type: application/download');
			header("Content-type: $this->mime");
			header("Content-Disposition: attachment; file_name=$this->file_name;");
		}
		else
		{
			header('Cache-Control: public');
			header("Content-type: $this->mime");
			header("Content-Disposition: inline; file_name=$this->file_name;");
		}
		
		header('Last-Modified: ' . date('D, d M Y H:i:s \G\M\T' , $this->file_modified));
		header("Content-Transfer-Encoding: binary\n");
		
		if($this->http_range_download)
		{ 
		    header('HTTP/1.0 206 Partial Content'); 
		    header('Status: 206 Partial Content'); 
		    header('Accept-Ranges: bytes'); 
		    header("Content-Range: bytes $this->seek_start-$this->seek_end/$this->file_size"); 
		}
		
	    header("Content-Length: $this->content_length"); 
	}

	# begin download
	function download()
	{
		
		# turn off php error reporting
		error_reporting(0);
		
		if(isset($this->config['position'])) $this->seek_start=intval($this->config['position']);
		if(isset($this->config['buffer_size'])) $this->buffer_size=intval($this->config['buffer_size']) * 1024;
		
		#try to set time limit to 0 for large file support
		if(!$this->time_limit()) $this->log('error','time_limit');
				
		# keep binary data safe
		if(!$this->set_magic_quotes()) $this->log('error','magic_quotes');
		
		# check http range header
		if(isset($this->config['use_http_range']) && $this->config['use_http_range'])
		{
			$this->http_range();
			# check seek positions
			if($this->seek_end < $this->seek_start) $this->seek_end=$this->file_size - 1;
		}

		# set content length
		$this->content_length=$this->seek_end - $this->seek_start + 1;
		
		# open the file
		if(!$file_handle=fopen($this->file,'rb'))
		{
			$this->log('error','file_open');
			$this->disconnect();
		}
		
		# output the header
		$this->header();
		
		# seek if we have to
		if($this->seek_start > 0)
		{
			$this->partial_download=true;
			
			fseek($file_handle , $this->seek_start);
			
			# print flv header if we have to
			if($this->mime == 'video/x-flv')
			{
				echo 'FLV' , pack('C', 1) , pack('C', 1) , pack('N', 9) , pack('N', 9);
				$this->log('activity','flv_random_access');
			}
		}
		else
		{
			$this->log('activity','file_access');
		}
		
		# get ready for take off
		$speed=0;
		$bytes_sent=0;
		$chunk=1;
		$throttle=isset($this->config['throttle']) ? $this->config['throttle'] : false;
		$burst=isset($this->config['burst_size']) ? $this->config['burst_size'] * 1024 : 0;
		
		# output file
		while(!(connection_aborted() || connection_status() == 1) && $bytes_sent < $this->content_length)
		{
			#st buffer size after the first burst has been sent
			if($bytes_sent >= $burst) $speed=$throttle;
			
			# make sure we don't read past the total file size
			if($bytes_sent + $this->buffer_size > $this->content_length) $this->buffer_size=$this->content_length - $bytes_sent;
			
			# send data
			echo fread($file_handle, $this->buffer_size);
			$bytes_sent += $this->buffer_size;
			
			# clean up
			flush();
			ob_flush();
			
			#throttle
			if($speed &&($bytes_sent-$burst > $speed*$chunk*1024))
			{
				sleep(1);
				$chunk++; 
			}
		}
			
		fclose($file_handle);
		
		if($bytes_sent == $this->file_size)
		{
			$this->log('activity','download_complete');
		}
		else if($bytes_sent == $this->content_length)
		{
			$this->log('activity','partial_download_complete');
		}
		else
		{
			$this->log('activity','user_abort');
		}
		$this->disconnect();
	}
	
	# exit script
	function disconnect()
	{
		$this->set_magic_quotes(true);
		exit();
	}
	
	# set file and check itegrity
	function set_file($file)
	{
		# make sure the file was set
		if(isset($file) && $file != '')
		{
			if ($file != $this->config['file'])
			{
				$this->config['file'] = $file;
			}
		}
		else
		{
			$this->log('error','file_empty');
			return false;		
		}
		
		# make sure we are not being hacked
		if(eregi('(^\.\.?)',$file) || eregi(basename($_SERVER['PHP_SELF']),$file) || eregi('.php',$file))
		{
			$this->log('error','security');
			return false;		
		}
		
		# make sure server path is a directory
		if(isset($this->config['file_path']) && is_dir($this->config['file_path']))
		{
			$file=$this->config['file_path'].$file;
		}
		else
		{
			$this->log('error','file_path');
			return false;	
		}
		
		# make sure the file exists
		if(!file_exists($file))
		{			
			$this->log('error','404');
			return false;	
		}
		
		# make sure file type is allowed
		$this->mime=$this->get_mime_type($file);
		if(!$this->mime)
		{			
			$this->log('error','file_type');
			return false;
		}
		
		# make sure the file is ok and finally set the file variables after all this paranoia
		if(is_readable($file) && is_file($file))
		{
			$this->file_name=isset($this->config['output_file_name']) ? $this->config['output_file_name'] : basename($file);
			if(strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) $this->file_name= preg_replace('/\./', '%2e', $this->file_name, substr_count($this->file_name, '.') - 1);
			$this->file_modified=filemtime($file);
			$this->file_size=filesize($file);
			$this->human_file_size=$this->convert_human_file_size($this->file_size);
			$this->file=$file;
			return true;
		}
	}
	
	# mime
	function get_mime_type($file=null)
	{
		if(!$file)
		{
			if (isset($this->config['file']))
			{
				$file = $this->config['file'];
			}
			else
			{
				return false;
			}
		}
		$ext=strtolower(eregi_replace("^(.*)\.","",$file));	
		return(isset($this->config['mime_types'][$ext])) ? $this->config['mime_types'][$ext] : false;
	}
	
	# pass events to event handler
	function log($event_type=null,$event=null)
	{	
		if($this->xsLog)
		{
			$this->xsLog->log($event_type,$event,$this->config);
		}
	}
	
	# convert file size
	function convert_human_file_size($size)
	{    	
		$sizes=array('Bytes','KB','MB','GB','TB','PB','EB','ZB','YB');    	
		return $size ? round($size/pow(1024,($i=floor(log($size,1024)))),2).$sizes[$i] : '0 Bytes';    	
	}
	
	function get_file_size($human=false,$file=null)
	{
		if(!$file)
		{
			if (isset($this->config['file']))
			{
				$file = $this->config['file'];
			}
			else
			{
				return false;
			}
		}
		return $human ? $this->convert_human_file_size($this->file_size) : $this->file_size;
	}
	
	# try to set script time limit to 0
	function time_limit()
	{
		if(ini_get('safe_mode')) 
		{
			return false;
		}
		set_time_limit(0);
		return true;
	}
	
	# try to set magic quotes runtime
	function set_magic_quotes($reset=false)
	{
		if($this->mqrM)
		{
			set_magic_quotes_runtime($this->mqrM);
			return true;
		}
		
		if(function_exists('get_magic_quotes_runtime'))
		{
			$this->mqrM=get_magic_quotes_runtime();
			return set_magic_quotes_runtime(0);
		}
		return false;
	}
}

?>