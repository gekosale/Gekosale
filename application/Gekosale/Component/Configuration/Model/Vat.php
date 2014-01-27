<?php

namespace Gekosale\Component\Configuration\Model;
use Gekosale\Core\Component\Model;

class Vat extends Model
{

    public function add ($Data)
    {
        $vat = new Vat\Vat();
        $vat->setValue($Data['value']);
        foreach ($Data['name'] as $languageid => $name){
            $vat->setLocale($this->getLocale($languageid));
            $vat->setName($name);
        }
        $vat->save();
    }

    public function update ($Data)
    {
        $vat = new Vat\Vat();
        $vat->setValue($Data['value']);
        foreach ($Data['name'] as $locale => $name){
            $vat->setLocale($locale);
            $vat->setName($name);
        }
        $vat->save();
    }

    public function delete ($id)
    {
        $this->get($id)->delete();
    }

    public function get ($id)
    {
        $vat = Vat\VatQuery::create()->findOneById($id);
    }
}
