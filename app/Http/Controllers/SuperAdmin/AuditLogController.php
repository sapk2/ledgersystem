<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $action = $request->query('action');

        $logs = AuditLog::with('user')
            ->when($search, fn ($query) => $query->where('description', 'like', "%{$search}%"))
            ->when($action, fn ($query) => $query->where('action', $action))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $actions = AuditLog::query()->distinct()->orderBy('action')->pluck('action');

        return view('super-admin.audit-logs.index', [
            'logs' => $logs,
            'actions' => $actions,
            'search' => $search,
            'action' => $action,
        ]);
    }
}
