<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Enforces a Controlled Application Write Freeze during schema migrations
 * and architectural transitions (Rule 7).
 *
 * When WRITEPATH . 'write_freeze.lock' exists:
 * - All mutating HTTP requests (POST, PUT, DELETE, PATCH) are immediately rejected
 *   with HTTP 503 Service Unavailable.
 * - DB-changing workflows (registration, scheduling, result entry, publishing) are blocked.
 * - Zero-downtime is explicitly rejected in favor of absolute data integrity.
 */
class WriteFreezeFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $lockFile = WRITEPATH . 'write_freeze.lock';
        if (file_exists($lockFile)) {
            $method = strtoupper($request->getMethod());
            if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
                $response = service('response');
                $response->setStatusCode(503);
                $response->setHeader('Content-Type', 'application/json');
                $response->setHeader('Retry-After', '300');
                $response->setBody(json_encode([
                    'status'  => 'error',
                    'code'    => 503,
                    'error'   => 'Service Unavailable: Controlled Application Write Freeze Active',
                    'message' => 'The system is undergoing controlled database migration. All write and mutation workflows (registration, scheduling, result entry, publishing) are temporarily frozen to guarantee complete data integrity.',
                ]));

                return $response;
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
