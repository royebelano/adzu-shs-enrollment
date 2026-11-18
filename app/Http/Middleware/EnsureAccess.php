<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage in routes:
 *   ->middleware('access:step3')            view for GET, edit for everything else
 *   ->middleware('access:step1,edit')       always needs edit
 *   ->middleware('access:step1|step3')      needs access to any of the modules
 *   ->middleware('access:system')           Administrator only
 */
class EnsureAccess
{
    public function handle(Request $request, Closure $next, string $modules, ?string $level = null): Response
    {
        $user = $request->user();

        $level ??= in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true) ? 'view' : 'edit';

        $allowed = $user && collect(explode('|', $modules))->contains(
            fn ($module) => $level === 'edit' ? $user->canEdit($module) : $user->canView($module)
        );

        if ($allowed) {
            return $next($request);
        }

        $message = $level === 'edit' && $user && collect(explode('|', $modules))->contains(fn ($m) => $user->canView($m))
            ? 'You can only view this. Your office is not allowed to make changes here.'
            : 'You do not have access to that page.';

        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 403);
        }

        // Changing something: go back to where the person was.
        if (!$request->isMethod('GET')) {
            return back()->with('error', $message);
        }

        return redirect($user->homeUrl())->with('error', $message);
    }
}
