<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 13.4.17
 * Time: 11:25
 */

namespace ArticleModule\Command;


use App\Uuid\UuidFactory;
use LiteCQRS\Command;
use Ramsey\Uuid\UuidInterface;

class ModifyTagCommand implements Command
{

	private $id;
	private $tag;

	public function __construct(
		UuidInterface $uuid,
		string $tag
	)
	{
		$this->id = $uuid;
		$this->tag = $tag;
	}

	public function id() {
		return $this->id;
	}

	public function tag() {
		return $this->tag;
	}

}