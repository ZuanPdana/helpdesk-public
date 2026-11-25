<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LanguageController extends Controller
{
    /** @var array<int, string> */
    protected array $supported = ['id', 'en'];

    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->input('locale');

        if (! in_array($locale, $this->supported, true)) {
            abort(422, 'Unsupported locale.');
        }

        // Persist in session for all users (guest and authenticated)
        $request->session()->put('locale', $locale);

        // Persist on user model if the column exists
        if ($request->user() && Schema::hasColumn('users', 'locale')) {
            $request->user()->update(['locale' => $locale]);
        }

        return redirect()->back()->withHeaders([
            'Vary' => 'Accept-Language',
        ]);
    }
}
