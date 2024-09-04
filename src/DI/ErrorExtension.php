<?php declare(strict_types = 1);

namespace WebChemistry\ErrorPresenter\DI;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Tracy\Debugger;
use WebChemistry\ErrorPresenter\ErrorTemplate;
use WebChemistry\ErrorPresenter\ErrorTemplateSingleton;

final class ErrorExtension extends CompilerExtension
{

	private const array Messages = [
		'default' => [
			'title' => 'Unknown error',
			'desc' => 'Something goes wrong, please try again later.',
		],
		400 => [
			'title' => 'Bad request',
			'desc' => 'The server cannot process the request due to something that is perceived to be a client error.',
		],
		401 => [
			'title' => 'Unauthorized',
			'desc' => 'The requested resource requires an authentication.',
		],
		403 => [
			'title' => 'Access denied',
			'desc' => 'The requested resource requires an authentication.',
		],
		404 => [
			'title' => 'Oops page not found',
			'desc' => 'The page you are looking for does not exist or has been moved.',
		],
		500 => [
			'title' => 'Internal server error',
			'desc' => 'Something goes wrong with our servers, please try again later.',
		],
	];

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'messages' => Expect::structure([
				'default' => Expect::structure([
					'title' => Expect::string('Unknown error'),
					'desc' => Expect::string('Something goes wrong, please try again later.'),
				])->castTo('array'),
				400 => Expect::structure([
					'title' => Expect::string('Bad request'),
					'desc' => Expect::string('The server cannot process the request due to something that is perceived to be a client error.'),
				])->castTo('array'),
				401 => Expect::structure([
					'title' => Expect::string('Unauthorized'),
					'desc' => Expect::string('The requested resource requires an authentication.'),
				])->castTo('array'),
				403 => Expect::structure([
					'title' => Expect::string('Access denied'),
					'desc' => Expect::string('The requested resource requires an authentication.'),
				])->castTo('array'),
				404 => Expect::structure([
					'title' => Expect::string('Oops page not found'),
					'desc' => Expect::string('The page you are looking for does not exist or has been moved.'),
				])->castTo('array'),
				500 => Expect::structure([
					'title' => Expect::string('Internal server error'),
					'desc' => Expect::string('Something goes wrong with our servers, please try again later.'),
				])->castTo('array'),
			])->castTo('array'),
			'template' => Expect::structure([
				'file' => Expect::string(__DIR__ . '/../templates/layout.phtml'),
				'options' => Expect::arrayOf(Expect::mixed()),
			])->castTo('array'),
		])->castTo('array');
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();

		$builder->addDefinition($this->prefix('template'))
			->setFactory(ErrorTemplate::class, [$config]);
	}

	public function afterCompile(ClassType $class)
	{
		$init = $class->getMethods()['initialize'];

		$init->addBody(ErrorTemplateSingleton::class . '::initialize($this->getService(?));', [$this->prefix('template')]);
		$init->addBody(Debugger::class . '::$errorTemplate = ?;', [__DIR__ . '/../templates/tracy.phtml']);
	}

	/**
	 * @param array<string, mixed> $templateOptions
	 */
	public static function startup(
		?string $templateFile = null, 
		array $templateOptions = [],
		string $homeUrl = '/',
	): void
	{
		ErrorTemplateSingleton::intialize(new ErrorTemplate([
			'messages' => self::Messages,
			'home' => $homeUrl,
			'template' => [
				'file' => $templateFile ?? __DIR__ . '/../templates/layout.phtml',
				'options' => $templateOptions,
			],
		]));
		
		Debugger::$errorTemplate = __DIR__ . '/../templates/tracy.phtml';
	}

}
