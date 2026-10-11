<?php
namespace App\Http\Controllers;

use App\Models\Bonus;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index bonus', ['only' => ['index']]);
        $this->middleware('permission:store bonus', ['only' => ['store']]);
        $this->middleware('permission:update bonus', ['only' => ['update']]);
        $this->middleware('permission:delete bonus', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $name = $request->input('name');

        $bonuses = Bonus::when($name, function ($query) use ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        })->paginate(10)->appends(request()->all());

        return view('bonus.index', ['bonuses' => $bonuses]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'   => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ], [
            'required' => __('required'),
            'string'   => __('string'),
            'numeric'  => __('numeric'),
        ]);
        Bonus::create($validatedData);
        return redirect()->route('bonus.index')->with('success', __('saved successfully'));
    }

    public function update(Request $request, $id)
    {
        $bonus         = Bonus::findOrFail($id);
        $validatedData = $request->validate([
            'name'   => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',

        ], [
            'required' => __('required'),
            'string'   => __('string'),
            'numeric'  => __('numeric'),
        ]);
        $bonus->update($validatedData);
        return redirect()->route('bonus.index')->with('success', __('updated successfully'));
    }

    public function destroy($id)
    {
        $bonus = Bonus::findOrFail($id);
        if ($bonus->employeeBonus()->count() > 0) {
            session()->flash('warning', 'المصدر مرتبط ... لا يمكن حذفه');
            return redirect()->back();
        }
        $bonus->delete();
        return redirect()->route('bonus.index')->with('success', __('deleted successfully'));
    }
}