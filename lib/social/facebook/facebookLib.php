<?php

/**
 * Facebook wrapper library for facebook new graph api calls and social plugins
 *
 * @author Vadim Gabriel <vadimg88[at]gmail[dot]com>
 * @link http://www.vadimg.com/
 * @copyright Vadim Gabriel
 * @license MIT
 */

/**
 	The MIT License

	Copyright (c) 2010 Vadim Gabriel

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
 */

require_once('facebook.php');
/**
 * Provides *Simple* access and a wrapper for various facebook calls
 *
 * @package Facebook Graph API
 * @version 0.1 Alpha
 * @author Vadim Gabriel <vadimg88@gmail.com>
 */
class facebookLib extends Facebook
{
	/**
	 * Library version
	 * @var string
	 */
	const VERSION = '1.2a';
	/**
	   * Default options for curl.
	   */
	  public static $CURL_OPTS = array(
	    CURLOPT_CONNECTTIMEOUT => 10,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_TIMEOUT        => 60,
	    CURLOPT_FRESH_CONNECT  => 1,
		CURLOPT_PORT		   => 443,
	    CURLOPT_USERAGENT      => 'facebook-php-2.0',
	    CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_RETURNTRANSFER => true,
	  );
	/**
	 * whether to disable the SSL check when performing the CURL calls
	 * By default facebook uses SSL verifications, This causes the calls to fails on sites that do not use an SSL
	 * certified domain or include a certification file.
	 * It's recommended not to set this to true but if the following fixes does not work you can use this a a last resort.
	 *
	 *  1. ) Download: http://www.gknw.net/php/phpscripts/mk-ca-bundle.phps to c:\
	 *  2. ) Open cmd and run: c:>php mk-ca-bundle.php
	 *  3. ) Output should read: Downloading 'certdata.txt' ...Done (140 CA certs processed).
	 *  4. ) This file should be created: c:\ca-bundle.crt
	 *  5. ) Move c:\ca-bundle.crt to your preferred path
	 *  6. ) After initiating this library add the following line right below that:
	 *  facebookLib::$CURL_OPTS[CURLOPT_CAINFO] = 'path\to\cert\ca-bundle.crt';
	 * 
	 * You'll end up with something looking like this:
	 * include_once "facebookLib.php";
	 * $facebook = new facebookLib($config);
	 * facebookLib::$CURL_OPTS[CURLOPT_CAINFO] = 'path\to\cert\ca-bundle.crt';
	 *
	 * @var boolean 
	 */
	public $disableSSLCheck = false;
	/**
	 *  the error code if one exists
	 * @var integer
	 */
	protected $errorCode = 0;
	/**
	 * the error message if one exists
	 * @var string 
	 */
	protected $errorMessage = '';
	/**
	 * whether to throw exceptions on error or not
	 * @var boolean 
	 */
	protected $throwExceptions = false;
	/**
	 *  the response message
	 * @var string
	 */
	protected $response = '';
	/**
	 *  the headers returned from the call made
	 * @var array
	 */
	protected $headers = '';
	/**
	 * By default facebook returns most of the time a json object
	 * You can enable this to use json_decode to decode the json object into a php object
	 * @var boolean 
	 */
	protected $decodeJson = false;
	/**
	 * By default facebook returns most of the time a json object
	 * You can enable this to convert an object to an array
	 * @var boolean 
	 */
	protected $returnAsArray = false;
	
	/**
	   * Initialize a Facebook Application.
	   *
	   * The configuration:
	   * - appId: the application API key
	   * - secret: the application secret
	   * - cookie: (optional) boolean true to enable cookie support
	   * - domain: (optional) domain for the cookie
	   *
	   * @param Array the application configuration
	   */
	public function __construct($config)
	{
		parent::__construct($config);
	}
	
	/**
	 * Get users information (based on the permission you have) 
	 *
	 * 
	 * @param string the member profile id, If null will get the current authenticated user
	 * @return array List of information about the user
	 */
	public function getInfo($id=null)
	{
		return $this->callApi('me', $id);
	}
	
