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

            $contact->is_enabled = $Data['is_enabled'];
            $contact->save();

            foreach ($Data['name'] as $languageId => $name) {

                $translation = ContactTranslation::firstOrNew([
                    'contact_id'  => $contact->id,
                    'language_id' => $languageId
                ]);

                $translation->name     = $name;
                $translation->email    = $Data['email'][$languageId];
                $translation->phone    = $Data['phone'][$languageId];
                $translation->street   = $Data['street'][$languageId];
                $translation->streetno = $Data['streetno'][$languageId];
                $translation->flatno   = $Data['flatno'][$languageId];
                $translation->province = $Data['province'][$languageId];
                $translation->city     = $Data['city'][$languageId];
                $translation->country  = $Data['country'][$languageId];
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
        $contactData = $this->find($id);

        return [
            'required_data'    => [
                'is_enabled' => $contactData->is_enabled
            ],
            'translation_data' => [
                'language_data' => $contactData->getLanguageData()
            ]
        ];
    }
}