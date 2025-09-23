<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureIsGroupMember
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $group = $request->route('group');

        if (! $group instanceof Group) {
            $group = Group::find($group);
        }

        if (! $group || ! $group->users->contains(auth()->user())) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
