<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 13.4.17
 * Time: 9:50
 */

namespace ArticleModule\Query;


interface TagQuery
{
	public function tagDataSource();
	public function arrayTag();
}