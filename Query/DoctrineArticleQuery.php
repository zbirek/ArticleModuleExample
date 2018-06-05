<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 11.4.17
 * Time: 15:30
 */

namespace ArticleModule\Query;


use ArticleModule\DTO\ArticleDTO;
use ArticleModule\Entity\Article;
use ArticleModule\Entity\Tag;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Query\Expr;
use Kdyby\Doctrine\Dql\Join;
use MatchModule\Entity\Match;
use Nette\Application\BadRequestException;
use Ramsey\Uuid\UuidInterface;

class DoctrineArticleQuery implements ArticleQuery
{

	/** @var  EntityManager */
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}


    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
	public function articleDataSource()
	{
		return $this->em->getRepository(Article::class)
				->createQueryBuilder('a');
	}


	/**
	 * @param UuidInterface $uuid
	 * @return ArticleDTO
	 * @throws BadRequestException
	 */
	public function articleFromId(UuidInterface $uuid) : ArticleDTO
	{
		$qb = $this->em->createQueryBuilder()
				->select('a')
				->from(Article::class, 'a')
				->where('a.id = :uuid');

		$qb->setParameter('uuid', $uuid);

		/** @var Article $result */
		$result = $qb->getQuery()->getOneOrNullResult();

		if (!$result) {
			throw new BadRequestException("Article not found");
		}

		return $this->convertToDto($result);
	}

    /**
     * @param UuidInterface $uuid
     * @return ArticleDTO
     * @throws BadRequestException
     */
    public function articleFromUri(string $uri) : ArticleDTO
    {
        $qb = $this->em->createQueryBuilder()
            ->select('a')
            ->from(Article::class, 'a')
            ->where('a.uri = :uri');

        $qb->setParameter('uri', $uri);

        /** @var Article $result */
        $result = $qb->getQuery()->getOneOrNullResult();

        if (!$result) {
            throw new BadRequestException("Article not found");
        }

        return $this->convertToDto($result);
    }

    /**
     * @param PersistentCollection $collection
     * @return array
     */
	private function getTagsId(PersistentCollection $collection)
	{
		return array_map(function ($item) {
			return $item->id()->toString();
		}, $collection->toArray());
	}

    /**
     * @param null $limit
     * @param null $offset
     * @return array
     */
	public function articleList($limit = NULL, $offset = NULL) : array
	{
		$qb = $this->em->createQueryBuilder()
				->select('a')
				->from(Article::class, 'a')
                ->where('a.showMain = 1')
                ->orderBy('a.priority', 'DESC')
				->addOrderBy('a.releaseDate','DESC');

		if ($limit) {
			$qb->setMaxResults($limit);
		}

		if ($offset) {
		    $qb->setFirstResult($offset);
        }

		/** @var Article $result */
		$results = $qb->getQuery()->getResult();
		$list = [];
		foreach ($results as $result) {
			$list[] = new ArticleDTO(
					$result->id(),
					$result->title(),
					$result->subtitle(),
					$result->perex(),
					$result->content(),
					$result->releaseDate(),
					$result->release(),
					$result->author(),
					$result->image(),
					$result->match(),
					//$result->gallery(),
					$this->getTagsId($result->tags()),
                    $result->uri());
		}

		return $list;
	}

    /**
     * @param $tag
     * @param int $limit
     * @param int $offset
     * @return array
     */
	public function articlesFromTag($tag, $limit = 0, $offset = 0) {
		$qb = 	$this->em->getRepository(Article::class)
				->createQueryBuilder('a')
				->select('a')
				->where(':tag MEMBER OF a.tags');

		$qb->setParameter('tag', $tag);

		if($limit) $qb->setMaxResults($limit);
		if($offset) $qb->setFirstResult($offset);

		$qb->orderBy('a.releaseDate', 'DESC');
		$result = $qb->getQuery()->getResult();

		$articles = array_map(function(Article $article){
		    return new ArticleDTO(
				$article->id(),
				$article->title(),
				$article->subtitle(),
				$article->perex(),
				$article->content(),
				$article->releaseDate(),
				$article->release(),
				$article->author(),
				$article->image(),
				$article->match(),
				[],
                $article->uri()
			);
		}, $result);

		return $articles;
	}

    /**
     * @param $tag
     * @return mixed
     */
	public function articlesCountFromTag($tag): int {
		$qb = 	$this->em->getRepository(Article::class)
			->createQueryBuilder('a')
			->select('COUNT(a)')
			->where(':tag MEMBER OF a.tags');

		$qb->setParameter('tag', $tag);

		return $qb->getQuery()->getSingleScalarResult();
	}

    /**
     * @return array
     */
	public function articlesUriTable()
    {
        $articles = $this->articleDataSource()->getQuery()->getResult();
        $uri = [];
        /** @var Article $article */
        foreach($articles as $article) {
            $uri[$article->id()->toString()] = $article->uri();
        }

        return $uri;
    }

    /**
     * @param Article $article
     * @return ArticleDTO
     */
    protected function convertToDto(Article $article){
        return new ArticleDTO(
            $article->id(),
            $article->title(),
            $article->subtitle(),
            $article->perex(),
            $article->content(),
            $article->releaseDate(),
            $article->release(),
            $article->author(),
            $article->image(),
            $article->match(),
            array_map(function(Tag $tag){return $tag->id();},$article->tags()->toArray()),
            $article->uri(),
            $article->priority(),
            $article->showMain());
    }

}