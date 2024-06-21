<?php declare(strict_types=1);

namespace Sylvera;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ProjectPostType
{
    /**
     * TODO: Refactor config to an external file
     * This would allow for future multiple related
     * configurations to be kept together in a single file
     */
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

    /**
     * Moved action registration to separate method for a cleaner constructor
     */
    public function __construct()
    {
        $this->registerActions();
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

    /**
     * Extracted from the constructor
     */
    private function registerActions(): void
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

    /**
     * The following methods have been made private -
     * they do not need to be publicly visible
     *
     * These two methods could be moved to a separate class
     */
    private function registerPostType(): void
    {
        register_post_type(strtolower(self::CONFIG['label']), self::CONFIG);
    }

    private function registerMetaBox(): void
    {
        add_meta_box(
            'project_meta',
            'Project Details',
            fn() => $this->renderMetaBox(),
            strtolower(self::CONFIG['label'])
        );
    }

    private function renderMetaBox(): void
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

    private function save(): void
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

    /**
     * TODO: Move the project response methods to a separate class
     * to adhere to SOLID Single Responsibility Principle
     */
    private function api(): void
    {
        register_rest_route(
            'sylvera/v1',
            '/projects(?:/(?P<id>\d+))?',
            [
                'methods' => 'GET',
                'args' => [
                    'id' => [
                        'required' => false,
                    ],
                ],
                'callback' => fn($request) => $this->getProjectsResponseData(((int)$request['id']) ?? null),
            ]
        );
    }

    private function getProjectsResponseData(?int $id): array
    {
        if ($id) {
            return $this->getPostResponse($id);
        }

        $response = [];
        foreach($this->getAllPosts() as $post) {
            $response[] = $this->getPostResponse($post->ID);
        }

        return $response;
    }

    private function getPostResponse(int $id): array
    {
        $post = get_post($id);
        $post->meta = get_post_meta($post->ID);

        return [
            'ID' => $post->ID,
            'post_title' => $post->post_title,
            'description' => $post->meta['project_description'][0],
            'founded' => (int)$post->meta['project_founded'][0],
        ];
    }

    private function getAllPosts(): array
    {
        return get_posts([
            'numberposts' => -1,
            'post_status' => 'any',
            'post_type' => 'project',
        ]);
    }
}