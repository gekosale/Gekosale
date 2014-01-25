<?php
namespace Gekosale\Component\Logout\Controller\Admin;
use Gekosale\Core\Component\Controller\Admin;

class Logout extends Admin
{

	public function index ()
	{
		$this->model->logout();
	}
}