<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Get all events with optional filtering
     */
    public function index(Request $request)
    {
        $query = Event::query();

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->byCategory($request->category);
        }

        // Filter by date
        if ($request->has('date')) {
            $query->byDate($request->date);
        }

        // Filter by month/year for calendar
        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('date_time', $request->month)
                  ->whereYear('date_time', $request->year);
        }

        // Only upcoming events by default
        if ($request->boolean('upcoming', true)) {
            $query->upcoming();
        } else {
            $query->orderBy('date_time', 'desc');
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return $query->paginate($request->get('per_page', 12));
    }

    /**
     * Get featured events
     */
    public function featured()
    {
        return Event::featured()->upcoming()->limit(6)->get();
    }

    /**
     * Get a single event
     */
    public function show(Event $event)
    {
        return $event;
    }

    /**
     * Get all categories
     */
    public function categories()
    {
        return Event::select('category')
            ->distinct()
            ->pluck('category');
    }

    /**
     * Get events for calendar (grouped by date)
     */
    public function calendar(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $events = Event::whereMonth('date_time', $month)
            ->whereYear('date_time', $year)
            ->orderBy('date_time')
            ->get()
            ->groupBy(function ($event) {
                return $event->date_time->format('Y-m-d');
            });

        return $events;
    }
}
