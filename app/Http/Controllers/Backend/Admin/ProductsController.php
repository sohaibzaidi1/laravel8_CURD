<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Catagories;
use App\Models\Products;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use View;
use DB;

class ProductsController extends Controller
{
   /**
    * Display a listing of the resource.
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.product.index');
   }

   public function getAll()
   {
      $can_edit = $can_delete = '';
      if (!auth()->user()->can('user-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('user-delete')) {
         $can_delete = "style='display:none;'";
      }
      $products = Products::with(['category'])->get();

      return Datatables::of($products)
        ->addColumn('file_path', function ($products) {
           return "<img src='" . asset($products->file_path) . "' class='img-thumbnail' width='50px'>";
        })
        ->addColumn('catagory', function ($products) {
            if (isset($products->category)) {
                return ucfirst($products->category->title);
            }else{
                return 'No Catagory Found';
            }

         })
        ->addColumn('status', function ($products) {
           return $products->status ? '<label class="badge badge-success">Active</label>' : '<label class="badge badge-danger">Inactive</label>';
        })
        ->addColumn('action', function ($products) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $products->id . '" class="btn btn-xs btn-info mr-1 view" title="View"><i class="fa fa-eye"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $products->id . '" class="btn btn-xs btn-info mr-1 edit" title="Edit"><i class="fa fa-edit"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $products->id . '" class="btn btn-xs btn-danger mr-1 delete" title="Delete"><i class="fa fa-trash"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->rawColumns(['action', 'file_path', 'status'])
        ->addIndexColumn()
        ->make(true);
   }


   /**
    * Show the form for creating a new resource.
    * @return \Illuminate\Http\Response
    */
   public function create(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('user-create');
         if ($haspermision) {
            $catagories = Catagories::pluck('title', 'id');
            $view = View::make('backend.admin.product.create', compact('catagories'))->render();
            return response()->json(['html' => $view]);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request $request
    *
    * @return \Illuminate\Http\Response
    */
   public function store(Request $request)
   {
      if ($request->ajax()) {
         // Setup the validator
         $rules = [
           'title' => 'required',
           'description' => 'required',
           'photo' => 'image|max:2024|mimes:jpeg,jpg,png'
         ];

         $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            return response()->json([
              'type' => 'error',
              'errors' => $validator->getMessageBag()->toArray()
            ]);
         } else {

            $file_path = "assets/images/products/default.png";

            if ($request->hasFile('photo')) {
               if ($request->file('photo')->isValid()) {
                  $destinationPath = public_path('assets/images/products/');
                  $extension = $request->file('photo')->getClientOriginalExtension();
                  $fileName = time() . '.' . $extension;
                  $file_path = 'assets/images/products/' . $fileName;
                  $request->file('photo')->move($destinationPath, $fileName);
               } else {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Please! File is not valid</div>"
                  ]);
               }
            }


            DB::beginTransaction();
            try {
               $product = new Products();
               $product->title = $request->input('title');
               $product->description = $request->input('description');
               $product->cat_id = $request->input('cat_id');
               $product->file_path = $file_path;
               $product->save();
               DB::commit();
               return response()->json(['type' => 'success', 'message' => "Successfully Created"]);

            } catch (\Exception $e) {
               DB::rollback();
               return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   /**
    * Display the specified resource.
    *
    * @param  int $id
    *
    * @return \Illuminate\Http\Response
    */
   public function show($id, Request $request)
   {
      if ($request->ajax()) {
         $product = Products::findOrFail($id);
         $view = View::make('backend.admin.product.view', compact('product'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  int $id
    *
    * @return \Illuminate\Http\Response
    */
   public function edit($id, Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('user-edit');
         if ($haspermision) {
            $catagories = Catagories::pluck('title', 'id');
            $product = Products::where('id', $id)->first();
            $view = View::make('backend.admin.product.edit', compact('product','catagories'))->render();
            return response()->json(['html' => $view]);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request $request
    * @param  int $id
    *
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Products $product)
   {
      if ($request->ajax()) {

        Products::findOrFail($product->id);

         $rules = [
           'title' => 'required',
           'description' => 'required',
           'photo' => 'image|max:2024|mimes:jpeg,jpg,png'
         ];

         $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            return response()->json([
              'type' => 'error',
              'errors' => $validator->getMessageBag()->toArray()
            ]);
         } else {

            $file_path = $request->input('SelectedFileName');;

            if ($request->hasFile('photo')) {
               if ($request->file('photo')->isValid()) {
                  $destinationPath = public_path('assets/images/products/');
                  $extension = $request->file('photo')->getClientOriginalExtension(); // getting image extension
                  $fileName = time() . '.' . $extension;
                  $file_path = 'assets/images/products/' . $fileName;
                  $request->file('photo')->move($destinationPath, $fileName);
               } else {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Please! File is not valid</div>"
                  ]);
               }
            }

            DB::beginTransaction();
            try {
               $product->title = $request->input('title');
               $product->description = $request->input('description');
               $product->cat_id = $request->input('cat_id');
               $product->status = $request->input('status');
               $product->file_path = $file_path;
               $product->save();
               DB::commit();
               return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);

            } catch (\Exception $e) {
               DB::rollback();
               return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
            }

         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  int $id
    *
    * @return \Illuminate\Http\Response
    */
   public function destroy($id, Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('user-delete');
         if ($haspermision) {
            $user = Products::findOrFail($id); //Get Product with specified id
            $user->delete();
            return response()->json(['type' => 'success', 'message' => "Successfully Deleted"]);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}
