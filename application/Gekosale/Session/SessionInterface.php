<?php

/**
 * WellCommerce
 *
 * @copyright   Copyright (c) 2012-2014 WellCommerce
 * @author      WellCommerce, info@wellcommerce.pl
 */
namespace Gekosale\Session;

interface SessionInterface
{

    /**
     * Open new session
     *
     * @return 	SessionInterface
     */
    public function open ();

    /**
     * Close session
     *
     * @return 	SessionInterface
     */
    public function close ();

    /**
     * Read session data
     *
     * @param 	string	$sessionId		 
     * @return 	SessionInterface
     */
    public function read ($sessionid);

    /**
     * Write session data
     *
     * @param 	string	$sessionId
     * @param 	string	$sessioncontent
     * @return 	SessionInterface
     */
    public function write ($sessionid, $sessioncontent);

    /**
     * Destroy session
     *
     * @param 	string	$sessionId
     * @return 	SessionInterface
     */
    public function destroy ($sessionid);

    /**
     * Run garbage collector
     *
     * @param 	int		$lifeTime
     * @return 	SessionInterface
     */
    public function gc ($lifeTime);
}