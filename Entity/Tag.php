<?php
/**
 * Created by PhpStorm.
 * User: jirka
 * Date: 29.8.16
 * Time: 22:13
 */

namespace ArticleModule\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nette\Object;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Tag
 * @package App\Entity
 * @ORM\Entity()
 */
class Tag extends Object
{

	const CLUB_TAG = 'cdd0153f-a990-440a-8b80-24c015d307a8';

	/**
	 * @var UuidInterface
	 * @ORM\Id()
	 * @ORM\Column(type="uuid")
	 */
	private $id;

	/**
	 * @ORM\Column(type="text")
	 */
	private $tag;

	/**
	 * @ORM\ManyToMany(targetEntity="Article", mappedBy="tags")
	 */
	private $articles;

	public function __construct(
		UuidInterface $uuid,
		string $tag
	)
	{
		$this->id = $uuid;
		$this->tag = $tag;
	}

	/**
	 * @param mixed $tag
	 */
	public function changeTag($tag)
	{
		$this->tag = $tag;
	}

	/**
	 * @return mixed
	 */
	public function id()
	{
		return $this->id;
	}

	public function getId()
	{
		return $this->id->toString();
	}

	/**
	 * @return mixed
	 */
	public function tag()
	{
		return $this->tag;
	}

	public function articles()
	{
		return $this->articles;
	}

}