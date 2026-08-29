<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

use CodeIgniter\HTTP\Exceptions\RedirectException;
use CodeIgniter\HTTP\RedirectResponse;

if (! function_exists('redirect')) {
    /**
     * Redirect to a new URL.
     *
     * Partially overrides the CI4.7.4 core `redirect()` to preserve CI3 legacy
     * semantics: calling `redirect('login');` immediately stops execution and
     * produces an HTTP 302 redirect, even when the call is made from a
     * controller constructor (where the return value cannot be used).
     *
     * The RedirectResponse object is passed to RedirectException (not the raw
     * route string) so that absolute URLs are not double-prefixed with baseURL,
     * while relative URLs are resolved against the configured base URL.
     */
    function redirect(?string $route = null): RedirectResponse
    {
        $response = service('redirectresponse');

        if (func_num_args() > 0 && $route !== null) {
            throw new RedirectException($response->to((string) $route));
        }

        return $response;
    }
}
