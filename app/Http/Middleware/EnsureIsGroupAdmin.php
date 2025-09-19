<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsGroupAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $group = $request->route('group');

        if (! $group instanceof Group) {
            $group = Group::find($group);
        }

        if (! $group || Auth::id() !== $group->created_by) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
