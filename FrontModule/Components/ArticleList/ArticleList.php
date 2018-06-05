<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 26.4.17
 * Time: 21:13
 */

namespace ArticleModule\Component;


use ArticleModule\Query\ArticleQuery;
use Nette\Application\UI\Component;
use Nette\Application\UI\Control;

class ArticleList extends Control
{

	private $articleLimit;

	private $articleQuery;
    /**
     * @var int
     */
    private $offset;

    public function __construct($limit, $offset = 0,ArticleQuery $articleQuery)
	{
		$this->articleLimit = $limit;
		$this->articleQuery = $articleQuery;
        $this->offset = $offset;
    }

	public function render() {
		$template = $this->getTemplate();
		$template->setFile(__DIR__ . "/list.latte");

		$articles = $this->articleQuery->articleList($this->articleLimit);
		$template->topArticle = array_shift($articles);
		$template->articles = $articles;

		$template->render();
	}

	public function renderWidget() {
		$template = $this->getTemplate();
		$template->setFile(__DIR__ . "/widget.latte");

		$articles = $this->articleQuery->articleList($this->articleLimit);
		$template->articles = $articles;
		$template->render();
	}

	public function renderOther() {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/other.latte');

        $articles = $this->articleQuery->articleList($this->articleLimit, $this->offset);
        $template->articles = $articles;
        $template->render();
    }

}

interface IArticleListFactory {

	/** @return ArticleList */
	public function create($limit = 3, $offset = 0);

}