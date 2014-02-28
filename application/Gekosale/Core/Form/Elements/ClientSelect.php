<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace Gekosale\Core\Form\Elements;

class ClientSelect extends Select implements ElementInterface
{
    public $datagrid;
    protected $_jsFunction;
    protected $_jsFunctionDetails;

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        $this->_jsFunction                      = 'LoadClients_' . $this->_id;
        $this->_jsFunctionDetails               = 'LoadClientData_' . $this->_id;
        $this->_attributes['jsfunction']        = 'xajax_' . $this->_jsFunction;
        $this->_attributes['jsfunctiondetails'] = 'xajax_' . $this->_jsFunctionDetails;
        App::getRegistry()->xajax->registerFunction(array(
            $this->_jsFunction,
            $this,
            'loadClients'
        ));
        App::getRegistry()->xajaxInterface->registerFunction(array(
            $this->_jsFunctionDetails,
            $this,
            'loadClientDetails'
        ));
    }

    protected function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('help', 'sHelp'),
            $this->formatAttributeJs('comment', 'sComment'),
            $this->formatAttributeJs('error', 'sError'),
            $this->formatAttributeJs('jsfunction', 'fLoadClients', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('jsfunctiondetails', 'fLoadClientData', ElementInterface::TYPE_FUNCTION),
            $this->formatRepeatableJs(),
            $this->formatRulesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }

    public function loadClients($request, $processFunction)
    {
        return $this->getDatagrid()->getData($request, $processFunction);
    }

    public function loadClientDetails($request)
    {
        return App::getModel('order')->getClientDataWithAddresses($request);
    }

    public function getDatagrid()
    {
        $this->datagrid = App::getModel('datagrid/datagrid');
        $this->initDatagrid($this->datagrid);

        return $this->datagrid;
    }

    protected function initDatagrid($datagrid)
    {
        $datagrid->setTableData('clientdata', Array(
            'idclient'    => Array(
                'source' => 'C.idclient'
            ),
            'disable'     => Array(
                'source' => 'disable'
            ),
            'clientorder' => Array(
                'source' => 'SUM(O.globalprice)'
            ),
            'firstname'   => Array(
                'source'                => 'CD.firstname',
                'prepareForAutosuggest' => true,
                'encrypted'             => true
            ),
            'surname'     => Array(
                'source'                => 'CD.surname',
                'prepareForAutosuggest' => true,
                'encrypted'             => true
            ),
            'email'       => Array(
                'source'    => 'CD.email',
                'encrypted' => true
            ),
            'groupname'   => Array(
                'source'           => 'CGT.name',
                'prepareForSelect' => true
            ),
            'phone'       => Array(
                'source'    => 'CD.phone',
                'encrypted' => true
            ),
            'phone2'      => Array(
                'source'    => 'CD.phone2',
                'encrypted' => true
            ),
            'adddate'     => Array(
                'source' => 'CD.adddate'
            ),
        ));
        $datagrid->setFrom('
			client C
			LEFT JOIN clientdata CD ON CD.clientid = C.idclient
			LEFT JOIN clientgrouptranslation CGT ON CGT.clientgroupid = CD.clientgroupid AND CGT.languageid=:languageid
			LEFT JOIN orderclientdata OCD ON OCD.clientid = CD.clientid
			LEFT JOIN `order` O ON O.idorder = OCD.orderid
		');

        $datagrid->setGroupBy('C.idclient');

        $datagrid->setAdditionalWhere('
			IF(:viewid IS NULL,1,C.viewid = :viewid)
		');
    }
}