<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();
		$router[] = new Route('/cron-work', 'Front:Cron:work');
		$router[] = new Route('[<locale=cs cs|en|ru|vi>/]<presenter>/<action>[/<id>]', 'Front:Homepage:default');		
		return $router;
	}

}
