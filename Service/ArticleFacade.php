<?php
/**
 * Created by PhpStorm.
 * Copyright (c) 2018 Jiri Jelinek <jelinekvb@gmail.com>
 * Created at: 18.01.18 10:05
 */

namespace ArticleModule\Service;


use ArticleModule\Query\ArticleQuery;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;

class ArticleFacade
{

    /**
     * @var ArticleQuery
     */
    private $articleQuery;
    /**
     * @var IStorage
     */
    private $storage;

    public function __construct(
        ArticleQuery $articleQuery,
        IStorage $storage
    ) {

        $this->articleQuery = $articleQuery;
        $this->storage = $storage;
    }

    /**
     * @return array
     */
    public function getArticleUriTable() : array {
        if(!$table = $this->storage->read('uri_table')) {
            $table = $this->articleQuery->articlesUriTable();
            $this->storage->write('uri_table', $table, [
                Cache::TAGS => 'uri_table'
            ]);
        }

        return $table;
    }

}