	/**
	 * Get information about a certain object
	 * All objects in Facebook can be accessed in the same way
	 *
	 *   - Users: https://graph.facebook.com/btaylor (Bret Taylor)
	 *   - Pages: https://graph.facebook.com/cocacola (Coca-Cola page)
	 *   - Events: https://graph.facebook.com/251906384206 (Facebook Developer Garage Austin)
	 *   - Groups: https://graph.facebook.com/2204501798 (Emacs users group)
	 *   - Applications: https://graph.facebook.com/2439131959 (the Graffiti app)
	 *   - Status messages: https://graph.facebook.com/367501354973 (A status message from Bret)
	 *   - Photos: https://graph.facebook.com/98423808305 (A photo from the Coca-Cola page)
	 *   - Photo albums: https://graph.facebook.com/99394368305 (Coca-Cola's wall photos)
	 *   - Videos: https://graph.facebook.com/614004947048 (A Facebook tech talk on Tornado)
	 *   - Notes: https://graph.facebook.com/122788341354 (Note announcing Facebook for iPhone 3.0)
	 * 
	 * @param mixed the name or id of the object who's information we would like to retrieve 
	 * 				If the param is null then it will fetch the current users information
	 * @return array information related to that object
	 *
	 */
	public function get($id=null)
	{
		return $this->callApi(null, $id);
	}
	
	/**
	 * Get an object picture 
	 * The same URL pattern works for all objects in the graph:
     *
	 *   - People: http://graph.facebook.com/vadim.v.gabriel/picture
	 *   - Events: http://graph.facebook.com/331218348435/picture
	 *   - Groups: http://graph.facebook.com/335845912900/picture
	 *   - Pages: http://graph.facebook.com/DoloresPark/picture
	 *   - Applications: http://graph.facebook.com/2318966938/picture
	 *   - Photo Albums: http://graph.facebook.com/platform/picture
     *	
	 * @param mixed the name or id of the object who's information we would like to retrieve 
	 * 				If the param is null then it will fetch the current users information
	 * @return string the link that will display the objects picture
	 *
	 */
	public function getPicture($id=null)
	{
		return $this->callApi('picture', $id, true);
	}
	
	/**
	 * Get a users links
	 *
	 * @param mixed the name or id of the object who's information we would like to retrieve 
	 * 				If the param is null then it will fetch the current users information
	 * @return array information regarding the user links
	 */
	public function getLinks($userid=null)
	{
		return $this->callApi('links', $userid);
	}
	
	/**
	 * Get a users friends list
	 *
	 * @param mixed the name or id of the object who's information we would like to retrieve 
	 * 				If the param is null then it will fetch the current users information
	 * @return array information regarding the user friends
	 */
	public function getFriends($userid=null)
	{
		return $this->callApi('friends', $userid);
	}
	
	/**
	 * Get a users news feed (the news appearing on his wall)
	 *
	 * @param mixed the name or id of the object who's information we would like to retrieve 
	 * 				If the param is null then it will fetch the current users information
	 * @return array information regarding the users news feed wall
	 */
	public function getNewsFeed($userid=null)
	{
		return $this->callApi('home', $userid);
	}
	
	/**
	 * Get a users profile feed (news from his profile only)
	 *
	 * @param mixed the name or id of the object who's information we would like to retrieve 
	 * 				If the param is null then it will fetch the current users information
	 * @return array information regarding the users profile feed
	 */
	public function getProfileFeed($userid=null)
	{
		return $this->callApi('feed', $userid);
	}
	/**
	 * Get all the likes a user made
	 *
	 * @param mixed the name or id of the object who's information we would like to retrieve 
	 * 				If the param is null then it will fetch the current users information
	 * @return array information regarding the user likes
	 */
	public function getLikes($userid=null)
	{
		return $this->callApi('likes', $userid);
	}
	
	/**
	 * Get users movies
	 *
	 * @param mixed the name or id of the object who's information we would like to retrieve 
	 * 				If the param is null then it will fetch the current users information
	 * @return array information regarding the users favorite movies
	 */
	public function getMovies($userid=null)
	{
		return $this->callApi('movies', $userid);
	}
	
	/**
	 * Get the users books
	 *
	 * @param mixed the name or id of the object who's information we would like to retrieve 
	 * 				If the param is null then it will fetch the current users information
	 * @return array information regarding the users books
	 */
	public function getBooks($userid=null)
	{
		return $this->callApi('books', $userid);
	}
	
