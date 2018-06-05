<?php
/**
 * Created by PhpStorm.
 * User: jirka
 * Date: 29.8.16
 * Time: 20:36
 */

namespace ArticleModule\Forms;


use App\Entity\Article;
use App\Entity\Image;
use App\Entity\Match;
use App\Entity\Season;
use App\Entity\Tag;
use App\Entity\User;
use App\FormFactory;
use ArticleModule\Query\TagQuery;
use Kdyby\Doctrine\EntityManager;
use MatchModule\Query\MatchQuery;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class ArticleFormFactory
{

	/** @var  FormFactory */
	private $formFactory;

	/** @var  TagQuery */
	private $tagQuery;
	/**
	 * @var MatchQuery
	 */
	private $matchQuery;

	public function __construct(FormFactory $formFactory, TagQuery $tagQuery, MatchQuery $matchQuery)
	{
		$this->formFactory = $formFactory;
		$this->tagQuery = $tagQuery;
		$this->matchQuery = $matchQuery;
	}

	public function createForm()
	{

		$form = $this->formFactory->create();
		$yesNo = array(
				1 => "Ano",
				0 => "Ne"
		);

		$form->addText('title', 'Nadpis')
				->setRequired('Přidejte nadpis článku')
				->setAttribute('class', 'input-xxlarge');


		$form->addText('subtitle', 'Podnadpis')
				->setAttribute('class', 'input-xxlarge');

		$form->addTextArea('perex', 'Úvod')
				->setRequired('Přidejte úvod článku')
				->setAttribute('class', 'input-xxlarge');

		$form->addTextArea('content', 'Obsah')
				->setRequired(FALSE)
				->setAttribute('class', 'editor');

		$form->addText('releaseDate', 'Datum vydání')
				->setDefaultValue(date('j.n.Y'));

		$form->addText('priority', 'Priorita článku')
				->setDefaultValue(1);

		$form->addRadioList('release', 'Vydat', [1 => 'Ano', 0 => 'Ne'])
				->setDefaultValue(1);

		$form->addCheckbox('showMain', 'Zobrazit na hlavní stránce')
				->setDefaultValue(true);

		$form->addText('author', 'Autor', []);

		$form->addHidden('image')
				->setAttribute('id', 'image-url');

		$form->addUpload('uploadImage', 'Nahrát nový obrázek')
				->setRequired(FALSE)
				->addRule(Form::IMAGE, 'Soubor musí být obrázek');

		$form->addSelect('match', 'Vyber zápas', $this->matchQuery->matchToSelectbox())
				->setPrompt("Možnost výběru zápasu")
				->setAttribute('class', 'select2');

		$form->addSelect('gallery', 'Vyber fotogalerii', [])
				->setPrompt('Možnost výběru fotogalerie');

		$form->addSubmit('save', 'Uložit článek')
				->setAttribute('class', 'btn-primary');

		$form->addSubmit('saveStay', 'Uložit článek a zůstat');

		$form->addMultiSelect('tags', 'Tagy', $this->tagQuery->arrayTag())
				->setAttribute('class', 'select2');

		return $form;
	}



}