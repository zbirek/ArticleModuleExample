<?php

namespace ArticleModule\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use MatchModule\Entity\Match;
use UserModule\Entity\User;
use Nette\Object;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Article
 * @package App\Entity
 * @ORM\Entity()
 */
class Article
{
	/**
	 * @ORM\Id()
	 * @ORM\Column(type="uuid")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string")
	 */
	private $title;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $subtitle;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $perex;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $content;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $releaseDate;

	/**
	 * @ORM\Column(name="`release`", type="boolean", options={"default":1})
	 */
	private $release = 1;

	/**
	 * @ORM\Column(type="boolean", options={"default":1})
	 */
	private $showSlide = 1;

	/**
	 * @ORM\Column(type="boolean", options={"default":1})
	 */
	private $showMain = 1;

	/**
	 * @ORM\Column(type="smallint", options={"default":0})
	 */
	private $priority = 0;

	/**
	 * @ORM\Column(type="string", unique=true, nullable=true)
	 */
	private $uri;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $author;

	/**
	 * @ORM\ManyToOne(targetEntity="\UserModule\Entity\User")
	 * @ORM\JoinColumn(name="created_user_id", referencedColumnName="id")
	 */
	private $createdUser;

	/**
	 * @var
	 * @ORM\Column(type="datetime")
	 */
	private $createdAt;

	/**
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $image;

	/**
	 * @ORM\ManyToOne(targetEntity="MatchModule\Entity\Match")
	 */
	private $match;

	/**
	 * @var ArrayCollection
	 * @ORM\ManyToMany(targetEntity="ArticleModule\Entity\Tag", inversedBy="articles")
	 */
	private $tags;

	public function __construct(
		UuidInterface $uuid,
		string $title,
		string $subtitle,
		string $perex,
		string $content,
		\DateTime $releaseDate,
		int $release,
		string $author,
		string $image,
		User $createdUser,
		Match $match = NULL,
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
		$this->createdUser = $createdUser;
		$this->match = $match;

		$this->tags = new ArrayCollection();
		$this->createdAt = new DateTime();
		$this->uri = $this->createUri($uuid, $title);
		$this->priority = $priority;
		$this->showMain = $showMain;
	}

	private function createUri(UuidInterface $uuid, $title)
	{
		return Strings::webalize("{$uuid->getNode()}-{$title}");
	}

	//private $gallery;

	/**
	 * @param mixed $title
	 */
	public function changeTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @param mixed $subtitle
	 */
	public function changeSubtitle($subtitle)
	{
		$this->subtitle = $subtitle;
	}

	/**
	 * @param mixed $perex
	 */
	public function changePerex($perex)
	{
		$this->perex = $perex;
	}

	/**
	 * @param mixed $content
	 */
	public function changeContent($content)
	{
		$this->content = $content;
	}

	/**
	 * @param mixed $releaseDate
	 */
	public function changeReleaseDate(\DateTime $releaseDate)
	{
		$this->releaseDate = $releaseDate;
	}

	/**
	 * @param mixed $priority
	 */
	public function changePriority($priority)
	{
		$this->priority = $priority;
	}


	/**
	 * @param mixed $release
	 */
	public function changeRelease($release)
	{
		$this->release = $release;
	}

	/**
	 * @param mixed $showSlide
	 */
	public function setShowSlide($showSlide)
	{
		$this->showSlide = $showSlide;
	}

	/**
	 * @param mixed $showMain
	 */
	public function setShowMain($showMain)
	{
		$this->showMain = $showMain;
	}

	/**
	 * @param mixed $author
	 */
	public function changeAuthor($author)
	{
		$this->author = $author;
	}

	/**
	 * @param mixed $image
	 */
	public function changeImage($image)
	{
		$this->image = $image;
	}

	/**
	 * @param mixed $match
	 */
	public function changeMatch(Match $match)
	{
		$this->match = $match;
	}

	public function clearMatch()
	{
		$this->match = NULL;
	}

	public function setTags(array $tags)
	{
		$this->tags->clear();

		foreach ($tags as $tag) {
			$this->tags[] = $tag;
		}


	}


	/**
	 * @return mixed
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function title()
	{
		return $this->title;
	}

	/**
	 * @return mixed
	 */
	public function subtitle()
	{
		return $this->subtitle;
	}

	/**
	 * @return mixed
	 */
	public function perex()
	{
		return $this->perex;
	}

	/**
	 * @return mixed
	 */
	public function content()
	{
		return $this->content;
	}

	/**
	 * @return mixed
	 */
	public function image()
	{
		return $this->image;
	}

	/**
	 * @return Match
	 */
	public function match()
	{
		return $this->match;
	}

	/**
	 * @return mixed
	 */
	public function author()
	{
		return $this->author;
	}


	/**
	 * @return mixed
	 */
	public function priority()
	{
		return $this->priority;
	}

	/**
	 * @return mixed
	 */
	public function release()
	{
		return $this->release;
	}

	/**
	 * @return mixed
	 */
	public function releaseDate()
	{
		return $this->releaseDate;
	}

	/**
	 * @return mixed
	 */
	public function showMain()
	{
		return $this->showMain;
	}

	/**
	 * @return mixed
	 */
	public function showSlide()
	{
		return $this->showSlide;
	}

	/**
	 * @return mixed
	 */
	public function tags()
	{
		return $this->tags;
	}

	/**
	 * @return mixed
	 */
	public function uri()
	{
		return $this->uri;
	}

	public function gallery()
	{
		return NULL;
	}

	public function changeShowMain($showMain)
	{
		$this->showMain = $showMain;
	}


}