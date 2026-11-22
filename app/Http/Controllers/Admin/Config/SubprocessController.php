<?php

namespace App\Http\Controllers\Admin\Config;

use App\Actions\Admin\DeleteMasterRecord;
use App\Actions\Admin\UpsertMasterRecord;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubprocessRequest;
use App\Models\Process;
use App\Models\Subprocess;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class SubprocessController extends Controller
{
    public function index(): View
    {
        $subprocesses = Subprocess::query()->with('process')->orderBy('sort_order')->orderBy('id')->paginate(20);

        return view('admin.config.subprocesses.index', compact('subprocesses'));
    }

    public function create(): View
    {
        $processes = Process::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.config.subprocesses.create', compact('processes'));
    }

    public function store(SubprocessRequest $request, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['process_id', 'description', 'sort_order']);

        $upsert->handle(Subprocess::class, $data);

        return redirect()->route('admin.config.subprocesses.index')->with('status', 'Subproses berhasil ditambahkan.');
    }

    public function edit(Subprocess $subprocess): View
    {
        $processes = Process::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.config.subprocesses.edit', compact('subprocess', 'processes'));
    }

    public function update(SubprocessRequest $request, Subprocess $subprocess, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['process_id', 'description', 'sort_order']);

        $upsert->handle(Subprocess::class, $data, $subprocess);

        return redirect()->route('admin.config.subprocesses.index')->with('status', 'Subproses berhasil diperbarui.');
    }

    public function destroy(Subprocess $subprocess, DeleteMasterRecord $delete): RedirectResponse
    {
        try {
            $delete->handle($subprocess);
        } catch (RuntimeException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()->route('admin.config.subprocesses.index')->with('status', 'Subproses berhasil dihapus.');
    }
}
