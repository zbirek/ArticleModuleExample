<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 11.4.17
 * Time: 16:32
 */

namespace ArticleModule\Service;


use App\Entity\Image;
use App\Service\FileStorage;
use App\Service\ImageStoreService;
use ArticleModule\Command\CreateArticleCommand;
use ArticleModule\Command\DeleteArticleCommand;
use ArticleModule\Command\ModifyArticleCommand;
use ArticleModule\Entity\Article;
use ArticleModule\Entity\Tag;
use Doctrine\ORM\EntityManager;
use MatchModule\Entity\Match;
use Nette\Http\FileUpload;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Debug\Tests\Fixtures\PSR4CaseMismatch;
use UserModule\Entity\User;

class DoctrineArticleService implements ArticleService
{

	/** @var  EntityManager */
	private $em;

	/** @var  ImageStoreService */
	private $imageStoreService;

	public function __construct(
		EntityManager $em,
		ImageStoreService $imageStoreService
	)
	{
		$this->em = $em;
		$this->imageStoreService = $imageStoreService;
	}

    /**
     * @param CreateArticleCommand $command
     */
	public function createArticle(CreateArticleCommand $command): void
	{
		$image = $command->image();
		if($image instanceof FileUpload) {
			$image = $this->imageStoreService->storeImage($image, 'article');
		}

		$user = $this->em->getRepository(User::class)->find($command->createdUser());

			$match = NULL;
			if($command->match()) {
				$match = $this->em->getRepository(Match::class)->find($command->match());
			}

			$article = new Article(
			$command->id(),
			$command->title(),
			$command->subtitle(),
			$command->perex(),
			$command->content(),
			new \DateTime($command->releaseDate()),
			$command->release(),
			$command->author(),
			$image,
			$user,
			$match,
            $command->priority(),
            $command->showMain()
		);

		if($command->tags()) {
			$article->setTags($this->getTags($command->tags()));
		}

		$this->em->persist($article);
		$this->em->flush();
	}

    /**
     * @param ModifyArticleCommand $command
     */
	public function modifyArticle(ModifyArticleCommand $command) : void
	{
		/** @var Article $article */
		$article = $this->em->getRepository(Article::class)->find($command->id());

		$article->changeTitle($command->title());
		$article->changeSubtitle($command->subtitle());
		$article->changePerex($command->perex());
		$article->changeContent($command->content());

		$article->changeReleaseDate(new \DateTime($command->releaseDate()));
		$article->changeRelease($command->release());
		$article->changeAuthor($command->author());

		$image = $command->image();
		if($image instanceof FileUpload) {
			$image = $this->imageStoreService->storeImage($image, 'article');
		}
		$article->changeImage($image);

		if($command->match()) {
			$match = $this->em->getRepository(Match::class)->find($command->match());
			$article->changeMatch($match);
		}else{
			$article->clearMatch();
		}

		if($command->tags()) {
			$article->setTags($this->getTags($command->tags()));
		}else{
		    $article->setTags([]);
        }

		/** @TODO GALLERY TAGS  */
		$article->changePriority($command->priority());
		$article->changeShowMain($command->showMain());

		$this->em->persist($article);
		$this->em->flush();
	}


    /**
     * @param array $tags
     * @return array
     */
	private function getTags(array $tags) {
		return $this->em->getRepository(Tag::class)->findBy(['id'=>$tags]);
	}

    /**
     * @param DeleteArticleCommand $command
     */
	public function deleteArticle(DeleteArticleCommand $command): void {
		$article = $this->em->getRepository(Article::class)->find($command->uuid());
		$this->em->remove($article);

		$this->em->flush();
	}

}