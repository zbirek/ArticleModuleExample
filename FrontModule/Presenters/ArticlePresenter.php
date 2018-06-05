<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 26.4.17
 * Time: 22:29
 */

namespace FrontModule;


use App\Uuid\UuidFactory;
use ArticleModule\Component\IArticleListFactory;
use ArticleModule\Query\ArticleQuery;
use MatchModule\FrontModule\Components\MatchInfoControlFactory;

class ArticlePresenter extends BasePresenter
{

	/** @var  UuidFactory */
	private $uuidFactory;

	private $articleQuery;
	/**
	 * @var MatchInfoControlFactory
	 */
	private $matchInfoControlFactory;

	public $matchId;
    /**
     * @var IArticleListFactory
     */
    private $articleListFactory;

    public function __construct(
	    UuidFactory $uuidFactory,
        ArticleQuery $articleQuery,
        MatchInfoControlFactory $matchInfoControlFactory,
        IArticleListFactory $articleListFactory
    ){
		$this->uuidFactory = $uuidFactory;
		$this->articleQuery = $articleQuery;
		$this->matchInfoControlFactory = $matchInfoControlFactory;
        $this->articleListFactory = $articleListFactory;
    }

	public function actionDefault($slug) {
		$article = $this->articleQuery->articleFromUri($slug);
		$this->template->article = $article;

		$this->template->matchInfo = function($matchId) {
		    $this->matchId = $matchId;
			$this->createComponent('matchInfo');
		};
	}

	public function createComponentMatchInfo() {
		return $this->matchInfoControlFactory->create($this->matchId);
	}

	public function createComponentArticleList() {
        return $this->articleListFactory->create(5);
    }
}