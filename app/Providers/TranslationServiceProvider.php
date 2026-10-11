<?php

namespace App\Providers;

use Illuminate\Translation\TranslationServiceProvider as BaseTranslationServiceProvider;
use Illuminate\Translation\Translator;

class TranslationServiceProvider extends BaseTranslationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();
        
        // Override the translator to handle JSON translations
        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];
            
            $translator = new class($loader, $locale) extends Translator {
                protected $jsonTranslations = [];
                
                public function get($key, array $replace = [], $locale = null, $fallback = true) {
                    // Load JSON translations if not loaded
                    if (empty($this->jsonTranslations)) {
                        $this->loadJsonTranslations();
                    }
                    
                    $locale = $locale ?: $this->locale;
                    
                    // Check JSON translations first
                    if (isset($this->jsonTranslations[$locale][$key])) {
                        return $this->makeReplacements(
                            $this->jsonTranslations[$locale][$key],
                            $replace
                        );
                    }
                    
                    // Fallback to parent
                    return parent::get($key, $replace, $locale, $fallback);
                }
                
                protected function loadJsonTranslations() {
                    $locales = ['ar', 'en'];
                    
                    foreach ($locales as $locale) {
                        $path = base_path("lang/{$locale}.json");
                        
                        if (file_exists($path)) {
                            $translations = json_decode(file_get_contents($path), true);
                            if (is_array($translations)) {
                                $this->jsonTranslations[$locale] = $translations;
                            }
                        }
                    }
                }
            };
            
            $translator->setFallback($app['config']['app.fallback_locale']);
            
            return $translator;
        });
    }
}