	/**
	 * Get a users notes
	 *
	 * @param mixed the name or id of the object who's information we would like to retrieve 
	 * 				If the param is null then it will fetch the current users information
	 * @return array information regarding the users notes
	 */
	public function getNotes($userid=null)
	{
		return $this->callApi('notes', $userid);
	}
	
	/**
	 * Get the users photos
	 *
	 * @param mixed the name or id of the object who's information we would like to retrieve 
	 * 				If the param is null then it will fetch the current users information
	 * @return array information regarding the users photos
	 */
	public function getPhotos($userid=null)
	{
		return $this->callApi('photos', $userid);
	}
	
	/**
	 * Get the users videos
	 *
	 * @param mixed the name or id of the object who's information we would like to retrieve 
	 * 				If the param is null then it will fetch the current users information
	 * @return array information regarding the users videos
	 */
	public function getVideos($userid=null)
	{
		return $this->callApi('videos', $userid);
	}
	
	/**
	 * Get the users events
	 *
	 * @param mixed the name or id of the object who's information we would like to retrieve 
	 * 				If the param is null then it will fetch the current users information
	 * @return array information regarding the users event his attending
	 */
	public function getEvents($userid=null)
	{
		return $this->callApi('events', $userid);
	}
	
	/**
	 * Get the users groups
	 *
	 * @param mixed the name or id of the object who's information we would like to retrieve 
	 * 				If the param is null then it will fetch the current users information
	 * @return array information regarding the users groups his a member of
	 */
	public function getGroups($userid=null)
	{
		return $this->callApi('groups', $userid);
	}
	
	/**
	 * Get the users that are attending to an event
	 *
	 * @param mixed the id of the event we would like to see the attenders
	 * @return array information regarding the users who are attending to the event
	 */
	public function getEventAttenders($eventid)
	{
		return $this->callApi('attending', $eventid);
	}
	
	/**
	 * Post a status update
	 *
	 * @param mixed the name or id of the object we would like to post a update his status
	 * 				If the param is null then it will fetch the current users information
	 * @param mixed access_token of the currently logged in user
	 * @param string the status message
	 * @return string the id of the newly created status
	 */
	public function postStatus( $profileid=null, $access_token, $message )
	{
		return $this->doCall($this->callApi('feed', $profileid, true), array('access_token'=>$access_token,'message'=>$message));
	}

	/**
	 * Post a feed to a users wall
	 *
	 * @param mixed the name or id of the object we would like to post a feed to it's wall
	 * 				If the param is null then it will fetch the current users information
	 * @param mixed access_token of the currently logged in user
	 * @param array list of additional parameters to pass
	 * 		        This method supports the following parameters:
	 * 				- message, picture, link, name, description
	 * @return string the id of the newly created feed
	 */
	public function postFeed( $profileid=null, $access_token, $params=array() )
	{
		return $this->doCall($this->callApi('feed', $profileid, true), array_merge(array('access_token'=>$access_token), $params));
	}
	
	/**
	 * Post a comment on for a certain feed
	 *
	 * @param mixed the id of the feed we would like to post a comment to
	 * @param mixed access_token of the currently logged in user
	 * @param string the comment
	 * @return string the id of the newly created comment
	 */
	public function postComment( $postid, $access_token, $comment )
	{
		return $this->doCall($this->callApi('comments', $postid, true), array('access_token'=>$access_token, 'comment'=>$comment));
	}
	
	/**
	 * Like a certain post feed
	 *
	 * @param mixed the name or id of the object we would like to like (feed)
	 * @param mixed access_token of the currently logged in user
	 * @return boolean true/false on success
	 */
	public function postLike( $postid, $access_token )
	{
		return $this->doCall($this->callApi('likes', $postid, true), array('access_token'=>$access_token));
	}

	/**
	 * Post a note to a users wall
	 *
	 * @param mixed the name or id of the object we would like to post a note to it's wall
	 * 				If the param is null then it will fetch the current users information
	 * @param mixed access_token of the currently logged in user
	 * @param array list of additional parameters to pass
	 * 		        This method supports the following parameters:
	 * 				- message, subject
	 * @return string the id of the newly created note
	 */
	public function postNote($profileid=null, $access_token, $params=array())
	{
		return $this->doCall($this->callApi('notes', $profileid, true), array_merge(array('access_token'=>$access_token), $params));
	}

