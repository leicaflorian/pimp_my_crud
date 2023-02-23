<?php
namespace App\Http\Controllers{{ controllerNamespace }};

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
{{ extraImport }}

class {{ controllerName }}Controller extends Controller {
  /**
   * Display a listing of the resource.
   *
   * @return View
   */
  public function index(): View {
    ${{ varNamePlural }} = {{ modelName }}::all();
    
    return view('{{ routePrefix }}{{ resource }}.index', compact("{{ varNamePlural }}"));
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
    ${{ varName }} = {{ modelName }}::create($data);
    
    return redirect()->route('{{ routePrefix }}{{ resource }}.show', ${{ varName }}->id);
  }
  
  /**
   * Display the specified resource.
   *
   * @param {{ modelName }} ${{ resourceSingular }}
   *
   * @return View
   */
  public function show({{ modelName }} ${{ varName }}): View {
    return view('{{ routePrefix }}{{ resource }}.show', compact("{{ varName }}"));
  }
  
  /**
   * Show the form for editing the specified resource.
   *
   * @param  {{ modelName }}  ${{ resourceSingular }}
   *
   * @return View
   */
  public function edit({{ modelName }} ${{ varName }}): View {
    {{ extraQuery }}
    return view('{{ routePrefix }}{{ resource }}.edit', [
      "{{ varName }}" => ${{ varName }},
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
  public function update(Request $request, {{ modelName }} ${{ varName }}): RedirectResponse {
    $data = $request->all();
    
    ${{ varName }}->update($data);
    
    return redirect()->route('{{ routePrefix }}{{ resource }}.show', ${{ varName }}->id);
  }
  
  /**
   * Remove the specified resource from storage.
   *
   * @param  {{ modelName }}  ${{ resourceSingular }}
   *
   * @return RedirectResponse
   */
  public function destroy({{ modelName }} ${{ varName }}): RedirectResponse {
    ${{ varName }}->destroy();
    
    return redirect()->route('{{ routePrefix }}{{ resource }}.index');
  }
}
