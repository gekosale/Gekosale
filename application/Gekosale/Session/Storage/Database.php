<?php

/**
 * WellCommerce
 *
 * @copyright   Copyright (c) 2012-2014 WellCommerce
 * @author      WellCommerce, info@wellcommerce.pl
 */
namespace Gekosale\Session\Storage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Gekosale\Session\SessionInterface;
use Gekosale\Db;
use Gekosale\App;

class Database implements SessionInterface
{

    protected $container;

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
        $this->ttl = $this->container->getParameter('session.session_gc_maxlifetime');
    }

    public function open ()
    {
        return true;
    }

    public function close ()
    {
        return true;
    }

    public function read ($sessionid)
    {
        $sql = 'SELECT sessioncontent FROM sessionhandler WHERE sessionid = :sessionid';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('sessionid', $sessionid);
        try{
            $stmt->execute();
            $rs = $stmt->fetch();
        }
        catch (Exception $e){
            throw new Exception('Session: read broken while query');
        }
        if (! $rs){
            return false;
        }
        return $rs['sessioncontent'];
    }

    public function write ($sessionid, $sessioncontent)
    {
        $clientid = (isset($_SESSION['CurrentState']['Clientid'][0]) && $_SESSION['CurrentState']['Clientid'][0] > 0) ? $_SESSION['CurrentState']['Clientid'][0] : 0;
        $cart = (isset($_SESSION['CurrentState']['Cart'][0])) ? $_SESSION['CurrentState']['Cart'][0] : NULL;
        $viewid = (isset($_SESSION['CurrentState']['MainsideViewId'][0]) && $_SESSION['CurrentState']['MainsideViewId'][0] > 0) ? $_SESSION['CurrentState']['MainsideViewId'][0] : 0;
        $globalprice = isset($_SESSION['CurrentState']['GlobalPrice'][0]) ? (float) $_SESSION['CurrentState']['GlobalPrice'][0] : 0;
        $cartcurrency = isset($_SESSION['CurrentState']['CurrencySymbol'][0]) ? (string) $_SESSION['CurrentState']['CurrencySymbol'][0] : NULL;
        $ipaddress = isset($_SERVER['REMOTE_ADDR']) ? substr($_SERVER['REMOTE_ADDR'], 0, 15) : '000.000.000.000';
        
        $browser = (isset($_SESSION['CurrentState']['BrowserData'][0]['browser']) && $_SESSION['CurrentState']['BrowserData'][0]['browser'] != '') ? $_SESSION['CurrentState']['BrowserData'][0]['browser'] : NULL;
        $platform = (isset($_SESSION['CurrentState']['BrowserData'][0]['platform']) && $_SESSION['CurrentState']['BrowserData'][0]['platform'] != '') ? $_SESSION['CurrentState']['BrowserData'][0]['platform'] : NULL;
        $ismobile = (isset($_SESSION['CurrentState']['BrowserData'][0]['ismobile']) && $_SESSION['CurrentState']['BrowserData'][0]['ismobile'] == 1) ? 1 : 0;
        $isbot = (isset($_SESSION['CurrentState']['BrowserData'][0]['isbot']) && $_SESSION['CurrentState']['BrowserData'][0]['isbot'] == 1) ? 1 : 0;
        
        $sql = 'REPLACE INTO sessionhandler(
					sessioncontent,
					sessionid,
					expiredate,
					clientid,
					globalprice,
					cartcurrency,
					ipaddress,
					browser,
					platform,
					ismobile,
					isbot,
					viewid,
					url,
					cart
				)
				VALUES (
					:sessioncontent,
					:sessionid,
					:expiredate,
					:clientid,
					:globalprice,
					:cartcurrency,
					:ipaddress,
					:browser,
					:platform,
					:ismobile,
					:isbot,
					:viewid,
					:url,
					:cart
				)';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('sessioncontent', $sessioncontent);
        $stmt->bindValue('sessionid', $sessionid);
        $stmt->bindValue('clientid', $clientid);
        $stmt->bindValue('globalprice', $globalprice);
        $stmt->bindValue('cartcurrency', $cartcurrency);
        $stmt->bindValue('ipaddress', $ipaddress);
        $stmt->bindValue('browser', $browser);
        $stmt->bindValue('cart', json_encode($cart));
        $stmt->bindValue('platform', $platform);
        $stmt->bindValue('url', App::getURL());
        $stmt->bindValue('ismobile', $ismobile);
        $stmt->bindValue('isbot', $isbot);
        if ($viewid > 0){
            $stmt->bindValue('viewid', $viewid);
        }
        else{
            $stmt->bindValue('viewid', NULL);
        }
        $stmt->bindValue('expiredate', date('Y-m-d H:i:s', time() + $this->ttl));
        try{
            return $stmt->execute();
        }
        catch (Exception $e){
            throw new \Exception('Session: write broken while query');
        }
    }

    public function destroy ($sessionid)
    {
        if (isset($_SESSION['CurrentState']['Clientid'][0]) && $_SESSION['CurrentState']['Clientid'][0] > 0 && ! isset($_SESSION['CurrentState']['Userid'][0])){
            $checkDeletedSessionHasCart = App::getModel('missingcart')->checkMissingCartSessionid($sessionid);
            if (is_array($checkDeletedSessionHasCart) && $checkDeletedSessionHasCart != NULL){
                $contentHasCart = App::getModel('missingcart')->checkSessionHandlerHasCartData($checkDeletedSessionHasCart['cart']);
                if ($contentHasCart === true){
                    App::getModel('missingcart')->saveMissingCartData($checkDeletedSessionHasCart['cart'], $sessionid);
                }
            }
        }
        
        $sql = 'DELETE FROM sessionhandler WHERE sessionid = :sessionid';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('sessionid', $sessionid);
        try{
            return $stmt->execute();
        }
        catch (Exception $e){
            throw new Exception('Session: destroy broken while query');
        }
    }

    public function gc ($lifeTime)
    {
        $sql = 'DELETE FROM sessionhandler WHERE expiredate < :killtime';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('killtime', date('Y-m-d H:i:s', time()));
        try{
            return $stmt->execute();
        }
        catch (Exception $e){
            throw new Exception('Session: garbage collector broken while query');
        }
    }
}