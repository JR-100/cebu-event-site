<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEvents = Event::count();
        $upcomingEvents = Event::where('date_time', '>=', now())->count();
        $featuredEvents = Event::where('is_featured', true)->count();
        $categoriesCount = Event::select('category')->distinct()->count();
        $recentEvents = Event::orderBy('created_at', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalEvents',
            'upcomingEvents',
            'featuredEvents',
            'categoriesCount',
            'recentEvents'
        ));
    }
}
