<?php

namespace Gekosale;

class AllegroApi extends \SOAP\Connector
{

    protected $config;

    protected $client;

    protected $version;

    protected $sessionHandle;

    protected $userId;

    protected $serverTime;

    protected $countryCode;

    protected $userLogin;

    protected $userPassword;

    protected $webApiKey;

    protected $wsdl = 'https://webapi.allegro.pl/uploader.php?wsdl';

    protected $instance = NULL;

    CONST TAB_NAME = 'allegro_data';

    public function __construct ()
    {
        $this->registry = App::getRegistry();
        
        $this->setWsdl($this->wsdl);
        
        $this->config = $this->registry->core->loadModuleSettings('allegro', Helper::getViewId());
        if (empty($this->config)){
            App::getContainer()->get('session')->setVolatileMessage('Najpierw musisz dokonać konfiguracji modułu Allegro');
            if (Helper::getViewId() > 0){
                App::redirect(__ADMINPANE__ . '/view/edit/' . Helper::getViewId() . '#' . self::TAB_NAME);
            }
            else{
                App::redirect(__ADMINPANE__ . '/view');
            }
        }
        $this->config['version'] = $this->version;
        $this->userLogin = $this->config['allegrologin'];
        $this->userPassword = $this->config['allegropassword'];
        $this->webApiKey = $this->config['allegrowebapikey'];
        if (App::getContainer()->get('session')->getActiveAllegroSessionTime() < time()){
            App::getContainer()->get('session')->setActiveAllegroSession(NULL);
        }
        if (App::getContainer()->get('session')->getActiveAllegroSession() == NULL){
            $this->version = $this->getVersion();
            $session = $this->doLoginEnc();
            App::getContainer()->get('session')->setActiveAllegroSessionTime($session['server-time'] + 3600);
            App::getContainer()->get('session')->setActiveAllegroSession($session['session-handle-part']);
            App::getContainer()->get('session')->setActiveAllegroVersion($this->version);
            App::getContainer()->get('session')->setActiveAllegroCountryId($this->config['allegrocountry']);
        }
        else{
            $this->version = App::getContainer()->get('session')->getActiveAllegroVersion();
            $this->sessionHandle = App::getContainer()->get('session')->getActiveAllegroSession();
        }
    }

    public function doLogin ()
    {
        $response = $this->getClient()->doLogin($this->userLogin, $this->userPassword, $this->config['allegrocountry'], $this->webApiKey, $this->version);
        if (! empty($response)){
            $this->setSessionData($response);
        }
        return $response;
    }

    public function doLoginEnc ()
    {
        $response = $this->getClient()->doLoginEnc($this->userLogin, $this->hashPassword($this->userPassword), $this->config['allegrocountry'], $this->webApiKey, $this->version);
        if (! empty($response)){
            $this->setSessionData($response);
        }
        return $response;
    }

    public function getSessionHandle ()
    {
        return App::getContainer()->get('session')->getActiveAllegroSession();
    }

    public function setSessionData ($data)
    {
        $this->sessionHandle = $data['session-handle-part'];
        $this->userId = $data['user-id'];
        $this->serverTime = $data['server-time'];
    }

    public function getUserId ()
    {
        return $this->userId;
    }

    public function getVersion ($sysvar = 3)
    {
        $response = $this->getClient()->doQuerySysStatus($sysvar, $this->config['allegrocountry'], $this->webApiKey);
        return $response['ver-key'];
    }

    public function getCountryCode ()
    {
        return $this->config['allegrocountry'];
    }

    public function getSessionAsArray ()
    {
        return array(
            'session-handle-part' => $this->sessionHandle,
            'user-id' => $this->userId,
            'server-time' => $this->serverTime
        );
    }

    public function objectToArray ($object)
    {
        if (! is_object($object) && ! is_array($object))
            return $object;
        if (is_object($object))
            $object = get_object_vars($object);
        return array_map(array(
            $this,
            'objectToArray'
        ), $object);
    }

