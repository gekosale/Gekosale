<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Plugin
 * @subpackage  Gekosale\Plugin\Vat
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Plugin\Vat\Model;

use Gekosale\Plugin\Vat\Event\ModelEvent;
use Gekosale\Core\Model;
use Gekosale\Plugin\Vat\Model\ORM\VatQuery;

class Vat extends Model
{

    public $datagrid;

    public function initDatagrid ()
    {
        foreach (VatQuery::with('translation')->get() as $vat)
        {
            echo $vat->name;
        }
        die();
//         $vat = VatQuery::all();
        
//         $vat->load('VatI18n');
        
//         echo $vat->toJson();
        
        $this->datagrid = $this->getDatagrid();
        
        $this->datagrid->setTableData(Array(
            'id' => Array(
                'source' => 'V.id'
            ),
            'name' => Array(
                'source' => 'VT.name'
            ),
            'value' => Array(
                'source' => 'V.value'
            ),
            'created_at' => Array(
                'source' => 'V.created_at'
            )
        ));
        
        $this->datagrid->setFrom('
            vat V
        	LEFT JOIN vat_i18n VT ON VT.id = V.id AND VT.locale = \'pl_PL\'
        ');
        
        $this->datagrid->setGroupBy('
        	V.id
        ');
    }

    public function getFilterData ()
    {
        return $this->datagrid->getFilterData();
    }

    public function doLoadVat ($request, $processFunction)
    {
        return $this->datagrid->getData($request, $processFunction);
    }

    /**
     * Deletes tax rate. Also triggers MODEL_DELETE_EVENT
     *
     * @param   int     $id         Tax ID to delete
     * @param   int     $datagrid   Datagrid instance
     * @see
     *
     * @return  object  \xajaxResponse
     */
    public function doDeleteVAT ($id, $datagrid)
    {
        return $this->datagrid->deleteRow($datagrid, $id, Array(
            $this,
            'delete'
        ), $this->getName());
    }

    /**
     * Adds or updates tax rate. Also triggers MODEL_SAVE_EVENT
     * 
     * @param   array       $Data   Source data. Defaults to form submitted data
     * @param   int|null    $id     Existent tax ID or NULL if creating a new rate
     * 
     * @return  int         $id     Tax rate ID 
     */
    public function save ($Data, $id = null)
    {
        $vat = VatQuery::create()->filterByPrimaryKey($id)->findOneOrCreate();
        
        $vat->setValue($Data['value']);
        
        foreach ($Data['name'] as $locale => $name) {
            $vat->setLocale($locale);
            $vat->setName($name);
        }
        
        $vat->save();
        
        $id = $vat->getId();
        
        $event = new ModelEvent($Data, $id);
        
        $this->getDispatcher()->dispatch(ModelEvent::MODEL_SAVE_EVENT, $event);
        
        return $id;
    }

    /**
     * Deletes tax rate. Also triggers MODEL_DELETE_EVENT
     *
     * @param int    $id    Tax ID to delete
     * @throws \InvalidArgumentException if the tax rate is not found
     * 
     * @return void
     */
    public function delete ($id)
    {
        $vat = VatQuery::create()->findById($id);
        
        if (null === $vat) {
            throw new \InvalidArgumentException(sprintf('Tax rate for ID=%s not found.', $id));
        }
        
        $vat->delete();
    }

    public function getPopulateData ($id)
    {
        $vat = VatQuery::create()->findOneById($id);
        
        if (null === $vat) {
            throw new \InvalidArgumentException(sprintf('Tax rate for ID=%s not found.', $id));
        }
        
        $translations = Array();
        
        foreach ($this->getLocales() as $locale) {
            $translations[$locale] = Array(
                'name' => $vat->getTranslation($locale)->getName()
            );
        }
        
        $Data = Array(
            'required_data' => Array(
                'value' => $vat->getValue(),
                'language_data' => $translations
            )
        );
        return $Data;
    }
}
