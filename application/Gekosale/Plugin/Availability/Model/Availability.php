<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Plugin
 * @subpackage  Gekosale\Plugin\Availability
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Plugin\Availability\Model;

use Gekosale\Plugin\Availability\Event\ModelEvent;
use Gekosale\Core\Model;
use Gekosale\Core\Datagrid;
use Gekosale\Plugin\Availability\Model\ORM\AvailabilityQuery;
use Gekosale\Plugin\Availability\Model\ORM\AvailabilityTranslationQuery;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;

class Availability extends Model
{

    public $datagrid;

    public function initDatagrid ()
    {
        $this->datagrid = $this->getDatagrid();
        
        $this->datagrid->setTableData(Array(
            'id' => Array(
                'source' => 'A.id'
            ),
            'name' => Array(
                'source' => 'AT.name'
            ),
            'created_at' => Array(
                'source' => 'A.created_at'
            )
        ));
        
        $this->datagrid->setFrom('
            availability A
        	LEFT JOIN availability_i18n AT ON AT.id = A.id AND AT.locale = \'pl_PL\'
        ');
        
        $this->datagrid->setGroupBy('
        	A.id
        ');
    }

    public function getFilterData ()
    {
        return $this->datagrid->getFilterData();
    }

    public function doLoadAvailability ($request, $processFunction)
    {
        return $this->datagrid->getData($request, $processFunction);
    }

    /**
     * Deletes availability. Also triggers MODEL_DELETE_EVENT
     *
     * @param   int     $id         Availability ID to delete
     * @param   int     $datagrid   Datagrid instance
     * @see
     *
     * @return  object  \xajaxResponse
     */
    public function doDeleteAvailability ($id, $datagrid)
    {
        return $this->datagrid->deleteRow($datagrid, $id, Array(
            $this,
            'delete'
        ), $this->getName());
    }

    /**
     * Adds or updates availability. Also triggers MODEL_SAVE_EVENT
     * 
     * @param   array       $Data   Source data. Defaults to form submitted data
     * @param   int|null    $id     Existent availability ID or NULL if creating a new rate
     * 
     * @return  int         $id     Tax rate ID 
     */
    public function save ($Data, $id = null)
    {
        $availability = AvailabilityQuery::create()->filterByPrimaryKey($id)->findOneOrCreate();
        
        foreach ($Data['name'] as $locale => $name) {
            $availability->setLocale($locale);
            $availability->setName($name);
        }
        
        $availability->save();
        
        $id = $availability->getId();
        
        $event = new ModelEvent($Data, $id);
        
        $this->getDispatcher()->dispatch(ModelEvent::MODEL_SAVE_EVENT, $event);
        
        return $id;
    }

    /**
     * Deletes availability. Also triggers MODEL_DELETE_EVENT
     *
     * @param int    $id    Availability to delete
     * @throws \InvalidArgumentException if the tax rate is not found
     * 
     * @return void
     */
    public function delete ($id)
    {
        $availability = AvailabilityQuery::create()->findById($id);
        
        if (null === $availability) {
            throw new \InvalidArgumentException(sprintf('Availability record for ID=%s not found.', $id));
        }
        
        $availability->delete();
    }

    public function getPopulateData ($id)
    {
        $availability = AvailabilityQuery::create()->findOneById($id);
        
        if (null === $availability) {
            throw new \InvalidArgumentException(sprintf('Availability record for ID=%s not found.', $id));
        }
        
        $translations = Array();
        
        foreach ($this->getLocales() as $locale) {
            $translations[$locale] = Array(
                'name' => $availability->getTranslation($locale)->getName()
            );
        }
        
        $Data = Array(
            'required_data' => Array(
                'language_data' => $translations
            )
        );
        return $Data;
    }
}
