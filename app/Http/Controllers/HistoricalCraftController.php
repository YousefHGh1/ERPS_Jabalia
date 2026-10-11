<?php
namespace App\Http\Controllers;

use App\Models\HistoricalCraft;
use Illuminate\Http\Request;

class HistoricalCraftController extends Controller
{
    public function index(Request $request)
    {
        $query = HistoricalCraft::query();

        $column = $request->get('column');
        $value  = $request->get('value');

        if ($column && $value !== null) {
            $query->whereJsonContains('data->' . $column, $value);
        }

        $columns = [];
        $first   = HistoricalCraft::first();
        if ($first) {
            $columns = array_keys($first->data ?? []);
        }

        $items = $query->latest()->paginate(10)->withQueryString();

        return view('historical_crafts.index', compact('items', 'columns', 'column', 'value'));
    }
}
