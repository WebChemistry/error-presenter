<?php declare(strict_types = 1);

namespace WebChemistry\ErrorPresenter\DI;

use Nette;
use Nette\DI\CompilerExtension;
use Thunbolt\Application\DI\ApplicationExtension;
use Tracy\Debugger;
use WebChemistry\ErrorPresenter\ErrorHelper;
use WebChemistry\ErrorPresenter\ErrorPresenter;
use WebChemistry\ErrorPresenter\PresenterMapping;

final class ErrorExtension extends CompilerExtension {

	/** @var array */
	public $defaults = [
		'messages' => [
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
		'home' => 'Domovská stránka',
		'homepage' => null,
		'colors' => [
			'primary' => '#ff6f68',
			'secondary' => '#ffa34f',
		],
		'layout' => null,
	];

	public function loadConfiguration() {
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('helper'))
			->setType(ErrorHelper::class);
	}

	public function beforeCompile() {
		$builder = $this->getContainerBuilder();
		$def = $builder->getDefinitionByType(Nette\Application\IPresenterFactory::class);
		if (class_exists(ApplicationExtension::class)) {
			$def->addSetup('addMapping', [
				'Error', new Nette\DI\Statement(PresenterMapping::class),
			]);
		} else {
			$def->addSetup('setMapping', ['Error', ['WebChemistry\\ErrorPresenter\\', '*\\', '*Presenter']]);
		}

		$builder->getDefinitionByType(Nette\Application\Application::class)
			->addSetup('$errorPresenter', ['Error:Error']);
	}

	public function afterCompile(Nette\PhpGenerator\ClassType $class) {
		$init = $class->getMethods()['initialize'];
		$config = $this->validateConfig($this->defaults);
		
		$init->addBody(Debugger::class . '::$errorTemplate = ?;', [__DIR__ . '/../templates/tracy.phtml']);
		$init->addBody(ErrorHelper::class . '::setConfig(' . var_export($config, true) . ');');
		$init->addBody('$this->getService(?);', [$this->prefix('helper')]);
	}

}
