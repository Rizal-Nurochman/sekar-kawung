<?php

namespace App\Http\Controllers\Admin\Config;

use App\Actions\Admin\DeleteMasterRecord;
use App\Actions\Admin\UpsertMasterRecord;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProcessRequest;
use App\Models\Process;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class ProcessController extends Controller
{
    public function index(): View
    {
        $processes = Process::query()->with('product')->orderBy('sort_order')->orderBy('id')->paginate(20);

        return view('admin.config.processes.index', compact('processes'));
    }

    public function create(): View
    {
        $products = Product::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.config.processes.create', compact('products'));
    }

    public function store(ProcessRequest $request, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['product_id', 'description', 'sort_order']);

        $upsert->handle(Process::class, $data);

        return redirect()->route('admin.config.processes.index')->with('status', 'Proses berhasil ditambahkan.');
    }

    public function edit(Process $process): View
    {
        $products = Product::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.config.processes.edit', compact('process', 'products'));
    }

    public function update(ProcessRequest $request, Process $process, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['product_id', 'description', 'sort_order']);

        $upsert->handle(Process::class, $data, $process);

        return redirect()->route('admin.config.processes.index')->with('status', 'Proses berhasil diperbarui.');
    }

    public function destroy(Process $process, DeleteMasterRecord $delete): RedirectResponse
    {
        try {
            $delete->handle($process);
        } catch (RuntimeException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()->route('admin.config.processes.index')->with('status', 'Proses berhasil dihapus.');
    }
}
