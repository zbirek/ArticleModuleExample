<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 13.4.17
 * Time: 9:51
 */

namespace ArticleModule\Query;


use ArticleModule\Entity\Tag;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

class DoctrineTagQuery implements TagQuery
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
	public function tagDataSource() : QueryBuilder
	{
		return $this->em->getRepository(Tag::class)->createQueryBuilder('t');
	}

    /**
     * @return array
     */
	public function arrayTag()
	{
		$result = $this->em->getRepository(Tag::class)->findAll();

		$tags = [];
		/** @var Tag $tag */
		foreach($result as $tag) {
			$tags[$tag->id()->toString()] = $tag->tag();
		}

		return $tags;
	}

}