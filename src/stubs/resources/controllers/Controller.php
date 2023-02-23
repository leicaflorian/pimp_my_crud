<?php
namespace App\Http\Controllers{{ controllerNamespace }};

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
{{ extraImport }}

class {{ modelName }}Controller extends Controller {
  /**
   * Display a listing of the resource.
   *
   * @return View
   */
  public function index(): View {
    ${{ resource }} = {{ modelName }}::all();
    
    return view('{{ routePrefix }}{{ resource }}.index', compact("{{ resource }}"));
  }
  
  /**
   * Show the form for creating a new resource.
   *
   * @return View
   */
  public function create(): View {
    {{ extraQuery }}
    return view('{{ routePrefix }}{{ resource }}.create', [{{ extraViewData }}]);
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
    
    return redirect()->route('{{ routePrefix }}{{ resource }}.show', ${{ resourceSingular }}->id);
  }
  
  /**
   * Display the specified resource.
   *
   * @param {{ modelName }} ${{ resourceSingular }}
   *
   * @return View
   */
  public function show({{ modelName }} ${{ resourceSingular }}): View {
    return view('{{ routePrefix }}{{ resource }}.show', compact("{{ resourceSingular }}"));
  }
  
  /**
   * Show the form for editing the specified resource.
   *
   * @param  {{ modelName }}  ${{ resourceSingular }}
   *
   * @return View
   */
  public function edit({{ modelName }} ${{ resourceSingular }}): View {
    {{ extraQuery }}
    return view('{{ routePrefix }}{{ resource }}.edit', [
      "{{ resourceSingular }}" => ${{ resourceSingular }},
      {{ extraViewData }}
    ]);
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
    
    ${{ resourceSingular }}->update($data);
    
    return redirect()->route('{{ routePrefix }}{{ resource }}.show', ${{ resourceSingular }}->id);
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
    
    return redirect()->route('{{ routePrefix }}{{ resource }}.index');
  }
}