    public function hashPassword ($password = '')
    {
        return base64_encode(hash('sha256', $password, true));
    }
    
    /*
     * Auth
     */
    public function doGetMyData ()
    {
        return $this->getClient()->doGetMyData($this->sessionHandle);
    }

    public function doQueryAllSysStatus ()
    {
        return $this->getClient()->doQueryAllSysStatus($this->config['allegrocountry'], $this->webApiKey);
    }

    public function doQuerySysStatus ($sysvar = 3)
    {
        return $this->getClient()->doQuerySysStatus($sysvar, $this->config['allegrocountry'], $this->webApiKey);
    }
    
    /*
     * Categories
     */
    public function doGetCatsData ()
    {
        return $this->objectToArray($this->getClient()->doGetCatsData($this->config['allegrocountry'], $this->version, $this->webApiKey));
    }

    public function doGetCategoryPath ($categoryId)
    {
        return $this->objectToArray($this->getClient()->doGetCategoryPath($this->sessionHandle, $categoryId));
    }

    public function doGetCatsDataCount ()
    {
        return $this->getClient()->doGetCatsDataCount($this->config['allegrocountry'], $this->version, $this->webApiKey);
    }

    public function doGetCatsDataLimit ($offset = 0, $packageElement = 5000)
    {
        return $this->getClient()->doGetCatsDataLimit($this->config['allegrocountry'], $this->version, $this->webApiKey, $offset, $packageElement);
    }

    public function doGetFavouriteCategories ()
    {
        return $this->objectToArray($this->getClient()->doGetFavouriteCategories($this->sessionHandle));
    }
    
    /*
     * Journal
     */
    public function doGetSiteJournal ($journalStart = NULL, $infoType = 0)
    {
        $stopCondition = false;
        $journal = array();
        $currentLimit = 0;
        while (! $stopCondition){
            $journalPortion = $this->getClient()->doGetSiteJournal($this->sessionHandle, $journalStart, $infoType);
            $journal = array_merge($journal, $journalPortion);
            $packageSize = count($journalPortion);
            if ($packageSize < 100){
                $stopCondition = true;
            }
            else{
                $journalStart = $journalPortion[99]->{'row-id'};
            }
        }
        
        return $journal;
    }

    public function doGetSiteJournalDeals ($journalStart = NULL)
    {
        $stopCondition = false;
        $journal = array();
        $currentLimit = 0;
        while (! $stopCondition){
            $journalPortion = $this->getClient()->doGetSiteJournalDeals($this->sessionHandle, $journalStart);
            $journal = array_merge($journal, $journalPortion);
            $packageSize = count($journalPortion);
            if ($packageSize < 100){
                $stopCondition = true;
            }
            else{
                $journalStart = $journalPortion[99]->{'row-id'};
            }
        }
        
        return $journal;
    }

    public function doGetSiteJournalDealsInfo ($journalStart = NULL)
    {
        return $this->getClient()->doGetSiteJournalDealsInfo($this->sessionHandle, $journalStart);
    }

    public function doGetSiteJournalInfo ($startingPoint = NULL, $infoType = 0)
    {
        return $this->getClient()->doGetSiteJournalInfo($this->sessionHandle, $startingPoint, $infoType);
    }
    
    /*
     * Modyify Item
     */
    public function doAddDescToItems ($itemsIdArray, $itDescription)
    {
        return $this->getClient()->doAddDescToItems($this->sessionHandle, $itemsIdArray, $itDescription);
    }

    public function doCancelBidItem ($cancelItemId, $cancelBidsArray, $cancelBidsReason, $cancelAddToBlackList = 0)
    {
        return $this->getClient()->doCancelBidItem($this->sessionHandle, $cancelItemId, $cancelBidsArray, $cancelBidsReason);
    }

    public function doChangeItemFields ($itemId, $fieldsToModify = NULL, $fieldsToRemove = NULL, $previewOnly = 0)
    {
        return $this->getClient()->doChangeItemFields($this->sessionHandle, $itemId, $fieldsToModify, $fieldsToRemove, $previewOnly);
    }

