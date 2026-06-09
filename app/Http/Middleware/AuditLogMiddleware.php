<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log modifying requests for authenticated users
        if (auth()->check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            // Skip logout
            if ($request->routeIs('logout')) {
                return $response;
            }

            $actionName = $request->route() ? $request->route()->getName() : $request->path();
            $actionName = $actionName ?: 'unknown_action';

            $inputs = $request->except(['_token', '_method', 'password', 'password_confirmation']);

            // Filter out UploadedFile objects to prevent JSON encoding errors
            array_walk_recursive($inputs, function (&$item) {
                if ($item instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $item = '[File: ' . $item->getClientOriginalName() . ' (' . round($item->getSize() / 1024, 2) . ' KB)]';
                }
            });

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $request->method() . ': ' . $actionName,
                'new_values' => $inputs,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);
        }

        return $response;
    }
}
