<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DeveloperController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('developer.panel', compact('users'));
    }

    public function flush()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Artisan::call('migrate:fresh', ['--force' => true]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return back()->with('success', 'Database was refreshed using migrate:fresh.');

    }

    public function flushAndSeed()
    {
        Artisan::call('migrate:refresh', [
            '--seed'  => true,
            '--force' => true,
        ]);

        return back()->with('success', 'Database was refreshed and seeders have been executed.');
    }

    public function seed()
    {
        Artisan::call('db:seed', ['--force' => true]);

        return back()->with('success', 'Seeders worked.');
    }

    public function clearSession()
    {
        Session::flush();

        return redirect('/')->with('success', 'Session is cleared.');
    }

    public function loginAs(User $user)
    {
        Auth::logout();
        Auth::login($user);
        return redirect('/')->with('success', 'You are now logged in as ' . $user->name);
    }

    public function clearCache()
    {
        Artisan::call('optimize:clear');

        return back()->with('success', 'Application cache cleared.');
    }

    public function createStorageLink()
    {
        try {
            Artisan::call('storage:link');
            return back()->with('success', 'Storage symlink created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create symlink: ' . $e->getMessage());
        }
    }
}
