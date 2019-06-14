<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;
use DataTables;

class ItemController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
   		$data = array();
        if ($request->ajax()) {
            $data = Item::latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
   
                           $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editItem">Edit</a>';
   
                           $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteItem">Delete</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
      
        
       
        return view('items',compact('items'));
    }
     
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
      $validator = \Validator::make($request->all(), [
          'name' => 'required|max:255',
          'description' => 'required|max:500',
        ]);
        
        if ($validator->fails())
        {
            return response()->json(['errors'=>$validator->errors()->all(),'status'=>0]);
        }
      
        Item::updateOrCreate(['id' => $request->item_id],
                      ['name' => $request->name, 'description' => $request->description,]);        
        return response()->json(['success'=>'Item saved successfully.','status'=>1]);

        
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Item  $Item
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Item = Item::find($id);
        return response()->json($Item);
    }
  
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $Item
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Item::find($id)->delete();
     
        return response()->json(['success'=>'Item deleted successfully.']);
    }
}
