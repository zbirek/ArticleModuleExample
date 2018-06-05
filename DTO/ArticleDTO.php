<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 11.4.17
 * Time: 23:23
 */

namespace ArticleModule\DTO;


use MatchModule\Entity\Match;
use Ramsey\Uuid\UuidInterface;

class ArticleDTO
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
	private $tags;
	private $gallery;
	private $uri;
    /**
     * @var bool
     */
    private $priority;
    /**
     * @var int
     */
    private $showMain;

    public function __construct(
			UuidInterface $uuid,
			string $title,
			string $subtitle,
			string $perex,
			string $content,
			\DateTime $releaseDate,
			string $release,
			string $author,
			string $image,
			Match $match = null,
			$tags = [],
            string $uri = null,
            bool $priority = true,
            int $showMain = 1
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
		$this->tags = $tags;
		$this->uri = $uri;
        $this->priority = $priority;
        $this->showMain = $showMain;
    }

	public function id() {
		return $this->id;
	}

	public function title() {
		return $this->title;
	}

	public function subtitle() {
		return $this->subtitle;
	}

	public function perex() {
		return $this->perex;
	}

	public function content() {
		return $this->content;
	}

	public function releaseDate() {
		return $this->releaseDate;
	}

	public function release() {
		return $this->release;
	}

	public function author() {
		return $this->author;
	}

	public function image() {
		return $this->image;
	}

	public function match() {
		return $this->match;
	}

	public function gallery() {
		return $this->gallery;
	}

	public function tags() {
		return $this->tags;
	}

	public function uri() {
	    return $this->uri;
    }

    public function priority() {
        return $this->priority;
    }

    public function showMain() {
        return $this->showMain;
    }

}