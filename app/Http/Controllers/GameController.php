<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ApiRequestException;

class GameController extends Controller
{
    public function index()
    {
        try {
            $season = 2022; 
            $apiKey = 'a2efe8f87fmshf54ad09e164ba3dp1afae4jsnca580a2984ca';

            $filteredDate = request()->query('date');

            $gamesQuery = Http::withHeaders([
                'x-rapidapi-key' => $apiKey,
                'x-rapidapi-host' => 'api-nba-v1.p.rapidapi.com',
            ])->get('https://api-nba-v1.p.rapidapi.com/games', [
                'season' => $season,
                'date' => $filteredDate,
            ]);

            if ($gamesQuery->failed()) {
                throw new ApiRequestException('Failed to fetch games data. Please try again later.');
            }

            $gamesData = $gamesQuery->json('response');

            // Convert the dates to 'YYYY-MM-DD' format
            foreach ($gamesData as &$game) {
                $game['date']['start'] = Carbon::parse($game['date']['start'])->format('Y-m-d');
            }

            // validatee the data 
            $validatedGamesData = $this->validateGamesData($gamesData);

            //paginate the games
            $perPage = 15;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $gamesCollection = new Collection($validatedGamesData);
            $currentPageGames = $gamesCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $gamesPaginated = new LengthAwarePaginator($currentPageGames, count($gamesCollection), $perPage);
            $gamesPaginated->setPath(request()->url());

            return view('games.index', compact('gamesPaginated'));
        } catch (ApiRequestException $e) {
            Log::error($e->getMessage());
            // API error page
            return redirect()->route('error.api', ['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // generic error page
            return redirect()->route('error.generic');
        }
    }

    private function validateGamesData($gamesData)
    {
        // validation
        // check if required fields are present
        $validatedData = [];
        foreach ($gamesData as $game) {
            if (isset($game['id'], $game['date'], $game['teams'], $game['status'], $game['scores'], $game['arena'])) {
                $validatedData[] = $game;
            }
        }
        return $validatedData;
    }
}