    public function doChangePriceItem ($itemId, $newStartingPrice = NULL, $newReservePrice = NULL, $newBuyNowPrice = NULL)
    {
        return $this->getClient()->doChangePriceItem($this->sessionHandle, $itemId, $newStartingPrice, $newReservePrice, $newBuyNowPrice);
    }

    public function doChangeQuantityItem ($itemIds, $newItemQuantity)
    {
        return $this->getClient()->doChangeQuantityItem($this->sessionHandle, $itemIds, $newItemQuantity);
    }

    public function doFinishItem ($finishItemId, $finishCancelAllBids = NULL, $finishCancelReason = NULL)
    {
        $tmp = $this->getClient()->doFinishItems($this->sessionHandle, array(
            array(
                'finish-item-id' => 0 + $finishItemId, //long
                'finish-cancel-all-bids' => $finishCancelAllBids,
                'finish-cancel-reason' => $finishCancelReason
            )
        ));
        //dump($tmp);
        return $tmp;
    }

    public function doFinishItems ($finishItemsList)
    {
        return $this->getClient()->doFinishItems($this->sessionHandle, $finishItemsList);
    }
    
    /*
     * My Allegro
     */
    public function doGetFavouriteSellers ()
    {
        return $this->getClient()->doGetFavouriteSellers($this->sessionHandle);
    }

    public function doGetMyBidItems ($sortOptions = NULL, $searchValue = NULL, $categoryId = NULL, $itemIds = NULL, $pageSize = 1000, $pageNumber = 0)
    {
        return $this->getClient()->doGetMyBidItems($this->sessionHandle, $sortOptions, $searchValue, $categoryId, $itemIds, $pageSize, $pageNumber);
    }

    public function doGetMyFutureItems ($sortOptions = NULL, $filerOptions = NULL, $itemIds = NULL, $pageSize = 1000, $pageNumber = 0)
    {
        return $this->objectToArray($this->getClient()->doGetMyFutureItems($this->sessionHandle, $sortOptions, $filerOptions, $itemIds, $pageSize, $pageNumber));
    }

    public function doGetMyNotSoldItems ($sortOptions = NULL, $filerOptions = NULL, $searchValue = NULL, $categoryId = NULL, $itemIds = NULL, $pageSize = 1000, $pageNumber = 0)
    {
        return $this->objectToArray($this->getClient()->doGetMyNotSoldItems($this->sessionHandle, $sortOptions, $filerOptions, $searchValue, $categoryId, $itemIds, $pageSize, $pageNumber));
    }

    public function doGetMyNotWonItems ($sortOptions = NULL, $searchValue = NULL, $categoryId = NULL, $itemIds = NULL, $pageSize = 1000, $pageNumber = 0)
    {
        return $this->getClient()->doGetMyNotWonItems($this->sessionHandle, $sortOptions, $searchValue, $categoryId, $itemIds, $pageSize, $pageNumber);
    }

    public function doGetMySellItems ($sortOptions = NULL, $filerOptions = NULL, $searchValue = NULL, $categoryId = NULL, $itemIds = NULL, $pageSize = 1000, $pageNumber = 0)
    {
        return $this->objectToArray($this->getClient()->doGetMySellItems($this->sessionHandle, $sortOptions, $filerOptions, $searchValue, $categoryId, $itemIds, $pageSize, $pageNumber));
    }

    public function doGetMySoldItems ($sortOptions = NULL, $filerOptions = NULL, $searchValue = NULL, $categoryId = NULL, $itemIds = NULL, $pageSize = 1000, $pageNumber = 0)
    {
        return $this->objectToArray($this->getClient()->doGetMySoldItems($this->sessionHandle, $sortOptions, $filerOptions, $searchValue, $categoryId, $itemIds, $pageSize, $pageNumber));
    }

