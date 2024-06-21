<?php declare(strict_types=1);

namespace Sylvera;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ProjectPostType
{
    private const CONFIG = [
        'label' => 'Project',
        'supports' => [
            'title',
        ],
        'public' => true,
        'menu_icon' => 'dashicons-portfolio',
        'menu_position' => 2,
        'labels' => [
            'name' => 'Projects',
            'singular_name' => 'Project',
            'add_new' => 'Add New Project',
            'add_new_item' => 'Add New Project',
            'edit_item' => 'Edit Project',
            'update_item' => 'Update Project',
            'search_items' => 'Search Projects',
        ],
    ];

    public function __construct()
    {
        add_action(
            'init',
            fn() => $this->registerPostType(),
        );
        add_action(
            'admin_init',
            fn() => $this->registerMetaBox(),
        );
        add_action(
            'save_post',
            fn() => $this->save(),
        );
        add_action(
            'rest_api_init',
            fn() => $this->api(),
        );
        add_action(
            'admin_enqueue_scripts',
            fn() => $this->enqueueScripts(),
        );
    }

    public function registerPostType(): void
    {
        register_post_type(strtolower(self::CONFIG['label']), self::CONFIG);
    }

    public function registerMetaBox(): void
    {
        add_meta_box(
            'project_meta',
            'Project Details',
            fn() => $this->renderMetaBox(),
            strtolower(self::CONFIG['label'])
        );
    }

    public function renderMetaBox(): void
    {
        $twig = new Environment(
            new FilesystemLoader(
                __DIR__.'/../templates'
            )
        );
        $post = get_post();
        echo $twig->render(
            'project-meta-box.html.twig',
            [
                'post' => $post,
                'meta' => get_post_meta($post->ID),
            ]
        );
    }

    public function save(): void
    {
        $post = get_post();
        update_post_meta(
            $post->ID,
            "project_description",
            $_POST["project_description"]
        );
        update_post_meta(
            $post->ID,
            "project_founded",
            $_POST["project_founded"]
        );
    }

    public function api(): void
    {
        register_rest_route(
            'sylvera/v1',
            '/projects/(?P<id>\d+)',
            [
                'methods' => 'GET',
                'callback' => function ($request) {
                    $post = get_post($request['id']);
                    $post->meta = get_post_meta($post->ID);

                    $response = [
                        'id' => $post->ID,
                        'post_title' => $post->post_title,
                        'description' => $post->meta['project_description'][0],
                        'founded' => (int)$post->meta['project_founded'][0],
                    ];

                    return $response;
                },
            ]
        );
    }

    public function enqueueScripts(): void
    {
        wp_enqueue_style(
            'projects-css',
            plugins_url(
                '/css/projects.css',
                __DIR__
            )
        );
        wp_enqueue_script(
            'projects-js',
            plugins_url(
                '/js/projects.js',
                __DIR__
            )
        );
    }
}