<?php

namespace ArticleModule\DI;
use ArticleModule\Command\CreateArticleCommand;
use ArticleModule\Command\CreateTagCommand;
use ArticleModule\Command\DeleteArticleCommand;
use ArticleModule\Command\ModifyArticleCommand;
use ArticleModule\Command\ModifyTagCommand;
use CqrsExtension\CommandMapper;
use Kdyby\Doctrine\DI\IEntityProvider;
use RosterModule\Command\RemovePlayerPhoto;

/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 7.4.17
 * Time: 15:17
 */
class ArticleExtension extends \Nette\DI\CompilerExtension implements CommandMapper, IEntityProvider
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$data = $this->loadFromFile(__DIR__ . "/services.neon");
		$this->compiler->parseServices($builder, $data, $this->prefix('services'));
	}

	public function getCommandMapping()
	{
		return [
			CreateArticleCommand::class => $this->prefix('@services.articleService'),
			ModifyArticleCommand::class => $this->prefix('@services.articleService'),
			DeleteArticleCommand::class => $this->prefix('@services.articleService'),
			CreateTagCommand::class => $this->prefix('@services.tagService'),
			ModifyTagCommand::class => $this->prefix('@services.tagService'),
		];
	}

	public function getEntityMappings()
	{
		return [
			'Article' => __DIR__ . "/../Entity"
		];
	}
}