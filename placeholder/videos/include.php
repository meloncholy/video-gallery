<?php
/*	

	xmoovStream Loader
	########################################################################
	
	xmoovStream Server Version: 0.8.4b
	
	Author: Eric Lorenzo Benjamin jr. stream (AT) xmoov (DOT) com
	License: Creative Commons Attribution-Noncommercial-Share Alike 3.0 United States License. http://stream.xmoov.com/support/licensing/	
	
	Description:
	########################################################################
	
	This file loads all of the xmoovStream classes and functions.
	
*/

# Define the xmoovStream directory
define ("XS_DIR", dirname(__FILE__));

# load token class
if (file_exists (XS_DIR . '/xmoovStream_includes/xsToken.class.php'))
{
	require_once (XS_DIR . '/xmoovStream_includes/xsToken.class.php');
}

# load logging class
if (file_exists (XS_DIR . '/xmoovStream_includes/xsLog.class.php'))
{
	require_once (XS_DIR . '/xmoovStream_includes/xsLog.class.php');
}

# load xmoovStream class
if (file_exists (XS_DIR . '/xmoovStream_includes/xmoovStream.class.php'))
{
	require_once (XS_DIR . '/xmoovStream_includes/xmoovStream.class.php');
}
else
{
	echo "xmoovStream fatal error : could not find xmoovStream.php";
	exit(0);
}

# xmoovStream initialize
function xmoovStream ($server=0,$init=0)
{
	# load config
	if (file_exists (XS_DIR . '/xmoovStream_includes/xsConfig.php'))
	{
		require_once (XS_DIR . '/xmoovStream_includes/xsConfig.php');
	}
	else
	{
		echo "xmoovStream fatal error: could not find config.php";
		exit(0);
	}
	# load server configuration
	if ($server && file_exists(XS_DIR . '/xmoovStream_servers/' . $server . '.inc.php'))
	{
		require_once (XS_DIR . '/xmoovStream_servers/' . $server . '.inc.php');
	}
	else if ($server)
	{
		echo "xmoovStream fatal error: could not find " . $server . ".inc.php";
		exit(0);
	}
	if ($server_config = init_server ())
	{
		# initialize xmoovStream
		if ($defaults)
		{
			$xs = new xmoovStream ($defaults, $server_config);
			if ($xs->init($init)) return $xs;
			return false;
		}
		else
		{
			echo "xmoovStream fatal error: defaults not found";
			exit(0);
		}
	}
	return false;
}

?>