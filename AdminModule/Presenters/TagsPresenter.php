<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AdminModule;

use App\Entity\Tag;
use ArticleModule\Grids\TagGridFactory;
use Kdyby\Doctrine\EntityManager;

/**
 * Description of TagsPresenter
 *
 * @author jirka
 */
class TagsPresenter extends BasePresenter
{


	/** @var TagGridFactory */
	private $tagGridFactory;

	public function __construct(
		TagGridFactory $tagGridFactory
	)
	{
		$this->tagGridFactory = $tagGridFactory;
	}

	/**
	 * @return \Ublaboo\DataGrid\DataGrid
	 */
	public function createComponentTagGrid()
	{
		return $this->tagGridFactory->createGrid(
			function () {
				$this->flashMessage('Tag byl přidán', 'success');
				$this->redrawControl('grid');
				$this->redrawControl('flashMessages');
			},
			function () {
				$this->flashMessage('Tag byl upraven', 'success');
				$this->redrawControl('grid');
				$this->redrawControl('flashMessages');
			});
	}


}
