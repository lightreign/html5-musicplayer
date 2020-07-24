<?php

namespace MusicPlayer\Controller;

use MusicPlayer\EnvironmentCheck;
use MusicPlayer\Exception\AuthenticationFailed;
use MusicPlayer\Library\Library;
use MusicPlayer\Message\Errors;
use MusicPlayer\Message\Success;
use MusicPlayer\Twig;
use MusicPlayer\User\Auth;
use MusicPlayer\User\Users;

/**
 * Main controller
 * Frameworkess MVC is ok...
 * 
 * @author Adrian Pennington <adrian@ajpennington.net>
 */
class Main extends Controller {
	/** @var Twig $twig */
	protected $twig;

	/** @var Library $library */
	protected $library;

	public function __construct() {
		parent::__construct();

		$this->twig = new Twig;
		$this->library = new Library;
	}

	public function index() {
		$variables = [
			'title' => 'Music Player',
			'files' => $this->library->files()
		];

		$this->view('index.html.twig', $variables);
	}

	public function settings() {
		$users = new Users();

		$variables = [
			'title' => 'Settings',
			'directories' => $this->library->directories(),
			'users' => $users->getAll()
		];

		$this->view('settings.html.twig', $variables);
	}

	public function check() {
		$variables = [
			'database_dir_writable' => EnvironmentCheck::database_dir_writable(),
			'database_file_exists' => EnvironmentCheck::database_file_exists(),
			'play_dir_writable' => EnvironmentCheck::play_dir_writable(),
			'check_failed' => !EnvironmentCheck::is_ok()
		];

		$this->view('check.html.twig', $variables);
	}

	public function login() {
		if (Auth::is_authenticated()) {
			$this->redirect('');
		}

		if ($this->using_http_method('post') && !empty($this->request->username)) {
			$auth = new Auth;

			try {
				$auth->authenticate($this->request->username, $this->request->password);

				$this->redirect('');
			} catch (AuthenticationFailed $e) {
				Errors::add($e->getMessage());

			} catch (Exception $e) {
				// TODO: handle something else
			}
		}

		$this->view('login.html.twig');
	}

	public function logout() {
		$auth = new Auth;
		$auth->unauthenticate();

		Success::addFlash('You are logged out');
		$this->redirect('login');
	}

	/**
	 * Sets the view
	 * @param string $template Twig template name
	 * @param array $variables Variables to bind to template
	 */
	public function view($template, array $variables = []) {
		$this->set_response($this->twig->view($template, $variables));
	}
}