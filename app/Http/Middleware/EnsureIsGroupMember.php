<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class EnsureIsGroupMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $group = $request->route('group');

        if (!$group instanceof Group) {
            $group = Group::find($group);
        }

        if (!$group || !$group->users->contains(auth()->user())) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
