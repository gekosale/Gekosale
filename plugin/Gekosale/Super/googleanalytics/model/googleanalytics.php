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
namespace Gekosale;

use FormEngine;

class GoogleAnalyticsModel extends Component\Model
{

		public function addFields ($event, $request)
		{
		    
		    $form = &$request['form'];
		    
		    $analyticsData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
	            'name' => 'analytics_data',
	            'label' => $this->trans('TXT_ANALYTICS_DATA')
	        )));
	        
	        
	         $analyticsData->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p align="center">Aby skorzystać z Universal Analytics, upewnij się, że włączyłeś tą opcję w swoich ustawieniach. <a href="https://support.google.com/analytics/answer/2817075?hl=pl" target="_blank">Czytaj więcej &raquo;</a>.</p>',
            'direction' => FormEngine\Elements\Tip::DOWN
			)));
	        
	        $analyticsData->AddChild(new FormEngine\Elements\Checkbox(Array(
	            'name' => 'enableuniversalga',
	            'label' => $this->trans('TXT_ENABLE_UNIVERSAL_GA')
	        )));
	        
	        $analyticsData->AddChild(new FormEngine\Elements\TextField(Array(
	            'name' => 'gacode',
	            'label' => $this->trans('TXT_GA_CODE'),
	            'comment' => 'UA-XXXXXXXX-X'
	        )));
	        
	        $analyticsData->AddChild(new FormEngine\Elements\Checkbox(Array(
	            'name' => 'gatransactions',
	            'label' => $this->trans('TXT_GA_TRANSACTIONS')
	        )));
	        
	        $analyticsData->AddChild(new FormEngine\Elements\Checkbox(Array(
	            'name' => 'gapages',
	            'label' => $this->trans('TXT_GA_PAGES')
	        )));
	        
		    $settings = $this->registry->core->loadModuleSettings('googleanalytics', (int) $request['id']);
			
			if (! empty($settings)){
				$populate = Array(
					'analytics_data' => Array(
						'gacode' => $settings['gacode'],
						'enableuniversalga' => $settings['enableuniversalga'],
						'gatransactions' => $settings['gatransactions'],
						'gapages' => $settings['gapages']
					)
				);
				
				$event->setReturnValues($populate);
			}
		}
		
		public function saveSettings ($request)
		{
			$Settings = Array(
				'gacode' => $request['data']['gacode'],
				'enableuniversalga' => $request['data']['enableuniversalga'],
				'gatransactions' => $request['data']['gatransactions'],
				'gapages' => $request['data']['gapages']
			);
			
			$this->registry->core->saveModuleSettings('googleanalytics', $Settings, $request['id']);
		}
		
		private function checkIfAnalyticsEnabled($code) {
			return strlen($code) > 0 && $code != 'UA-00000000-0';
		}
		
		public function getGoogleAnalyticsJs ()
		{
			
			$settings = $this->registry->core->loadModuleSettings('googleanalytics', Helper::getViewId());
			
			$code = '';
			
			if(isset($settings['gacode']) && $this->checkIfAnalyticsEnabled($settings['gacode']))
			{
				if($settings['enableuniversalga'] == 1) 
				{
				
					$code .= " <script>
								  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
								  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
								  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
								  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
								
								  ga('create', '{$settings['gacode']}', document.domain);
								  ".($settings['gatransactions'] == 1 ? "ga('require', 'ecommerce', 'ecommerce.js');" : "")."
								  ga('send', 'pageview');
							  </script>";
							  
				} 
				else 
				{
					$code .= " <script>
								    var _gaq = _gaq || [];
									_gaq.push(['_setAccount', '{$settings['gacode']}']);
									_gaq.push(['_setDomainName', document.domain]);
									_gaq.push(['_trackPageview']);
									(function() {
									  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
									  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
									  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
									})();
							  </script>";
				}
			}
			
			return $code;
			
		}
		
		public function getTransGoogleAnalyticsJs ($Data)
		{
			
			$settings = $this->registry->core->loadModuleSettings('googleanalytics', Helper::getViewId());
			
			if($settings['enableuniversalga'] == 1) {
				$code = $this->getUniversalTransactionJs($Data, $settings);
			} else {
				$code = $this->getStandardTransactionJs($Data, $settings);
			}
			
			return $code;
			
		}
		
		private function getStandardTransactionJs($Data, $settings) 
		{
			
			$code = '';
			
			if ($this->checkIfAnalyticsEnabled($settings['gacode']) && $settings['gatransactions'] == 1)
			{
			
			  $tax = $Data['orderData']['priceWithDispatchMethod'] - $Data['orderData']['priceWithDispatchMethodNetto'];
			  $shopname = App::getContainer()->get('session')->getActiveShopName();
			  
			  $code .= "<script type=\"text/javascript\">
					      _gaq.push(['_addTrans',
					        '{$Data['orderId']}',
					        '{$shopname}',
					        '{$Data['orderData']['priceWithDispatchMethod']}',
					        '{$tax}',
					        '{$Data['orderData']['dispatchmethod']['dispatchmethodcost']}',
					        '{$Data['orderData']['clientaddress']['placename']}',
					        '',
					        ''
					      ]);
					  ";
			  foreach ($Data['orderData']['cart'] as $key => $item)
			  {
			  
			    if (isset($item['attributes']))
			    {
			      foreach ($item['attributes'] as $key => $prod)
			      {
			        $code .= "_gaq.push(['_addItem',
						          '{$Data['orderId']}',  
						          '{$prod['name']}', 
						          '{$prod['name']}',
						          '',
						          '{$prod['newprice']}',
						          '{$prod['qty']}'
						      ]);";
			      }
			    }
			    else
			    {
			      $code .= "_gaq.push(['_addItem',
						        '{$Data['orderId']}',  
						        '{$item['ean']}', 
						        '{$item['name']}',
						        '',
						        '{$item['newprice']}',
						        '{$item['qty']}'
						      ]);";
			    }
			  
			  }
			  $code .= "_gaq.push(['_trackTrans']);</script>";
			  
			}
			return $code;
		
		}
			 
	    private function getUniversalTransactionJs($Data, $settings) 
	    {
	    	
	    	$code = '';
	    	
			if ($this->checkIfAnalyticsEnabled($settings['gacode']) && $settings['gatransactions'] == 1)
			{
				$tax = $Data['orderData']['priceWithDispatchMethod'] - $Data['orderData']['priceWithDispatchMethodNetto'];
				$shopname = App::getContainer()->get('session')->getActiveShopName();
				
				$code .= "<script type=\"text/javascript\">
							ga('ecommerce:addTransaction', 
								{ 'id': '{$Data['orderId']}', 
								  'affiliation': '{$shopname}', 
								  'revenue': '{$Data['orderData']['priceWithDispatchMethod']}', 
								  'shipping': '{$Data['orderData']['dispatchmethod']['dispatchmethodcost']}', 
								  'tax': '{$tax}' 
								}
							);
						";
				
				foreach ($Data['orderData']['cart'] as $key => $item)
				{
					
					if (isset($item['attributes']))
					{
						foreach ($item['attributes'] as $key => $prod)
						{
							$code .= "ga('ecommerce:addItem', 
										{ 'id': '{$Data['orderId']}', 
										  'name': '{$prod['name']}', 
										  'sku': '{$prod['name']}', 
										  'category': '', 
										  'price': '{$prod['newprice']}', 
										  'quantity': '{$prod['qty']}' 
										 }
									  );";
						}
					}
					else
					{
						$code .= "ga('ecommerce:addItem', 
										{ 'id': '{$Data['orderId']}', 
										  'name': '{$item['name']}', 
										  'sku': '{$item['ean']}', 
										  'category': '', 
										  'price': '{$item['newprice']}', 
										  'quantity': '{$item['qty']}' 
										 }
									  );";
					}
				
				}
				
				$code .= "ga('ecommerce:send');</script>";
			}
			return $code;
		}
		
}
        