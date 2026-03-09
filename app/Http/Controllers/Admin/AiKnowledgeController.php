<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiKnowledge;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AiKnowledgeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $items = AiKnowledge::when($search, fn($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%")
                ->orWhere('keywords', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/AiKnowledge', [
            'items'   => $items,
            'filters' => ['search' => $search],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'category'  => 'required|string|max:100',
            'keywords'  => 'required|string',
            'content'   => 'required|string',
            'is_active' => 'boolean',
        ]);

        AiKnowledge::create($data);

        return back()->with('success', 'Pengetahuan berhasil ditambahkan. AI sekarang lebih pintar!');
    }

    public function update(Request $request, AiKnowledge $knowledge)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'category'  => 'required|string|max:100',
            'keywords'  => 'required|string',
            'content'   => 'required|string',
            'is_active' => 'boolean',
        ]);

        $knowledge->update($data);

        return back()->with('success', 'Pengetahuan berhasil diperbarui.');
    }

    public function destroy(AiKnowledge $knowledge)
    {
        $knowledge->delete();
        return back()->with('success', 'Pengetahuan dihapus.');
    }
}
