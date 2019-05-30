<?php
namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

use App\Providers\Services\Macros\FormMacros as FormMacros;

class ViewServiceProvider extends \Illuminate\View\ViewServiceProvider {


    public function register() {
        parent::register();
        $this->app->singleton('document', \App\Providers\Services\Document::class);
        $this->app->singleton('FormMacros', \App\Providers\Services\Macros\FormMacros::class);
    }

    public function boot() {
        // hook to Collective Form & Html
        if ($this->app->form && $this->app->html) 
            $this->app->FormMacros->register($this->app->form, $this->app->html);

        //$this->app->document->addJS('js/app.js', 'app');
        //$this->app->document->addJS('js/img-placeholder.js', 'image-placeholder', 'app');
        $this->app->document->addCSS('css/app.css', 'app');

        View::share('document', $this->app->document);

        // blade directives
        Blade::directive('title', function ($title="") {
            if ($title) return "<?php app('document')->setTitle($title); ?>";
            return "<?php echo app('document')->getTitle(); ?>";
        });

        Blade::directive('page_title', function ($page_title="") {
            if ($page_title) return "<?php app('document')->setPageTitle($page_title); ?>";
            return "<?php echo app('document')->getPageTitle(); ?>";
        });

        Blade::directive('body_class', function ($body_class="") {
            if ($body_class) return "<?php app('document')->addBodyClass($body_class); ?>";
            return "<?php echo app('document')->getBodyClass(); ?>";
        });

        Blade::directive('css', function ($exp) {
            return "<?php call_user_func_array([app('document'), 'addCSS'], [$exp]); ?>";
        });

        Blade::directive('js', function ($exp) {
            return "<?php call_user_func_array([app('document'), 'addJS'], [$exp]); ?>";
        });

        Blade::directive('assets', function ($type) {
            return "<?php echo app('document')->printAssets($type); ?>";
        });

        /**
        *@param inc view css tmpl to include
        */
        Blade::directive('cssblock', function ($exp) {
            return "<?php call_user_func_array([app('document'), 'addCSSBlock'], [$exp]); ?>";
        });
        /**
        *@param inc view js tmpl to include
        */
        Blade::directive('jsblock', function ($exp) {
            return "<?php call_user_func_array([app('document'), 'addJSBlock'], [$exp]); ?>";
        });

        /**
        *@param inc view js tmpl to include
        */
        Blade::directive('groupblock', function ($exp) {
            return "<?php call_user_func_array([app('document'), 'addGroupBlock'], [$exp]); ?>";
        });

        /**
        *@param inc view js tmpl to include
        */
        Blade::directive('dynamicblock', function ($exp) {
            return "<?php echo call_user_func_array([app('document'), 'printAssets'], [$exp]); ?>";
        });
    }
}



