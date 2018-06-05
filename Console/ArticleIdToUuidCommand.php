<?php
/**
 * Created by PhpStorm.
 * User: Jiří Jelínek <jelinekvb@gmail.com>
 * Date: 10.4.17
 * Time: 12:15
 */

namespace RosterModule\Console\Command;


use ActualityModule\Entity\Actuality;
use App\Uuid\UuidFactory;
use ArticleModule\Entity\Article;
use ArticleModule\Entity\Tag;
use Kdyby\Doctrine\EntityManager;
use LeagueModule\Command\CreateLeagueCommand;
use LeagueModule\Command\CreateSeasonCommand;
use LeagueModule\Entity\League;
use LeagueModule\Entity\Season;
use LiteCQRS\Commanding\CommandBus;
use MatchModule\Command\CreateMatchCommand;
use MatchModule\Command\CreateMatchLineUpCommand;
use MatchModule\Command\CreateTeamCommand;
use MatchModule\Entity\Goal;
use MatchModule\Entity\LineUp;
use MatchModule\Entity\Match;
use MatchModule\Entity\Team;
use Nette\Database\Context;
use Nette\Utils\DateTime;
use Ramsey\Uuid\UuidInterface;
use RosterModule\Command\CreatePlayerCommand;
use RosterModule\Command\CreatePositionCommand;
use RosterModule\Command\CreateTeamCategoryCommand;
use RosterModule\Entity\Player;
use RosterModule\Entity\Position;
use RosterModule\Entity\Roster;
use RosterModule\Entity\TeamCategory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UserModule\Entity\User;

class ArticleIdToUuidCommand extends Command
{
	/** @var  Context */
	private $db;

	/** @var  uuidFactory */
	private $uuidFactory;

	/** @var  CommandBus */
	private $commandBus;

	/** @var  EntityManager */
	private $em;

	private $articles;
	private $images;
	private $tags;

	public function __construct(
		Context $db,
		UuidFactory $uuidFactory,
		CommandBus $commandBus,
		EntityManager $em
	)
	{
		parent::__construct();
		$this->db = $db;
		$this->uuidFactory = $uuidFactory;
		$this->commandBus = $commandBus;
		$this->em = $em;
	}


