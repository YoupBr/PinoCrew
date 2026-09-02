<?php

namespace App\Http\Controllers;

use App\Models\HockeyTeam;
use App\Models\Shift;
use App\Models\Signup;
use Illuminate\Http\Request;

class CrewPrintController extends Controller
{
    public function __invoke(Request $request)
    {
        $shiftId = $request->integer('shift');
        $hockeyTeamId = $request->integer('team');
        $search = trim((string) $request->query('search'));

        $signups = Signup::query()
            ->with(['shift', 'hockeyTeam'])

            ->when($shiftId, function ($query) use ($shiftId) {
                $query->where('shift_id', $shiftId);
            })

            ->when($hockeyTeamId, function ($query) use ($hockeyTeamId) {
                $query->where('hockey_team_id', $hockeyTeamId);
            })

            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })

            ->orderBy('name')
            ->get();

        return view('print.signups', [
            'signups' => $signups,

            'shift' => $shiftId
                ? Shift::find($shiftId)
                : null,

            'hockeyTeam' => $hockeyTeamId
                ? HockeyTeam::find($hockeyTeamId)
                : null,
        ]);
    }
}