	/**
	 * Post a link to a users wall
	 *
	 * @param mixed the name or id of the object we would like to post a link to it's wall
	 * 				If the param is null then it will fetch the current users information
	 * @param mixed access_token of the currently logged in user
	 * @param array list of additional parameters to pass
	 * 		        This method supports the following parameters:
	 * 				- message, link
	 * @return string the id of the newly created link
	 */
	public function postLink($profileid=null, $access_token, $params=array())
	{
		return $this->doCall($this->callApi('links', $profileid, true), array_merge(array('access_token'=>$access_token), $params));
	}

	/**
	 * Post an event by a certain user
	 *
	 * @param mixed the name or id of the object we would like to post an event
	 * 				If the param is null then it will fetch the current users information
	 * @param mixed access_token of the currently logged in user
	 * @param array list of additional parameters to pass
	 * 		        This method supports the following parameters:
	 * 				- name, start_time, end_time, description, owner, location
	 * @return string id of the newly created event
	 */
	public function postEvent($profileid=null, $access_token, $params=array())
	{
		return $this->doCall($this->callApi('events', $profileid, true), array_merge(array('access_token'=>$access_token), $params));
	}

	/**
	 * Attend an event
	 *
	 * @param mixed the event id we would like to attend to
	 * @param mixed access_token of the currently logged in user
	 * @return boolean true/false on success
	 */
	public function postAttending($eventid, $access_token)
	{
		return $this->doCall($this->callApi('attending', $eventid, true), array('access_token'=>$access_token));
	}
	
	/**
	 * Decline an event
	 *
	 * @param mixed the event id we would not like to attend
	 * @param mixed access_token of the currently logged in user
	 * @return boolean true/false on success
	 */
	public function postNotAttending($eventid, $access_token)
	{
		return $this->doCall($this->callApi('declined', $eventid, true), array('access_token'=>$access_token));
	}
	
	/**
	 * Maybe Attend an event
	 *
	 * @param mixed the event id we might attend
	 * @param mixed access_token of the currently logged in user
	 * @return boolean true/false on success
	 */
	public function postMaybeAttending($eventid, $access_token)
	{
		return $this->doCall($this->callApi('maybe', $eventid, true), array('access_token'=>$access_token));
	}

	/**
	 * Post a new album to a users profile
	 *
	 * @param mixed the id of the object we would like to post an album
	 * 				If the param is null then it will fetch the current users information
	 * @param mixed access_token of the currently logged in user
	 * @param array list of additional parameters to pass
	 * 		        This method supports the following parameters:
	 * 				- name, message
	 * @return string id of the newly created album
	 */
	public function postAlbum($profileid=null, $access_token, $params=array())
	{
		return $this->doCall($this->callApi('albums', $profileid, true), array_merge(array('access_token'=>$access_token), $params));
	}

	/**
	 * Upload a photo to an album
	 *
	 * @param mixed the id of the object we would like to upload a photo to
	 * 				If the param is null then it will fetch the current users information
	 * @param mixed access_token of the currently logged in user
	 * @param string the message describing the photo
	 * @return string id of the newly created photo
	 */
	public function postPhoto( $albumid, $access_token, $message )
	{
		return $this->doCall($this->callApi('photos', $albumid, true), array('access_token'=>$access_token, 'message'=>$message));
	}

	/**
	 * Search for a certain object
	 *
	 * All public posts: 
	 * - https://graph.facebook.com/search?q=watermelon&type=post
     * - People: https://graph.facebook.com/search?q=mark&type=user
     * - Pages: https://graph.facebook.com/search?q=platform&type=page
     * - Events: https://graph.facebook.com/search?q=conference&type=event
     * - Groups: https://graph.facebook.com/search?q=programming&type=group
 	 *
	 * @param mixed type of the search
	 * @param mixed query the query to search for
	 * @param array list of additional parameters to pass
	 * @return object search results
	 */
	public function search( $type, $query, $params=array() )
	{
		return $this->doCall($this->callApi('search', 0, true, true, array_merge(array('q'=>$query,'type'=>$type), $params)), array(), false );
	}
	
