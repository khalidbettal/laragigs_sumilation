<?php

namespace App\Http\Controllers;

use App\Models\listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class listingController extends Controller
{
    public function index(){
        return view('listings.index',[
            'listings' => listing::latest()->filter(request(['tag','search']))->simplePaginate(6)
        ]);
    }
    public function show(listing $listing){
        return view('listings.show',[
            'listing' => $listing
          ]);
    }
    public function create()
{
        return view('listings.create');
}
    public function store(Request $request)
{
    // dd();
       $formFields = $request->validate([
       'title' => 'required',
       'tags'=> 'required',
       'company' => ['required',Rule::unique('listings','company')],
       'location'=> 'required',
       'email'=> ['required',Rule::unique('listings','email')],
       'website'=> 'required',
       'description'=> 'required'

    ]);
        if($request->hasFile('logo')){
        $formFields['logo']= $request->file('logo')->store('logos','public');
    }
    $formFields['user_id'] = auth()->id();
       listing::create($formFields);
       return redirect('/')->with('message','listing created succesfully');
}
    public function edit(listing $listing){
        return view('listings.edit', ['listing'=> $listing]);

    }
    public function update(Request $request, listing $listing)
{
    if($listing->user_id != auth()->id()) {
        abort(403, 'Unauthorized Action');
    }
       $formFields = $request->validate([
       'title' => 'required',
       'tags'=> 'required',
       'company' => 'required',
       'location'=> 'required',
       'email'=> 'required',
       'website'=> 'required',
       'description'=> 'required'

    ]);
        if($request->hasFile('logo')){
        $formFields['logo']= $request->file('logo')->store('logos','public');
    }
       $listing->update($formFields);
       return redirect('/listings/'.$listing->id)->with('message','listing has edited');
}
    public function destroy(listing $listing){
        if($listing->user_id != auth()->id()) {
            abort(403, 'Unauthorized Action');
        }
      $listing->delete();
      return redirect('/')->with('message', 'Deleted successfully!');
    }

    public function manage(){
        return view('listings.manage',['listings' => auth()->user()->listings()->get()]);
    }
    
}
