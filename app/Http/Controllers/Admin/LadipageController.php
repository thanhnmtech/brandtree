<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LadipagePage;
use App\Models\LadipageFormSubmission;
use App\Services\LadipageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LadipageController extends Controller
{
    protected $ladipageService;

    public function __construct(LadipageService $ladipageService)
    {
        $this->middleware('auth');
        $this->ladipageService = $ladipageService;
    }

    /**
     * Display a listing of Ladipage pages
     */
    public function index()
    {
        $pages = LadipagePage::with('user')
            ->latest()
            ->paginate(15);

        $statistics = $this->ladipageService->getStatistics();

        return view('admin.ladipage.index', compact('pages', 'statistics'));
    }

    /**
     * Show the form for creating a new Ladipage page
     */
    public function create()
    {
        return view('admin.ladipage.create');
    }

    /**
     * Store a newly created Ladipage page
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ladipage_pages,slug',
            'ladipage_url' => 'nullable|url',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,published',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['user_id'] = auth()->id();

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $page = LadipagePage::create($validated);

        return redirect()
            ->route('admin.ladipage.index')
            ->with('success', __('messages.ladipage.created'));
    }

    /**
     * Display the specified Ladipage page
     */
    public function show(LadipagePage $ladipage)
    {
        $ladipage->load('user', 'formSubmissions');

        return view('admin.ladipage.show', compact('ladipage'));
    }

    /**
     * Show the form for editing the specified Ladipage page
     */
    public function edit(LadipagePage $ladipage)
    {
        return view('admin.ladipage.edit', compact('ladipage'));
    }

    /**
     * Update the specified Ladipage page
     */
    public function update(Request $request, LadipagePage $ladipage)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:ladipage_pages,slug,' . $ladipage->id,
            'ladipage_url' => 'nullable|url',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
        ]);

        if ($validated['status'] === 'published' && $ladipage->status !== 'published') {
            $validated['published_at'] = now();
        } elseif ($validated['status'] !== 'published') {
            $validated['published_at'] = null;
        }

        $ladipage->update($validated);

        return redirect()
            ->route('admin.ladipage.index')
            ->with('success', __('messages.ladipage.updated'));
    }

    /**
     * Remove the specified Ladipage page
     */
    public function destroy(LadipagePage $ladipage)
    {
        $ladipage->delete();

        return redirect()
            ->route('admin.ladipage.index')
            ->with('success', __('messages.ladipage.deleted'));
    }

    /**
     * Display webhook configuration
     */
    public function settings()
    {
        $webhookUrl = $this->ladipageService->getWebhookUrl();
        $apiToken = $this->ladipageService->getApiToken();
        $statistics = $this->ladipageService->getStatistics();

        return view('admin.ladipage.settings', compact('webhookUrl', 'apiToken', 'statistics'));
    }

    /**
     * Display form submissions
     */
    public function submissions()
    {
        $submissions = LadipageFormSubmission::with('ladipagePage')
            ->latest()
            ->paginate(20);

        return view('admin.ladipage.submissions', compact('submissions'));
    }
}
