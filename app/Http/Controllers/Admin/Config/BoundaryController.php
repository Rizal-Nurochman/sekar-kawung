<?php

namespace App\Http\Controllers\Admin\Config;

use App\Actions\Admin\DeleteMasterRecord;
use App\Actions\Admin\UpsertMasterRecord;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BoundaryRequest;
use App\Models\Boundary;
use App\Models\Scheme;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class BoundaryController extends Controller
{
    public function index(): View
    {
        $boundaries = Boundary::query()->with('scheme')->orderBy('sort_order')->orderBy('id')->paginate(20);

        return view('admin.config.boundaries.index', compact('boundaries'));
    }

    public function create(): View
    {
        $schemes = Scheme::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.config.boundaries.create', compact('schemes'));
    }

    public function store(BoundaryRequest $request, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['scheme_id', 'description', 'sort_order']);

        $upsert->handle(Boundary::class, $data);

        return redirect()->route('admin.config.boundaries.index')->with('status', 'Boundary berhasil ditambahkan.');
    }

    public function edit(Boundary $boundary): View
    {
        $schemes = Scheme::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.config.boundaries.edit', compact('boundary', 'schemes'));
    }

    public function update(BoundaryRequest $request, Boundary $boundary, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['scheme_id', 'description', 'sort_order']);

        $upsert->handle(Boundary::class, $data, $boundary);

        return redirect()->route('admin.config.boundaries.index')->with('status', 'Boundary berhasil diperbarui.');
    }

    public function destroy(Boundary $boundary, DeleteMasterRecord $delete): RedirectResponse
    {
        try {
            $delete->handle($boundary);
        } catch (RuntimeException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()->route('admin.config.boundaries.index')->with('status', 'Boundary berhasil dihapus.');
    }
}
