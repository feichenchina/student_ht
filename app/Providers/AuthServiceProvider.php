<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Passport::loadKeysFrom(__DIR__ . '/../secrets/oauth');
        // Passport::tokensExpireIn(now()->addDays(15));
        // Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addDays(5));
        Passport::tokensCan([
            'student' => 'student auth',
            'teacher' => 'teacher auth',
        ]);
        $this->registerPolicies();
        // Passport::useTokenModel(Token::class);
        // Passport::useRefreshTokenModel(RefreshToken::class);
        // Passport::useAuthCodeModel(AuthCode::class);
        // Passport::useClientModel(Client::class);
        // Passport::usePersonalAccessClientModel(PersonalAccessClient::class);

        // Passport::loadKeysFrom(storage_path());
        // Passport::usePersonalAccessClientModel(PersonalAccessClient::class);
        // Passport::tokensCan([
        //     'student' => 'student auth',
        //     'teacher' => 'teacher auth',
        // ]);

        // // 选择一个私人访问的客户端
        // Passport::personalAccessClient(1);
    }
}