    public function doGetMyWatchedItems ($sortOptions = NULL, $searchValue = NULL, $categoryId = NULL, $itemIds = NULL, $pageSize = 1000, $pageNumber = 0)
    {
        return $this->objectToArray($this->getClient()->doGetMyWatchedItems($this->sessionHandle, $sortOptions, $searchValue, $categoryId, $itemIds, $pageSize, $pageNumber));
    }

    public function doGetMyWatchItems ($sortOptions = NULL, $searchValue = NULL, $categoryId = NULL, $itemIds = NULL, $pageSize = 1000, $pageNumber = 0)
    {
        return $this->objectToArray($this->getClient()->doGetMyWatchItems($this->sessionHandle, $sortOptions, $searchValue, $categoryId, $itemIds, $pageSize, $pageNumber));
    }

    public function doGetMyWonItems ($sortOptions = NULL, $searchValue = NULL, $categoryId = NULL, $itemIds = NULL, $pageSize = 1000, $pageNumber = 0)
    {
        return $this->objectToArray($this->getClient()->doGetMyWonItems($this->sessionHandle, $sortOptions, $searchValue, $categoryId, $itemIds, $pageSize, $pageNumber));
    }

    public function doMyAccount2 ($accountType = 'bid', $offset = 0, $itemsArray = NULL, $limit = 25)
    {
        return $this->objectToArray($this->getClient()->doMyAccount2($this->sessionHandle, $accountType, $offset, $itemsArray, $limit));
    }

    public function doMyAccountItemsCount ($accountType = 'bid', $itemsArray = NULL)
    {
        return $this->getClient()->doMyAccountItemsCount($this->sessionHandle, $accountType, $itemsArray);
    }

    public function doRemoveFromWatchList ($itemsIdArray)
    {
        return $this->getClient()->doRemoveFromWatchList($this->sessionHandle, $itemsIdArray);
    }
    
    /*
     * New auction
     */
    public function doCheckItemDescription ($descriptionContent)
    {
        return $this->getClient()->doCheckItemDescription($this->sessionHandle, $descriptionContent);
    }

    public function doCheckNewAuctionExt ($fields)
    {
        return $this->getClient()->doCheckNewAuctionExt($this->sessionHandle, $fields);
    }

    public function doGetSellFormFieldsExt ()
    {
        return $this->getClient()->doGetSellFormFieldsExt($this->config['allegrocountry'], $localVersion_deprecated = '123', $this->webApiKey);
    }

    public function doGetSellFormFieldsExtLimit ($offset = 0, $packageElement = 50)
    {
        return $this->getClient()->doGetSellFormFieldsExtLimit($this->config['allegrocountry'], $localVersion_deprecated = '123', $this->webApiKey, $offset, $packageElement);
    }

    public function doGetSellFormFieldsForCategory ($categoryId)
    {
        return $this->getClient()->doGetSellFormFieldsForCategory($this->webApiKey, $this->config['allegrocountry'], $categoryId);
    }

    public function doNewAuctionExt ($fields, $itemTemplateId = 0, $localId = NULL, $itemTemplateCreate = NULL)
    {
        return $this->getClient()->doNewAuctionExt($this->sessionHandle, $fields, $itemTemplateId, $localId, $itemTemplateCreate);
    }

    public function doSellSomeAgain ($sellItemsArray, $sellStartingTime = 0, $sellAuctionDuration, $sellOptions = NULL, $localIds = NULL)
    {
        return $this->getClient()->doSellSomeAgain($this->sessionHandle, $sellItemsArray, $sellStartingTime, $sellAuctionDuration, $sellOptions, $localIds);
    }

    public function doVerifyItem ($localId)
    {
        return $this->getClient()->doVerifyItem($this->sessionHandle, $localId);
    }
    
    /*
     * Post buy
     */
    public function doGetFilledPostBuyForms ($paymentType = NULL, $userRole, $fillingTimeFrom = NULL, $fillingTimeTo = NULL)
    {
        return $this->getClient()->doGetFilledPostBuyForms($this->sessionHandle, $paymentType, $userRole, $fillingTimeFrom, $fillingTimeTo);
    }

    public function doGetMyAddresses ()
    {
        return $this->getClient()->doGetMyAddresses($this->sessionHandle);
    }

