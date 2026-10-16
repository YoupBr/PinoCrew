<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AttendancePosterController extends Controller
{
    public function __invoke(Request $request): View
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:70'],
            'location' => ['nullable', 'string', 'max:90'],
            'instruction' => ['required', 'string', 'max:220'],
        ], [
            'title.required' => 'Vul een titel in.',
            'title.string' => 'De titel moet tekst zijn.',
            'title.max' => 'Gebruik maximaal 70 tekens voor de titel.',
            'location.string' => 'De locatie moet tekst zijn.',
            'location.max' => 'Gebruik maximaal 90 tekens voor de locatie.',
            'instruction.required' => 'Vul een instructie in.',
            'instruction.string' => 'De instructie moet tekst zijn.',
            'instruction.max' => 'Gebruik maximaal 220 tekens voor de instructie.',
        ]);

        return view('print.attendance-poster', $data);
    }
}
