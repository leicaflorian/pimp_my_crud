<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller {
  /**
   * Display a listing of the resource.
   *
   * @return View
   */
  public function index(): View {
    $users = User::all();
    
    return view('users.index', compact("users"));
  }
  
  /**
   * Show the form for creating a new resource.
   *
   * @return View
   */
  public function create(): View {
    return view('users.create');
  }
  
  /**
   * Store a newly created resource in storage.
   *
   * @param  Request  $request
   *
   * @return RedirectResponse
   */
  public function store(Request $request): RedirectResponse {
    $data = $request->all();
    $user = User::create($data);
    
    return redirect()->route('users.show', $user->id);
  }
  
  /**
   * Display the specified resource.
   *
   * @param User $user
   *
   * @return View
   */
  public function show(User $user): View {
    return view('users.show', compact("user"));
  }
  
  /**
   * Show the form for editing the specified resource.
   *
   * @param  User  $user
   *
   * @return View
   */
  public function edit(User $user): View {
    return view('users.edit', compact("user"));
  }
  
  /**
   * Update the specified resource in storage.
   *
   * @param  Request $request
   * @param  User $user
   *
   * @return RedirectResponse
   */
  public function update(Request $request, User $user): RedirectResponse {
    $data = $request->all();
    
    User::update($data);
    
    return redirect()->route('users.show', $user->id);
  }
  
  /**
   * Remove the specified resource from storage.
   *
   * @param  User  $user
   *
   * @return RedirectResponse
   */
  public function destroy(User $user): RedirectResponse {
    $user->destroy();
    
    return redirect()->route('users.index');
  }
}