	/**
	 * Display the facebook social plugin activity box 
	 * 
	 * @param string the site url we would like to get the activity from
	 * @param integer the width of the activity box
	 * @param integer the height of the activity box
	 * @param string 'true'/'false' display the activity box header or not
	 * @param string 'light'/'dark' the activity box color scheme
	 * @param boolean whether we would like to use the fb:activity fbml tag or an iframe
	 * @return string the generated iframe
	 */
	public function showActivity( $site, $width=300, $height=300, $header='true', $color='light', $fbml=true )
	{
		if( $fbml )
		{
			return "<fb:activity site='{$site}' width='{$width}' height='{$height}' colorscheme='{$color}' header='{$header}'></fb:activity>";
		}
		return "<iframe src='http://www.facebook.com/plugins/activity.php?site={$site}width={$width}&amp;height={$height}&amp;header={$header}&amp;colorscheme={$colorScheme}' scrolling='no' frameborder='0' allowTransparency='true' style='border:none; overflow:hidden; width:{$width}px; height:{$height}px'></iframe>";
	}
	
	/**
	 * Display the facebook social plugin comments box
	 * 
	 * @param integer unique id of this widget
	 * @param integer the width of the comments box
	 * @param integer number of posts to show in the comments box
	 * @return string the generated fb:comments tag
	 */
	public function showComments( $id='widget', $width=500, $numposts=10 )
	{
		return "<fb:comments xid='{$id}' numposts='{$numposts}' width='{$width}'></fb:comments>";
	}
	
	/**
	 * Display the facebook social plugin face pile
	 * 
	 * @param integer the width of the face pile box
	 * @param integer number of rows to display
	 * @return string the generated fb:facepile 
	 */
	public function showFacePile( $width=200, $numrows=10 )
	{
		return "<fb:facepile max-rows='{$numrows}' width='{$width}'></fb:facepile>";
	}
	
	/**
	 * Display the facebook social plugin like button
	 * 
	 * @param string the url we would like to like
	 * @param string the layout of the like button
	 * @param string 'true'/'false' whether to show faces under the like button
	 * @param integer 
	 * @param string 'like'/'recommended' the action name
	 * @param string 'light'/'dark' the activity box color scheme
	 * @param boolean whether we would like to use the fb:activity fbml tag or an iframe
	 * @return string the generated iframe
	 */
	public function showLike( $url, $layout='standard', $showfaces='true', $width=450, $action='like', $color='light', $fbml=true )
	{
		if( $fbml )
		{
			return "<fb:like href='{$url}' layout='{$layout}' show_faces='{$showfaces}' width='{$width}' action='{$action}' colorscheme='{$color}'></fb:like>";
		}
		return "<iframe src='http://www.facebook.com/plugins/like.php?href={$url}&layout={$layout}&amp;show_faces={$showfaces}&amp;width={$width}&amp;action={$action}&amp;colorscheme={$color}' scrolling='no' frameborder='0' allowTransparency='true' style='border:none; overflow:hidden; width:{$width}px; height:px'></iframe>";
	}
	
	/**
	 * The Like Box is a social plugin that enables Facebook Page owners to attract and gain Likes from their own website. 
	 * The Like Box enables users to:
     *
	 * - See how many users already like this page, and which of their friends like it too
	 * - Read recent posts from the page
	 * - Like the page with one click, without needing to visit the page
	 *
	 * @param integer the profile id of the page we would like to show likes from
	 * @param integer the width of the like box widget
	 * @param integer the number of connections to show
	 * @param string 'true'/'false' whether to show the page public stream or not
	 * @param string 'true'/'false' whether to show the header in the box or not
	 * @return string the generated iframe	
	 *
	 */
	public function showLikeBox( $profileid, $width=292, $connections=10, $stream='true', $header='true' )
	{
		return "<fb:like-box profile_id='{$profileid}' width='{$width}' connections='{$connections}' stream='{$stream}' header='{$header}'></fb:like-box>";
	}
	
