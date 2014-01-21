<?php

namespace Gekosale;

class LogoutModel extends Component\Model
{

	public function logout ()
	{
		if ($this->registry->router->getAdministrativeMode() == 1){
			App::getModel('Frontend/login/login')->destroyAdminAutologinKey();
			App::getContainer()->get('session')->flush();
			App::redirect('login');
		}
		else{
			App::getContainer()->get('session')->flush();
			App::redirect('');
		}
	}
}