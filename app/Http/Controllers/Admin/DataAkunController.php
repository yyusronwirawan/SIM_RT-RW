<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\EditAkunRequest;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\SettingProfileRequest;

class DataAkunController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $query = User::all();

            return DataTables::of($query)
                ->addColumn('action', function ($item) {
                    return '
                        <a class="btn btn-primary" href="' . route('DataAkun.edit', $item->id) . '">
                            <i class="fas fa-pen"></i> Ubah
                        </a>
                        <button class="btn btn-danger delete_akun" data-id="' . $item->id . '">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    ';
                })
                ->editColumn('role', function($item) {
                    if($item->role == "superadmin")
                    {
                        return '
                            <label class="badge badge-success mr-2">' . $item->role . '</label>
                        ';
                    }else if($item->role == "rt") {
                        return '
                            <label class="badge badge-primary mr-2">' . $item->role . '</label>
                        ';
                    }else{
                        return '
                            <label class="badge badge-danger mr-2">' . $item->role . '</label>
                        ';
                    }
                })
                ->rawColumns(['action', 'role'])
                ->addIndexColumn()
                ->make();
        }
        
        return view('admin.user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SettingProfileRequest $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        
        if($request->hasFile('avatar')){
            $data['avatar'] = $request->file('avatar')->store('profile/avatars', 'public');
        }

        User::create($data);
        return redirect()->route('DataAkun.index')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditAkunRequest $request, $id)
    {   
        $item = User::findOrFail($id);
        $data = $request->all();

        if($request->hasFile('avatar')){
            Storage::delete('public/' . $item->avatar);
            $data['avatar'] = $request->file('avatar')->store('profile/avatars', 'public');
        }
        
        if(!empty($data['password'])){
            $data['password'] = Hash::make($data['password']);
        }else{
            $data = Arr::except($data, ['password']);
        }

        $item->update($data);
        return redirect()->route('DataAkun.index')->with('success', 'Data berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $item = User::findOrFail($id);
        Storage::delete('public/' . $item->avatar);
        $item->delete();    

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }   
}
