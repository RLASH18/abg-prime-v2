<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;

trait InteractsWithFlash
{
    /**
     * Flash a success message and redirect.
     *
     * @param string $message The notification text
     * @param string|null $route The route name to redirect to (optional)
     * @param array $parameters Route parameters (optional)
     */
    protected function flashSuccess(string $message, ?string $route = null, array $parameters = []): RedirectResponse
    {
        notyf()->success($message);

        return $this->redirectDestination($route, $parameters);
    }

    /**
     * Flash an error message and redirect.
     */
    protected function flashError(string $message, ?string $route = null, array $parameters = []): RedirectResponse
    {
        notyf()->error($message);

        return $this->redirectDestination($route, $parameters);
    }

    /**
     * Flash an info message and redirect.
     */
    protected function flashInfo(string $message, ?string $route = null, array $parameters = []): RedirectResponse
    {
        notyf()->info($message);

        return $this->redirectDestination($route, $parameters);
    }

    /**
     * Helper to handle the redirection logic.
     */
    private function redirectDestination(?string $route, array $parameters): RedirectResponse
    {
        if ($route) {
            return to_route($route, $parameters);
        }

        return back();
    }
}
