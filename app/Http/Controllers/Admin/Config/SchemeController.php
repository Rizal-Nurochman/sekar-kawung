<?php

namespace App\Http\Controllers\Admin\Config;

use App\Actions\Admin\DeleteMasterRecord;
use App\Actions\Admin\UpsertMasterRecord;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SchemeRequest;
use App\Models\Scheme;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class SchemeController extends Controller
{
    public function index(): View
    {
        $schemes = Scheme::query()->orderBy('sort_order')->orderBy('id')->paginate(20);

        return view('admin.config.schemes.index', compact('schemes'));
    }

    public function create(): View
    {
        return view('admin.config.schemes.create');
    }

    public function store(SchemeRequest $request, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['description', 'sort_order']);

        $upsert->handle(Scheme::class, $data);

        return redirect()->route('admin.config.schemes.index')->with('status', 'Skema berhasil ditambahkan.');
    }

    public function edit(Scheme $scheme): View
    {
        return view('admin.config.schemes.edit', compact('scheme'));
    }

    public function update(SchemeRequest $request, Scheme $scheme, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['description', 'sort_order']);

        $upsert->handle(Scheme::class, $data, $scheme);

        return redirect()->route('admin.config.schemes.index')->with('status', 'Skema berhasil diperbarui.');
    }

    public function destroy(Scheme $scheme, DeleteMasterRecord $delete): RedirectResponse
    {
        try {
            $delete->handle($scheme);
        } catch (RuntimeException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()->route('admin.config.schemes.index')->with('status', 'Skema berhasil dihapus.');
    }
}
