<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Crypt;

class UrlEncryptorServiceProvider extends ServiceProvider {
    public function register() {
        $this->app->bind('encryptUrlId', function() {
            return function($id) {
                $encrypted = Crypt::encryptString($id);
                return str_replace(['/', '+', '='], ['-', '_', ''], base64_encode($encrypted));
            };
        });

        $this->app->bind('decryptUrlId', function() {
            return function($encryptedId) {
                $decoded = base64_decode(str_replace(['-', '_'], ['/', '+'], $encryptedId));
                return Crypt::decryptString($decoded);
            };
        });
    }
}
