<?php

namespace ArticleModule\Grids;

use App\Entity\Article;
use App\GridFactory;
use ArticleModule\Query\ArticleQuery;
use Kdyby\Doctrine\EntityManager;
use Ublaboo\DataGrid\DataGrid;

/**
 * Created by PhpStorm.
 * User: jirka
 * Date: 31.8.16
 * Time: 9:21
 */
class ArticleGridFactory extends DataGrid
{

	/** @var  GridFactory */
	private $gridFactory;

	/** @var  ArticleQuery */
	private $articleQuery;

	public function __construct(
		GridFactory $gridFactory,
		ArticleQuery $articleQuery
	){
		$this->gridFactory = $gridFactory;
		$this->articleQuery = $articleQuery;
	}


	public function createGrid() {
		$grid = $this->gridFactory->create();


		$grid->setDataSource($this->articleQuery->articleDataSource());

		$grid->setDefaultPerPage(30);
		$grid->setDefaultSort(['releaseDate'=>'DESC']);

		$grid->addColumnText('title', 'Titulek')
			->setSortable()
			->setFilterText();

		$grid->addColumnDateTime('releaseDate', 'Datum vydání')
			->setSortable();


		$grid->addColumnText('author', 'Autor')
			->setFilterText();

		$grid->addColumnStatus('release', 'Vydáno')
				->addOption(1, 'Vydáno')
					->setClass('btn-primary')
					->endOption()
				->addOption(0, 'Nevydáno')
					->setClass('btn-danger')
					->endOption()
				->onChange[] = function($id, $status) {
						$this->changeStatus($id, $status);
				};


		$grid->addAction('editArticle', '')
				->setTitle('Upravit článek')
				->setIcon('edit')
				->setTitle('upravit článek')
				->setClass('btn btn-success');

		$grid->addAction('deleteArticle', '')
				->setTitle('Smazat článek')
				->setIcon('trash')
				->setTitle('Smazat článek')
				->setClass('btn btn-danger ajax')
				->setConfirm('Opravdu chcete smazat článek %s?', 'title');

		$grid->addToolbarButton('Tags:default', 'Tagy')
			->setIcon('tag')
			->setClass('btn btn-primary');

		$grid->addToolbarButton('addArticle', 'Přidat článek')
			->setIcon('plus')
			->setClass('btn btn-success');

		return $grid;
	}

	public function changeStatus($id, $status)
	{
		//$this->onChangeStatus($id, $status);
		$this->redrawItem($id);
	}


}