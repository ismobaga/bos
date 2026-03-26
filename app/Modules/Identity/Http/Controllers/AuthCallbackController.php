<?php

namespace App\Modules\Identity\Http\Controllers;

use App\Models\User;
use App\Modules\Identity\Domain\Models\IdentityAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthCallbackController
{
    public function handle(Request $request): RedirectResponse
    {
        // This controller handles the OIDC callback from Authentik.
        // The actual OAuth flow is managed by the Identity module's OIDC service.
        // Here we expect the validated claims to have been passed via state/session.

        $claims = session('oidc_claims');

        if (! $claims) {
            return redirect('/')->withErrors(['message' => 'Authentication failed.']);
        }

        $user = $this->resolveUser($claims);

        Auth::login($user, remember: true);

        session()->forget('oidc_claims');
        session()->regenerate();

        return redirect()->intended(config('app.url') . '/dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to Authentik for single logout
        $authentikUrl = config('services.authentik.base_url');

        return redirect("{$authentikUrl}/if/flow/default-provider-logout/");
    }

    private function resolveUser(array $claims): User
    {
        $provider = 'authentik';
        $providerUserId = $claims['sub'];
        $email = $claims['email'] ?? null;

        $account = IdentityAccount::where('provider', $provider)
            ->where('provider_user_id', $providerUserId)
            ->with('user')
            ->first();

        if ($account) {
            $account->update([
                'raw_claims' => $claims,
                'email' => $email,
                'last_login_at' => now(),
            ]);

            return $account->user;
        }

        // Provision new user
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $claims['name'] ?? $email,
                'status' => 'active',
            ]
        );

        IdentityAccount::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_user_id' => $providerUserId,
            'email' => $email,
            'subject' => $providerUserId,
            'raw_claims' => $claims,
            'last_login_at' => now(),
        ]);

        return $user;
    }
}