	public function configure()
	{
		$this->setName('article:convertId')
				->setDescription('Convert article ID to uuid');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{


		$this->em->beginTransaction();


		$teamsDb = $this->db->table('team')->fetchAssoc('id');
		$teams = [];
		foreach($teamsDb as $t) {
			$uuid = $this->uuidFactory->uuid();
			$teamCommand = new CreateTeamCommand($uuid, $t['name'], $t['short_name']);
			$teams[$t['id']] = $teamCommand;

			$team = new Team($teamCommand->id(), $teamCommand->name(), $teamCommand->shortName(), "logo/".$t['logo'], $t['facr'], $t['id']);
			$this->em->persist($team);
			$this->em->flush();
		}

		$teamCategory = [];
		foreach($this->db->table('team_category') as $tc) {
			$uuid = $this->uuidFactory->uuid();
			$category = new TeamCategory($uuid, $tc->name, $tc->uri);
			$teamCategory[$tc->id] = new CreateTeamCategoryCommand($uuid, $tc->name, $tc->uri);

			$this->em->persist($category);
			$this->em->flush();
		}

		$leagues = [];
		foreach($this->db->table('league') as $l) {
			$uuid = $this->uuidFactory->uuid();
			$leagueCommand = new CreateLeagueCommand($uuid, $l->name, $teamCategory[$l->team_category_id]->id());
			$tc = $this->em->getRepository(TeamCategory::class)->find($teamCategory[$l->team_category_id]->id());
			$league = new League($uuid, $l->name, $tc);
			$leagues[$l->id] = $leagueCommand;

			$this->em->persist($league);
			$this->em->flush();
		}
		$seasons = [];
		foreach($this->db->table('season') as $s) {
			$uuid = $this->uuidFactory->uuid();
			$seasonCommand = new CreateSeasonCommand($uuid, $leagues[$s->league_id]->id(), $s->start_season, $s->end_season, $s->id);
			$seasons[$s->id] = $seasonCommand;
			$this->commandBus->handle($seasonCommand);
		}

		$positions = [];
		foreach($this->db->table('position') as $p) {
			$uuid = $this->uuidFactory->uuid();
			$positionCommand = new Position($uuid, $p->name, $p->priority, $p->id);
			$positions[$p->id] = new CreatePositionCommand($uuid, $p->name, $p->priority);

			$this->em->persist($positionCommand);
			$this->em->flush();
		}

		$players = [];
		foreach($this->db->table('player') as $pl) {
			$uuid = $this->uuidFactory->uuid();
			//dump($pl->birth->getTimestamp());
			$playerCommand = new CreatePlayerCommand($uuid,
				$pl->name,
				$pl->surname,
				$pl->position_id ? $positions[$pl->position_id]->id() : NULL,
				isset($pl->birth) && $pl->birth->getTimestamp()!=-62169987600 ? $pl->birth : NULL ,
				NULL,
				$pl->facr);
			$pos = $pl->position_id ? $this->em->getRepository(Position::class)->find($playerCommand->position()) : NULL;
			$player = new Player($uuid, $playerCommand->name(), $playerCommand->surname(), $pos, new DateTime($playerCommand->birth()), "photo/".$pl->photo, $playerCommand->facrId(), $pl->archive, $pl->id);
			$players[$pl->id] = $playerCommand;

			$this->em->persist($player);
			$this->em->flush();

		}

		$matches = [];
		foreach($this->db->table('match') as $m) {
			$uuid = $this->uuidFactory->uuid();
			$matchCommand = new CreateMatchCommand(
				$uuid,
				$teams[$m->home_team_id]->id(),
				$teams[$m->guest_team_id]->id(),
				$seasons[$m->season_id]->id(),
				$m->round,
				$m->date->format('j.n.Y'),
				$m->date->format('H:i'),
				$m->departure
			);

			$ht = $this->em->getRepository(Team::class)->find($matchCommand->homeTeam());
			$gt = $this->em->getRepository(Team::class)->find($matchCommand->guestTeam());
			$se = $this->em->getRepository(Season::class)->find($matchCommand->season());
			$match = new Match($uuid, $ht,$gt, $se, $matchCommand->round(), $m->date, $m->departure, $m->id);
			$match->setHomeGoal($m->goal_home);
			$match->setHomeGoalHalf($m->goal_home_half);
			$match->setGuestGoal($m->goal_guest);
			$match->setGuestGoalHalf($m->goal_guest_half);
			$match->setComment($m->comment);
			$match->setGuid($m->guid);

			$matches[$m->id] = $match;
			$this->em->persist($match);
			$this->em->flush();
		}

		// LineUp ups
		foreach($this->db->table('line_up') as $lu) {
			$uuid = $this->uuidFactory->uuid();
			$team = $this->em->getRepository(Team::class)->find($teams[$lu->team_id]->id());
			$player = $this->em->getRepository(Player::class)->find($players[$lu->player_id]->id());
			$position = $lu->position_id ? $this->em->getRepository(Position::class)->find($positions[$lu->position_id]->id()) : NULL;
			$match = $this->em->getRepository(Match::class)->find($matches[$lu->match_id]->id());
			$lineUp = new LineUp($uuid, $team, $player, $position, $lu->yellow_card, $lu->second_yellow_card, $lu->red_card, $lu->substitute, $match);

			$this->em->persist($lineUp);
			$this->em->flush();

		}

		//goals
		foreach($this->db->table('goal') as $g) {
			$uuid = $this->uuidFactory->uuid();
			$match = $this->em->getRepository(Match::class)->find($matches[$g->match_id]->id());
			$player = $this->em->getRepository(Player::class)->find($players[$g->player_id]->id());

			$goal = new Goal($uuid, $match, $g->minute, $player);
			$this->em->persist($goal);
			$this->em->flush();
		}

		//substitution
		foreach($this->db->table('roster') as $ro) {
			$uuid = $this->uuidFactory->uuid();
			$season = $this->em->getRepository(Season::class)->find($seasons[$ro->season_id]->id());
			$team = $this->em->getRepository(Team::class)->find($teams[$ro->team_id]->id());
			$player = $this->em->getRepository(Player::class)->find($players[$ro->player_id]->id());

			$roster = new Roster($uuid, $season, $player, $team, $ro->player_number);
			$this->em->persist($roster);
			$this->em->flush();
		}

		// tags
		$tags = [];
		foreach($this->db->table('tag') as $ta) {
			$uuid = $this->uuidFactory->uuid();
			$tag = new Tag($uuid, $ta->tag);
			$tags[$ta->id] = $tag;

			$this->em->persist($tag);
			$this->em->flush();
		}

		$articles = [];
		foreach($this->db->table('article') as $a) {
			$uuid = $this->uuidFactory->uuid();
			/** @var \UserModule\Entity\User */
			$user = $this->em->getRepository(User::class)->findOneBy(['email'=>$a->ref('user', 'author_id')->email]);

			$article = new Article(
				$uuid,
				$a->title,
				$a->subtitle,
				$a->perex,
				$a->content,
				$a->release_date,
				$a->release,
				$user->fullName(),
				"article/".$a->ref('image', 'image_id')->source,
				$user,
				$a->match_id ? $matches[$a->match_id] : NULL
			);
/*
			if(file_exists(WWW_DIR."/article/big-".$a->ref('image', 'image_id')->source)) {
				rename(WWW_DIR . "/article/big-" . $a->ref('image', 'image_id')->source, WWW_DIR . "/storage/article/" . $a->ref('image', 'image_id')->source);
			}
			*/
			if($a->match_id) {
				$articleMatch = $this->em->getRepository(Match::class)->find($matches[$a->match_id]->id());
				$article->changeMatch($articleMatch);
			}

			$articleTags = [];
			foreach($this->db->table('article_tag')->where('article_id', $a->id) as $at) {
				$articleTags[] = $this->em->getRepository(Tag::class)->find($tags[$at->tag_id]->id());
			}
			$article->setTags($articleTags);

			$articles[] = $article;
			$this->em->persist($article);
			$this->em->flush();
		}

		// actuality
		foreach($this->db->table('news') as $n) {
			$uuid = $this->uuidFactory->uuid();
			$actuality = new Actuality($uuid, $n->date, $n->title, $n->text);
			$this->em->persist($actuality);
			$this->em->flush();
		}


		$this->em->commit();


	}

}