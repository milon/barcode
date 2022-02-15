<?php

namespace Milon\Barcode\Test;

use Illuminate\Container\Container;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Support\Facades\File;

/**
 * Base class for the test cases.
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Illuminate\Contracts\Container\Container test application instance.
     */
    protected $app;

    /**
     * @var string tmp files path.
     */
    protected $tmpPath;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->createApplication();

        $this->tmpPath = __DIR__ . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR;

        if (! file_exists($this->tmpPath)) {
            File::makeDirectory($this->tmpPath, 0755, true);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        File::cleanDirectory($this->tmpPath);

        parent::tearDown();
    }

    /**
     * Creates dummy application instance, ensuring facades functioning.
     */
    protected function createApplication()
    {
        $this->app = new Container();

        $this->app->singleton('files', function () {
            return new Filesystem;
        });

        Container::setInstance($this->app);

        Facade::setFacadeApplication($this->app);
    }
}
