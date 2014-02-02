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

use Gekosale\Core\Model;
use Gekosale\Core\Datagrid;
use Gekosale\Plugin\Vat\Model\ORM\VatQuery;
use Gekosale\Plugin\Vat\Model\ORM\VatTranslationQuery;

class Vat extends Model
{

    public $datagrid;

    public function initDatagrid ()
    {
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
            'add_date' => Array(
                'source' => 'V.add_date'
            )
        ));
        
        $this->datagrid->setFrom('
            vat V
        	LEFT JOIN vat_translation VT ON VT.vat_id = V.id AND VT.language_id = :languageid
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

    public function doDeleteVAT ($id, $datagrid)
    {
        return $this->datagrid->deleteRow($datagrid, $id, Array(
            $this,
            'delete'
        ), $this->getName());
    }

    public function save ($Data, $id = null)
    {
        $vat = VatQuery::create()->filterByPrimaryKey($id)->findOneOrCreate()->setValue($Data['value']);
        
        foreach ($Data['name'] as $languageId => $name) {
            $vatTranslation = VatTranslationQuery::create()->filterByLanguageId($languageId)
                ->filterByVatId($id)
                ->findOneOrCreate()
                ->setLanguageId($languageId)
                ->setName($name);
            
            $vat->addVatTranslation($vatTranslation);
        }
        
        $vat->save();
        
        return $vat->getId();
    }

    public function delete ($id)
    {
        $vat = VatQuery::create()->findById($id);
        
        if (null == $vat) {
            throw new \InvalidArgumentException('Tax rate not found.');
        }
        
        $vat->delete();
    }

    public function getPopulateData ($id)
    {
        $vat = VatQuery::create()->findOneById($id);
        
        if (null == $vat) {
            throw new \InvalidArgumentException('Tax rate not found.');
        }
        
        $translations = Array();
        
        foreach ($vat->getVatTranslations() as $translation) {
            $translations[$translation->getLanguageId()] = Array(
                'name' => $translation->getName()
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
