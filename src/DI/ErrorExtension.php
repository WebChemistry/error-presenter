<?php declare(strict_types = 1);

namespace WebChemistry\ErrorPresenter\DI;

use Nette\Application\Application;
use Nette\Application\IPresenterFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Tracy\Debugger;
use WebChemistry\ErrorPresenter\ErrorTemplate;
use WebChemistry\ErrorPresenter\ErrorTemplateSingleton;
use WebChemistry\ErrorPresenter\PresenterMapping;

final class ErrorExtension extends CompilerExtension
{

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

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		/** @var ServiceDefinition $def */
		$def = $builder->getDefinitionByType(IPresenterFactory::class);
		$def->addSetup('setMapping', [
			['Error' => 'WebChemistry\ErrorPresenter\*Presenter']
		]);

		/** @var ServiceDefinition $def */
		$def = $builder->getDefinitionByType(Application::class);
		$def->addSetup('$errorPresenter', ['Error:Error']);
	}

	public function afterCompile(ClassType $class)
	{
		$init = $class->getMethods()['initialize'];

		$init->addBody('new ' . ErrorTemplateSingleton::class . '($this->getService(?));', [$this->prefix('template')]);
		$init->addBody(Debugger::class . '::$errorTemplate = ?;', [__DIR__ . '/../templates/tracy.phtml']);
	}

}
