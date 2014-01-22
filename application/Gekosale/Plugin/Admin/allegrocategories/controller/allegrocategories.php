<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace Gekosale\Plugin;

use FormEngine;

class AllegroCategoriesController extends Component\Controller\Admin
{

	public function index ()
	{
		try {
			$form = new FormEngine\Elements\Form(Array(
				'name' => 'allegro_categories',
				'action' => '',
				'method' => 'post'
			));

			$favouritecategories = Array();
			$favourite = App::getModel('allegro/allegrocategories')->doGetFavouriteCategoriesFromAllegro();
			foreach ($favourite as $fav){
				$favouritecategories[] = Array(
					$fav['id']
				);
			}
			$rawRelatedCategories = App::getModel('allegro/allegrocategories')->getRelatedAllegroCatsToSelect();

			$allegroRelCatsPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
				'name' => 'relatedcategories_pane',
				'label' => $this->trans('TXT_ALLEGRO_RELATED_CATEGORIES')
			)));

			$allegroRelCatsPane->AddChild(new FormEngine\Elements\Tip(Array(
				'tip' => '<p align="center">Poniżej możesz ustawić powiązania między kategoriami sklepu a kategoriami Allegro. Powiązania takie będą automatycznie stosowane w przypadku tworzenia nowych aukcji.</p>',
				'direction' => FormEngine\Elements\Tip::DOWN
			)));

			$allegroRelCatsPane->AddChild(new FormEngine\Elements\RelatedCategories(Array(
				'name' => 'relatedcategories',
				'label' => $this->trans('TXT_ALLEGRO_RELATED_CATEGORIES'),
				'help' => $this->trans('TXT_ALLEGRO_RELATED_CATEGORIES_HELP'),
				'choosable' => false,
				'selectable' => false,
				'sortable' => false,
				'clickable' => true,
				'items' => App::getModel('allegro/allegrocategories')->getLocalChildAllegroCategories(),
				'load_children' => Array(
					App::getModel('allegro/allegrocategories'),
					'getLocalChildAllegroCategories'
				),
				'columns' => Array(
					Array(
						'caption' => $this->trans('TXT_ALLEGRO_CATEGORY')
					),
					Array(
						'caption' => $this->trans('TXT_SHOP_CATEGORY')
					)
				),
				'load_selected_info' => Array(
					App::getModel('allegro/allegrocategories'),
					'getRelatedCategoryPath'
				),
				'shop_categories' => App::getModel('category/category')->getCategoryAll()
			)));

			//
			// //////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// /////////////////////////////// FAVOURITE CATEGORIES
			// ///////////////////////////////////////////////
			//
			// //////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$allegroFavouriteCategoriesPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
				'name' => 'favouritecategories_pane',
				'label' => $this->trans('TXT_ALLEGRO_FAVOURITE_CATEGORIES')
			)));

			$allegroFavouriteCategoriesPane->AddChild(new FormEngine\Elements\Tip(Array(
				'tip' => '<p align="center">Poniżej możesz wybrać ulubione kategorie Allegro. Będziesz mógł dzięki temu szybko wybierać je podczas wystawiania nowych aukcji.</p>',
				'direction' => FormEngine\Elements\Tip::DOWN
			)));

			$allegroFavouriteCategoriesPane->AddChild(new FormEngine\Elements\FavouriteCategories(Array(
				'name' => 'favouritecategories',
				'label' => $this->trans('TXT_ALLEGRO_FAVOURITE_CATEGORIES'),
				'help' => $this->trans('TXT_CATEGORY_HELP'),
				'choosable' => false,
				'selectable' => true,
				'sortable' => false,
				'clickable' => false,
				'items' => App::getModel('allegro/allegrocategories')->getLocalChildAllegroCategories(),
				'load_children' => Array(
					App::getModel('allegro/allegrocategories'),
					'getLocalChildAllegroCategories'
				),
				'columns' => Array(
					Array(
						'caption' => $this->trans('TXT_ALLEGRO_CATEGORY')
					)
				),
				'load_selected_info' => Array(
					App::getModel('allegro/allegrocategories'),
					'getCategoryPath'
				)
			)));

			$allegroData = Array(
				'relatedcategories_pane' => Array(
					'relatedcategories' => $rawRelatedCategories
				),
				'favouritecategories_pane' => Array(
					'favouritecategories' => $favouritecategories
				)
			);

			$form->Populate($allegroData);

			if ($form->Validate(FormEngine\FE::SubmittedData())){
				try{
					App::getModel('allegrocategories')->editAllegroCategories($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
				}
				catch (Exception $e){
					$this->registry->template->assign('error', $e->getMessage());
				}
				App::redirect(__ADMINPANE__ . '/allegrocategories');
			}

			$this->renderLayout(array(
				'form' => $form->Render()
			));
		}
		catch(\Exception $e) {
			$this->renderLayout(array(
				'errormsg' => $e->getMessage()
			));
		}
	}

	public function confirm ()
	{
		try {
			App::getModel('allegro/allegrocategories')->updateAllegroCategories();
		}
		catch(\Exception $e) {
		}
		App::redirect(__ADMINPANE__ . '/allegrocategories');
	}
}
