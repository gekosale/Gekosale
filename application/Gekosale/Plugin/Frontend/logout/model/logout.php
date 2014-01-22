<?php

namespace Gekosale\Plugin;

class LogoutModel extends Component\Model
{

	public function logout ()
	{
		if ($this->registry->router->getAdministrativeMode() == 1){
			App::getContainer()->get('session')->flush();
			App::redirectUrl($this->registry->router->generate('frontend.login', true));
		}
		else{
			App::getModel('clientlogin')->destroyAutologinKey();
			App::getContainer()->get('session')->flush();
			App::redirectUrl($this->registry->router->generate('frontend.home', true));
		}
	}
}