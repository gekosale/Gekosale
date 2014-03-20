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
namespace Gekosale\Plugin\Contact\Repository;

use Gekosale\Core\Repository;
use Gekosale\Core\Model\Contact;
use Gekosale\Core\Model\ContactTranslation;

/**
 * Class ContactRepository
 *
 * @package Gekosale\Plugin\Contact\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ContactRepository extends Repository
{

    /**
     * Returns all tax rates
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Contact::with('translation')->get();
    }

    /**
     * Returns a single tax rate
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return Contact::with('translation')->findOrFail($id);
    }

    /**
     * Deletes tax rate by ID
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->transaction(function () use ($id) {
            return Contact::destroy($id);
        });
    }

    /**
     * Saves contact
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $this->transaction(function () use ($Data, $id) {

            $contact = Contact::firstOrNew([
                'id' => $id
            ]);

            $contact->enabled = $Data['enabled'];
            $contact->save();

            foreach ($this->getLanguageIds() as $language) {

                $translation = ContactTranslation::firstOrNew([
                    'contact_id'  => $contact->id,
                    'language_id' => $language
                ]);

                $translation->setTranslationData($Data, $language);
                $translation->save();
            }

        });
    }

    /**
     * Returns array containing values needed to populate the form
     *
     * @param $id
     *
     * @return array
     */
    public function getPopulateData($id)
    {
        $contactData  = $this->find($id);
        $populateData = [];
        $accessor     = $this->getPropertyAccessor();
        $languageData = $contactData->getTranslationData();

        $accessor->setValue($populateData, '[required_data]', [
            'enabled' => $contactData->enabled
        ]);

        $accessor->setValue($populateData, '[translation_data]', [
            'language_data' => $languageData
        ]);

        return $populateData;
    }
}