<?php declare(strict_types=1);

use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\CoversClass;
use Sylvera\ProjectPostType;
use PHPUnit\Framework\TestCase;

use function Brain\Monkey\Functions\expect;

#[CoversClass(ProjectPostType::class)]
class ProjectPostTypeTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * This is an example test using BrainMonkey for WP function mocking
     *
     * `plugins_url` is mocked to return $pluginDir - it should be called exactly twice.
     *
     * `wp_enqueue_style` is mocked to expect specific parameters passed to it (the `with` method)
     * As we have 2 expected calls this function is mocked twice with an expectation of each set of input parameters
     * being passed to it exactly once.
     *
     * If different parameters are passed, this will change the call count. If the call counts do not match
     * this will trigger a test failure.
     */
    public function test_enqueueScripts(): void
    {
        $pluginDir = 'pluginDir';
        expect('plugins_url')
            ->times(2)
            ->andReturn($pluginDir);

        expect('wp_enqueue_style')
            ->times(1)
            ->with(
                'projects-css',
                $pluginDir
            );
        expect('wp_enqueue_script')
            ->times(1)
            ->with(
                'projects-js',
                $pluginDir
            );

        (new ProjectPostType())->enqueueScripts();
    }
}