	/**
	 * The Live Stream plugin lets users visiting your site or application share activity and comments in real time. 
	 * The Live Stream Box works best when you are running a real-time event, like live streaming video for concerts, speeches, or webcasts, live Web chats, webinars, massively multiplayer games.
	 * 
	 * @param integer the application id to show the live stream from
	 * @param integer the width of the live stream box
	 * @param integer the height of the live stream box
	 * @param mixed the live stream unique id (if you have more then one live stream on the page then enter a unique name for each one)
	 * @return string the live stream widget
	 */
	public function showLiveStream( $appid, $width=400, $height=500, $xid='livestream' )
	{
		return "<fb:live-stream app_id='{$appid}' width='{$width}' height='{$height}' xid='{$xid}'></fb:live-stream>";
	}
	
	/**
	 * The Login with Faces plugin shows profile pictures of the user's friends who have already signed up for your site in addition to a login button.
	 * 
	 * @param string 'true'/'false' whether to show the faces of the facebook users who already use this application
	 * @param integer the width of the facebook login button
	 * @param integer number of rows to display the faces in
	 * @return string the login button fbml tag
	 */
	public function showLoginButton( $showfaces='true', $width=200, $maxrows=1 )
	{
		return "<fb:login-button show-faces='{$showfaces}' width='{$width}' max-rows='{$maxrows}'></fb:login-button>";
	}
	
	/**
	 * The Recommendations plugin shows personalized recommendations to your users. 
	 * Since the content is hosted by Facebook, the plugin can display personalized recommendations whether or not the user has logged into your site. 
	 * To generate the recommendations, the plugin considers all the social interactions with URLs from your site. 
	 * For a logged in Facebook user, the plugin will give preference to and highlight objects her friends have interacted with.
	 * 
	 * @param string the url we would like to show the recommendations from
	 * @param integer the width of the recommendations box
	 * @param integer the height of the recommendations box
	 * @param string 'true'/'false' whether to include the header of the recommendations box
	 * @param string 'light'/'dark' display the recommendations box in a certain color scheme
	 * @param boolean whether to show the recommendation box using facebook fbml tag or an iframe
	 * @return string the recommendations fbml tag or iframe
	 */
	public function showRecommendations( $url, $width=300, $height=300, $header='true', $color='light', $fbml=true )
	{
		if( $fbml )
		{
			return "<fb:recommendations site='{$url}' width='{$width}' height='{$height}' header='{$header}' colorscheme='{$color}'></fb:recommendations>";
		}
		return "<iframe src='http://www.facebook.com/plugins/recommendations.php?site={$url}&amp;width={$width}&amp;height={$height}&amp;header={$header}&amp;colorscheme={$color}' scrolling='no' frameborder='0' allowTransparency='true' style='border:none; overflow:hidden; width:{$width}px; height:{$height}px'></iframe>";
	}
	
	/**
	 * 
	 * 
	 * @param integer the application id
	 * @param string 'true'/'false' whether to check the login status or not
	 * @param string 'true'/'false' whether to enable cookies to allow the server to access the session
	 * @param string 'true'/'false' whether to enable parsing XFBML
	 * @param string the language of the JS file loaded canonical ID for example 'en_US', 'he_IL' etc..
	 * @return string the generated script tag and div
	 */
	public function includeScript( $appid, $status='true', $cookie='true', $xfbml='true', $language='en_US' )
	{
		$code = <<<EOF
			<div id="fb-root"></div>
			<script>
			  window.fbAsyncInit = function() {
			    FB.init({appId: '{$appid}', status: {$status}, cookie: {$cookie}, xfbml: {$xfbml} });
			  };
			  (function() {
			    var e = document.createElement('script'); e.async = true;
			    e.src = document.location.protocol +
			      '//connect.facebook.net/{$language}/all.js';
			    document.getElementById('fb-root').appendChild(e);
			  }());
			</script>
EOF;
	
		return $code;
		
	}
	
