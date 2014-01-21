<?php
namespace Gekosale;

class LogoutController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->model->logout();
	}
}