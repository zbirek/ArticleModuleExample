<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 11.4.17
 * Time: 15:29
 */

namespace ArticleModule\Query;


use ArticleModule\DTO\ArticleDTO;
use Ramsey\Uuid\UuidInterface;

interface ArticleQuery
{

	public function articleDataSource();

	/**
	 * @param UuidInterface $uuid
	 * @return ArticleDTO
	 */
	public function articleFromId(UuidInterface $uuid) : ArticleDTO;
	public function articleList($limit = NULL, $offet = NULL) : array;
    public function articlesUriTable();
    public function articleFromUri(string $uri) : ArticleDTO;

}