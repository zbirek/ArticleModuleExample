<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 13.4.17
 * Time: 9:50
 */

namespace ArticleModule\Grids;


use App\GridFactory;
use App\Uuid\UuidFactory;
use ArticleModule\Command\CreateTagCommand;
use ArticleModule\Command\ModifyTagCommand;
use ArticleModule\Query\TagQuery;
use LiteCQRS\Commanding\CommandBus;

class TagGridFactory
{

	/** @var GridFactory */
	private $gridFactory;

	/** @var TagQuery */
	private $tagQuery;

	/** @var  CommandBus */
	private $commandBus;

	/** @var  UuidFactory */
	private $uuidFactory;

	public function __construct(
		GridFactory $gridFactory,
		TagQuery $tagQuery,
		CommandBus $commandBus,
		UuidFactory $uuidFactory
	)
	{
		$this->gridFactory = $gridFactory;
		$this->tagQuery = $tagQuery;
		$this->commandBus = $commandBus;
		$this->uuidFactory = $uuidFactory;
	}

	public function createGrid(callable $onSuccessAdd, callable $onSuccessEdit)
	{
		$grid = $this->gridFactory->create();

		$grid->setDataSource($this->tagQuery->tagDataSource());
		$grid->per_page = 30;

		//$grid->addColumnText('id', 'Id');

		$grid->addColumnText('tag', 'Tag')
			->setSortable();

		$grid->addInlineAdd()
			->setText('Přidat tag')
			->setClass('btn btn-success')
			->setPositionTop()
			->onControlAdd[] = function ($container) {
			$container->addText('tag');
		};

		$grid->addInlineEdit()
			->setClass('btn btn-success ajax')
			->setTitle('Upravit tag')
			->onControlAdd[] = function ($container) {
			$container->addText('tag', '');
		};

		$grid->getInlineAdd()->onSubmit[] = function ($values) use ($onSuccessAdd) {
			$uuid = $this->uuidFactory->uuid();
			$command = new CreateTagCommand($uuid, $values->tag);

			$this->commandBus->handle($command);
			$onSuccessAdd();
		};

		$grid->getInlineEdit()
			->onSetDefaults[] = function ($container, $item) {
			$container->setDefaults([
				'tag' => $item->tag()
			]);
		};

		$grid->getInlineEdit()
			->onSubmit[] = function ($id, $values) use ($onSuccessEdit) {
			$uuid = $this->uuidFactory->uuidFromString($id);
			$command = new ModifyTagCommand($uuid, $values->tag);

			$this->commandBus->handle($command);
			$onSuccessEdit();
		};


		return $grid;
	}

}