	/**
	 * Set the SSL Check status
	 *
	 * @param boolean the SSL check status
	 * @return facebookLib object
	 */
	public function setSSLStatus($status=false)
	{
		$this->disableSSLCheck = $status;
		return $this;
	}
	/**
	 * Get the SSL check status
	 * 
	 * @return boolean the SSL Check status
	 */
	public function getSSLStatus()
	{
		return $this->disableSSLCheck;
	}
	/**
	 * Set the headers
	 *
	 * @param array the headers array
	 * @return facebookLib object
	 */
	public function setHeaders( $headers='' )
	{
		$this->headers = $headers;
		return $this;
	}
	/**
	 * Get the headers
	 * 
	 * @return array the headers returned from the call
	 */
	public function getHeaders()
	{
		return $this->headers;
	}
	/**
	 * Set the error code number
	 *
	 * @param integer the error code number
	 * @return facebookLib object
	 */
	public function setErrorCode($code=0)
	{
		$this->errorCode = $code;
		return $this;
	}
	/**
	 * Get the error code number
	 * 
	 * @return integer error code number
	 */
	public function getErrorCode()
	{
		return $this->errorCode;
	}
	/**
	 * Set the error message
	 *
	 * @param string the error message
	 * @return facebookLib object
	 */
	public function setErrorMessage($message='')
	{
		$this->errorMessage = $message;
		return $this;
	}
	/**
	 * Get the error code message
	 * 
	 * @return string error code message
	 */
	public function getErrorMessage()
	{
		return $this->errorMessage;
	}
	/**
	 * Set the throwExceptions status
	 *
	 * @param boolean the throwExceptions status
	 * @return facebookLib object
	 */
	public function setThrowExceptions($status=true)
	{
		$this->throwExceptions = $status;
		return $this;
	}
	/**
	 * Get the throwExceptions
	 * 
	 * @return boolean the throwExceptions
	 */
	public function getThrowExceptions()
	{
		return $this->throwExceptions;
	}
	/**
	 * Set the response
	 *
	 * @param mixed the response returned from the call
	 * @return facebookLib object
	 */
	public function setResponse( $response='' )
	{
		$this->response = $response;
		return $this;
	}
	/**
	 * Get the response data
	 * 
	 * @return mixed the response data
	 */
	public function getResponse()
	{
		return $this->response;
	}
	/**
	 * Set the Json Decode property
	 *
	 * @param boolean the json decode status
	 * @return facebookLib object
	 */
	public function setDecodeJson($status=false)
	{
		$this->decodeJson = $status;
		return $this;
	}
	/**
	 * Get the DecodeJson property status
	 *
	 * @return boolean the decodeJson status
	 */
	public function getDecodeJson()
	{
		return $this->decodeJson;
	}
	/**
	 * Set the return as an array property
	 *
	 * @param boolean
	 * @return facebookLib object
	 */
	public function setReturnArray($status=false)
	{
		$this->returnAsArray = $status;
		return $this;
	}
	/**
	 * Get the DecodeJson property status
	 *
	 * @return boolean the decodeJson status
	 */
	public function getReturnArray()
	{
		return $this->returnAsArray;
	}
	
	/**
	 * This function is used to 
	 *
	 * @param string the url we would like to parse
	 * @param array array of post parameters to include in the call
	 * @param boolean whether to use a post request or a get request
	 * @throws Exception
	 * @return string error message on failure mixed on success (based on the certain call can be an array or information, integer representing an id or a string)
	 */
	protected function doCall($url, $params=array(), $usepost=true)
	{	
		$var = '';
		// rebuild url if we don't use post
		if(count( $params ))
		{
			// rebuild parameters
			foreach($params as $key => $value) 
			{
				$var .= '&'. $key .'='. urlencode($value);
			}	
		}
		
		// If disabled is on we need to convert the https to http for post calls
		// Since it won't allow use to post stuff to facebook
		if( $this->disableSSLCheck )
		{
			$url = str_replace('https://', 'http://', $url);
		}
		
		// set options
		$options = self::$CURL_OPTS;
		$options[CURLOPT_URL] = $url;
		// set extra options
		if( $usepost )
		{
			$options[CURLOPT_POST] = true;
			$options[CURLOPT_POSTFIELDS] = trim($var, '&');
		}
		
		// Disable SSL check if we do not use SSL here
		if( $this->disableSSLCheck )
		{
			$options[CURLOPT_SSL_VERIFYPEER] = false;
		}

		// init
		$curl = curl_init();

		// set options
		curl_setopt_array($curl, $options);

		// execute
		$this->setResponse( curl_exec($curl) );
		$this->setHeaders( curl_getinfo($curl) );

		// fetch errors
		$this->setErrorCode( curl_errno($curl) );
		$this->setErrorMessage( curl_error($curl) );
		
		// If error then return the error
		if( $this->getErrorMessage() )
		{
			if( $this->getThrowExceptions() )
			{
				throw new Exception( $this->getErrorMessage() );
			}
			else
			{
				return $this->getErrorMessage();
			}
		}

		// close
		curl_close($curl);
		
		// Do we need to convert Json?
		if( substr($this->getResponse(), 0, 2) == '{"' )
		{
			if( $this->getDecodeJson() )
			{
                $return = json_decode( $this->getResponse() );
			    // Convert to array
                if($this->getReturnArray()) {
                    $return = $this->objectToArray($return);
                }
				$this->setResponse( $return );
			}
		}
		
		return $this->getResponse();
	}
	
