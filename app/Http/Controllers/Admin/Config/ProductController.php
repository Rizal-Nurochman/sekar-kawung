<?php

namespace App\Http\Controllers\Admin\Config;

use App\Actions\Admin\DeleteMasterRecord;
use App\Actions\Admin\UpsertMasterRecord;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use App\Models\Scheme;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()->with('scheme')->orderBy('sort_order')->orderBy('id')->paginate(20);

        return view('admin.config.products.index', compact('products'));
    }

    public function create(): View
    {
        $schemes = Scheme::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.config.products.create', compact('schemes'));
    }

    public function store(ProductRequest $request, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['scheme_id', 'description', 'sort_order']);

        $upsert->handle(Product::class, $data);

        return redirect()->route('admin.config.products.index')->with('status', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $schemes = Scheme::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.config.products.edit', compact('product', 'schemes'));
    }

    public function update(ProductRequest $request, Product $product, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['scheme_id', 'description', 'sort_order']);

        $upsert->handle(Product::class, $data, $product);

        return redirect()->route('admin.config.products.index')->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product, DeleteMasterRecord $delete): RedirectResponse
    {
        try {
            $delete->handle($product);
        } catch (RuntimeException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()->route('admin.config.products.index')->with('status', 'Produk berhasil dihapus.');
    }
}
