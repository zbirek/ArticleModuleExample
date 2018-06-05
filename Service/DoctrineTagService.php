<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 13.4.17
 * Time: 10:49
 */

namespace ArticleModule\Service;


use ArticleModule\Command\CreateTagCommand;
use ArticleModule\Command\ModifyTagCommand;
use ArticleModule\Entity\Tag;
use Doctrine\ORM\EntityManager;

class DoctrineTagService implements TagService
{

	/** @var  EntityManager $em */
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}


	public function createTag(CreateTagCommand $command)
	{
		$tag = new Tag($command->id(), $command->tag());

		$this->em->persist($tag);
		$this->em->flush();
	}

	public function modifyTag(ModifyTagCommand $command) {
		$tag = $this->em->getRepository(Tag::class)->find($command->id());

		$tag->changeTag($command->tag());
		$this->em->persist($tag);
		$this->em->flush();
	}
}