   	/**
   	* Makes an HTTP request. This method can be overriden by subclasses if
   	* developers want to do fancier things or use something other than curl to
   	* make the request.
   	*
   	* @param String the URL to make the request to
   	* @param Array the parameters to use for the POST body
   	* @param CurlHandler optional initialized curl handle
   	* @return String the response text
   	*/
  	protected function makeRequest($url, $params, $ch=null) 
	{
    	if (!$ch) {
      	$ch = curl_init();
    	}
		
		// If disabled is on we need to convert the https to http for post calls
		// Since it won't allow use to post stuff to facebook
		if( $this->disableSSLCheck )
		{
			$url = str_replace('https://', 'http://', $url);
		}
		
    	$opts = self::$CURL_OPTS;
    	$opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
    	$opts[CURLOPT_URL] = $url;

		// Disable SSL check if we do not use SSL here
		if( $this->disableSSLCheck )
		{
			$opts[CURLOPT_SSL_VERIFYPEER] = false;
		}

		// init
		$ch = curl_init();

		// set options
		curl_setopt_array($ch, $opts);

		// execute
		$this->setResponse( curl_exec($ch) );
		$this->setHeaders( curl_getinfo($ch) );

		// fetch errors
		$this->setErrorCode( curl_errno($ch) );
		$this->setErrorMessage( curl_error($ch) );
		
		// If error then return the error
		if( $this->getErrorMessage() )
		{
			if( $this->getThrowExceptions() )
			{
				throw new Exception( $this->getErrorMessage() );
			}
			else
			{
				return $this->getErrorMessage();
			}
		}

		// close
		curl_close($ch);
		
		// Do we need to convert JSON?
		if( substr($this->getResponse(), 0, 2) == '{"' )
		{
			if( $this->getDecodeJson() )
			{   
			    $return = json_decode( $this->getResponse() );
			    // Convert to array
                if($this->getReturnArray()) {
                    $return = $this->objectToArray($return);
                }
				$this->setResponse( $return );
			}
		}
		
		return $this->getResponse();
    }
	
	/**
	 * Internal Call to facebook graph API 
	 *
	 *
	 * @param string the API method we would like to call
	 * @param integer/string the user id or name we use to make the call
	 * @param boolean if to perform a call or just return a link of that call instead
	 * @param boolean if we would like to perform the call without the user id in the link
	 * @param array any additional parameters to pass to the call method
	 * @return mixed response
	 */
	protected function callApi($api='', $userid=null, $returnLink=false, $nouid=false, $params=array())
	{
		$uid = $userid !== null ? $userid : $this->getUser();
		$uid = $nouid === true ? '' : $uid . '/';
		$apimethod = $api == 'me' ? '' : $api;
		$query = !empty($params) ? '?' . http_build_query($params) : '';
		if( $returnLink )
		{
			return Facebook::$DOMAIN_MAP['graph'] . $uid . $apimethod . $query;
		}
        return $this->api($uid . $apimethod . $query);
	}
	
	/**
	 * Convert an object to an array
	 *
	 */
	protected function objectToArray( $object )
    {
        if( !is_object( $object ) && !is_array( $object ) )
        {
            return $object;
        }
        if( is_object( $object ) )
        {
            $object = get_object_vars( $object );
        }
        return array_map( array($this, 'objectToArray'), $object );
    }
	
}