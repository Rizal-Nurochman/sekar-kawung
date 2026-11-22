<?php

namespace App\Http\Controllers\Admin\Config;

use App\Actions\Admin\DeleteMasterRecord;
use App\Actions\Admin\UpsertMasterRecord;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SourceRequest;
use App\Models\Source;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class SourceController extends Controller
{
    public function index(): View
    {
        $sources = Source::query()->orderBy('id')->paginate(20);

        return view('admin.config.sources.index', compact('sources'));
    }

    public function create(): View
    {
        return view('admin.config.sources.create');
    }

    public function store(SourceRequest $request, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['title', 'link_reference']);

        $upsert->handle(Source::class, $data);

        return redirect()->route('admin.config.sources.index')->with('status', 'Sumber berhasil ditambahkan.');
    }

    public function edit(Source $source): View
    {
        return view('admin.config.sources.edit', compact('source'));
    }

    public function update(SourceRequest $request, Source $source, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['title', 'link_reference']);

        $upsert->handle(Source::class, $data, $source);

        return redirect()->route('admin.config.sources.index')->with('status', 'Sumber berhasil diperbarui.');
    }

    public function destroy(Source $source, DeleteMasterRecord $delete): RedirectResponse
    {
        try {
            $delete->handle($source);
        } catch (RuntimeException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()->route('admin.config.sources.index')->with('status', 'Sumber berhasil dihapus.');
    }
}
