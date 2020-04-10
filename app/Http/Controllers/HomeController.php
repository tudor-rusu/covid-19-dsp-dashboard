<?php

namespace App\Http\Controllers;

use App\Checkpoint;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Factory|View
     */
    public function index()
    {
        $checkpoints = Checkpoint::all(Checkpoint::API_BORDER_URL(), ['status' => 'active']);

        if(!is_array($checkpoints)) {
            session()->flash('type', 'danger');
            session()->flash('message', $checkpoints);
            $checkpoints = [];
        }

        return view('home', ['checkpoints' => $checkpoints]);
    }
}
