<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Plugin
 * @subpackage  Gekosale\Plugin\Company
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Plugin\Company\Model;

use Gekosale\Plugin\Company\Event\ModelEvent;
use Gekosale\Core\Model;
use Gekosale\Core\Datagrid;
use Gekosale\Plugin\Company\Model\ORM\CompanyQuery;
use Gekosale\Plugin\Company\Model\ORM\CompanyTranslationQuery;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;

class Company extends Model
{

    public $datagrid;

    public function initDatagrid ()
    {
        $this->datagrid = $this->getDatagrid();
        
        $this->datagrid->setTableData(Array(
            'id' => Array(
                'source' => 'C.id'
            ),
            'company_name' => Array(
                'source' => 'C.company_name'
            ),
            'created_at' => Array(
                'source' => 'C.created_at'
            )
        ));
        
        $this->datagrid->setFrom('
            company C
        ');
        
        $this->datagrid->setGroupBy('
        	C.id
        ');
    }

    public function getFilterData ()
    {
        return $this->datagrid->getFilterData();
    }

    public function doLoadCompany ($request, $processFunction)
    {
        return $this->datagrid->getData($request, $processFunction);
    }

    /**
     * Deletes company. Also triggers MODEL_DELETE_EVENT
     *
     * @param   int     $id         Company ID to delete
     * @param   int     $datagrid   Datagrid instance
     * @see
     *
     * @return  object  \xajaxResponse
     */
    public function doDeleteCompany ($id, $datagrid)
    {
        return $this->datagrid->deleteRow($datagrid, $id, Array(
            $this,
            'delete'
        ), $this->getName());
    }

    /**
     * Adds or updates company. Also triggers MODEL_SAVE_EVENT
     * 
     * @param   array       $Data   Source data. Defaults to form submitted data
     * @param   int|null    $id     Existent company ID or NULL if creating a new rate
     * 
     * @return  int         $id     Company ID 
     */
    public function save ($Data, $id = null)
    {
        $company = CompanyQuery::create()->filterByPrimaryKey($id)->findOneOrCreate();
        
        $company->save();
        
        $id = $company->getId();
        
        $event = new ModelEvent($Data, $id);
        
        $this->getDispatcher()->dispatch(ModelEvent::MODEL_SAVE_EVENT, $event);
        
        return $id;
    }

    /**
     * Deletes company. Also triggers MODEL_DELETE_EVENT
     *
     * @param int    $id    Company to delete
     * @throws \InvalidArgumentException if the company is not found
     * 
     * @return void
     */
    public function delete ($id)
    {
        $company = CompanyQuery::create()->findById($id);
        
        if (null === $company) {
            throw new \InvalidArgumentException(sprintf('Company record for ID=%s not found.', $id));
        }
        
        $company->delete();
    }

    public function getPopulateData ($id)
    {
        $company = CompanyQuery::create()->findOneById($id);
        
        if (null === $company) {
            throw new \InvalidArgumentException(sprintf('Company record for ID=%s not found.', $id));
        }
        
        $Data = Array(
            'required_data' => Array(
                'language_data' => $translations
            )
        );
        return $Data;
    }
}
