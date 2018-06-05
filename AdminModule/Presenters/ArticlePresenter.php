<?php

namespace AdminModule;

use AdminModule\Components\ArticleForm;
use AdminModule\Components\ArticleListGrid;
use AdminModule\Components\IArticleImageControlFactory;
use ArticleModule\Command\DeleteArticleCommand;
use ArticleModule\Entity\Article;
use App\Entity\Image;
use App\Service\ArticleService;
use App\Uuid\UuidFactory;
use ArticleModule\Command\CreateArticleCommand;
use ArticleModule\Command\ModifyArticleCommand;
use ArticleModule\Forms\ArticleFormFactory;
use ArticleModule\Grids\ArticleGrid;
use ArticleModule\Grids\ActualityGridFactory;
use ArticleModule\Grids\ArticleGridFactory;
use ArticleModule\Query\ArticleQuery;
use LiteCQRS\Commanding\CommandBus;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Form,
	Nette\Utils\Strings;
use Nette\Utils\Paginator;
use ondrs\UploadManager\Upload;
use Ramsey\Uuid\Uuid;

/**
 * Description of ArticlePresenter
 *
 * @author Jiri Jelinek <jelinek7@seznam.cz>
 */
class ArticlePresenter extends DefaultPresenter
{

	/** @var CommandBus */
	private $commandBus;

	/** @var UuidFactory */
	private $uuidFactory;

	/** @var  ArticleFormFactory */
	private $articleFormFactory;

	/** @var ActualityGridFactory */
	private $articleGridFactory;

	/** @var  ArticleQuery */
	private $articleQuery;

	public function __construct(
		CommandBus $commandBus,
		UuidFactory $uuidFactory,
		ArticleFormFactory $articleFormFactory,
		ArticleGridFactory $articleGridFactory,
		ArticleQuery $articleQuery
	)
	{
		$this->commandBus = $commandBus;
		$this->uuidFactory = $uuidFactory;
		$this->articleFormFactory = $articleFormFactory;
		$this->articleGridFactory = $articleGridFactory;
		$this->articleQuery = $articleQuery;
	}

	/**
	 *
	 */
	public function actionAddArticle()
	{
		$this['addArticleForm']->setDefaults(
			['author' => "{$this->user->identity->firstName} {$this->user->identity->lastName}"]
		);
	}


	public function actionEditArticle($id)
	{
		$uuid = $this->uuidFactory->uuidFromString($id);
		$article = $this->articleQuery->articleFromId($uuid);

		$this['editArticleForm']->setDefaults([
			'title' => $article->title(),
			'subtitle' => $article->subtitle(),
			'perex' => $article->perex(),
			'content' => $article->content(),
			'releaseDate' => $article->releaseDate()->format('j.n.Y'),
			'release' => $article->release(),
			'image' => $article->image(),
			'match' => $article->match() ? $article->match()->id()->toString() : NULL,
			'gallery' => $article->gallery(),
			'author' => $article->author(),
			'tags' => $article->tags(),
			'showMain' => $article->showMain(),
			'priority' => $article->priority()
		]);
		$this->template->article = $article;
	}


	/**
	 * @return \Ublaboo\DataGrid\DataGrid
	 */
	public function createComponentArticleGrid()
	{
		return $this->articleGridFactory->createGrid();
	}

	/**
	 * Tovarnicka pro vytvoreni Formulare na pridani clanku
	 * @return \ArticleForm
	 *
	 */
	public function createComponentAddArticleForm()
	{
		$form = $this->articleFormFactory->createForm();
		$form->onSuccess[] = function (Form $form, $values) {
			$uuid = $this->uuidFactory->uuid();
			$image = $values->uploadImage->isOk() ? $values->uploadImage : $values->image;

			$command = new CreateArticleCommand(
				$uuid,
				$values->title,
				$values->subtitle,
				$values->perex,
				$values->content,
				$values->releaseDate,
				$values->release,
				$values->author,
				$this->user->getId(),
				$image,
				$values->match,
				$values->gallery,
				$values->tags,
				$values->priority,
				$values->showMain
			);

			$this->commandBus->handle($command);
			$this->flashMessage('Článek byl přidán', 'success');

			if ($form->isSubmitted()->name == 'saveStay') {
				$this->redirect('editArticle', $uuid);
			} else {
				$this->redirect('default');
			}
		};


		return $form;
	}

	/**
	 * @return Form
	 */
	public function createComponentEditArticleForm()
	{
		$form = $this->articleFormFactory->createForm();
		$form->onSuccess[] = function (Form $form, $values) {
			$uuid = $this->uuidFactory->uuidFromString($this->getParameter('id'));
			$image = $values->uploadImage->isOk() ? $values->uploadImage : $values->image;
			$tags = $this->uuidFactory->uuidArray($values->tags);

			$command = new ModifyArticleCommand(
				$uuid,
				$values->title,
				$values->subtitle,
				$values->perex,
				$values->content,
				$values->releaseDate,
				$values->release,
				$values->author,
				$image,
				$values->match,
				$values->gallery,
				$tags,
				$values->priority,
				$values->showMain
			);

			$this->commandBus->handle($command);
			$this->flashMessage('Článek byl upraven', 'success');

			if ($form->isSubmitted()->name == 'saveStay') {
				$this->redirect('editArticle', $uuid);
			} else {
				$this->redirect('default');
			}
		};


		return $form;
	}


	public function createComponentArticleList()
	{
		$grid = new ArticleListGrid($this->em);

		$grid->onChangeStatus = function ($id, $status) {
			$this->articleService->setRelease($id, $status);
		};

		return $grid;
	}

	/**
	 * Signal pro smazani clanku
	 * @param int $id id clanku
	 */
	public function actionDeleteArticle($id)
	{
		$uuid = $this->uuidFactory->uuidFromString($id);
		$command = new DeleteArticleCommand($uuid);

		$this->commandBus->handle($command);
		$this->flashMessage('Článek byl smazán', 'success');
		$this->redirect('default');
	}


	public function createComponentArticleImage()
	{
		$component = $this->articleImageControlFactory->create();

		$component->onChoose = function ($image) {
			$this->chooseImage($image);
		};

		return $component;
	}

	public function handleUploadImage()
	{
		$uploader = new \UploadHandler();
		$uploader->allowedExtensions = array("jpeg", "jpg", "png", "gif");

		$image = new Image();
		$image->setDescription($uploader->getName());
		$image->setSource($uploader->getName());
		$this->em->persist($image);
		$this->em->flush();

		$this->chooseImage($image);

		$result = $uploader->handleUpload(WWW_DIR . '/images/article');
		$this->sendResponse(new JsonResponse($result));
	}


}
