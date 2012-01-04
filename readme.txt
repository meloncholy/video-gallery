VIDEO GALLERY

A (hopefully) nice video gallery web app. Well, I like it anyway. 

There's a load of bumf about it here

http://meloncholy.com/portfolio/video-gallery/

and a demo here

http://bits.meloncholy.com/video-gallery/

Do get in touch if you something doesn't work / you have questions / you just want to say hi. Lovely. 

http://meloncholy.com/contact/


SETTING UP THE VIDEO GALLERY

The download includes some sample photos and videos to get the Video Gallery up and running quickly. 

1. Upload the files to your server. 

2. Create a new database using the sample data in the database folder. 

3. Open application/config/config.php and change $config['base_url'] to point at the folder in which you've put the files. If you're not using mod_rewrite, change $config['index_page'] to 'index.php'. 

4. Open application/config/database.php and change $db['default']['username'], $db['default']['password'], $db['default']['database'] to match your settings. 

5. The .htaccess file in the Video Gallery main folder includes rules to redirect video and image requests to the samples included. If you don't want to use them, please remove these lines. Note that the included xmoov video streamer has also been lightly modified to support the placeholder videos. If you don't want this, please replace index.php with index.php.original in placeholder\videos. 


LEGAL FUN

Copyright (c) 2011 Andrew Weeks http://meloncholy.com

Dual licensed under the MIT and GPLv2 licences. See http://meloncholy.com/licence 
Includes some code written by others; see dev source for details. 
Version 0.1


INCLUDED PACKAGES

The Video Gallery includes the xmoovStream video http pseudostreamer by Eric Lorenzo Benjamin jr. stream (AT) xmoov (DOT) com, released under the Creative Commons Attribution-Noncommercial-Share Alike 3.0 United States licence. http://stream.xmoov.com/support/licensing/

http://stream.xmoov.com/

The streamer is not an integral part of the Video Gallery and will work fine without it. However a suitable alternative must be provided if you want to make use of the video player's streaming capabilities. 

Longtail Video's JW Player is included, released under Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0) licence. Commercial licences are also available. 

http://www.longtailvideo.com/

You're of course welcome to swap the player for another one if you don't like it, though you'll need to change some of the source files that set up parameters to pass to the player. 

The Video Gallery also includes some short video clips and still images of these video clips by Catrin Hedström and released under a Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0) licence. 

http://www.theycallusanimals.com/

