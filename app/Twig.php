<?php

namespace MusicPlayer;

use MusicPlayer\Message\Errors;
use MusicPlayer\Message\Success;
use MusicPlayer\User\Auth;
use MusicPlayer\User\User;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Twig wrapper
 *
 * @author Adrian Pennington <git@penningtonfamily.net>
 */
class Twig {
    protected $loader;
    protected $view;

    protected $templates_folder = 'templates/view';

    public function __construct($params = []) {
        // Disable template cache during testing
        $default_params = [
            'cache' => Config::get('testing') ? false : BASE_DIR . 'templates/cache/',
        ];

        $this->loader = new FilesystemLoader(BASE_DIR . $this->templates_folder);
        $this->view = new Environment($this->loader, array_merge($default_params, $params));
    }

    public function view($template_name, array $variables = []) {
        $template = $this->view->load($template_name);
        return $template->render(array_merge($variables, $this->config()));
    }

    protected function config() {
        if (Auth::is_authenticated()) {
            $user = new User(Auth::get_authenticated_user()['id']);

            $use_icons = $user->settings()['show_icons'] ?? false;
        } else {
            $use_icons = Config::get('settings.show_icons');
        }

        return [
            'base_url' => Config::get('base_url'),
            'theme' => Config::get('theme'),
            'errors' => Errors::get(),
            'success' => Success::get(),
            'auth_enabled' => Auth::is_auth_enabled(),
            'use_icons' =>  $use_icons,
            'volume_buttons' => Config::get('volume_buttons'),
        ];
    }
}
