<?php
/**
 * Created by PhpStorm.
 * Copyright (c) 2017 Jiri Jelinek <jelinekvb@gmail.com>
 * Created at: 22.08.17 16:43
 */

namespace ArticleModule\Command;


use LiteCQRS\Command;
use Ramsey\Uuid\UuidInterface;

class DeleteArticleCommand implements Command
{

	/**
	 * @var UuidInterface
	 */
	private $uuid;

	function __construct(UuidInterface $uuid)
	{
		$this->uuid = $uuid;
	}

	public function uuid() {
		return $this->uuid();
	}

}