<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 11.4.17
 * Time: 16:14
 */

namespace ArticleModule\Command;


use LiteCQRS\Command;
use Ramsey\Uuid\UuidInterface;

class ModifyArticleCommand implements Command
{

	private $id;
	private $title;
	private $subtitle;
	private $perex;
	private $content;
	private $releaseDate;
	private $release;
	private $author;
	private $image;
	private $match;
	private $gallery;
	private $tags;
	/**
	 * @var int
	 */
	private $priority;
	/**
	 * @var bool
	 */
	private $showMain;

	public function __construct(
		UuidInterface $uuid,
		string $title,
		string $subtitle,
		string $perex,
		string $content,
		string $releaseDate,
		int $release,
		string $author,
		string $image,
		string $match = null,
		string $gallery = null,
		$tags = [],
		int $priority = 1,
		bool $showMain = true
	)
	{
		$this->id = $uuid;
		$this->title = $title;
		$this->subtitle = $subtitle;
		$this->perex = $perex;
		$this->content = $content;
		$this->releaseDate = $releaseDate;
		$this->release = $release;
		$this->author = $author;
		$this->image = $image;
		$this->match = $match;
		$this->gallery = $gallery;
		$this->tags = $tags;
		$this->priority = $priority;
		$this->showMain = $showMain;
	}

	public function id()
	{
		return $this->id;
	}

	public function title()
	{
		return $this->title;
	}

	public function subtitle()
	{
		return $this->subtitle;
	}

	public function perex()
	{
		return $this->perex;
	}

	public function content()
	{
		return $this->content;
	}

	public function releaseDate()
	{
		return $this->releaseDate;
	}

	public function release()
	{
		return $this->release;
	}

	public function author()
	{
		return $this->author;
	}

	public function image()
	{
		return $this->image;
	}

	public function match()
	{
		return $this->match;
	}

	public function gallery()
	{
		return $this->gallery;
	}

	public function tags()
	{
		return $this->tags;
	}

	public function showMain()
	{
		return $this->showMain;
	}

	public function priority()
	{
		return $this->priority;
	}

}