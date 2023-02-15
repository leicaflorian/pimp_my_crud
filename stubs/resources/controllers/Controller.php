<?php
namespace App\Http\Controllers;

use {{ modelNamespace }};
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class {{ modelName }}Controller extends Controller {
  /**
   * Display a listing of the resource.
   *
   * @return View
   */
  public function index(): View {
    ${{ resource }} = {{ modelName }}::all();
    
    return view('{{ resource }}.index', compact("{{ resource }}"));
  }
  
  /**
   * Show the form for creating a new resource.
   *
   * @return View
   */
  public function create(): View {
    return view('{{ resource }}.create');
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
    ${{ resourceSingular }} = {{ modelName }}::create($data);
    
    return redirect()->route('{{ resource }}.show', ${{ resourceSingular }}->id);
  }
  
  /**
   * Display the specified resource.
   *
   * @param {{ modelName }} ${{ resourceSingular }}
   *
   * @return View
   */
  public function show({{ modelName }} ${{ resourceSingular }}): View {
    return view('{{ resource }}.show', compact("{{ resourceSingular }}"));
  }
  
  /**
   * Show the form for editing the specified resource.
   *
   * @param  {{ modelName }}  ${{ resourceSingular }}
   *
   * @return View
   */
  public function edit({{ modelName }} ${{ resourceSingular }}): View {
    return view('{{ resource }}.edit', compact("{{ resourceSingular }}"));
  }
  
  /**
   * Update the specified resource in storage.
   *
   * @param  Request $request
   * @param  {{ modelName }} ${{ resourceSingular }}
   *
   * @return RedirectResponse
   */
  public function update(Request $request, {{ modelName }} ${{ resourceSingular }}): RedirectResponse {
    $data = $request->all();
    
    {{ modelName }}::update($data);
    
    return redirect()->route('{{ resource }}.show', ${{ resourceSingular }}->id);
  }
  
  /**
   * Remove the specified resource from storage.
   *
   * @param  {{ modelName }}  ${{ resourceSingular }}
   *
   * @return RedirectResponse
   */
  public function destroy({{ modelName }} ${{ resourceSingular }}): RedirectResponse {
    ${{ resourceSingular }}->destroy();
    
    return redirect()->route('{{ resource }}.index');
  }
}
