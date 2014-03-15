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
namespace Gekosale\Plugin\Language\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class LanguageDataGrid
 *
 * @package Gekosale\Plugin\Language\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LanguageDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->registerEventHandlers();

        $this->addColumn('id', [
            'source' => 'language.id'
        ]);

        $this->addColumn('name', [
            'source' => 'language.name'
        ]);

        $this->addColumn('locale', [
            'source' => 'language.locale'
        ]);

        $this->query = $this->getDb()
            ->table('language')
            ->groupBy('language.id');
    }

    /**
     * {@inheritdoc}
     */
    public function registerEventHandlers()
    {
        $this->getXajaxManager()->registerFunctions([
            'getLanguageForAjax' => [$this, 'getData'],
            'doDeleteLanguage'   => [$this, 'delete']
        ]);
    }
}