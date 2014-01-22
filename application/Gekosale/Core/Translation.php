<?php

namespace Gekosale\Core;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Propel\Runtime\Propel;

class Translation extends Translator
{

    protected $locale = 'pl_PL';

    public function __construct ()
    {
        parent::__construct($this->locale);
        parent::addLoader('array', new ArrayLoader());
        parent::addResource('array', $this->getResource(), $this->locale);
    }

    protected function getResource ()
    {
        if (($Data = App::getContainer()->get('cache')->load('translations')) === false){
            $sql = 'SELECT
                    	T.name,
                      	TD.translation
                    FROM translation T
                    LEFT JOIN translationdata TD ON T.idtranslation = TD.translationid
                    WHERE TD.languageid = :languageid';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('languageid', Helper::getLanguageId());
            $stmt->execute();
            while ($rs = $stmt->fetch()){
                $Data[$rs['name']] = $rs['translation'];
            }
            App::getContainer()->get('cache')->save('translations', $Data);
        }
        return $Data;
    }

    public static function get ($id)
    {
        $translation = new Translation();
        return $translation->trans($id);
    }
}