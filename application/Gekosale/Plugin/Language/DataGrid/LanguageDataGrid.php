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

use Gekosale\Core\DataGrid;
use Gekosale\Core\DataGrid\DataGridInterface;
use Gekosale\Plugin\Language\Event\LanguageDataGridEvent;

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
    public function configure()
    {
        $this->setOptions([
            'id'             => 'language',
            'event_handlers' => [
                'load'       => $this->getXajaxManager()->registerFunction(['LoadLanguage', $this, 'loadData']),
                'edit_row'   => 'editLanguage',
                'click_row'  => 'editLanguage',
                'delete_row' => $this->getXajaxManager()->registerFunction(['DeleteLanguage', $this, 'deleteRow']),
            ],
            'routes'         => [
                'edit' => $this->generateUrl('admin.language.edit')
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->addColumn('id', [
            'source'     => 'language.id',
            'caption'    => $this->trans('Id'),
            'sorting'    => [
                'default_order' => DataGridInterface::SORT_DIR_DESC
            ],
            'appearance' => [
                'width'   => 90,
                'visible' => false
            ],
            'filter'     => [
                'type' => DataGridInterface::FILTER_BETWEEN
            ]
        ]);

        $this->addColumn('name', [
            'source'     => 'language.name',
            'caption'    => $this->trans('Name'),
            'appearance' => [
                'width' => 70,
                'align' => DataGridInterface::ALIGN_LEFT
            ],
            'filter'     => [
                'type' => DataGridInterface::FILTER_INPUT
            ]
        ]);

        $this->addColumn('locale', [
            'source'     => 'language.locale',
            'caption'    => $this->trans('Locale'),
            'appearance' => [
                'width' => 70,
                'align' => DataGridInterface::ALIGN_LEFT
            ],
            'filter'     => [
                'type' => DataGridInterface::FILTER_INPUT
            ]
        ]);

        $this->query = $this->getDb()
            ->table('language')
            ->groupBy('language.id');

        $event = new LanguageDataGridEvent($this);

        $this->getDispatcher()->dispatch(LanguageDataGridEvent::DATAGRID_INIT_EVENT, $event);
    }

    /**
     * Returns route for editAction
     *
     * @return string
     */
    protected function getEditActionRoute()
    {
        return 'admin.language.edit';
    }
}