<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 13.4.17
 * Time: 10:48
 */

namespace ArticleModule\Service;


use ArticleModule\Command\CreateTagCommand;

interface TagService
{

	public function createTag(CreateTagCommand $command);
}