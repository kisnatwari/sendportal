<?php

use App\Http\Controllers\Auth\ApiTokenController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\PricingController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Sendportal\Base\Facades\Sendportal;
use App\Http\Middleware\OwnsCurrentWorkspace;
use App\Http\Middleware\RequireWorkspace;

Auth::routes(
    [
        'verify' => config('sendportal-host.auth.register', false),
        'register' => config('sendportal-host.auth.register', false),
        'reset' => config('sendportal-host.auth.password_reset'),
    ]
);

Route::get('setup', 'SetupController@index')->name('setup');

// Auth.
Route::middleware('auth')->namespace('Auth')->group(
    static function (Router $authRouter) {
        // Logout.
        $authRouter->get('logout', 'LoginController@logout')->name('logout');

        // Profile.
        $authRouter->middleware('verified')->name('profile.')->prefix('profile')->group(
            static function (
                Router $profileRouter
            ) {
                $profileRouter->get('/', 'ProfileController@show')->name('show');
                $profileRouter->get('/edit', 'ProfileController@edit')->name('edit');
                $profileRouter->put('/', 'ProfileController@update')->name('update');
            }
        );

        // API Tokens.
        $authRouter->middleware('verified')->name('api-tokens.')->prefix('api-tokens')->group(static function (Router $apiTokenRouter) {
            $apiTokenRouter->get('/', [ApiTokenController::class, 'index'])->name('index');
            $apiTokenRouter->post('/', [ApiTokenController::class, 'store'])->name('store');
            $apiTokenRouter->delete('{tokenid}', [ApiTokenController::class, 'destroy'])->name('destroy');
        });
    }
);

// Workspace User Management.
Route::namespace('Workspaces')
    ->middleware(['auth', 'verified', RequireWorkspace::class, OwnsCurrentWorkspace::class])
    ->name('users.')
    ->prefix('users')
    ->group(
        static function (Router $workspacesRouter) {
            $workspacesRouter->get('/', 'WorkspaceUsersController@index')->name('index');
            $workspacesRouter->delete('{userId}', 'WorkspaceUsersController@destroy')->name('destroy');

            // Invitations.
            $workspacesRouter->name('invitations.')->prefix('invitations')
                ->group(
                    static function (Router $invitationsRouter) {
                        $invitationsRouter->post('/', 'WorkspaceInvitationsController@store')->name('store');
                        $invitationsRouter->delete('{invitation}', 'WorkspaceInvitationsController@destroy')
                            ->name('destroy');
                    }
                );
        }
    );

// Workspace Management.
Route::namespace('Workspaces')->middleware(
    [
        'auth',
        'verified',
        RequireWorkspace::class
    ]
)->group(
    static function (Router $workspaceRouter) {
        $workspaceRouter->resource('workspaces', 'WorkspacesController')->except(
            [
                'create',
                'show',
                'destroy',
            ]
        );

        // Workspace Switching.
        $workspaceRouter->get('workspaces/{workspace}/switch', 'SwitchWorkspaceController@switch')
            ->name('workspaces.switch');

        // Invitations.
        $workspaceRouter->post('workspaces/invitations/{invitation}/accept', 'PendingInvitationController@accept')
            ->name('workspaces.invitations.accept');
        $workspaceRouter->post('workspaces/invitations/{invitation}/reject', 'PendingInvitationController@reject')
            ->name('workspaces.invitations.reject');
    }
);

Route::middleware(['auth', 'verified', 'check.subscription', RequireWorkspace::class])->group(
    static function () {
        Sendportal::webRoutes();
    }
);

Sendportal::publicWebRoutes();




Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');
    Route::post('/subscribe', [PricingController::class, 'subscribe'])->name('subscribe');

    // Protect dashboard with subscription check
    Route::middleware('check.subscription')->group(function () {
        Sendportal::webRoutes();
    });
});


Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware(['auth'])->group(function () {
    // Show the email verification notice
    Route::get('/email/verify', [VerificationController::class, 'show'])
        ->name('verification.notice');

    // Handle the email verification link
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed'])
        ->name('verification.verify');

    // Resend the email verification link
    Route::post('/email/resend', [VerificationController::class, 'resend'])
        ->name('verification.resend');
});