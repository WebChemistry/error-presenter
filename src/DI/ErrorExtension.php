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
use WebChemistry\ErrorPresenter\ErrorHelper;
use WebChemistry\ErrorPresenter\ErrorPresenter;
use WebChemistry\ErrorPresenter\PresenterMapping;

final class ErrorExtension extends CompilerExtension
{

	/** @var mixed[] */
	public array $defaults = [
		'messages' => [
			null => [
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
		],
		'home' => 'Homepage',
		'colors' => [
			'primary' => '#ff6f68',
			'secondary' => '#ffa34f',
		],
		'layout' => null,
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('helper'))
			->setType(ErrorHelper::class);
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
		$config = $this->validateConfig($this->defaults);

		$init->addBody('$this->getService(?);', [$this->prefix('helper')]);
		$init->addBody(Debugger::class . '::$errorTemplate = ?;', [__DIR__ . '/../templates/tracy.phtml']);
		$init->addBody(ErrorHelper::class . '::setConfig(' . var_export($config, true) . ');');
	}

}