    public function doGetPaymentMethods ()
    {
        return $this->getClient()->doGetPaymentMethods($this->sessionHandle);
    }

    public function doGetRelatedItems ($itemIds)
    {
        return $this->getClient()->doGetRelatedItems($this->sessionHandle, $itemIds);
    }

    public function doGetShipmentDataForRelatedItems ($itemIds)
    {
        return $this->getClient()->doGetShipmentDataForRelatedItems($this->sessionHandle, $itemIds);
    }

    public function doSendPostBuyForm ($newPostBuyFormSeller, $newPostBuyFormCommon)
    { // struktura
        return $this->getClient()->doSendPostBuyForm($this->sessionHandle, $newPostBuyFormSeller, $newPostBuyFormCommon);
    }
    
    /*
     * Products
     */
    public function doFindProductByName ($productName, $categoryId = NULL)
    {
        return $this->getClient()->doFindProductByName($this->sessionHandle, $productName, $categoryId);
    }

    public function doGetProductCategories ($productId)
    {
        return $this->getClient()->doGetProductCategories($this->sessionHandle, $productId);
    }

    public function doFindProductByCode ($productCode)
    {
        return $this->getClient()->doFindProductByCode($this->sessionHandle, $productCode);
    }

    public function doGetProductCatalogueCategories ()
    {
        return $this->getClient()->doGetProductCatalogueCategories($this->sessionHandle);
    }

    public function doGetProductItems ($productId, $categoryId, $pageSize = 50, $pageNumber = 0)
    {
        return $this->getClient()->doGetProductItems($this->sessionHandle, $productId, $categoryId, $pageSize, $pageNumber);
    }

    public function doTranslateProductID ($bdkProductId)
    {
        return $this->getClient()->doTranslateProductID($this->sessionHandle, $bdkProductId);
    }
    
    /*
     * Various
     */
    public function doCheckExternalKey ($userId, $itemId, $hashKey)
    {
        return $this->getClient()->doCheckExternalKey($this->webApiKey, $userId, $itemId, $hashKey);
    }

    public function doGetCountries ()
    {
        return $this->objectToArray($this->getClient()->doGetCountries($this->config['allegrocountry'], $this->webApiKey));
    }

    public function doGetShipmentData ()
    {
        return $this->getClient()->doGetShipmentData($this->config['allegrocountry'], $this->webApiKey);
    }

    public function doGetSitesFlagInfo ()
    {
        return $this->getClient()->doGetSitesFlagInfo($this->config['allegrocountry'], $this->webApiKey);
    }

    public function doGetSitesInfo ()
    {
        return $this->getClient()->doGetSitesInfo($this->config['allegrocountry'], $this->webApiKey);
    }

    public function doGetStatesInfo ($countryCode = '')
    {
        return $this->objectToArray($this->getClient()->doGetStatesInfo(($countryCode > 0) ? $countryCode : $this->config['allegrocountry'], $this->webApiKey));
    }

    public function doGetSystemTime ()
    {
        return $this->getClient()->doGetSystemTime($this->config['allegrocountry'], $this->webApiKey);
    }

    public function doGetPostBuyData ($items)
    {
        return $this->getClient()->doGetPostBuyData($this->sessionHandle, $items);
    }

    public function doGetTransactionsIDs ($items, $userRole = 'seller')
    {
        return $this->objectToArray($this->getClient()->doGetTransactionsIDs($this->sessionHandle, $items, $userRole));
    }

    public function doGetPostBuyFormsDataForSellers ($transactionIds)
    {
        return $this->objectToArray($this->getClient()->doGetPostBuyFormsDataForSellers($this->sessionHandle, $transactionIds));
    }

    public function doMyContact ($items)
    {
        return $this->objectToArray($this->getClient()->doMyContact($this->sessionHandle, $items));
    }

    public function doGetBidItem2 ($item)
    {
        return $this->getClient()->doGetBidItem2($this->sessionHandle, $item);
    }
}
