<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Home page with featured events
     */
    public function home()
    {
        $featuredEvents = Event::featured()->upcoming()->limit(6)->get();

        return view('public.home', compact('featuredEvents'));
    }

    /**
     * Events listing page with filtering
     */
    public function events(Request $request)
    {
        $query = Event::query();

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->byCategory($request->category);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Show all events, upcoming first
        $query->orderByRaw("CASE WHEN date_time >= NOW() THEN 0 ELSE 1 END")
              ->orderByRaw("CASE WHEN date_time >= NOW() THEN date_time END ASC")
              ->orderByRaw("CASE WHEN date_time < NOW() THEN date_time END DESC");

        $events = $query->paginate(12);
        $categories = Event::select('category')->distinct()->pluck('category');

        return view('public.events', compact('events', 'categories'));
    }

    /**
     * Single event detail page
     */
    public function eventDetail(Event $event)
    {
        return view('public.event-detail', compact('event'));
    }

    /**
     * Calendar page
     */
    public function calendar(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $events = Event::whereMonth('date_time', $month)
            ->whereYear('date_time', $year)
            ->orderBy('date_time')
            ->get();

        $calendarEvents = $events->groupBy(function ($event) {
            return $event->date_time->format('Y-m-d');
        });

        return view('public.calendar', compact('calendarEvents', 'month', 'year'));
    }

    /**
     * Calendar data as JSON for AJAX month switching
     */
    public function calendarData(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        $events = Event::whereMonth('date_time', $month)
            ->whereYear('date_time', $year)
            ->orderBy('date_time')
            ->get();

        $calendarEvents = [];
        foreach ($events->groupBy(fn($e) => $e->date_time->format('Y-m-d')) as $date => $evts) {
            $calendarEvents[$date] = $evts->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->name,
                'category' => $e->category,
                'location' => $e->location,
                'date_time' => $e->date_time->toISOString(),
                'description' => $e->description,
                'image' => $e->image,
                'is_featured' => $e->is_featured,
            ])->values();
        }

        $firstDay = \Carbon\Carbon::create($year, $month, 1);

        return response()->json([
            'month' => $month,
            'year' => $year,
            'daysInMonth' => $firstDay->daysInMonth,
            'startDow' => $firstDay->dayOfWeek,
            'prevMonthLastDay' => \Carbon\Carbon::create($year, $month, 1)->subDay()->day,
            'monthLabel' => $firstDay->format('F Y'),
            'today' => now()->format('Y-m-d'),
            'events' => $calendarEvents,
        ]);
    }
}
