<?php

declare(strict_types=1);

namespace Fandoogh\Core;

defined('ABSPATH') || exit;

use Fandoogh\Managers\AdminManager;
use Fandoogh\Managers\ModuleManager;
use Fandoogh\Core\Migration\Migrator;

use Fandoogh\Modules\Description\Module as DescriptionModule;
use Fandoogh\Modules\Video\Module as VideoModule;
use Fandoogh\Modules\Faq\Module as FaqModule;
use Fandoogh\Modules\Faq\ProductModule as ProductSeoModule;

use Fandoogh\Customers\Application as CustomersApplication;
use Fandoogh\Projects\Application as ProjectsApplication;
use Fandoogh\Modules\Reviews\Module as ReviewsModule;
use Fandoogh\Elementor\Application as ElementorApplication;
use Fandoogh\Calculator\Application as CalculatorApplication;
use Fandoogh\Modules\OrderCenter\Module as OrderCenterModule;


final class Application
{
    private static ?Application $instance = null;

    private Container $container;

    /** @var array<string,class-string> */
    private const MODULES = [
        'description' => DescriptionModule::class,
        'video' => VideoModule::class,
        'faq' => FaqModule::class,
        'reviews' => ReviewsModule::class,
        'customers' => CustomersApplication::class,
        'projects' => ProjectsApplication::class,
        'order-center' => OrderCenterModule::class,
    ];


    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    private function __construct()
    {
        $this->container = new Container();

        $this->boot();
    }


    private function boot(): void
    {
        (new Migrator())->boot();
        (new CleanupManager())->boot();

        /*
        |--------------------------------------------------------------------------
        | Core Managers
        |--------------------------------------------------------------------------
        */

        $admin   = new AdminManager();
        $modules = new ModuleManager();


        $modules->boot();

        $admin->boot();


        /*
        |--------------------------------------------------------------------------
        | Register Services
        |--------------------------------------------------------------------------
        */

        $this->container->set(
            'admin',
            $admin
        );

        $this->container->set(
            'modules',
            $modules
        );


        foreach (self::MODULES as $key => $moduleClass) {
            if ($modules->enabled($key)) {
                (new $moduleClass())->boot();
            }
        }

        if ($modules->enabled('product_faq') || $modules->enabled('product_reason')) {
            (new ProductSeoModule($modules))->boot();
        }

        (new CalculatorApplication())->boot();


        /*
        |--------------------------------------------------------------------------
        | Integrations
        |--------------------------------------------------------------------------
        */

        (new ElementorApplication($modules))->boot();
        (new \Fandoogh\LoginDesigner\Application())->boot();

    }


    public function container(): Container
    {
        return $this->container;
    }


    public function get(string $service): ?object
    {
        return $this->container->get($service);
    }
}
