<?php
/**
 * Created by PhpStorm.
 * Copyright (c) 2017 Jiri Jelinek <jelinekvb@gmail.com>
 * Created at: 22.08.17 15:49
 */

namespace ArticleModule\Entity;


use App\Utils\Invalidator;
use App\Utils\Tagger;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Kdyby\Events\LifeCycleEvent;
use Kdyby\Events\Subscriber;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;

class ArticleListener implements Subscriber
{

	/**
	 * @var Invalidator
	 */
	private $invalidator;

	public function __construct(
		Invalidator $invalidator
	)
	{
		$this->invalidator = $invalidator;
	}


	public function getSubscribedEvents()
	{
		return [
			'postPersist',
			'postUpdate'
		];
	}

	public function postPersist(LifecycleEventArgs $args)
	{
		if ($args->getEntity() instanceof Article) {
			$this->invalidator->article($args->getEntity()->id());
			$this->invalidator->articles();
		}
	}

	public function postUpdate(LifecycleEventArgs $args)
	{
		if ($args->getEntity() instanceof Article) {
			$this->invalidator->article($args->getEntity()->id());
			$this->invalidator->articles();
		}
	}

}