<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 11.4.17
 * Time: 16:31
 */

namespace ArticleModule\Service;


use ArticleModule\Command\CreateArticleCommand;
use ArticleModule\Command\DeleteArticleCommand;
use ArticleModule\Command\ModifyArticleCommand;

interface ArticleService
{
	public function createArticle(CreateArticleCommand $command): void;

	public function modifyArticle(ModifyArticleCommand $command): void;

	public function deleteArticle(DeleteArticleCommand $command): void;

}